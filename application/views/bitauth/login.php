<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
    <title>Bitauth Example: Login</title>
</head>
<body>
    <?php
		echo form_open(current_url());

		echo form_label(lang('bitauth_username'), 'username');
		echo form_input('username', set_value('username'), array('id' => 'username'));
		echo form_label(lang('bitauth_password'), 'password');
		echo form_password('password', NULL, array('id' => 'password'));
		echo form_submit('login', lang('bitauth_login'));

		echo validation_errors();

		echo form_close();
	?>
</body>
</html>
