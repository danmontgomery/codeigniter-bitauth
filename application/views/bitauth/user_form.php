<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
    <title>Bitauth Example: <?php echo ( ! empty($user) ? ( $edit == TRUE ? 'Edit' : 'View' ) : 'Add').' User'; ?></title>
	<style type="text/css">
		body { font-family: Arial, sans-serif; font-size: 12px; }
		h2 { margin: 0 0 8px 0; }
		p { margin-top: 0; }
		form { width: 300px; margin: 0 auto 10px auto; padding: 18px; border: 1px solid #262626; }
		label, input { margin: 0; }
		label { display: block; font-weight: bold; }
		input { margin-bottom: 12px; }
		input[type=text], input[type=password] { width: 100%; display: block; }
		.error { font-weight: bold; color: #F00; }
		.logininfo { width: 300px; margin: 7% auto 0 auto; }
		.creds { width: 600px; margin: 0 auto; padding: 0; }
	</style>
</head>
<body>
    <?php
		echo '<div class="logininfo"><strong>'.$bitauth->fullname.'</strong><span style="float: right;">'.anchor('bitauth_example/logout', 'Logout').'</span></div>';
		echo form_open(current_url());

		echo '<h2>BitAuth Example: '.( ! empty($user) ? ( $edit == TRUE ? 'Edit' : 'View' ) : 'Add').' User</h2>';

		echo form_label('Username', 'username');
		if($edit == TRUE)
		{
			echo form_input('username', set_value('username', ( ! empty($user) ? $user->username : '')));
		}
		else
		{
			echo '<p>'.( ! empty($user) ? $user->username : 'N/A').'</p>';
		}

		echo form_label('Email', 'email');
		if($edit == TRUE)
		{
			echo form_input('email', set_value('email', ( ! empty($user) ? $user->email : '')));
		}
		else
		{
			echo '<p>'.( ! empty($user) ? $user->email : 'N/A').'</p>';
		}

		echo form_label('Full Name', 'fullname');
		if($edit == TRUE)
		{
			echo form_input('fullname', set_value('fullname', ( ! empty($user) ? $user->fullname : '')));
		}
		else
		{
			echo '<p>'.( ! empty($user) ? $user->fullname : 'N/A').'</p>';
		}

		if($edit == TRUE)
		{
			if( ! isset($user))
			{
				echo form_label('Password', 'password');
				echo form_password('password', NULL, array('id' => 'password'));
			}

			echo form_submit('submit', ( ! empty($user) ? 'Save Changes' : 'Add User' )).' or '.anchor('bitauth_example', 'Cancel');
		}
		else
		{
			echo anchor('bitauth_example', 'Back to Users');
		}

		echo ( ! empty($error) ? $error : '' );

		echo form_close();
		echo '<p class="creds">
			This example uses a sample permission, <strong>can_edit</strong>, to showcase the ease of use of Bitauth. When logged in as adminstrator,
			you can add and edit users and groups. When logged in as the default user, you can only view this information, not make any edits.
		</p>';
	?>
</body>
</html>
