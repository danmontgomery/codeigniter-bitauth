<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Example extends CI_Controller
{

	/**
	 * Example::__construct()
	 *
	 */
	public function __construct()
	{
		parent::__construct();

		$this->load->library('bitauth');

		$this->load->helper('form');
		$this->load->helper('url');

		$this->load->library('form_validation');
		$this->form_validation->set_error_delimiters('<div class="error">', '</div>');

		$this->output->enable_profiler();
	}

	/**
	 * Example::index()
	 *
	 */
	public function index()
	{
		if( ! $this->bitauth->logged_in())
		{
			$this->session->set_userdata('redir', 'example');
			redirect('example/login');
		}

		$this->load->view('example/users', array('bitauth' => $this->bitauth, 'users' => $this->bitauth->get_users()));
	}

	/**
	 * Example::groups()
	 *
	 */
	public function groups()
	{
		if( ! $this->bitauth->logged_in())
		{
			$this->session->set_userdata('redir', 'example/groups');
			redirect('example/login');
		}

		$this->load->view('example/groups', array('bitauth' => $this->bitauth, 'groups' => $this->bitauth->get_groups()));
	}

	/**
	 * Bitauth_example::login()
	 *
	 */
	public function login()
	{
		$data = array();

		if($this->input->post())
		{
			$this->form_validation->set_rules('username', 'Username', 'trim|required');
			$this->form_validation->set_rules('password', 'Password', 'required');
			$this->form_validation->set_rules('remember_me','Remember Me','');

			if($this->form_validation->run() == TRUE)
			{
				// Login
				if($this->bitauth->login($this->input->post('username'), $this->input->post('password'), $this->input->post('remember_me')))
				{
					// Redirect
					if($redir = $this->session->userdata('redir'))
					{
						$this->session->unset_userdata('redir');
					}

					redirect($redir ? $redir : 'example');
				}
				else
				{
					$data['error'] = $this->bitauth->get_error();
				}
			}
			else
			{
				$data['error'] = validation_errors();
			}
		}

		$this->load->view('example/login', $data);
	}


	/**
	 * Bitauth_example::logout()
	 *
	 */
	public function logout()
	{
		$this->bitauth->logout();
		redirect('example');
	}

}