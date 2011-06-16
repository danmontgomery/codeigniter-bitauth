<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class MY_Form_validation extends CI_Form_validation
{

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