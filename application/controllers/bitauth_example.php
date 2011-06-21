<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Bitauth_example extends CI_Controller
{

	/**
	 * Bitauth_example::__construct()
	 *
	 */
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

	/**
	 * Bitauth_example::index()
	 *
	 */
	public function index()
	{
		if(! $this->bitauth->logged_in())
		{
			$this->session->set_userdata('redir', 'bitauth_example');
			redirect('bitauth_example/login');
		}

		$this->load->library('table');
		$this->load->view('bitauth/users', array('bitauth' => $this->bitauth, 'users' => $this->bitauth->get_users()));
	}

	/**
	 * Bitauth_example::add_user
	 *
	 */
	public function add_user()
	{
		if(! $this->bitauth->logged_in())
		{
			$this->session->set_userdata('redir', 'bitauth_example/add_user');
			redirect('bitauth_example/login');
		}

		if(! $this->bitauth->has_perm('can_edit'))
		{
			$this->load->view('bitauth/no_access');
			return;
		}

		$data = array('bitauth' => $this->bitauth);

		if($this->input->post())
		{
			$this->form_validation->set_rules('username', 'Username', 'trim|required|bitauth_unique_username');
			$this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email');
			$this->form_validation->set_rules('fullname', 'Fullname', '');
			$this->form_validation->set_rules('password', 'Password', 'required|bitauth_valid_password');

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
					redirect('bitauth_example');
				}

				$data['error'] = $this->bitauth->get_error();
			}
			else
			{
				$data['error'] = validation_errors();
			}
		}

		$this->load->view('bitauth/user_form', $data);
	}

	/**
	 * Bitauth_example::edit_user
	 *
	 */
	public function edit_user($user_id)
	{
		if(! $this->bitauth->logged_in())
		{
			$this->session->set_userdata('redir', 'bitauth_example/edit_user/'.$user_id);
			redirect('bitauth_example/login');
		}

		if(! $this->bitauth->has_perm('can_edit'))
		{
			$this->load->view('bitauth/no_access');
			return;
		}

		$user = $this->bitauth->get_user_by_id($user_id);
		$data = array('bitauth' => $this->bitauth, 'user' => $user);

		if($this->input->post())
		{
			$this->form_validation->set_rules('username', 'Username', 'trim|required|bitauth_unique_username['.$user_id.']');
			$this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email');
			$this->form_validation->set_rules('fullname', 'Fullname', '');

			if($this->form_validation->run() == TRUE)
			{
				$user = array(
					'username' => $this->input->post('username'),
					'email' => $this->input->post('email'),
					'fullname' => $this->input->post('fullname')
				);

				if($this->bitauth->update_user_info($user_id, $user))
				{
					redirect('bitauth_example');
				}

				$data['error'] = $this->bitauth->get_error();
			}
			else
			{
				$data['error'] = validation_errors();
			}
		}

		$this->load->view('bitauth/user_form', $data);
	}

	/**
	 * Bitauth_example::groups()
	 *
	 */
	public function groups()
	{
		if(! $this->bitauth->logged_in())
		{
			$this->session->set_userdata('redir', 'bitauth_example/groups');
			redirect('bitauth_example/login');
		}

		$this->load->library('table');
		$this->load->view('bitauth/groups', array('bitauth' => $this->bitauth, 'groups' => $this->bitauth->get_groups()));
	}

	/**
	 * Bitauth_example::add_group()
	 *
	 */
	public function add_group()
	{

		$this->output->enable_profiler();

		if(! $this->bitauth->logged_in())
		{
			$this->session->set_userdata('redir', 'bitauth_example/add_group');
			redirect('bitauth_example/login');
		}

		if(! $this->bitauth->has_perm('can_edit'))
		{
			$this->load->view('bitauth/no_access');
			return;
		}

		$data = array('bitauth' => $this->bitauth);

		if($this->input->post())
		{
			$this->form_validation->set_rules('name', 'Name', 'trim|required|bitauth_unique_group');
			$this->form_validation->set_rules('description', 'Description', '');
			$this->form_validation->set_rules('permissions', 'Permissions', '');

			if($this->form_validation->run() == TRUE)
			{
				$permissions = gmp_init(0);
				if($this->input->post('permissions'))
				{
					foreach($this->input->post('permissions') as $_perm => $on)
					{
						gmp_setbit($permissions, $_perm);
					}
				}
				$group = array(
					'name' => $this->input->post('name'),
					'description' => $this->input->post('description'),
					'permissions' => gmp_strval($permissions)
				);

				if($this->bitauth->add_group($group))
				{
					redirect('bitauth_example/groups');
				}

				$data['error'] = $this->bitauth->get_error();
			}
			else
			{
				$data['error'] = validation_errors();
			}
		}

		$this->load->view('bitauth/group_form', $data);
	}

	/**
	 * Bitauth_example::edit_group()
	 *
	 */
	public function edit_group($group_id)
	{
		if(! $this->bitauth->logged_in())
		{
			$this->session->set_userdata('redir', 'bitauth_example/edit_group/'.$group_id);
			redirect('bitauth_example/login');
		}

		if(! $this->bitauth->has_perm('can_edit'))
		{
			$this->load->view('bitauth/no_access');
			return;
		}

		$group = $this->bitauth->get_group_by_id($group_id);
		$data = array('bitauth' => $this->bitauth, 'group' => $group);

		if($this->input->post())
		{
			$this->form_validation->set_rules('name', 'Name', 'trim|required|bitauth_unique_group['.$group_id.']');
			$this->form_validation->set_rules('description', 'Description', '');
			$this->form_validation->set_rules('permissions', 'Permissions', '');

			if($this->form_validation->run() == TRUE)
			{
				$permissions = gmp_init(0);
				if($this->input->post('permissions'))
				{
					foreach($this->input->post('permissions') as $_perm => $on)
					{
						gmp_setbit($permissions, $_perm);
					}
				}
				$group = array(
					'name' => $this->input->post('name'),
					'description' => $this->input->post('description'),
					'permissions' => gmp_strval($permissions)
				);

				if($this->bitauth->update_group($group_id, $group))
				{
					redirect('bitauth_example/groups');
				}

				$data['error'] = $this->bitauth->get_error();
			}
			else
			{
				$data['error'] = validation_errors();
			}
		}

		$this->load->view('bitauth/group_form', $data);
	}

	/**
	 * Bitauth_example::register()
	 *
	 */
	public function register()
	{
		$data = array();

		if($this->input->post())
		{
			$this->form_validation->set_rules('username', 'Username', 'trim|required|bitauth_unique_username');
			$this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email');
			$this->form_validation->set_rules('fullname', 'Fullname', '');
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

	/**
	 * Bitauth_example::logout()
	 *
	 */
	public function logout()
	{
		$this->bitauth->logout();
		redirect('bitauth_example');
	}

}