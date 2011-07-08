<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Users must be activated before they can login
 * Default: TRUE
 */
$config['require_user_activation'] = TRUE;

/**
 * Default group users are added to when they first register (if one isn't specified)
 * Default: 2
 */
$config['default_group_id'] = 2;

/**
 * Name of the cookie where "remember me" login is kept
 * (Prefixed with the cookie_prefix value in config.php, if one is set)
 * Default: rememberme
 */
$config['remember_token_name'] = 'rememberme';

/**
 * Number of seconds the remembered login is valid
 * Default: 1 week
 */
$config['remember_token_expires'] = 604800;

/**
 * Does the "remember me" expiration time update every time the user revisits the site?
 * Default: TRUE
 */
$config['remember_token_updates'] = TRUE;

/**
 * Number of days before passwords expire. To disable, set to FALSE
 * Default: 90
 */
$config['pwd_max_age'] = 90;

/**
 * Number of days before password expiration to notify users their
 * password is about to expire. They will be redirected to a form
 * to change password every time they login until password is changed.
 * Default: 7
 */
$config['pwd_age_notification'] = 7;

/**
 * Required minimum length of passwords
 * Default: 8
 */
$config['pwd_min_length'] = 4;

/**
 * Required maximum length of passwords. Set to 0 to disable
 * Default: 20
 */
$config['pwd_max_length'] = 20;

/**
 * Optional password complexity options. Set a number for each to
 * require that many characters, or set to 0 to disable
 * Default: 1, 1, 0
 */
$config['pwd_complexity'] = array(
	'uppercase' => 0,
	'number' => 0,
	'special' => 0
);

/**
 * Which characters are included in each complexity check. Must be in
 * regex-friendly format. Using the Posix Collating Sequences should
 * make these language-independent, but are here in case you want to
 * change them.
 */
$config['pwd_complexity_chars'] = array(
	'uppercase' => '[[:upper:]]',
	'number' => '[[:digit:]]',
	'special' => '[[:punct:]]'
);

/**
 * Tables used by BitAuth
 */
$config['table'] = array(
	'users'		=> 'bitauth_users',
	'data'		=> 'bitauth_userdata',
	'groups'	=> 'bitauth_groups',
	'assoc'		=> 'bitauth_assoc'
);

/**
 * Base-2 logarithm of the iteration count used for password stretching by Phpass
 * See: http://en.wikipedia.org/wiki/Key_strengthening
 * Default: 8
 */
$config['phpass_iterations'] = 8;

/**
 * Require the hashes to be portable to older systems?
 * From: http://www.openwall.com/articles/PHP-Users-Passwords
 * Unless you force the use of "portable" hashes, phpass' preferred hashing method is
 * CRYPT_BLOWFISH, with a fallback to CRYPT_EXT_DES, and then a final fallback to the
 * "portable" hashes.
 * Default: FALSE
 */
$config['phpass_portable'] = FALSE;

/**
 * What format BitAuth stores the date as. By default, BitAuth uses DATETIME fields.
 * If you want to store date as a unix timestamp, you just need to change the columns
 * in the database, and change this line:
 * $config['date_format'] = 'U';
 * See: http://php.net/manual/en/function.date.php
 */
$config['date_format'] = 'Y-m-d H:i:s';

/**
 * Your permissions slugs. These are how you call permissions checks
 * in your code. eg: if($this->bitauth->has_perm('example_perm_1'))
 */
$config['permissions'] = array(

/**
 * THE FIRST PERMISSION IS ALWAYS THE ADMINISTRATOR PERMISSION
 * ANY USERS IN GROUPS GIVEN THIS PERMISSION WILL HAVE FULL ACCESS
 */
	'is_admin'		=> 'User is an Administrator',

/**
 * Add as many permissions slugs here as you like.
 * Follow the format:
 * 'permission_slug' => 'Permission Description',
 */
	'can_edit'		=> 'Can Add/Edit Users & Groups (Sample)',
	'can_change_pw'	=> 'Can Change User Passwords (Sample)'
);