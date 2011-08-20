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
		#table select, #table input[type=text], #table input[type=password], #table textarea { width: 270px; }
		.error { color: #940D0A; font-weight: bold; }
	</style>
	<title>BitAuth: Edit Group</title>
</head>
<body>
	<?php
		$yesno = array('No','Yes');

		echo form_open(current_url());

		echo '<table border="0" cellspacing="0" cellpadding="0" id="table">';
		echo '<caption>BitAuth Example: Edit Group</caption>';

		if( ! empty($group))
		{
			echo '<tr><td class="label">Group Name</td><td>'.form_input('name', set_value('name', $group->name)).'</td></tr>';
			echo '<tr><td class="label">Description</td><td>'.form_textarea('description', set_value('description', $group->description)).'</td></tr>';
			echo '<tr><td class="label">Roles</td><td>'.form_multiselect('roles[]', $roles, set_value('roles[]', $group_roles)).'</td></tr>';
			echo '<tr><td class="label">Members</td><td>'.form_multiselect('members[]', $users, set_value('members[]', $group->members)).'</td></tr>';

			if(validation_errors())
			{
				echo '<tr><td colspan="2">'.validation_errors().'</td></tr>';
			}

			echo '<tr><td class="label" colspan="2">'.anchor('example/groups', 'Cancel').' '.form_submit('submit','Update').'</td></tr>';
		} else {
			echo '<tr><td><p>Group Not Found</p><p>'.anchor('example/groups', 'Go Back').'</p></td></tr>';
		}

		echo '</table>';
		echo form_close();

		echo '<div id="bottom">';
		echo anchor('example/logout', 'Logout', 'style="float: right;"');
		echo '</div>';

	?>
</body>
</html>
