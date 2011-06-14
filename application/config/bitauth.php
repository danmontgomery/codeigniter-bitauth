<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Tables used by BitAuth
 */
$config['table'] = array(
	'users'		=> 'bitauth_users',
	'groups'	=> 'bitauth_groups',
	'assoc'		=> 'bitauth_assoc'
);

/**
 * Field users log in with, must be unique
 */
$config['username_field'] = 'username';

/**
 * Primary key for all BitAuth tables
 */
$config['primary_key'] = 'id';

/**
 * Default group users are added to when they first register
 */
$config['default_group'] = 'users';

/**
 * Name of the cookie where "remember me" login is kept
 * (Prefixed with the cookie_prefix value in config.php, if one is set)
 */
$config['remember_token_name'] = 'rememberme';

/**
 * Number of seconds the remembered login is valid
 * Default: 1 week
 */
$config['remember_token_expires'] = 604800;

/**
 * Does the "remember me" expiration time update every time the user does something?
 */
$config['remember_token_updates'] = TRUE;

/**
 * Number of days before passwords expire. To disable, set to FALSE
 */
$config['pwd_max_age'] = FALSE;

/**
 * Number of days before password expiration to notify users their
 * password is about to expire. They will be redirected to a form
 * to change password every time they login until password is changed.
 */
$config['pwd_age_notification'] = 7;

/**
 * Required minimum length of passwords
 */
$config['pwd_min_length'] = 8;

/**
 * Required maximum length of passwords. To disable, set to FALSE
 */
$config['pwd_max_length'] = FALSE;

/**
 * Optional password complexity options. Set a number for each to
 * require that many characters, or set to 0 to disable
 */
$config['pwd_complexity'] = array(
	'uppercase' => 1,
	'number' => 1,
	'special' => 1
);

/**
 * Which characters are included in each complexity check. Must be in
 * regex-friendly format. Using the Posix Collating Sequences should
 * make these language-independent, but are here in case you want to
 * change them.
 */
$config['pwd_complexity_chars'] = array(
	'uppercase' => '[:upper:]',
	'number' => '[:digit:]',
	'special' => '[:punct:]'
);

/**
 * Your permissions slugs. These are how you call permissions checks
 * in your code. eg: if($this->bitauth->has_perm('example_perm_1'))
 */
$config['permissions'] = array(

/**
 * THE FIRST PERMISSION IS ALWAYS THE ADMINISTRATOR PERMISSION
 * ANY GROUPS GIVEN THIS PERMISSION WILL HAVE FULL ACCESS
 */
	'is_admin' => 'User is an Administrator',

/**
 * Add as many permissions slugs here as you like.
 * Follow the format:
 * 'permission_slug' => 'Permission Description',
 */
	'example_perm_1' => 'Example Permission 1',
	'example_perm_2' => 'Example Permission 2'
);