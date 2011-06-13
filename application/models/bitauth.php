<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

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
	public $_pk;
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
	public $_permissions;

	private $_all_permissions;

	public function __construct()
	{
		parent::__construct();

		if(!function_exists('gmp_init'))
		{
			log_message('error', 'You must enable php_gmp to use Bitauth.');
			show_error('You must enable php_gmp to use Bitauth.');
		}

		$this->load->database();
		$this->load->library('session');
		$this->load->config('bitauth', TRUE);

		$this->_table					= $this->config->item('table', 'bitauth');
		$this->_pk						= $this->config->item('primary_key', 'bitauth');
		$this->_username_field			= $this->config->item('username_field', 'bitauth');
		$this->_default_group			= $this->config->item('default_group', 'bitauth');
		$this->_remember_token_name		= $this->config->item('remember_token_name', 'bitauth');
		$this->_remember_token_expires	= $this->config->item('remember_token_expires', 'bitauth');
		$this->_remember_token_updates	= $this->config->item('remember_token_updates', 'bitauth');
		$this->_pwd_max_age				= $this->config->item('pwd_max_age', 'bitauth');
		$this->_pwd_min_length			= $this->config->item('pwd_min_length', 'bitauth');
		$this->_pwd_max_length			= $this->config->item('pwd_max_length', 'bitauth');
		$this->_pwd_complexity			= $this->config->item('pwd_complexity', 'bitauth');

		$this->_all_permissions	= $this->config->item('permissions', 'bitauth');
		
		// Grab the first permission on the list as the administrator permission
		$slugs = array_keys($this->_all_permissions);
		$this->_admin_permission = $slugs[0];
		
		unset($slugs);

		if($this->logged_in())
		{
			$this->get_session_values();
			
			if($this->_remember_token_updates)
			{
				$this->update_remember_token();
			}
		}
		else if($this->input->cookie($this->_remember_token_name))
		{
			$this->login_from_token();
		}

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
			->join($this->_table['assoc'], $this->_table['assoc'].'.user_id = '.$this->table['user'].'.'.$this->_pk, 'left')
			->join($this->_table['groups'], $this->_table['groups'].'.'.$this->pk.' = '.$this->_table['assoc'].'.group_id', 'left')
			->where($this->_table['users'].'.'.$this->_username_field, $username)
			->group_by($this->_table['users'].'.'.$this->_pk)
			->limit(1)
			->get($this->_table['user']);

		if($query !== FALSE && $query->num_rows())
		{
			$user = $query->row();

			if(sha1($password.$user->salt) === $user->password || ($password === NULL && $user->remember_me == $token))
			{
				$this->set_session_values($user);

				if($remember != FALSE)
				{
					$this->update_remember_token();
				}
				
				return TRUE;
			}
		}
		
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
		
		$token = explode('|', $token);
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

			$_key = substr($_key, 0, 8);

			if(!isset($this->$_key))
			{
				$this->$_key = $_value;
			}
			else
			{
				log_message('error', 'You can\'t overwrite default BitAuth properties with custom userdata. Please change the name of the field: '.$_key);
				show_error('You can\'t overwrite default BitAuth properties with custom userdata. Please change the name of the field: '.$_key);
			}
		}
	}

	/**
	 * Bitauth::update_remember_token()
	 *
	 */
	public function update_remember_token()
	{
		if(!$this->logged_in())
		{
			return;
		}
		
		$user_id = $this->_username_field;
		$session_id = sha1(mt_rand(0, PHP_INT_MAX).time());
		
		$cookie = array(
			'prefix' => $this->config->item('cookie_prefix'),
			'name' => $this->_remember_token_name,
			'value' => $this->$user_id.'|'.$session_id,
			'expire' => $this->_remember_token_expires,
			'domain' => $this->config->item('cookie_domain'),
			'path' => $this->config->item('cookie_path'),
			'secure' => $this->config->item('cookie_secure')
		);
		
		$this->input->set_cookie($cookie);
		
		$pk = $this->_pk;
		$this->db
			->set('remember_me', $this->$user_id.'|'.$session_id)
			->where($this->_pk, $this->$pk)
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
				'expire' => ''
			);
			
			$this->db
				->set('remember_me', '')
				->where('remember_me', $this->input->cookie($this->_remember_token_name))
				->update($this->_table['users']);
				
			$this->input->set_cookie($cookie);
		}
	}
	
	/**
	 * Bitauth::has_perm()
	 *
	 */
	public function has_perm($slug)
	{
		if(($index = array_search($slug, array_keys($this->_all_permissions))) !== FALSE)
		{
			if($slug != $this->_admin_permission && $this->has_perm($this->_admin_permission))
			{
				return TRUE;
			}

			$check = gmp_init(0);
			gmp_setbit($check, $index);

			return gmp_strval(gmp_and($this->_permissions, $check)) === gmp_strval($check);
		}
	}

	/**
	 * Bitauth::hash_password()
	 *
	 */
	public function hash_password($str)
	{
		return sha1($str.$this->salt());
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
		return (bool)$this->session->userdata($this->_username_field);
	}

}