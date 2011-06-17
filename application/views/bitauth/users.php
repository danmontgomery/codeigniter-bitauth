<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
    <title>Bitauth Example: Users</title>
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
		.logininfo { width: 500px; margin: 7% auto 0 auto; }
		.creds { width: 600px; margin: 0 auto; padding: 0; }
	</style>
</head>
<body>
    <?php
		echo '<div class="logininfo"><strong>'.$bitauth->fullname.'</strong><span style="float: right;">'.anchor('bitauth_example/logout', 'Logout').'</span></div>';
		echo form_open(current_url());

		echo '<h2>BitAuth Example: Users</h2>';

		$this->table->set_heading('Full Name', 'Username', '');

		foreach($users->result() as $_user)
		{
			$this->table->add_row(array(
				array('width' => '45%', 'data' => $_user->fullname),
				array('data' => $_user->username),
				array('width' => 1, 'data' => anchor('bitauth_example/edit_user/'.$_user->id, ( $bitauth->has_perm('can_edit') ? 'Edit' : 'View' )))
			));
		}

		echo $this->table->generate();
		echo '<div style="width: 50%; float: left;">'.( $bitauth->has_perm('can_edit') ? anchor('bitauth_example/add_user', 'Add User') : '&nbsp;' ).'</div>';
		echo '<div style="width: 50%; float: left; text-align: right;">'.anchor('bitauth_example/groups', 'View Groups').'</div>';
		echo '<div style="clear: both;"></div>';

		echo form_close();
		echo '<p class="creds">
			This example uses a sample permission, <strong>can_edit</strong>, to showcase the ease of use of Bitauth. When logged in as adminstrator,
			you can add and edit users and groups. When logged in as the default user, you can only view this information, not make any edits.
		</p>';
	?>
</body>
</html>
