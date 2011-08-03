<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * BitAuth
 *
 * Authentication and Permissions System
 *
 * @author Dan Montgomery <dan@dmontgomery.net>
 * @license DBAD <http://dbad-license.org/license>
 * @link https://github.com/danmontgomery/codeigniter-bitauth
 * @link http://dmontgomery.net/bitauth
 */
class Bitauth extends CI_Model
{

	public $_table;
	public $_default_group_id;
	public $_admin_permission;
	public $_remember_token_name;
	public $_remember_token_expires;
	public $_remember_token_updates;
	public $_require_user_activation;
	public $_pwd_max_age;
	public $_pwd_min_length;
	public $_pwd_max_length;
	public $_pwd_complexity;
	public $_pwd_complexity_chars;
	public $_error_delim_prefix = '<p>';
	public $_error_delim_suffix = '</p>';
	public $_invalid_logins;
	public $_lockout_time;
	public $_date_format;

	private $_all_permissions;
	private $_error;

	// IF YOU CHANGE THE STRUCTURE OF THE `users` TABLE, THAT CHANGE MUST BE REFLECTED HERE
	private $_data_fields = array(
		'username','password','password_last_set','password_never_expires','remember_me', 'activation_code',
		'active','forgot_code','forgot_generated','enabled','last_login','last_login_ip'
	);

	public function __construct()
	{
		parent::__construct();

		$this->load->helper('language');
		$this->lang->load('bitauth');

		if( ! function_exists('gmp_init'))
		{
			log_message('error', lang('bitauth_enable_gmp'));
			show_error(lang('bitauth_enable_gmp'));
		}

		$this->load->database();
		$this->load->library('encrypt');
		$this->load->library('session');
		$this->load->config('bitauth', TRUE);

		// Load Phpass library
		$this->load->library('phpass', array(
			'iteration_count_log2' => $this->config->item('phpass_iterations', 'bitauth'),
			'portable_hashes' => $this->config->item('phpass_portable', 'bitauth')
		));

		$this->_table						= $this->config->item('table', 'bitauth');
		$this->_default_group_id			= $this->config->item('default_group_id', 'bitauth');
		$this->_remember_token_name			= $this->config->item('remember_token_name', 'bitauth');
		$this->_remember_token_expires		= $this->config->item('remember_token_expires', 'bitauth');
		$this->_remember_token_updates		= $this->config->item('remember_token_updates', 'bitauth');
		$this->_require_user_activation		= $this->config->item('require_user_activation', 'bitauth');
		$this->_pwd_max_age					= $this->config->item('pwd_max_age', 'bitauth');
		$this->_pwd_age_notification		= $this->config->item('pwd_age_notification', 'bitauth');
		$this->_pwd_min_length				= $this->config->item('pwd_min_length', 'bitauth');
		$this->_pwd_max_length				= $this->config->item('pwd_max_length', 'bitauth');
		$this->_pwd_complexity				= $this->config->item('pwd_complexity', 'bitauth');
		$this->_pwd_complexity_chars		= $this->config->item('pwd_complexity_chars', 'bitauth');
		$this->_invalid_logins				= $this->config->item('invalid_logins', 'bitauth');
		$this->_lockout_time				= $this->config->item('lockout_time', 'bitauth');
		$this->_date_format					= $this->config->item('date_format', 'bitauth');

		$this->_all_permissions				= $this->config->item('permissions', 'bitauth');

		// Grab the first permission on the list as the administrator permission
		$slugs = array_keys($this->_all_permissions);
		$this->_admin_permission = $slugs[0];

		// Specify any extra login fields
		$this->_login_fields = array();

		if($this->logged_in())
		{
			$this->get_session_values();
		}
		else if($this->input->cookie($this->_remember_token_name))
		{
			$this->login_from_token();
		}

		$this->set_error($this->session->flashdata('bitauth_error'), FALSE);
		unset($slugs);

	}

