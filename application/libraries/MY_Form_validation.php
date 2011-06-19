<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class MY_Form_validation extends CI_Form_validation
{

	/**
	 * My_Form_validation::bitauth_unique_username()
	 *
	 */
	public function bitauth_unique_username($username, $exclude_id = FALSE)
	{
		$CI = get_instance();
		if(! $CI->bitauth->username_is_unique($username, $exclude_id))
		{
			$this->set_message('bitauth_unique_username', $CI->bitauth->get_error());
			return FALSE;
		}

		return TRUE;
	}

	/**
	 * MY_Form_validation::bitauth_valid_password()
	 *
	 */
	public function bitauth_valid_password($password)
	{
		$CI = get_instance();
		if(! $CI->bitauth->password_is_valid($password))
		{
			$this->set_message('bitauth_valid_password', $CI->bitauth->get_error());
			return FALSE;
		}

		return TRUE;
	}

	/**
	 * MY_Form_validation::set_error_delimiters()
	 *
	 */
	public function set_error_delimiters($prefix = '<p>', $suffix = '</p>')
	{
		parent::set_error_delimiters($prefix, $suffix);

		$this->CI->bitauth->set_error_delimiters($prefix, $suffix);
	}

}