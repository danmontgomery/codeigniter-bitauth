<table cellpadding="0" cellspacing="0" id="table">
<th align="center" colspan="2">Change password</th>
<tr>
<?php
echo form_open('example/change_password/'.$forgot_code);
echo '<td>'.form_label('Password', 'password').'</td>';
echo '<td>'.form_password('password');
echo form_error('password').'</td><br/>';
echo '</tr><tr>';
echo '<td>'.form_label('Password confirm', 'password_conf').'</td>';
echo '<td>'.form_password('password_conf');
echo form_error('password_conf').'</td><br/>';	
echo '</tr><tr>';
echo '<td colspan="2">'.form_submit('submit', 'Save').'</td>';
echo '</tr>';
echo form_close();
?>
</tr>
</table>