	/**
	 * Bitauth::login()
	 *
	 */
	public function login($username, $password, $remember = FALSE, $extra = array(), $token = NULL)
	{
		if(empty($username))
		{
			$this->set_error(lang('bitauth_username_required'));
			return FALSE;
		}

		if($this->locked_out())
		{
			$this->set_error(lang('bitauth_user_locked_out'));
			return FALSE;
		}

		$user = $this->get_user_by_username($username);

		if($user !== FALSE)
		{
			if($this->phpass->CheckPassword($password, $user->password) || ($password === NULL && $user->remember_me == $token))
			{
				if( ! empty($this->_login_fields) && ! $this->check_login_fields($user, $extra))
				{
					$this->log_attempt($user->user_id, FALSE);
					return FALSE;
				}

				if( ! $user->active)
				{
					$this->log_attempt($user->user_id, FALSE);
					$this->set_error(lang('bitauth_user_inactive'));
					return FALSE;
				}

				if($this->password_is_expired($user))
				{
					$this->log_attempt($user->user_id, FALSE);
					$this->set_error(lang('bitauth_pwd_expired'));
					return FALSE;
				}

				$this->set_session_values($user);

				if($remember != FALSE)
				{
					$this->update_remember_token($user->username, $user->user_id);
				}

				$data = array(
					'last_login' => $this->timestamp(),
					'last_login_ip' => ip2long($_SERVER['REMOTE_ADDR'])
				);

				// If user logged in, they must have remembered their password.
				if( ! empty($user->forgot_code))
				{
					$data['forgot_code'] = '';
				}

				// Update last login timestamp and IP
				$this->update_user($user->user_id, $data);

				$this->log_attempt($user->user_id, TRUE);
				return TRUE;
			}

			$this->log_attempt($user->user_id, FALSE);
		}
		else
		{
			$this->log_attempt(FALSE, FALSE);
		}

		$this->set_error(sprintf(lang('bitauth_login_failed'), lang('bitauth_username')));
		return FALSE;
	}

	/**
	 * Bitauth::login_from_token()
	 *
	 */
	public function login_from_token()
	{
		if(($token = $this->input->cookie($this->_remember_token_name)))
		{
			$token = explode("\n", $token);
			$username = $token[0];

			if($this->login($username, NULL, (bool)$this->_remember_token_updates, FALSE, implode("\n", $token)))
			{
				return TRUE;
			}
		}

		$this->logout();
		return FALSE;
	}

	/**
	 * Bitauth::logout()
	 *
	 */
	public function logout()
	{
		$session_data = $this->session->all_userdata();
		foreach($session_data as $_key => $_value)
		{
			if(substr($_key, 0, 8) !== 'bitauth_')
			{
				$this->session->unset_userdata($_key);
			}
		}

		unset($this->username);
		$this->delete_remember_token();

		return;
	}

	/**
	 * Bitauth::check_login_fields()
	 *
	 */
	public function check_login_fields($user, $data)
	{
		if(empty($this->_login_fields))
		{
			return TRUE;
		}

		foreach($this->_login_fields as $_field)
		{
			if( ! isset($user->{$_field}) || $user->{$_field} != $data[$_field])
			{
				if(lang('bitauth_invalid_'.$_field))
				{
					$this->set_error(lang('bitauth_invalid_'.$_field));
				}
				else
				{
					$this->set_error(sprintf(lang('bitauth_lang_not_found'), 'bitauth_invalid_'.$_field));
				}
				return FALSE;
			}
		}

		return TRUE;
	}

	/**
	 * Bitauth::add_login_field()
	 *
	 */
	public function add_login_field($field)
	{
		if(is_array($field))
		{
			foreach($field as $_field)
			{
				$this->add_login_field($_field);
			}

			return;
		}

		$this->_login_fields[] = $field;
	}

