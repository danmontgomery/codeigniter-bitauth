<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<style type="text/css">
		body { margin: 0; padding: 0; font-size: 10pt; font-family: Verdana, Arial, sans-serif; }
		form { width: 400px; margin: 0 auto; padding: 10px; border: 1px solid #666; }
		label, input[type=text], input[type=password] { display: block; width: 100%; }
		input { margin-bottom: 8px; }
		#bottom { width: 420px; padding: 10px 0; margin: 0 auto; }
		#header { font-size: 1.4em; font-weight: bold; width: 420px; margin: 60px auto 0 auto; text-align: center; }
		.error { color: #940D0A; font-weight: bold; }
	</style>
	<title>BitAuth: Login</title>
</head>
<body>
<?php
	echo '<div id="header">Two Factor Auth</div>';

	echo form_open(current_url());
	echo form_label('Token','token');
	echo form_input('token');
	echo form_submit('login','Login');
	echo ( ! empty($error) ? $error : '' );
	echo form_close();

?>
</body>
</html>
