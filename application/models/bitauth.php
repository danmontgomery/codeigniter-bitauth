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
	public $_admin_group;
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

		$this->_table			= $this->config->item('table', 'bitauth');
		$this->_pk				= $this->config->item('primary_key', 'bitauth');
		$this->_username_field	= $this->config->item('username_field', 'bitauth');
		$this->_default_group	= $this->config->item('default_group', 'bitauth');
		$this->_admin_group		= $this->config->item('admin_group', 'bitauth');
		$this->_pwd_max_age		= $this->config->item('pwd_max_age', 'bitauth');
		$this->_pwd_min_length	= $this->config->item('pwd_min_length', 'bitauth');
		$this->_pwd_max_length	= $this->config->item('pwd_max_length', 'bitauth');
		$this->_pwd_complexity	= $this->config->item('pwd_complexity', 'bitauth');
		
		$this->_all_permissions	= $this->config->item('permissions', 'bitauth');
		
		if($this->logged_in())
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
		
	}
	
	/**
	 * Bitauth::has_perm()
	 *
	 */
	public function has_perm($slug)
	{
		if(($index = array_search($slug, $this->_all_permissions)) !== FALSE)
		{
			if($slug != $this->_admin_group && $this->has_perm($this->_admin_group))
			{
				return TRUE;
			}
			
			$check = gmp_init(0);
			gmp_setbit($check, $index);

			return gmp_strval(gmp_and($this->_permissions, $check)) === gmp_strval($check);
		}
	}
	
	/**
	 * Bitauth::login()
	 *
	 */
	public function login($username, $password, $remember = FALSE)
	{
		$query = $this->db
			->select($this->_table['users'].'.*')
			->select('BIT_OR('.$this->_table['groups'].'.permissions) AS permissions', FALSE)
			->join($this->_table['assoc'], $this->_table['assoc'].'.user_id = '.$this->table['user'].'.'.$this->_pk, 'left')
			->join($this->_table['groups'], $this->_table['groups'].'.'.$this->pk.' = '.$this->_table['assoc'].'.group_id', 'left')
			->where($this->_table['users'].'.'.$this->_username_field, $username)
			->get($this->_table['user'], 1);
			
		if($query !== FALSE && $query->num_rows())
		{
			$user = $query->row();
			
			if(sha1($password.$user->salt) === $user->password)
			{
				$session_data = array();
				foreach($user as $_key => $_value)
				{
					if($_key !== 'salt' && $_key !== 'password')
					{
						$session_data['bitauth_'.$_key] = $_value;
					}
				}
				
				$this->session->set_userdata($session_data);
				
				if($remember != FALSE)
				{
					$this->update_remember_token();
				}
				
			}
		}
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
	 * Bitauth::update_remember_token()
	 *
	 */
	public function update_remember_token()
	{
		
	}
	
	/**
	 * Bitauth::delete_remember_token()
	 *
	 */
	public function delete_remember_token()
	{
		
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