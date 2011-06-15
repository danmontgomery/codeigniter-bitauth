<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class MY_Form_validation extends CI_Form_validation
{

	/**
	 * My_Form_validation::bitauth_unique_username()
	 *
	 */
	public function bitauth_unique_username($username)
	{
		$CI = get_instance();
		if(! $CI->bitauth->username_is_unique($username))
		{
			$this->set_message('bitauth_unique_username', $CI->bitauth->get_error());
			return FALSE;
		}

		return TRUE;
	}

	/**
	 * MY_Form_validation::bitauth_password_is_valid()
	 *
	 */
	public function bitauth_password_is_valid($password)
	{
		$CI = get_instance();
		if(! $CI->bitauth->password_is_valid($password))
		{
			$this->set_message('bitauth_password_is_valid', $CI->bitauth->get_error());
			return FALSE;
		}

		return TRUE;
	}

}