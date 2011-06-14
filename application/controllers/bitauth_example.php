<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Bitauth_example extends CI_Controller
{
	
	public function __construct()
	{
		parent::__construct();
		
		$this->load->model('bitauth');
	}
	
	public function index()
	{
		
	}
	
	public function logout()
	{
		
	}
	
}