	/**
	 * Bitauth::locked_out()
	 *
	 */
	public function locked_out()
	{
		// If invalid_logins is disabled, can't be locked out
		if($this->_invalid_logins == 0)
		{
			return FALSE;
		}

		$query = $this->db
			->where('time >=', $this->timestamp(strtotime($this->_lockout_time.' minutes ago')))
			->where('ip_address', ip2long($_SERVER['REMOTE_ADDR']))
			->where('success', 0)
			->get($this->_table['logins']);

		if($query && $query->num_rows() >= $this->_invalid_logins)
		{
			return TRUE;
		}

		return FALSE;
	}

	/**
	 * Bitauth::log_attempt()
	 *
	 */
	public function log_attempt($user_id, $success = 0)
	{
		$data = array(
			'ip_address' => ip2long($_SERVER['REMOTE_ADDR']),
			'user_id' => $user_id,
			'success' => $success,
			'time' => $this->timestamp()
		);

		return $this->db->insert($this->_table['logins'], $data);
	}

	/**
	 * Bitauth::set_session_values()
	 *
	 */
	public function set_session_values($values)
	{
		$session_data = array();
		foreach($values as $_key => $_value)
		{
			if($_key !== 'password')
			{
				$this->$_key = $_value;
				$session_data['bitauth_'.$_key] = $_value;
			}
		}

		$this->session->set_userdata($session_data);
	}

	/**
	 * Bitauth::get_session_values()
	 *
	 */
	public function get_session_values()
	{
		$session_data = $this->session->all_userdata();
		foreach($session_data as $_key => $_value)
		{
			if(substr($_key, 0, 8) !== 'bitauth_')
			{
				continue;
			}

			$_key = substr($_key, 8);

			if( ! isset($this->$_key))
			{
				$this->$_key = $_value;
			}
			else
			{
				log_message('error', lang('bitauth_data_error').$_key);
				show_error(lang('bitauth_data_error').$_key);
			}
		}
	}

	/**
	 * Bitauth::update_remember_token()
	 *
	 */
	public function update_remember_token($username = NULL, $user_id = NULL)
	{
		if( ! $this->logged_in())
		{
			return;
		}

		if($username === NULL)
		{
			$username = $this->username;
		}
		if($user_id === NULL)
		{
			$user_id = $this->user_id;
		}

		$session_id = sha1(mt_rand(0, PHP_INT_MAX).time());

		$cookie = array(
			'prefix' => $this->config->item('cookie_prefix'),
			'name' => $this->_remember_token_name,
			'value' => $username."\n".$session_id,
			'expire' => $this->_remember_token_expires,
			'domain' => $this->config->item('cookie_domain'),
			'path' => $this->config->item('cookie_path'),
			'secure' => $this->config->item('cookie_secure')
		);

		$this->input->set_cookie($cookie);

		return $this->update_user($user_id, array('remember_me' => $username."\n".$session_id));
	}

	/**
	 * Bitauth::delete_remember_token()
	 *
	 */
	public function delete_remember_token()
	{
		if($this->input->cookie($this->_remember_token_name))
		{
			$cookie = array(
				'name' => $this->_remember_token_name,
				'value' => '',
				'expire' => -86400
			);

			$this->db
				->set('remember_me', '')
				->where('remember_me', $this->input->cookie($this->_remember_token_name))
				->update($this->_table['users']);

			$this->input->set_cookie($cookie);
		}
	}

