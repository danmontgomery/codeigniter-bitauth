<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
    <title>Bitauth Example: Login</title>
	<style type="text/css">
		body { font-family: Arial, sans-serif; font-size: 12px; }
		h2 { margin: 0 0 8px 0; }
		form { width: 300px; margin: 7% auto 10px auto; padding: 18px; border: 1px solid #262626; }
		label, input { margin: 0; }
		label { display: block; font-weight: bold; }
		input { margin-bottom: 12px; }
		input[type=text], input[type=password] { width: 100%; display: block; }
		.error { font-weight: bold; color: #F00; }
		.creds { width: 300px; margin: 0 auto; padding: 0; }
	</style>
</head>
<body>
    <?php
		echo form_open(current_url());

		echo '<h2>BitAuth Example: Register</h2>';

		echo form_label(lang('bitauth_username'), 'username');
		echo form_input('username', set_value('username'), array('id' => 'username'));
		echo form_label(lang('bitauth_email'), 'email');
		echo form_input('email', set_value('email'), array('id' => 'email'));
		echo form_label(lang('bitauth_fullname'), 'fullname');
		echo form_input('fullname', set_value('fullname'), array('id' => 'fullname'));
		echo form_label(lang('bitauth_password'), 'password');
		echo form_password('password', NULL, array('id' => 'password'));
		echo form_label(lang('bitauth_confirm_password'), 'confirm_password');
		echo form_password('confirm_password', NULL, array('id' => 'confirm_password'));

		echo form_submit('register', lang('bitauth_register')).' or '.anchor('bitauth_example/login', lang('bitauth_login'));;

		echo (!empty($error) ? $error : '' );

		echo form_close();
	?>
</body>
</html>
