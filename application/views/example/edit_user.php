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
	<title>BitAuth: Edit User</title>
</head>
<body>
	<?php
		$yesno = array('No','Yes');

		echo form_open(current_url());

		echo '<table border="0" cellspacing="0" cellpadding="0" id="table">';
		echo '<caption>BitAuth Example: Edit User</caption>';

		if( ! empty($user))
		{
			echo '<tr><td class="label">Username</td><td>'.form_input('username', set_value('username', $user->username)).'</td></tr>';
			echo '<tr><td class="label">Full Name</td><td>'.form_input('fullname', set_value('fullname', $user->fullname)).'</td></tr>';
			echo '<tr><td class="label">Email</td><td>'.form_input('email', set_value('email', $user->email)).'</td></tr>';
			echo '<tr><td class="label">Active</td><td>'.form_dropdown('active', $yesno, set_value('active', $user->active)).'</td></tr>';
			echo '<tr><td class="label">Enabled</td><td>'.form_dropdown('enabled', $yesno, set_value('enabled', $user->enabled)).'</td></tr>';
			echo '<tr><td class="label">Password Never Expires</td><td>'.form_dropdown('password_never_expires', $yesno, set_value('password_never_expires', $user->password_never_expires)).'</td></tr>';
			echo '<tr><td class="label">Groups</td><td>'.form_multiselect('groups[]', $groups, set_value('groups[]', $user->groups)).'</td></tr>';
			echo '<tr><td colspan="2"><strong>Only enter a password if you would like to set a new one</strong></td></tr>';
			echo '<tr><td class="label">New Password</td><td>'.form_password('password').'</td></tr>';
			echo '<tr><td class="label">Confirm New Password</td><td>'.form_password('password_conf').'</td></tr>';

			if(validation_errors())
			{
				echo '<tr><td colspan="2">'.validation_errors().'</td></tr>';
			}

			echo '<tr><td class="label" colspan="2">'.anchor('example', 'Cancel').' '.form_submit('submit','Update').'</td></tr>';
		} else {
			echo '<tr><td><p>User Not Found</p><p>'.anchor('example', 'Go Back').'</p></td></tr>';
		}

		echo '</table>';
		echo form_close();

		echo '<div id="bottom">';
		echo anchor('example/logout', 'Logout', 'style="float: right;"');
		echo '</div>';

	?>
</body>
</html>
