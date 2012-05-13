#BitAuth

* [Website](http://www.dmontgomery.net/bitauth) - http://www.dmontgomery.net/bitauth
* [Github](https://github.com/danmontgomery/codeigniter-bitauth) - https://github.com/danmontgomery/codeigniter-bitauth
* [Issues](https://github.com/danmontgomery/codeigniter-bitauth/issues) - https://github.com/danmontgomery/codeigniter-bitauth/issues

##Requirements
* PHP 5.1.6+, 5.3+ recommended
* CodeIgniter 2.0+
* CodeIgniter Sparks
* MySQL
* ~~php-gmp~~

##Features
* Phpass Integration: BitAuth uses [phpass](http://www.openwall.com/phpass/) to handle password hashing
* Password complexity rules: Along with minimum and maximum length, specify the required number of:
	* Uppercase Characters
	* Numbers
	* Special Characters
	* Spaces
	* ... Or, add your own
* Password aging: Require your users to change their passwords at a set interval
* Completely custom userdata: Easily customize BitAuth to include any custom you want. Full name, Nickname, Phone number, Favorite color... You name it!
* Groups and Roles: Create groups, and assign users to your groups. Your roles are set on a group, not a user, so changing roles, whether the scale is large or small, is fast and painless.
* Text-based roles: Simply list your roles in the configuration file, then check against them in your code. BitAuth handles everything in between.

##Installation
	php tools/spark install bitauth
Import bitauth.sql into your database. **If you would like to change the names of the tables BitAuth uses, you can change them in this .sql file, and must also change them in config/bitauth.php**.

##Usage
	$this->load->spark('bitauth/X.X.X');

##Updating
If updating from v0.1.x, there is a convert() function in the Example controller. This will modify the structure of your groups table, as well as convert any roles you have stored to the new format. This function uses base_convert(), which means results may vary depending on the machine you're running this on. After upgrading, be sure to check the roles in your groups for accuracy.

##Notes
As of v0.2.0, php-gmp is no longer used. The structure of the bitauth_groups table has changed, as well.

The default login is **admin**/**admin**.

I **highly** recommend you not use the default cookie session... [Try my driver replacement](http://getsparks.org/packages/session-driver/show) for CI's session library (end of shameless self promotion).

Currently, only MySQL is supported. This may change in the future. Or not. We'll see.