	/**
	 * Bitauth::add_user()
	 *
	 */
	public function add_user($data, $require_activation = NULL)
	{
		if( ! is_array($data) && ! is_object($data))
		{
			$this->set_error(lang('bitauth_add_user_datatype'));
			return FALSE;
		}

		if($require_activation === NULL)
		{
			$require_activation = $this->_require_user_activation;
		}

		$data = (array)$data;

		if(empty($data['username']))
		{
			$this->set_error(sprintf(lang('bitauth_username_required'), lang('bitauth_username')));
			return FALSE;
		}

		if(empty($data['password']))
		{
			$this->set_error(lang('bitauth_password_required'));
			return FALSE;
		}

		$data['active'] = ! (bool)$require_activation;
		if($require_activation)
		{
			$data['activation_code'] = $this->generate_code();
		}

		// Just in case
		if( ! empty($data['user_id']))
		{
			unset($data['user_id']);
		}

		if(isset($data['groups']))
		{
			$groups = $data['groups'];
			unset($data['groups']);
		}

		$userdata = array();
		foreach($data as $_key => $_val)
		{
			if( ! in_array($_key, $this->_data_fields))
			{
				$userdata[$_key] = $_val;
				unset($data[$_key]);
			}
		}

		$data['password'] = $this->hash_password($data['password']);
		$data['password_last_set'] = $this->timestamp();

		$this->db->trans_start();

		$this->db->insert($this->_table['users'], $data);

		$user_id = $this->db->insert_id();
		if( ! empty($userdata))
		{
			$userdata['user_id'] = $user_id;
			$this->db->insert($this->_table['data'], $userdata);
		}

		if(empty($groups))
		{
			$this->db->insert($this->_table['assoc'], array('user_id' => $user_id, 'group_id' => $this->_default_group_id));
		}
		else
		{
			$new_groups = array();
			foreach($groups as $group_id)
			{
				$new_groups[] = array(
					'user_id' => $user_id,
					'group_id' => (int)$group_id
				);
			}

			$this->db->insert_batch($this->_table['assoc'], $new_groups);
		}

		if($this->db->trans_status() === FALSE)
		{
			$this->set_error(lang('bitauth_add_user_failed'));
			$this->db->trans_rollback();

			return FALSE;
		}

		$this->db->trans_commit();
		return TRUE;
	}

	/**
	 * Bitauth::add_group()
	 *
	 */
	public function add_group($data)
	{
		if( ! is_array($data) && ! is_object($data))
		{
			$this->set_error(lang('bitauth_add_group_datatype'));
			return FALSE;
		}

		$data = (array)$data;

		if(empty($data['name']))
		{
			$this->set_error(lang('bitauth_groupname_required'));
			return FALSE;
		}

		// Just in case
		if( ! empty($data['group_id']))
		{
			unset($data['group_id']);
		}

		if(isset($data['members']))
		{
			$members = $data['members'];
			unset($data['members']);
		}

		$permissions = gmp_init(0);
		if(isset($data['permissions']) && is_array($data['permissions']))
		{
			foreach($data['permissions'] as $slug)
			{
				if(($index = $this->get_perm($slug)) !== FALSE)
				{
					gmp_setbit($permissions, $index);
				}
			}
		}

		$data['permissions'] = gmp_strval($permissions);

		$this->db->trans_start();

		$this->db->insert($this->_table['groups'], $data);

		$group_id = $this->db->insert_id();

		// If we were given an array of user id's, set them as the group members
		if( ! empty($members))
		{
			$new_members = array();
			foreach(array_unique($members) as $user_id)
			{
				$new_members[] = array(
					'group_id' => $group_id,
					'user_id' => (int)$user_id
				);
			}

			$this->db->insert_batch($this->_table['assoc'], $new_members);
		}

		if($this->db->trans_status() === FALSE)
		{
			$this->set_error(lang('bitauth_add_group_failed'));
			$this->db->trans_rollback();

			return FALSE;
		}

		$this->db->trans_commit();
		return TRUE;
	}

	/**
	 * Bitauth::activate()
	 *
	 */
	public function activate($activation_code)
	{
		$query = $this->db->where('activation_code', $activation_code)->get($this->_table['users']);
		if($query && $query->num_rows())
		{
			$user = $query->row();
			return $this->update_user($user->user_id, array('active' => 1, 'activation_code' => ''));
		}

		$this->set_error(lang('bitauth_activate_failed'));
		return FALSE;
	}

