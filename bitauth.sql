-- phpMyAdmin SQL Dump
-- version 3.4.11.1deb1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jul 07, 2013 at 06:20 AM
-- Server version: 5.5.31
-- PHP Version: 5.4.6-1ubuntu1.2

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `bitauth`
--

-- --------------------------------------------------------

--
-- Table structure for table `bitauth_assoc`
--

CREATE TABLE IF NOT EXISTS `bitauth_assoc` (
  `assoc_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `group_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`assoc_id`),
  KEY `user_id` (`user_id`,`group_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `bitauth_assoc`
--

INSERT INTO `bitauth_assoc` (`assoc_id`, `user_id`, `group_id`) VALUES
(1, 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `bitauth_groups`
--

CREATE TABLE IF NOT EXISTS `bitauth_groups` (
  `group_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(48) NOT NULL,
  `description` text NOT NULL,
  `roles` text NOT NULL,
  PRIMARY KEY (`group_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `bitauth_groups`
--

INSERT INTO `bitauth_groups` (`group_id`, `name`, `description`, `roles`) VALUES
(1, 'Administrators', 'Administrators (Full Access)', '1'),
(2, 'Users', 'Default User Group', '0');

-- --------------------------------------------------------

--
-- Table structure for table `bitauth_logins`
--

CREATE TABLE IF NOT EXISTS `bitauth_logins` (
  `login_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `ip_address` int(10) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `time` datetime NOT NULL,
  `success` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`login_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `bitauth_userdata`
--

CREATE TABLE IF NOT EXISTS `bitauth_userdata` (
  `userdata_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `fullname` varchar(40) NOT NULL,
  `email` varchar(254) NOT NULL,
  PRIMARY KEY (`userdata_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `bitauth_userdata`
--

INSERT INTO `bitauth_userdata` (`userdata_id`, `user_id`, `fullname`, `email`) VALUES
(1, 1, 'Administrator', 'admin@admin.com');

-- --------------------------------------------------------

--
-- Table structure for table `bitauth_users`
--

CREATE TABLE IF NOT EXISTS `bitauth_users` (
  `user_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(32) NOT NULL,
  `password` varchar(60) NOT NULL,
  `password_last_set` datetime NOT NULL,
  `password_never_expires` tinyint(1) NOT NULL DEFAULT '0',
  `remember_me` varchar(40) NOT NULL,
  `activation_code` varchar(40) NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '0',
  `forgot_code` varchar(40) NOT NULL,
  `forgot_generated` datetime NOT NULL,
  `enabled` tinyint(1) NOT NULL DEFAULT '1',
  `google_auth` tinyint(1) NOT NULL DEFAULT '0',
  `google_key` varchar(20) NOT NULL,
  `last_login` datetime NOT NULL,
  `last_login_ip` int(10) NOT NULL,
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `bitauth_users`
--

INSERT INTO `bitauth_users` (`user_id`, `username`, `password`, `password_last_set`, `password_never_expires`, `remember_me`, `activation_code`, `active`, `forgot_code`, `forgot_generated`, `enabled`, `google_auth`, `google_key`, `last_login`, `last_login_ip`) VALUES
(1, 'admin', '$2a$08$560JEYl2Np/7/6RLc/mq/ecnumuBXig3e.pHh1lnH1pgpk94sTZhu', '2013-07-07 06:13:56', 0, '', '', 1, '', '0000-00-00 00:00:00', 1, 0, '', '0000-00-00 00:00:00', 0);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
