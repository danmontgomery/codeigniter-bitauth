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
 * Administrators group (full access)
 */
$config['admin_group'] = 'admin';

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
 * Your permissions slugs. These are how you call permissions checks
 * in your code. eg: if($this->bitauth->has_perm('example_perm_1'))
 */
$config['permissions'] = array(
	'example_perm_1',
	'example_perm_2',
	'example_perm_3'
);