<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * BitAuth
 *
 * Group based bitwise permissions system
 *
 * @package Models
 * @access public
 */
class Bitauth extends CI_Model
{

	public $_table;
	public $_username_field;
	public $_default_group;
	public $_admin_permission;
	public $_remember_token_name;
	public $_remember_token_expires;
	public $_remember_token_updates;
	public $_pwd_max_age;
	public $_pwd_min_length;
	public $_pwd_max_length;
	public $_pwd_complexity;
	public $_pwd_complexity_chars;
	public $_error_delim_prefix = '<p>';
	public $_error_delim_suffix = '</p>';
	public $_permissions;

	private $_all_permissions;
	private $_error;

	public function __construct()
	{
		parent::__construct();

		$this->lang->load('bitauth');

		if( ! function_exists('gmp_init'))
		{
			log_message('error', $this->lang->line('bitauth_enable_gmp'));
			show_error($this->lang->line('bitauth_enable_gmp'));
		}

		$this->load->database();
		$this->load->library('session');
		$this->load->config('bitauth', TRUE);

		$this->_table					= $this->config->item('table', 'bitauth');
		$this->_username_field			= $this->config->item('username_field', 'bitauth');
		$this->_default_group			= $this->config->item('default_group', 'bitauth');
		$this->_remember_token_name		= $this->config->item('remember_token_name', 'bitauth');
		$this->_remember_token_expires	= $this->config->item('remember_token_expires', 'bitauth');
		$this->_remember_token_updates	= $this->config->item('remember_token_updates', 'bitauth');
		$this->_pwd_max_age				= $this->config->item('pwd_max_age', 'bitauth');
		$this->_pwd_age_notification	= $this->config->item('pwd_age_notification', 'bitauth');
		$this->_pwd_min_length			= $this->config->item('pwd_min_length', 'bitauth');
		$this->_pwd_max_length			= $this->config->item('pwd_max_length', 'bitauth');
		$this->_pwd_complexity			= $this->config->item('pwd_complexity', 'bitauth');
		$this->_pwd_complexity_chars	= $this->config->item('pwd_complexity_chars', 'bitauth');

		$this->_all_permissions			= $this->config->item('permissions', 'bitauth');

		// Grab the first permission on the list as the administrator permission
		$slugs = array_keys($this->_all_permissions);
		$this->_admin_permission = $slugs[0];

		unset($slugs);

		if($this->logged_in())
		{
			$this->get_session_values();

			if($this->input->cookie($this->_remember_token_name) && $this->_remember_token_updates)
			{
				$this->update_remember_token();
			}
		}
		else if($this->input->cookie($this->_remember_token_name))
		{
			$this->login_from_token();
		}

		$this->set_error($this->session->flashdata('bitauth_error'), FALSE);

	}

	/**
	 * Bitauth::login()
	 *
	 */
	public function login($username, $password, $remember = FALSE, $token = NULL)
	{
		$query = $this->db
			->select($this->_table['users'].'.*')
			->select('BIT_OR('.$this->_table['groups'].'.permissions) AS permissions', FALSE)
			->join($this->_table['assoc'], $this->_table['assoc'].'.user_id = '.$this->_table['users'].'.user_id', 'left')
			->join($this->_table['groups'], $this->_table['groups'].'.group_id = '.$this->_table['assoc'].'.group_id', 'left')
			->where($this->_table['users'].'.'.$this->_username_field, $username)
			->group_by($this->_table['users'].'.user_id')
			->limit(1)
			->get($this->_table['users']);

		if($query !== FALSE && $query->num_rows())
		{
			$user = $query->row();

			if($this->hash_password($password, $user->salt) === $user->password || ($password === NULL && $user->remember_me == $token))
			{
				$this->set_session_values($user);

				if($remember != FALSE)
				{
					$this->update_remember_token();
				}

				return TRUE;
			}
		}

		$this->set_error(sprintf(lang('bitauth_login_failed'), lang('bitauth_username_field')));
		return FALSE;
	}

