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

		$this->output->enable_profiler();
	}

	/**
	 * Bitauth_example::index()
	 *
	 */
	public function index()
	{
		if( ! $this->bitauth->logged_in())
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
		if( ! $this->bitauth->logged_in())
		{
			$this->session->set_userdata('redir', 'bitauth_example/add_user');
			redirect('bitauth_example/login');
		}

		if( ! $this->bitauth->has_perm('can_edit'))
		{
			$this->load->view('bitauth/no_access');
			return;
		}

		$data = array('bitauth' => $this->bitauth, 'edit' => TRUE);

		if($this->input->post())
		{
			$this->form_validation->set_rules('username', 'Username', 'trim|required|bitauth_unique_username');
			$this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email');
			$this->form_validation->set_rules('fullname', 'Fullname', '');
			$this->form_validation->set_rules('password', 'Password', 'required|bitauth_valid_password');
			$this->form_validation->set_rules('groups','Groups','');

			if($this->form_validation->run() == TRUE)
			{
				$user = array(
					'username' => $this->input->post('username'),
					'email' => $this->input->post('email'),
					'fullname' => $this->input->post('fullname'),
					'password' => $this->input->post('password'),
					'groups' => $this->input->post('groups')
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
		if( ! $this->bitauth->logged_in())
		{
			$this->session->set_userdata('redir', 'bitauth_example/edit_user/'.$user_id);
			redirect('bitauth_example/login');
		}

		$user = $this->bitauth->get_user_by_id($user_id);
		$data = array('bitauth' => $this->bitauth, 'user' => $user, 'edit' => (bool)$this->bitauth->has_perm('can_edit'));

		if($this->input->post())
		{
			$this->form_validation->set_rules('username', 'Username', 'trim|required|bitauth_unique_username['.$user_id.']');
			$this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email');
			$this->form_validation->set_rules('fullname', 'Fullname', '');
			$this->form_validation->set_rules('groups', 'Groups', '');

			if($this->form_validation->run() == TRUE)
			{
				$user = array(
					'username' => $this->input->post('username'),
					'email' => $this->input->post('email'),
					'fullname' => $this->input->post('fullname'),
					'groups' => $this->input->post('groups')
				);

				if($this->bitauth->update_user($user_id, $user))
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
		if( ! $this->bitauth->logged_in())
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

		if( ! $this->bitauth->logged_in())
		{
			$this->session->set_userdata('redir', 'bitauth_example/add_group');
			redirect('bitauth_example/login');
		}

		if( ! $this->bitauth->has_perm('can_edit'))
		{
			$this->load->view('bitauth/no_access');
			return;
		}

		$data = array('bitauth' => $this->bitauth, 'edit' => TRUE);

		if($this->input->post())
		{
			$this->form_validation->set_rules('name', 'Name', 'trim|required|bitauth_unique_group');
			$this->form_validation->set_rules('description', 'Description', '');
			$this->form_validation->set_rules('permissions', 'Permissions', '');

			if($this->form_validation->run() == TRUE)
			{
				$group = array(
					'name' => $this->input->post('name'),
					'description' => $this->input->post('description'),
					'permissions' => $this->input->post('permissions')
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
		if( ! $this->bitauth->logged_in())
		{
			$this->session->set_userdata('redir', 'bitauth_example/edit_group/'.$group_id);
			redirect('bitauth_example/login');
		}

		$group = $this->bitauth->get_group_by_id($group_id);
		$data = array('bitauth' => $this->bitauth, 'group' => $group, 'edit' => (bool)$this->bitauth->has_perm('can_edit'));

		if($this->input->post())
		{
			$this->form_validation->set_rules('name', 'Name', 'trim|required|bitauth_unique_group['.$group_id.']');
			$this->form_validation->set_rules('description', 'Description', '');
			$this->form_validation->set_rules('permissions', 'Permissions', '');

			if($this->form_validation->run() == TRUE)
			{
				$group = array(
					'name' => $this->input->post('name'),
					'description' => $this->input->post('description'),
					'permissions' => $this->input->post('permissions')
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
	 * Bitauth_example::reset_password()
	 *
	 */
	public function reset_password($user_id = NULL)
	{
		if( ! $this->bitauth->logged_in())
		{
			$this->session->set_userdata('redir', 'bitauth_example');
			redirect('bitauth_example/login');
		}

		$user = $this->bitauth->get_user_by_id($user_id);
		if( ! $user )
		{
			redirect('bitauth_example');
		}

		if( ! $this->bitauth->has_perm('can_change_pw') || ( ! $this->bitauth->has_perm('is_admin') && $this->bitauth->has_perm('is_admin', $user->permissions)))
		{
			$this->load->view('bitauth/no_access');
			return;
		}

		if($user_id === NULL)
		{
			$user_id = $this->bitauth->user_id;
		}

		$data = array('bitauth' => $this->bitauth);

		if($this->input->post())
		{
			$this->form_validation->set_rules('password', 'Password', 'required|bitauth_valid_password');
			$this->form_validation->set_rules('confirm_password', 'Confirm Password', 'required|matches[password]');

			if($this->form_validation->run() == TRUE)
			{
				if($this->bitauth->set_password($user_id, $this->input->post('password')))
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

		$this->load->view('bitauth/reset_password', $data);
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
	 * Bitauth_example::activate()
	 *
	 */
	public function activate($activation_code)
	{
		if($this->bitauth->activate($activation_code))
		{
			if($this->bitauth->logged_in())
			{
				redirect('bitauth_example');
			}

			redirect('bitauth_example/login');
		}

		echo lang('bitauth_code_not_found');
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

	public function test()
	{
		$user = array('username' => 'testuser123','password' => 'testuser123','fullname' => 'Test User 123');
		$group = array('name' => 'Test Group 123');

		echo '<table width="50%">';

		echo '<tr><td width="50%">Add User</td>';
		if($this->bitauth->add_user($user))
			echo '<td style="font-color: green;">Success</td>';
		else
			echo '<td style="font-color: red;">Failed: '.$this->bitauth->get_error().'</td>';
		echo '</tr>';

		echo '<tr><td width="50%">Add Group</td>';
		if($this->bitauth->add_group($group))
			echo '<td style="font-color: green;">Success</td>';
		else
			echo '<td style="font-color: red;">Failed: '.$this->bitauth->get_error().'</td>';
		echo '</tr>';

		echo '<tr><td width="50%">Fetch User</td>';
		if($_user = $this->bitauth->get_user_by_username($user['username']))
			echo '<td style="font-color: green;">Success</td>';
		else
			echo '<td style="font-color: red;">Failed: '.$this->bitauth->get_error().'</td>';
		echo '</tr>';

		echo '<tr><td width="50%">Fetch Group</td>';
		if($_group = $this->bitauth->get_group_by_name($group['name']))
			echo '<td style="font-color: green;">Success</td>';
		else
			echo '<td style="font-color: red;">Failed: '.$this->bitauth->get_error().'</td>';
		echo '</tr>';

		echo '<tr><td width="50%">Activate User</td>';
		if($this->bitauth->activate($_user->activation_code))
			echo '<td style="font-color: green;">Success</td>';
		else
			echo '<td style="font-color: red;">Failed: '.$this->bitauth->get_error().'</td>';
		echo '</tr>';

		echo '<tr><td width="50%">Edit User (Empty Groups)</td>';
		$_user = $this->bitauth->get_user_by_username($user['username']);
		if($this->bitauth->update_user($_user->user_id, array('groups' => '')))
			echo '<td style="font-color: green;">Success</td>';
		else
			echo '<td style="font-color: red;">Failed: '.$this->bitauth->get_error().'</td>';
		echo '</tr>';

		echo '<tr><td width="50%">Edit Group (Add User)</td>';
		if($this->bitauth->update_group($_group->group_id, array('members' => array($_user->user_id))))
			echo '<td style="font-color: green;">Success</td>';
		else
			echo '<td style="font-color: red;">Failed: '.$this->bitauth->get_error().'</td>';
		echo '</tr>';

		echo '<tr><td width="50%">Disable User</td>';
		$_user = $this->bitauth->get_user_by_username($user['username']);
		if($this->bitauth->disable($_user->user_id))
			echo '<td style="font-color: green;">Success</td>';
		else
			echo '<td style="font-color: red;">Failed: '.$this->bitauth->get_error().'</td>';
		echo '</tr>';
		$this->bitauth->enable($_user->user_id);

		echo '<tr><td width="50%">Password Almost Expired</td>';
		$_user = $this->bitauth->get_user_by_username($user['username']);
		$_user->password_last_set = date('Y-m-d H:i:s', strtotime('88 days ago'));
		$this->bitauth->update_user($_user->user_id, $_user);
		if($this->bitauth->password_almost_expired($_user->user_id))
			echo '<td style="font-color: green;">Yes</td>';
		else
			echo '<td style="font-color: red;">No</td>';
		echo '</tr>';

		echo '<tr><td width="50%">Password Is Expired</td>';
		$_user = $this->bitauth->get_user_by_username($user['username']);
		$_user->password_last_set = date('Y-m-d H:i:s', strtotime('1 year ago'));
		$this->bitauth->update_user($_user->user_id, $_user);
		if($this->bitauth->password_is_expired($_user->user_id))
			echo '<td style="font-color: green;">Yes</td>';
		else
			echo '<td style="font-color: red;">No</td>';
		echo '</tr>';

		echo '<tr><td width="50%">Reset Password</td>';
		if($this->bitauth->set_password($_user->user_id, 'derp'))
			echo '<td style="font-color: green;">Success</td>';
		else
			echo '<td style="font-color: red;">Failed: '.$this->bitauth->get_error().'</td>';
		echo '</tr>';

		echo '<tr><td width="50%">Delete Group</td>';
		if($this->bitauth->delete_group($_group->group_id))
			echo '<td style="font-color: green;">Success</td>';
		else
			echo '<td style="font-color: red;">Failed: '.$this->bitauth->get_error().'</td>';
		echo '</tr>';

		echo '<tr><td width="50%">Delete User</td>';
		if($this->bitauth->delete($_user->user_id))
			echo '<td style="font-color: green;">Success</td>';
		else
			echo '<td style="font-color: red;">Failed: '.$this->bitauth->get_error().'</td>';
		echo '</tr>';

		echo '</table>';

	}

}