	/**
	 * Bitauth::update_user()
	 *
	 */
	public function update_user($id, $data)
	{
		if( ! is_array($data) && ! is_object($data))
		{
			$this->set_error(lang('bitauth_edit_user_datatype'));
			return FALSE;
		}

		$data = (array)$data;

		if(isset($data['username']) && ! strlen($data['username']))
		{
			$this->set_error(sprintf(lang('bitauth_username_required'), lang('bitauth_username')));
			return FALSE;
		}

		// Just in case
		unset($data['user_id'], $data['permissions'], $data['id']);

		if(isset($data['groups']))
		{
			$groups = $data['groups'];
			unset($data['groups']);
		}

		$userdata = array();
		foreach($data as $_key => $_val)
		{
			if( ! in_array($_key, $this->_data_fields))
			{
				$userdata[$_key] = $_val;
				unset($data[$_key]);
			}
		}

		$this->db->trans_start();

		if( ! empty($data))
		{
			$this->db->set($data)->where('user_id', $id)->update($this->_table['users']);
		}

		if( ! empty($userdata))
		{
			$this->db->set($userdata)->where('user_id', $id)->update($this->_table['data']);
		}


		if(isset($groups))
		{
			$this->db->where('user_id', $id)->delete($this->_table['assoc']);
			$new_groups = array();
			if( ! empty($groups))
			{
				foreach($groups as $group_id)
				{
					$new_groups[] = array(
						'user_id' => $id,
						'group_id' => (int)$group_id
					);
				}

				$this->db->insert_batch($this->_table['assoc'], $new_groups);
			}
		}

		if($this->db->trans_status() === FALSE)
		{
			$this->set_error(lang('bitauth_edit_user_failed'));
			$this->db->trans_rollback();

			return FALSE;
		}

		if($this->user_id == $id)
		{
			$user = $this->get_user_by_id($id);
			$this->set_session_values($user);
		}

		$this->db->trans_commit();
		return TRUE;
	}

	/**
	 * Bitauth::enable()
	 *
	 */
	public function enable($user_id)
	{
		return $this->disable($user_id, 1);
	}

	/**
	 * Bitauth::disable()
	 *
	 */
	public function disable($user_id, $enabled = 0)
	{
		if($user = $this->get_user_by_id($user_id, $enabled))
		{
			return $this->update_user($user_id, array('enabled' => $enabled));
		}

		$this->set_error(sprintf(lang('bitauth_user_not_found'), $user_id));
		return FALSE;
	}

	/**
	 * Bitauth::delete()
	 *
	 */
	public function delete($user_id)
	{
		if($user = $this->get_user_by_id($user_id))
		{
			$this->db->trans_start();

			$this->update_user($user_id, array('enabled' => 0, 'groups' => array()));
			$this->db->where('user_id', $user_id)->delete($this->_table['data']);

			if($this->db->trans_status() == FALSE)
			{
				$this->set_error(lang('bitauth_del_user_failed'));
				$this->db->trans_rollback();
				return FALSE;
			}

			$this->db->trans_commit();
			return TRUE;
		}

		$this->set_error(sprintf(lang('bitauth_user_not_found'), $user_id));
		return FALSE;
	}

	/**
	 * Bitauth::forgot_password()
	 *
	 */
	public function forgot_password($user_id)
	{
		if($user = $this->get_user_by_id($user_id))
		{
			$user->forgot_code = $this->generate_code();
			$user->forgot_generated = $this->timestamp();

			return $this->update_user($user_id, $user);
		}

		return FALSE;
	}

	/**
	 * Bitauth::set_password()
	 *
	 */
	public function set_password($user_id, $new_password)
	{
		$new_password = $this->hash_password($new_password);

		$data = array(
			'password' => $new_password,
			'password_last_set' => $this->timestamp()
		);

		if($this->update_user($user_id, $data))
		{
			return TRUE;
		}

		//$this->set_error(lang('bitauth_set_pw_failed'));
		return FALSE;
	}

