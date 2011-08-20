<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<style type="text/css">
		body { margin: 0; padding: 0; font-size: 10pt; font-family: Verdana, Arial, sans-serif; }
		#bottom { width: 600px; padding: 10px; margin: 0 auto; }
		#table { width: 600px; margin: 60px auto 0 auto; border-left: 1px solid #666; border-bottom: 1px solid #666; }
		#table td, #table th { border: 1px solid #666; border-left: 0; border-bottom: 0; padding: 6px; width: 50%; text-align: left; vertical-align: top; }
		#table td.label { text-align: right; }
		#table caption { font-size: 1.4em; font-weight: bold; }
		#table select, #table input[type=text], #table input[type=password] { width: 270px; }
		.error { color: #940D0A; font-weight: bold; }
	</style>
	<title>BitAuth: <?php echo $title; ?></title>
</head>
<body>
	<?php
		echo form_open(current_url());

		echo '<table border="0" cellspacing="0" cellpadding="0" id="table">';
		echo '<caption>BitAuth Example: '.$title.'</caption>';
		echo '<tr><td class="label">Username</td><td>'.form_input('username', set_value('username')).'</td></tr>';
		echo '<tr><td class="label">Full Name</td><td>'.form_input('fullname', set_value('fullname')).'</td></tr>';
		echo '<tr><td class="label">Email</td><td>'.form_input('email', set_value('email')).'</td></tr>';
		echo '<tr><td class="label">Password</td><td>'.form_password('password').'</td></tr>';
		echo '<tr><td class="label">Confirm Password</td><td>'.form_password('password_conf').'</td></tr>';

		if(validation_errors())
		{
			echo '<tr><td colspan="2">'.validation_errors().'</td></tr>';
		}

		echo '<tr><td class="label" colspan="2">'.form_submit('submit',$title).'</td></tr>';
		echo '</table>';
		echo form_close();

		echo '<div id="bottom">';
		if(isset($bitauth) && $bitauth->logged_in())
		{
			echo anchor('example/logout', 'Logout', 'style="float: right;"');
		}
		else
		{
			echo anchor('example/login', 'Login', 'style="float: right;"');
		}
		echo '</div>';

	?>
</body>
</html>
