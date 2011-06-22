<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
    <title>Bitauth Example: Reset Password</title>
	<style type="text/css">
		body { font-family: Arial, sans-serif; font-size: 12px; }
		h2 { margin: 0 0 8px 0; }
		p { margin-top: 0; }
		form { width: 300px; margin: 0 auto 10px auto; padding: 18px; border: 1px solid #262626; }
		label, input, textarea { margin: 0; }
		label { display: block; font-weight: bold; }
		input[type=text], input[type=password], input[type=submit], textarea { margin-bottom: 12px; }
		input[type=checkbox] { margin-bottom: 4px; position: relative; top: 2px; }
		input[type=text], input[type=password], textarea { width: 100%; display: block; }
		.error { font-weight: bold; color: #F00; }
		.logininfo { width: 300px; margin: 7% auto 0 auto; }
		.creds { width: 600px; margin: 0 auto; padding: 0; }
	</style>
</head>
<body>
    <?php
		echo '<div class="logininfo"><strong>'.$bitauth->fullname.'</strong><span style="float: right;">'.anchor('bitauth_example/logout', 'Logout').'</span></div>';
		echo form_open(current_url());

		echo '<h2>Reset Password: '.$bitauth->username.'</h2>';

		echo form_label('New Password', 'new_password');
		echo form_password('new_password', NULL, array('id' => 'new_password'));
		echo form_label('Confirm New Password', 'confirm_password');
		echo form_password('confirm_password', NULL, array('id' => 'confirm_password'));
		echo form_submit('submit', 'Reset Password');

		echo validation_errors();

		echo form_close();
	?>
</body>
</html>