	/**
	 * Bitauth::update_group()
	 *
	 */
	public function update_group($id, $data)
	{
		if( ! is_array($data) && ! is_object($data))
		{
			$this->set_error(lang('bitauth_edit_group_datatype'));
			return FALSE;
		}

		$data = (array)$data;

		// Just in case
		unset($data['group_id'], $data['id']);

		// If this array was returned by get_groups(), don't try to update a non-existent column
		if(isset($data['members']))
		{
			$members = $data['members'];
			unset($data['members']);
		}

		$permissions = gmp_init(0);

		if(isset($data['permissions']))
		{
			if(is_array($data['permissions']))
			{
				foreach($data['permissions'] as $slug)
				{
					if(($index = $this->get_perm($slug)) !== FALSE)
					{
						gmp_setbit($permissions, $index);
					}
				}
			}
			else if(is_numeric($data['permissions']))
			{
				$permissions = gmp_init($data['permissions']);
			}
		}

		$data['permissions'] = gmp_strval($permissions);

		$this->db->trans_start();

		$this->db->set($data)->where('group_id', $id)->update($this->_table['groups']);

		// If we were given an array of user id's, set them as the group members
		if(isset($members))
		{
			$this->db->where('group_id', $id)->delete($this->_table['assoc']);

			$new_members = array();
			if( ! empty($members))
			{
				foreach(array_unique($members) as $user_id)
				{
					$new_members[] = array(
						'group_id' => $id,
						'user_id' => (int)$user_id
					);
				}

				$this->db->insert_batch($this->_table['assoc'], $new_members);
			}
		}

		if($this->db->trans_status() === FALSE)
		{
			$this->set_error(lang('bitauth_edit_group_failed'));
			$this->db->trans_rollback();

			return FALSE;
		}

		$this->db->trans_commit();
		return TRUE;

	}

	/**
	 * Bitauth::delete_group()
	 *
	 */
	public function delete_group($group_id)
	{
		$this->db->trans_start();

		$this->db->where('group_id', $group_id)->delete($this->_table['groups']);
		$this->db->where('group_id', $group_id)->delete($this->_table['assoc']);

		if($this->db->trans_status() == FALSE)
		{
			$this->set_error(lang('bitauth_del_group_failed'));
			$this->db->trans_rollback();
			return FALSE;
		}

		$this->db->trans_commit();
		return TRUE;
	}

	/**
	 * Bitauth::has_perm()
	 *
	 */
	public function has_perm($slug, $mask = NULL)
	{
		if($mask === NULL)
		{
			$mask = $this->permissions;
		}

		$mask = $this->encrypt->decode($mask);

		// No point checking, user doesn't have permission
		if($mask == 0)
		{
			return FALSE;
		}

		// Make sure it's a valid slug, otherwise don't give permission, even to administrators
		if(($index = $this->get_perm($slug)) !== FALSE)
		{
			if($slug != $this->_admin_permission && $this->has_perm($this->_admin_permission, $mask))
			{
				return TRUE;
			}

			$check = gmp_init(0);
			gmp_setbit($check, $index);

			return gmp_strval(gmp_and($mask, $check)) === gmp_strval($check);
		}

		return FALSE;
	}

	/**
	 * Bitauth::is_admin()
	 *
	 */
	public function is_admin($mask = NULL)
	{
		return $this->has_perm($this->_admin_permission, $mask);
	}

	/**
	 * Bitauth::get_perm()
	 *
	 */
	public function get_perm($slug)
	{
		return array_search($slug, array_keys($this->_all_permissions));
	}

	/**
	 * Bitauth::get_permissions()
	 *
	 */
	public function get_permissions()
	{
		return $this->_all_permissions;
	}

	/**
	 * Bitauth::username_is_unique()
	 *
	 */
	public function username_is_unique($username, $exclude_user = FALSE)
	{
		if($exclude_user != FALSE)
		{
			$this->db->where('user_id !=', (int)$exclude_user);
		}

		$query = $this->db->where('LOWER(`username`)', strtolower($username))->get($this->_table['users']);
		if($query && $query->num_rows())
		{
			$this->set_error(lang('bitauth_unique_username'));
			return FALSE;
		}

		return TRUE;
	}

