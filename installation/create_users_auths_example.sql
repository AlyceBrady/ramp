--
-- RAMP: Record and Activity Management Program
-- SMART: Software for Managing Academic Records and Transcripts
--
-- Create a sample RAMP database and its Users and Authorizations
-- tables.
--

-- Prerequisite: The database name and the database administrator role
-- (ramp_appl and ramp_dba in this example) must be defined in the 
-- application/configs/application.ini file.

-- This file, as provided, is meant to be a TEMPLATE for creating
-- a database, its required users and authorizations tables, and a
-- single administrator who can then (a) add other users (including
-- additional administrators) with their roles to the Users table,
-- and (b) fill in the Authorizations table.  Note that the
-- Authorizations table may also give "guest" users (those who have
-- not logged in) access to certain activities and tables.

-- You must run MySQL as root (or some other user that has permission
-- to create databases) to execute the commands found in this file.

--
-- Create Database: `ramp_appl`
--

DROP DATABASE IF EXISTS `ramp_appl`;
CREATE DATABASE `ramp_appl`;

USE `ramp_appl`;

-- Create Users Table (ramp_auth_users):
--
-- For most databases, there should be one initial user record
-- for a database administrator who would then be responsible for both
-- (a) adding other users (including other database administrators)
-- with their roles, and (b) filling in the authorizations table.
--
-- NOTE: If using internal authentication, the 'password' field should
-- be set to NOT NULL and given a default password.  The default
-- password should also be specified in application.ini as
-- ramp.defaultPassword.  The default password will cause Ramp/Smart
-- to redirect the user to the Set Password screen when they first
-- log in, allowing the password to be entered and encrypted correctly.
-- This should be done for the active dba user account immediately
-- after Ramp is set up.  (See the bottom of this file for an example
-- using external authentication.)
--
-- The Users table may keep track of identification and contact
-- information (e.g., name and email address) or may store keys to
-- similar information in another table (e.g., domainID), but would
-- be very unlikely to do both.
--
-- This example assumes that the role 'ramp_dba' has been defined in
-- application.ini.


DROP TABLE IF EXISTS `ramp_auth_users`;
CREATE TABLE `ramp_auth_users` (
  `username` varchar(100) NOT NULL,
  `password` varchar(150) NOT NULL DEFAULT 'no_pw',
  `active` enum('FALSE', 'TRUE') NOT NULL DEFAULT 'FALSE',
  `role` varchar(100) NOT NULL DEFAULT 'guest' ,
  `first_name` varchar(100) DEFAULT NULL,
  `last_name` varchar(100) DEFAULT NULL,
  `email` varchar(150) NOT NULL,
  `domainID` int,
  PRIMARY KEY (`username`)
);


-- Create Initial User (Database Administrator):

LOCK TABLES `ramp_auth_users` WRITE;
INSERT INTO `ramp_auth_users`
(username, active, role, first_name, last_name, email)
VALUES
('dba', 'TRUE', 'ramp_dba', 'Firstname', 'Lastname', 'emailAddr@yahoo.com')
;
UNLOCK TABLES;

--
-- Create Resources and Authorizations Table (ramp_auth_auths):
--
-- Most resources and authorizations can be defined using Ramp/Smart
-- itself, but to do that the initial user created above must have
-- authorization to edit the Users and Authorizations tables.
--
-- This example assumes that the role 'ramp_dba' has been defined in
-- application.ini and that the Ramp/Smart settings include a
-- ManageAuths activity with appropriate settings files for the
-- ramp_auth_users and ramp_auth_auths tables.  In this example,
-- administrative users with the ramp_dba role may view, add, and modify
-- the Users table, but may not delete users (although that could be
-- done by a database administrator working in MySQL directly).  Users
-- with the ramp_dba role may do any of these activities in the
-- Authorizations table, including deleting records.
--
-- If the initial activity defined in application.ini is anything other
-- than ManageAuths, then you should give "guest" or the database
-- administrator role authorization to go to that activity.  For
-- example, ('guest','Activity','InitialActivity','All')

DROP TABLE IF EXISTS `ramp_auth_auths`;
CREATE TABLE `ramp_auth_auths` (
  `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `role` varchar(100) NOT NULL,
  `resource_type` enum('Activity','Report','Table') NOT NULL,
  `resource_name` varchar(100) NOT NULL,
  `action` enum('All','View','AddRecords','ModifyRecords','DeleteRecords')
        NOT NULL DEFAULT 'View'
);

LOCK TABLES `ramp_auth_auths` WRITE;
INSERT INTO `ramp_auth_auths`
(`role`, `resource_type`, `resource_name`, `action`) VALUES
('ramp_dba','Activity','ManageAuths','All')
,('ramp_dba','Table','ramp_auth_users','View')
,('ramp_dba','Table','ramp_auth_users','AddRecords')
,('ramp_dba','Table','ramp_auth_users','ModifyRecords')
,('ramp_dba','Table','ramp_auth_auths','All')
;
UNLOCK TABLES;



-- External Authentication Example of User Table:
--
-- If using external authentication, the 'password' field should be set
-- to DEFAULT NULL and the Ramp/Smart menus should be adjusted so that
-- there is no Change Password option.
--
-- Example using external authentication, where identifying and
-- contact information is also stored externally (Active Directory, for
-- example):
--   CREATE TABLE `ramp_auth_users` (
--     `id` int(11) NOT NULL AUTO_INCREMENT,
--     `username` varchar(100) NOT NULL,
--     `password` varchar(1) DEFAULT NULL,
--     `active` enum('FALSE', 'TRUE') NOT NULL DEFAULT 'FALSE',
--     `role` varchar(100) NOT NULL DEFAULT 'guest' ,
--     PRIMARY KEY (`id`)
--   );
