<table cellpadding="0" cellspacing="0" id="table">
<th align="center">Forgot password</th>
<tr>
<td align="center">
<?php
echo form_open('example/forgot_password');
echo form_label('Email', 'email').'<br/>';
echo form_input('email');
echo form_error('email').'<br/>';
echo form_submit('submit', 'Send');
echo form_close();
?>
</td>
</tr>
</table>