	/**
	 * Bitauth::group_is_unique()
	 *
	 */
	public function group_is_unique($group_name, $exclude_group = FALSE)
	{
		if($exclude_group != FALSE)
		{
			$this->db->where('group_id !=', (int)$exclude_group);
		}

		$query = $this->db->where('LOWER(`name`)', strtolower($group_name))->get($this->_table['groups']);
		if($query && $query->num_rows())
		{
			$this->set_error(lang('bitauth_unique_group'));
			return FALSE;
		}

		return TRUE;
	}

	/**
	 * Bitauth::password_is_valid()
	 *
	 */
	public function password_is_valid($password)
	{
		if($this->_pwd_min_length > 0 && strlen($password) < $this->_pwd_min_length)
		{
			$this->set_error(sprintf(lang('bitauth_passwd_min_length'), $this->_pwd_min_length));
			return FALSE;
		}

		if($this->_pwd_max_length > 0 && strlen($password) > $this->_pwd_max_length)
		{
			$this->set_error(sprintf(lang('bitauth_passwd_max_length'), $this->_pwd_max_length));
			return FALSE;
		}

		foreach($this->_pwd_complexity_chars as $_label => $_rule)
		{
			if(preg_match('/'.$_rule.'/', $password) < $this->_pwd_complexity[$_label])
			{
				$this->set_error(sprintf(lang('bitauth_passwd_complexity'), $this->complexity_requirements()));
				return FALSE;
			}
		}

		return TRUE;
	}

	/**
	 * Bitauth::password_almost_expired()
	 *
	 */
	public function password_almost_expired($user = NULL)
	{
		if($user === NULL)
		{
			$user = $this;
		}

		if( ! is_array($user) && ! is_object($user))
		{
			$this->set_error(lang('bitauth_expired_datatype'));
			return TRUE;
		}

		$user = (object)$user;

		if($this->_pwd_max_age == 0 || $user->password_never_expires == 1)
		{
			return FALSE;
		}

		return (bool)$this->timestamp(time(), 'U') > ( strtotime($user->password_last_set) + (($this->_pwd_max_age - $this->_pwd_age_notification) * 86400));
	}

	/**
	 * Bitauth::password_is_expired()
	 *
	 */
	public function password_is_expired($user = NULL)
	{
		if($user === NULL)
		{
			$user = $this;
		}

		if( ! is_array($user) && ! is_object($user))
		{
			$this->set_error(lang('bitauth_expiring_datatype'));
			return TRUE;
		}

		$user = (object)$user;

		if($this->_pwd_max_age == 0 || $user->password_never_expires == 1)
		{
			return FALSE;
		}

		return (bool)$this->timestamp(time(), 'U') > ( strtotime($user->password_last_set) + ($this->_pwd_max_age * 86400) );
	}

	/**
	 * Bitauth::get_users()
	 *
	 */
	public function get_users($include_disabled = FALSE)
	{
		if( ! $include_disabled)
		{
			$this->db->where('users.enabled', 1);
		}

		$query = $this->db
			->select('users.*')
			->select('userdata.*')
			->select('GROUP_CONCAT(assoc.group_id SEPARATOR "|") AS groups')
			->select('BIT_OR(groups.permissions) AS permissions')
			->join($this->_table['data'].' userdata', 'userdata.user_id = users.user_id', 'left')
			->join($this->_table['assoc'].' assoc', 'assoc.user_id = users.user_id', 'left')
			->join($this->_table['groups'].' groups', 'groups.group_id = assoc.group_id', 'left')
			->group_by('users.user_id')
			->get($this->_table['users'].' users');

		if($query && $query->num_rows())
		{
			$ret = array();
			$result = $query->result();
			foreach($result as $row)
			{
				$row->groups = explode('|', $row->groups);
				$row->last_login_ip = long2ip($row->last_login_ip);
				$row->permissions = $this->encrypt->encode($row->permissions);

				$ret[] = $row;
			}

			return $ret;
		}

		return FALSE;
	}

