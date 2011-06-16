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

		$this->load->view('bitauth/default');
	}

	public function register()
	{
		$data = array();

		if($this->input->post())
		{
			$this->form_validation->set_rules('username', lang('bitauth_username'), 'trim|required|callback_bitauth_unique_username');
			$this->form_validation->set_rules('email', lang('bitauth_email'), 'trim|required|valid_email');
			$this->form_validation->set_rules('password', lang('bitauth_password'), 'required|callback_bitauth_valid_password');
			$this->form_validation->set_rules('confirm_password', lang('bitauth_confirm_password'), 'required|matches[password]');

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
			$this->form_validation->set_rules('username', lang('bitauth_username'), 'trim|required');
			$this->form_validation->set_rules('password', lang('bitauth_password'), 'required');

			if($this->form_validation->run() == TRUE)
			{
				// Login
				if($this->bitauth->login($this->input->post('username'), $this->input->post('password')))
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

	/**
	 * My_Form_validation::bitauth_unique_username()
	 *
	 */
	public function bitauth_unique_username($username)
	{
		if(! $this->bitauth->username_is_unique($username))
		{
			$this->form_validation->set_message('bitauth_unique_username', $this->bitauth->get_error());
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
		if(! $this->bitauth->password_is_valid($password))
		{
			$this->form_validation->set_message('bitauth_valid_password', $this->bitauth->get_error());
			return FALSE;
		}

		return TRUE;
	}

}