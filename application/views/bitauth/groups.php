<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
    <title>Bitauth Example: Groups</title>
	<style type="text/css">
		body { font-family: Arial, sans-serif; font-size: 12px; }
		p { line-height: 1.5em; }
		h2 { margin: 0 0 8px 0; }
		table { width: 100%; border-bottom: 1px solid #BBB; }
		th { text-align: left; border-bottom: 1px solid #BBB; padding: 0; }
		td { padding: 6px 0; }
		tbody tr:nth-child(odd) { background-color: #F2F2F2; }
		form { width: 500px; margin: 0 auto 10px auto; padding: 18px; border: 1px solid #262626; }
		label, input { margin: 0; }
		label { display: block; font-weight: bold; }
		input { margin-bottom: 12px; }
		input[type=text], input[type=password] { width: 100%; display: block; }
		.error { font-weight: bold; color: #F00; }
		.logininfo { width: 500px; margin: 4% auto 0 auto; }
		.creds { width: 600px; margin: 0 auto; padding: 0; }
	</style>
</head>
<body>
    <?php
		echo '<div class="logininfo"><strong>'.$bitauth->fullname.'</strong><span style="float: right;">'.anchor('bitauth_example/logout', 'Logout').'</span></div>';
		echo form_open(current_url());

		echo '<h2>BitAuth Example: Groups</h2>';

		$this->table->set_heading('Group', '');

		foreach($groups as $_group)
		{
			$this->table->add_row(array(
				array('data' => $_group->name),
				array('width' => 1, 'data' => anchor('bitauth_example/edit_group/'.$_group->group_id, ( $bitauth->has_perm('can_edit') ? 'Edit' : 'View' )))
			));
		}

		echo $this->table->generate();
		echo '<div style="width: 50%; float: left;">'.( $bitauth->has_perm('can_edit') ? anchor('bitauth_example/add_group', 'Add Group') : '&nbsp;' ).'</div>';
		echo '<div style="width: 50%; float: left; text-align: right;">'.anchor('bitauth_example', 'View Users').'</div>';
		echo '<div style="clear: both;"></div>';

		echo form_close();
		echo '<p class="creds">
			This example uses two sample permissions: <strong>can_edit</strong> and <strong>can_change_pw</strong> to showcase the ease of use of Bitauth.
			When logged in as adminstrator, you have full access. When logged in as the default user, you can only view user and group information, and reset user passwords.
		</p>';

	?>
</body>
</html>