	/**
	 * Bitauth::get_user_by_username()
	 *
	 */
	public function get_user_by_username($username, $include_disabled = FALSE)
	{
		$this->db->where('users.username', $username);
		$users = $this->get_users($include_disabled);

		if(is_array($users) && ! empty($users))
		{
			return $users[0];
		}

		return FALSE;
	}

	/**
	 * Bitauth::get_user_by_id()
	 *
	 */
	public function get_user_by_id($id, $include_disabled = FALSE)
	{
		$this->db->where('users.user_id', $id);
		$users = $this->get_users($include_disabled);

		if(is_array($users) && ! empty($users))
		{
			return $users[0];
		}

		return FALSE;
	}

	 /**
	  * Bitauth::get_groups()
	  *
	  */
	public function get_groups()
	{
		$query = $this->db
			->select('groups.*, GROUP_CONCAT(assoc.user_id SEPARATOR "|") AS members')
			->join($this->_table['assoc'].' assoc', 'assoc.group_id = groups.group_id', 'left')
			->group_by('groups.group_id')
			->get($this->_table['groups'].' groups');

		if($query && $query->num_rows())
		{
			$ret = array();
			$result = $query->result();
			foreach($result as $row)
			{
				$row->members = explode('|', $row->members);
				$row->permissions = $this->encrypt->encode($row->permissions);
				$ret[] = $row;
			}

			return $ret;
		}

		return FALSE;
	}

	/**
	 * Bitauth::get_group_by_name()
	 *
	 */
	public function get_group_by_name($group_name)
	{
		$this->db->where('LOWER(groups.name)', strtolower($group_name));
		$groups = $this->get_groups();

		if(is_array($groups) && ! empty($groups))
		{
			return $groups[0];
		}

		return FALSE;
	}

	/**
	 * Bitauth::get_group_by_id()
	 *
	 */
	public function get_group_by_id($id)
	{
		$this->db->where('groups.group_id', $id);
		$groups = $this->get_groups();

		if(is_array($groups) && ! empty($groups))
		{
			return $groups[0];
		}

		return FALSE;
	}

	/**
	 * Bitauth::set_error()
	 *
	 */
	public function set_error($str, $update_session = TRUE)
	{
		$this->_error = $str;

		if($update_session == TRUE)
		{
			$this->session->set_flashdata('bitauth_error', $this->_error);
		}
	}

	/**
	 * Bitauth::get_error()
	 *
	 */
	public function get_error()
	{
		return $this->_error_delim_prefix.$this->_error.$this->_error_delim_suffix;
	}

	/**
	 * Bitauth::set_error_delimiters()
	 *
	 */
	public function set_error_delimiters($prefix, $suffix)
	{
		$this->_error_delim_prefix = $prefix;
		$this->_error_delim_suffix = $suffix;
	}

	/**
	 * Bitauth::hash_password()
	 *
	 */
	public function hash_password($str)
	{
		return $this->phpass->HashPassword($str);
	}

	/**
	 * Bitauth::generate_code()
	 *
	 */
	public function generate_code()
	{
		return sha1(uniqid().time());
	}

	/**
	 * Bitauth::logged_in()
	 *
	 */
	public function logged_in()
	{
		return (bool)$this->session->userdata('bitauth_username');
	}

	/**
	 * Bitauth::complexity_requirements()
	 *
	 */
	public function complexity_requirements($separator = '<br/>')
	{
		$ret = array();

		foreach($this->_pwd_complexity as $_label => $_count)
		{
			if($_count > 0)
			{
				$ret[] = lang('bitauth_pwd_'.$_label).': '.$_count;
			}
		}

		return implode($separator, $ret);
	}

	/**
	 * Bitauth::timestamp()
	 *
	 */
	public function timestamp($time = NULL, $format = NULL)
	{
		if($time === NULL)
		{
			$time = time();
		}
		if($format === NULL)
		{
			$format = $this->_date_format;
		}

		if($this->config->item('time_reference') == 'local')
		{
			return date($format, $time);
		}

		return gmdate($format, $time);
	}

}