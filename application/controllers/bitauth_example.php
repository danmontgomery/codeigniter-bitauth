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

		$this->load->library('form_validation');
		$this->form_validation->set_error_delimiters('<div class="error">', '</div>');
	}

	public function index()
	{
		if(! $this->bitauth->logged_in())
		{
			$this->session->set_userdata('redir', 'bitauth_example');
			redirect('bitauth_example/login');
		}

		$this->load->library('table');
		$this->load->view('bitauth/default', array('bitauth' => $this->bitauth, 'users' => $this->bitauth->get_users()));
	}

	public function register()
	{
		$data = array();

		if($this->input->post())
		{
			$this->form_validation->set_rules('username', 'Username', 'trim|required|bitauth_unique_username');
			$this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email');
			$this->form_validation->set_rules('password', 'Password', 'required|bitauth_valid_password');
			$this->form_validation->set_rules('confirm_password', 'Confirm Password', 'required|matches[password]');

			if($this->form_validation->run() == TRUE)
			{
				$user = array(
					'username' => $this->input->post('username'),
					'email' => $this->input->post('email'),
					'fullname' => $this->input->post('fullname'),
					'password' => $this->input->post('password')
				);

				if($this->bitauth->add_user($user))
				{
					// @todo success message
					redirect('bitauth_example/login');
				}

				$data['error'] = $this->bitauth->get_error();
			}
			else
			{
				$data['error'] = validation_errors();
			}
		}

		$this->load->view('bitauth/register', $data);
	}

	public function login()
	{
		$data = array();

		if($this->input->post())
		{
			$this->form_validation->set_rules('username', 'Username', 'trim|required');
			$this->form_validation->set_rules('password', 'Password', 'required');

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

					redirect($redir ? $redir : 'bitauth_example');
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

		$this->load->view('bitauth/login', $data);
	}

	public function logout()
	{
		$this->bitauth->logout();
		redirect('bitauth_example');
	}

}