<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
    <title>Bitauth Example: <?php echo ( ! empty($group) ? ( $edit == TRUE ? 'Edit' : 'View' ) : 'Add').' Group'; ?></title>
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

		echo '<h2>BitAuth Example: '.( ! empty($group) ? ( $edit == TRUE ? 'Edit' : 'View' ) : 'Add').' Group</h2>';

		echo form_label('Group Name', 'name');
		if($edit == TRUE)
		{
			echo form_input('name', set_value('name', ( ! empty($group) ? $group->name : '')));
		}
		else
		{
			echo '<p>'.( ! empty($group) ? $group->name : 'N/A').'</p>';
		}

		echo form_label('Description', 'description');
		if($edit == TRUE)
		{
			echo form_textarea('description', set_value('description', ( ! empty($group) ? $group->description : '')));
		}
		else
		{
			echo '<p>'.( ! empty($group) ? $group->description : 'N/A').'</p>';
		}

		echo form_label('Permissions', 'permissions');
		$permissions = $bitauth->get_all_permissions();
		$slugs = array_keys($permissions);
		$submitted = $this->input->post('permissions');

		foreach(array_values($permissions) as $_index => $_desc)
		{
			if($edit == TRUE)
			{
				echo '<div>'.form_checkbox('permissions['.$_index.']', TRUE, (isset($submitted[$_index]) || ( ! empty($group) && $bitauth->has_perm($slugs[$_index], $group->permissions)) ? TRUE : FALSE )).' '.$_desc.'</div>';
			}
			else if($bitauth->has_perm($slugs[$_index], $group->permissions))
			{
				echo '<div>'.$_desc.'</div>';
			}
		}

		if($edit != TRUE && $group->permissions == 0)
		{
			echo '<div>None</div>';
		}

		if($edit == TRUE)
		{
			echo form_submit('submit', ( ! empty($group) ? 'Save Changes' : 'Add Group' ), 'style="margin-top: 12px;"').' or '.anchor('bitauth_example/groups', 'Cancel');
		}
		else
		{
			echo '<div style="margin-top: 12px;">'.anchor('bitauth_example/groups', 'Back to Groups').'</div>';
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