	/**
	 * Bitauth::login_from_token()
	 *
	 */
	public function login_from_token()
	{
		if(($token = $this->input->cookie($this->_remember_token_name)) === FALSE)
		{
			return FALSE;
		}

		$token = explode("\n", $token);
		$username = $token[0];
		$session_id = $token[1];

		if($this->login($username, NULL, (bool)$this->_remember_token_updates, $session_id))
		{
			return TRUE;
		}

		$this->delete_remember_token();
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

		unset($this->{$this->_username_field});
		$this->delete_remember_token();

		return;
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
			if($_key !== 'salt' && $_key !== 'password')
			{
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
				log_message('error', $this->lang->line('bitauth_data_error').$_key);
				show_error($_key);
				//show_error($this->lang->line('bitauth_data_error').$_key);
			}
		}
	}

	/**
	 * Bitauth::update_remember_token()
	 *
	 */
	public function update_remember_token()
	{
		if( ! $this->logged_in())
		{
			return;
		}

		$user_id = $this->_username_field;
		$session_id = sha1(mt_rand(0, PHP_INT_MAX).time());

		$cookie = array(
			'prefix' => $this->config->item('cookie_prefix'),
			'name' => $this->_remember_token_name,
			'value' => $this->$user_id."\n".$session_id,
			'expire' => $this->_remember_token_expires,
			'domain' => $this->config->item('cookie_domain'),
			'path' => $this->config->item('cookie_path'),
			'secure' => $this->config->item('cookie_secure')
		);

		$this->input->set_cookie($cookie);

		$this->db
			->set('remember_me', $this->$user_id."\n".$session_id)
			->where('user_id', $this->user_id)
			->update($this->_table['users']);
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
	public function add_user($data)
	{
		if( ! is_array($data) && ! is_object($data))
		{
			$this->set_error(lang('bitauth_add_user_datatype'));
			return FALSE;
		}

		$data = (array)$data;

		if(empty($data[$this->_username_field]))
		{
			$this->set_error(sprintf(lang('bitauth_username_required'), $this->_username_field));
			return FALSE;
		}

		if(empty($data['password']))
		{
			$this->set_error(lang('bitauth_password_required'));
			return FALSE;
		}

		// Just in case
		if( ! empty($data['user_id']))
		{
			unset($data['user_id']);
		}

		$data['salt'] = $this->salt();
		$data['password'] = $this->hash_password($data['password'], $data['salt']);
		$data['password_last_set'] = date('Y-m-d H:i:s', time());

		$this->db->trans_start();
		if( ! $this->db->insert($this->_table['users'], $data))
		{
			$this->set_error(lang('bitauth_add_user_failed'));
			$this->db->trans_rollback();

			return FALSE;
		}

		$user_id = $this->db->insert_id();
		$group = $this->get_group_by_name($this->_default_group);

		if($group)
		{
			$this->db->insert($this->_table['assoc'], array('user_id' => $user_id, 'group_id' => $group->group_id));
		}
		else
		{
			$this->set_error(lang('bitauth_no_default_group'));
			$this->db->trans_rollback();

			return FALSE;
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

		$this->db->trans_start();
		if( ! $this->db->insert($this->_table['groups'], $data))
		{
			$this->set_error(lang('bitauth_add_group_failed'));
			$this->db->trans_rollback();

			return FALSE;
		}

		$this->db->trans_commit();
		return TRUE;
	}

	/**
	 * Bitauth::update_user()
	 *
	 */
	public function update_user_info($id, $data)
	{
		if( ! is_array($data) && ! is_object($data))
		{
			$this->set_error(lang('bitauth_edit_user_datatype'));
			return FALSE;
		}

		$data = (array)$data;

		if(empty($data[$this->_username_field]))
		{
			$this->set_error(sprintf(lang('bitauth_username_required'), $this->_username_field));
			return FALSE;
		}

		// Just in case
		if( ! empty($data['user_id']))
		{
			unset($data['user_id']);
		}

		$this->db->trans_start();

		$this->db->set($data)->where('user_id', $id)->update($this->_table['users']);

		if($this->db->trans_status() === FALSE)
		{
			$this->set_error(lang('bitauth_edit_user_failed'));
			$this->db->trans_rollback();

			return FALSE;
		}

		$this->db->trans_commit();
		return TRUE;

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

		$this->db->trans_start();

		$this->db->set($data)->where('group_id', $id)->update($this->_table['groups']);

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
	 * Bitauth::has_perm()
	 *
	 */
	public function has_perm($slug, $mask = NULL)
	{
		if($mask === NULL)
		{
			$mask = $this->permissions;
		}

		if($mask == 0)
		{
			return FALSE;
		}

		if(($index = array_search($slug, array_keys($this->_all_permissions))) !== FALSE)
		{
			if($slug != $this->_admin_permission && $this->has_perm($this->_admin_permission, $mask))
			{
				return TRUE;
			}

			$check = gmp_init(0);
			gmp_setbit($check, $index);

			return gmp_strval(gmp_and($mask, $check)) === gmp_strval($check);
		}
	}

	/**
	 * Bitauth::get_all_permissions()
	 *
	 */
	public function get_all_permissions()
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

		$query = $this->db->where('LOWER(`'.$this->_username_field.'`)', strtolower($username))->get($this->_table['users']);
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
	public function password_almost_expired()
	{
		if($this->_pwd_max_age == 0)
		{
			return FALSE;
		}

		return (bool)(time() > ( strtotime($this->password_last_set) + (($this->_pwd_max_age - $this->_pwd_age_notification) * 86400)));
	}

	/**
	 * Bitauth::password_is_expired()
	 *
	 */
	public function password_is_expired()
	{
		if($this->_pwd_max_age == 0)
		{
			return FALSE;
		}

		return (bool)(time() > ( strtotime($this->password_last_set) + ($this->_pwd_max_age * 86400) ));
	}

	/**
	 * Bitauth::get_users()
	 *
	 */
	 public function get_users()
	 {
		return $this->db
			->select('users.*, GROUP_CONCAT(assoc.group_id) AS groups')
			->join($this->_table['assoc'].' assoc', 'assoc.user_id = users.user_id', 'left')
			->group_by('users.user_id')
			->get($this->_table['users'].' users');
	 }

	/**
	 * Bitauth::get_user_by_id()
	 *
	 */
	public function get_user_by_id($id)
	{
		$this->db->where('users.user_id', $id);

		$query = $this->get_users();
		if($query && $query->num_rows())
		{
			return $query->row();
		}

		return FALSE;
	}

	 /**
	  * Bitauth::get_groups()
	  *
	  */
	 public function get_groups()
	 {
		return $this->db
			->select('groups.*, GROUP_CONCAT(assoc.user_id) AS members')
			->join($this->_table['assoc'].' assoc', 'assoc.group_id = groups.group_id', 'left')
			->group_by('groups.group_id')
			->get($this->_table['groups'].' groups');
	 }

	/**
	 * Bitauth::get_group_by_name()
	 *
	 */
	public function get_group_by_name($group_name)
	{
		$this->db->where('groups.name', $group_name);

		$query = $this->get_groups();
		if($query && $query->num_rows())
		{
			return $query->row();
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

		$query = $this->get_groups();
		if($query && $query->num_rows())
		{
			return $query->row();
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
	public function hash_password($str, $salt)
	{
		return sha1($str.$salt);
	}

	/**
	 * Bitauth::salt()
	 *
	 */
	public function salt()
	{
		return substr(md5(mt_rand(0, PHP_INT_MAX).time()), -10);
	}

	/**
	 * Bitauth::logged_in()
	 *
	 */
	public function logged_in()
	{
		return (bool)$this->session->userdata('bitauth_'.$this->_username_field);
	}

	/**
	 * Bitauth::complexity_requirements()
	 *
	 */
	public function complexity_requirements()
	{
		$ret = array();

		foreach($this->_pwd_complexity as $_label => $_count)
		{
			if($_count > 0)
			{
				$ret[] = lang('bitauth_pwd_'.$_label).': '.$_count;
			}
		}

		return implode('<br/>', $ret);
	}

}