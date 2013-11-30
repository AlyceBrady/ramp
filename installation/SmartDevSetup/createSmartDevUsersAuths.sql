--
-- RAMP: Record and Activity Management Program
-- SMART: Software for Managing Academic Records and Transcripts
--
-- Create a RAMP/SMART developer database and its Users and Authorizations
-- tables.  Define a single user, a database administrator, who can
-- create other users and define what they are authorized to do.
-- To make setting up new development environments easier, define
-- additional generic users with different access permissions as well.

-- Prerequisite: The database name (smart_dev) and the database
-- administrator role (smart_dba) must be defined in the 
-- application/configs/application.ini file.
-- The application.ini file should also define three roles used by the
-- additional generic users set up here.  Those roles are: hr_staff,
-- regist_staff, and developer.

-- You must run MySQL as root (or some other user that has permission
-- to create databases) to execute the commands found in this file.

--
-- Create Database: `smart_dev`
--

DROP DATABASE IF EXISTS `smart_dev`;
CREATE DATABASE `smart_dev`;

USE `smart_dev`;

-- Create Users Table (ramp_auth_users):
--
-- For most databases, there should be only one initial user record
-- for a database administrator who would then be responsible for both
-- (a) adding other users (including other database administrators)
-- with their roles, and (b) filling in the authorizations table.
--
-- This file, however, provides SQL code to create several generic
-- users so that the the developer database could be run "out of the box",
-- without requiring the initial database administrator to create
-- additional users.  (Real usernames for an actual Smart system should
-- identify individuals, not be shared among offices or roles.)
--
-- This developer database uses internal authentication, so the
-- 'password' field is set to be NOT NULL.  The default password should
-- also be specified in application.ini as ramp.defaultPassword.  The
-- default password will cause Smart to redirect users to the
-- Change Password screen when they first log in, allowing the
-- password to be entered and encrypted correctly.  This should be done
-- for the dba user account immediately after Ramp/Smart is set up.
--
-- The creation of these developer "users" assumes that the
-- 'smart_dba', 'hr_staff', and 'reg_staff' roles have been defined
-- in application.ini.


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


-- Create Initial Users:
--   (In a production Smart database, users should represent individuals, not
--   offices or roles.)

LOCK TABLES `ramp_auth_users` WRITE;
INSERT INTO `ramp_auth_users`
(username, active, role, first_name, last_name, email)
VALUES
('dba', 'TRUE', 'smart_dba', 'Firstname', 'Lastname', 'emailAddr@yahoo.com')
, ('dba2', 'TRUE', 'smart_dba', 'SecondDB', 'Admin', 'emailAddr2@yahoo.com')
, ('hr', 'TRUE', 'hr_staff', 'Generic', 'HR Person', 'hrstaff@gmail.com')
, ('reg', 'TRUE', 'regist_staff', 'Generic', 'Regist Person',
    'regstaff@gmail.com')
, ('developer', 'TRUE', 'developer', 'Powerful', 'Developer',
    'regstaff@gmail.com')
;
UNLOCK TABLES;

--
-- Create Resources and Authorizations Table (ramp_auth_auths):
--
-- Usually most resources and authorizations would be defined using
-- Smart itself, but for this pre-defined development environment,
-- they are defined here.
--
-- "Guests" (anyone who is not logged in) may see the activities listed
-- in activity files in the docs/rampDocs directory and other
-- directories containing public Smart information, such as lists of
-- terms, academic programs, modules, and module offerings.  Database
-- administrators, HR staff, and Registrar staff may see the same data
-- (inherited from the "guest" role), and may each see other things as
-- well.  The generic "hr" user may access activities and tables
-- related to institutional staff records, while the generic "reg" user
-- may access activities and tables related to student records.  The
-- database administrator may view the contents of the Users
-- table (ramp_auth_users) and view, add, modify, and delete resources
-- and access rules from the Authorizations table (ramp_auth_auths).
-- The "developer" user is provided as a convenience for developers, and has
-- access to everything that the "hr", "reg" and "dba" users do.

DROP TABLE IF EXISTS `ramp_auth_auths`;
CREATE TABLE `ramp_auth_auths` (
  `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `role` varchar(100) NOT NULL,
  `resource_type` enum('Activity','Document','Report','Table') NOT NULL,
  `resource_name` varchar(100) NOT NULL,
  `action` enum('All','View','AddRecords','ModifyRecords','DeleteRecords')
        NOT NULL DEFAULT 'View'
);


-- Create Initial Authorization Rules:

LOCK TABLES `ramp_auth_auths` WRITE;
INSERT INTO `ramp_auth_auths`
(`role`, `resource_type`, `resource_name`, `action`) VALUES
('smart_dba','Activity','Admin','All')
, ('smart_dba','Table','ramp_auth_users','View')
, ('smart_dba','Table','ramp_auth_auths','All')
, ('guest','Activity','.','All')
, ('guest','Activity','../docs/rampDocs','All')
, ('guest','Document','../..','All')
, ('guest','Document','rampDocs','All')
, ('guest','Activity','Smart','All')
, ('guest','Activity','Smart/Curriculum','All')
, ('guest','Activity','Smart/Person','All')
, ('guest','Table','Terms','View')
, ('guest','Table','AcadProgram','View')
, ('guest','Table','Modules','View')
, ('guest','Table','ModuleOfferings','View')
, ('hr_or_reg','Activity','Smart/Staff','All')
, ('hr_or_reg','Table','Person','View')
, ('hr_or_reg','Table','Person','AddRecords')
, ('hr_or_reg','Table','Person','ModifyRecords')
, ('hr_staff','Table','Staff','View')
, ('hr_staff','Table','Staff','AddRecords')
, ('hr_staff','Table','Staff','ModifyRecords')
, ('hr_staff','Table','StaffContract','All')
, ('hr_staff','Report','StaffContract','View')
, ('hr_staff','Table','Advising','View')
, ('hr_staff','Table','ModuleAssignments','View')
, ('hr_staff','Table','Enrollment','View')
, ('regist_staff','Table','AcadProgram','All')
, ('regist_staff','Table','Modules','All')
, ('regist_staff','Table','ModuleOfferings','All')
, ('regist_staff','Table','ModuleAssignments','All')
, ('regist_staff','Table','ModuleSchedule','All')
, ('regist_staff','Activity','Smart/Student','All')
, ('regist_staff','Table','Student','View')
, ('regist_staff','Table','Student','AddRecords')
, ('regist_staff','Table','Student','ModifyRecords')
, ('regist_staff','Table','Advising','All')
, ('regist_staff','Table','StudentAcadProgram','All')
, ('regist_staff','Table','Enrollment','All')
, ('regist_staff','Table','TermStanding','All')
, ('regist_staff','Table','TestScores','All')
, ('developer','Activity','tests','All')
, ('developer','Activity','tests/activityTesting','All')
, ('developer','Activity','tests/formTesting','All')
, ('developer','Activity','tests/menuTests','All')
, ('developer','Activity','tests/miscTests','All')
, ('developer','Activity','tests/settingTesting','All')
;
UNLOCK TABLES;

-- Very basic authorizations can alternatively be defined in 
-- the application.ini file, e.g., guest access to the docs/rampDocs
-- directory or smart_dba access to the Admin directory and the
-- ramp_auth_users and ramp_auth_auths tables.
