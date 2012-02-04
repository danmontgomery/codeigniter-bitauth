<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<style type="text/css">
		body { margin: 0; padding: 0; font-size: 10pt; font-family: Verdana, Arial, sans-serif; }
		#bottom { width: 600px; padding: 10px; margin: 0 auto; }
		#table { width: 600px; margin: 60px auto 0 auto; border-left: 1px solid #666; border-bottom: 1px solid #666; }
		#table td, #table th { border: 1px solid #666; border-left: 0; border-bottom: 0; padding: 4px; text-align: left; vertical-align: top; }
		#table caption { font-size: 1.4em; font-weight: bold; }
	</style>
	<title>BitAuth: Users</title>
</head>
<body>
	<?php

		echo '<table border="0" cellspacing="0" cellpadding="0" id="table">';
		echo '<caption>BitAuth Example: Users</caption>';
		echo '<tr><th width="1">ID</th><th>Username</th><th>Full Name</th><th>Actions</th></tr>';
		if( ! empty($users))
		{
			foreach($users as $_user)
			{
				$actions = '';
				if($bitauth->has_role('admin'))
				{
					$actions = anchor('example/edit_user/'.$_user->user_id, 'Edit User');
					if( ! $_user->active)
					{
						$actions .= '<br/>'.anchor('example/activate/'.$_user->activation_code, 'Activate User');
					}

				}

				echo '<tr>'.
					'<td>'.$_user->user_id.'</td>'.
					'<td>'.$_user->username.'</td>'.
					'<td>'.$_user->fullname.'</td>'.
					'<td>'.$actions.'</td>'.
				'</tr>';
			}
		}
		echo '</table>';

		echo '<div id="bottom">';
		echo anchor('example/logout', 'Logout', 'style="float: right;"');
		echo anchor('example/groups', 'View Groups');
		if($bitauth->is_admin())
		{
			echo '<br/>'.anchor('example/add_user', 'Add User');
		}
		echo '</div>';

	?>
</body>
</html>
