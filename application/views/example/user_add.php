<?php $this->load->view('backend/partials/messages');?>

<div id="main-content">
<div class="ui-widget">
<div class="ui-widget-content ui-corner-all">
<div class="ui-widget-header ui-corner-all">
<p class="left">Felhasználó hozzáadása</p><div id="help_1" class="helpbutton sort_asc"><span class="ui-icon ui-icon-help left"></span>Segítség</div>
<p class="clear"/>
</div>
<?php echo form_open('admin/user_add'); ?>

<table border="0" cellspacing="0" cellpadding="0" id="table">
<tr><td class="label">Felhasználó név*</td><td><?php echo form_input('username', set_value('username'));?></td></tr>
<tr><td class="label">Teljes név*</td><td><?php echo form_input('fullname', set_value('fullname'));?></td></tr>
<tr><td class="label">Email*</td><td><?php echo form_input('email', set_value('email'));?></td></tr>
<tr><td class="label">Jelszó*</td><td><?php echo form_password('password');?></td></tr>
<tr><td class="label">Jelszó mégegyszer*</td><td><?php echo form_password('password_conf');?></td></tr>
<tr><td class="label">Csoportok</td><td><?php echo form_multiselect('groups[]', $groups, set_value('groups[]'));?></td></tr>
<?php if(validation_errors()) : ?>
	<tr><td colspan="2"><?php echo validation_errors();?></td></tr>
<?php endif; ?>

<tr>
	<td class="label" colspan="2"><?php echo form_submit(array('name' => 'submit', 'value' => 'Hozzáad', 'class' => 'uibutton')).anchor('admin/user','Mégsem',array('class' => 'uibutton'));?></td>
</tr>
</table>
<?php echo form_close(); ?>
</div>
</div>
</div>