--
-- RAMP: Record and Activity Management Program
-- SMART: Software for Managing Academic Records and Transcripts
--
-- Create Users and Authorizations tables for a RAMP Demo
-- database.  Define a single user, a database administrator, who can
-- create other users and define what they are authorized to do.
-- (This is mostly for illustration purposes, since there might not
-- be any need to define users for the RAMO demo database; the
-- Authorizations table allows "guest" users -- those who have not
-- logged in -- read-only access to the basic tables in the demo.)

-- Prerequisite: The database name (ramp_demo) and the database
-- administrator role (ramp_dba) must be defined in the 
-- application/configs/application.ini file.

-- You must run MySQL as root (or some other user that has permission
-- to create databases) to execute the commands found in this file.

USE `ramp_demo`;

-- Create Users Table (ramp_auth_users):
--
-- For most databases, there should be one initial user record
-- for a database administrator who would then be responsible for both
-- (a) adding other users (including other database administrators)
-- with their roles, and (b) filling in the authorizations table.
--
-- This demo database example might not really need a database
-- administrator user, but one is provided here as an example or in case
-- there is any desire to add users with more authorization than the
-- built-in "guest" role for users not logged in.
--
-- This demo database example uses internal authentication, so the
-- 'password' field is set to be NOT NULL.  The default password should
-- also be specified in application.ini as ramp.defaultPassword.  The
-- default password will cause Ramp to redirect users to the
-- Change Password screen when they first log in, allowing the
-- password to be entered and encrypted correctly.  This should be done
-- for the dba user account immediately after Ramp is set up.
--
-- This example assumes that the 'ramp_dba' role has been defined in
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
  PRIMARY KEY (`username`)
);


-- Create Initial User:

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
-- Usually most resources and authorizations would be defined using
-- Ramp itself, but for this pre-defined demo, they are defined
-- here.
--
-- "Guests" (anyone who is not logged in) may see the activities listed
-- in activity files in the PublicActivities and demo directories
-- (the demo directory is also known as "Home" in the Ramp Demo menu),
-- and may view two data tables ('albums' and 'places') without
-- modifying them.  The database administrator has the same access
-- (inherited from the "guest" role) and may also view the contents
-- of the Users table (ramp_auth_users) and view, add, modify, and
-- delete resources and access rules from the Authorizations table
-- (ramp_auth_auths).

DROP TABLE IF EXISTS `ramp_auth_auths`;
CREATE TABLE `ramp_auth_auths` (
  `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `role` varchar(100) NOT NULL,
  `resource_type` enum('Activity','Document','Report','Table',
        'Admin-Table') NOT NULL,
  `resource_name` varchar(100) NOT NULL,
  `action` enum('All','View','AddRecords','ModifyRecords','DeleteRecords')
        NOT NULL DEFAULT 'View'
);


-- Create Initial Authorization Rules:

LOCK TABLES `ramp_auth_auths` WRITE;
INSERT INTO `ramp_auth_auths`
(`role`, `resource_type`, `resource_name`, `action`) VALUES
('guest','Activity','.','All')
, ('guest','Activity','../docs','All')
, ('guest','Document','.','All')
, ('guest','Document','../..','All')
, ('guest','Table','albums','View')
, ('guest','Report','places','View')
, ('guest','Table','places','View')
, ('guest','Table','reviews','View')
, ('guest','Table','reviewers','View')
, ('ramp_dba','Activity','../adminSettings','All')
, ('ramp_dba','Document','../../installation','All')
, ('ramp_dba','Table','ramp_auth_users','View')
, ('ramp_dba','Admin-Table','ramp_auth_users','View')
, ('ramp_dba','Admin-Table','ramp_auth_users','AddRecords')
, ('ramp_dba','Table','ramp_auth_auths','All')
, ('ramp_dba','Table','ramp_lock_relations','All')
, ('ramp_dba','Table','ramp_lock_locks','View')
, ('ramp_dba','Table','ramp_lock_locks','DeleteRecords')
;
UNLOCK TABLES;

-- Very basic authorizations can alternatively be defined in 
-- the application.ini file, e.g., guest access to the PublicActivities
-- directory or ramp_dba access to the Admin directory and the
-- ramp_auth_users and ramp_auth_auths tables.
