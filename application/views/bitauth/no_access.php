<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
    <title>Bitauth Example: Login</title>
	<style type="text/css">
		body { font-family: Arial, sans-serif; font-size: 12px; }
		h2 { margin: 0 0 8px 0; }
		form { width: 500px; margin: 4% auto 10px auto; padding: 18px; border: 1px solid #262626; text-align: center; }
	</style>
</head>
<body>
    <?php
		echo form_open(current_url());

		echo '<h2>You do not have access to view this page</h2>';
		echo anchor('bitauth_example', 'Back');

		echo form_close();
	?>
</body>
</html>
