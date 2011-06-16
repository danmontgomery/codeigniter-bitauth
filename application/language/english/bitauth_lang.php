<?php

/**
 * Field Names
 */
$lang['bitauth_username']			= 'Username';
$lang['bitauth_password']			= 'Password';
$lang['bitauth_email']				= 'Email';
$lang['bitauth_fullname']			= 'Full Name';
$lang['bitauth_current_password']	= 'Current Password';
$lang['bitauth_new_password']		= 'New Password';
$lang['bitauth_confirm_password']	= 'Confirm Password';
$lang['bitauth_reset_password']		= 'Reset Password';
$lang['bitauth_login']				= 'Login';
$lang['bitauth_register']			= 'Register';

/**
 * Password Complexity Labels
 */
$lang['bitauth_pwd_uppercase']		= 'Uppercase Letters';
$lang['bitauth_pwd_number']			= 'Numbers';
$lang['bitauth_pwd_special']		= 'Special Characters';

/**
 * Login Error Messages
 */
$lang['bitauth_login_failed']		= 'Invalid %s or Password';

/**
 * Registration Error Messages
 */
$lang['bitauth_unique_username']	= 'The %s field must be unique.';
$lang['bitauth_password_is_valid']	= '%s does not meet the complexity requirements: ';
$lang['bitauth_username_required']	= 'The %s field is required.';
$lang['bitauth_password_required']	= 'The %s field is required.';
$lang['bitauth_passwd_complexity']	= 'Password does not meet complexity requirements: %s';
$lang['bitauth_passwd_min_length']	= 'Password must be at least %d characters.';
$lang['bitauth_passwd_max_length']	= 'Password may not be longer than %d characters.';


/**
 * General Error Messages
 */
$lang['bitauth_data_error']			= 'You can\'t overwrite default BitAuth properties with custom userdata. Please change the name of the field: ';
$lang['bitauth_enable_gmp']			= 'You must enable php_gmp to use Bitauth.';
$lang['bitauth_add_user_datatype']	= '$data must be an array or an object in Bitauth::add_user';
$lang['bitauth_add_user_failed']	= 'Adding user failed, please notify an administrator.';
$lang['bitauth_no_default_group']	= 'Default group was either not specified or not found, please notify an administrator.';
