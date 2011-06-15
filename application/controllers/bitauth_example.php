<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Bitauth_example extends CI_Controller
{

	public function __construct()
	{
		parent::__construct();

		$this->load->model('bitauth');
		$this->load->helper('form');
		$this->load->helper('url');
		$this->load->helper('language');
	}

	public function index()
	{
		if(! $this->bitauth->logged_in())
		{
			// Set this value and the user will be redirected once logged in
			$this->session->set_userdata('redir', 'bitauth_example');
			redirect('bitauth_example/login');
		}

		$this->load->view('bitauth/default');
	}

	public function login()
	{
		if($this->input->post())
		{

		}

		$this->load->view('bitauth/login');
	}

	public function logout()
	{
		$this->bitauth->logout();
		redirect('bitauth_example');
	}

}