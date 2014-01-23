--
-- RAMP: Record and Activity Management Program
-- SMART: Software for Managing Academic Records and Transcripts
--
-- Create Users and Authorizations tables for a RAMP/SMART
-- database.  Define a single user, a database administrator, who can
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

USE `smart_dev`;

-- Create Users Table (ramp_auth_users):
--
-- For most databases, there should be only one initial user record
-- for a database administrator who would then be responsible for both
-- (a) adding other users (including other database administrators)
-- with their roles, and (b) filling in the authorizations table.
--
-- This file, however, provides SQL code to create several generic
-- test users for development test purposes straight "out of the box",
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
-- The identification and contact information for the Ramp/Smart users
-- is defined in the Person table, since all of the Ramp/Smart users are
-- also, presumably, staff members.  For this reason, the domainID is
-- specified as being NOT NULL.
--
-- The creation of these developer "users" assumes that the
-- 'smart_dba', 'hr_staff', 'reg_staff', and 'developer' roles have
-- been defined in application.ini.


DROP TABLE IF EXISTS `ramp_auth_users`;
CREATE TABLE `ramp_auth_users` (
  `username` varchar(100) NOT NULL,
  `password` varchar(150) NOT NULL DEFAULT 'no_pw',
  `active` enum('FALSE', 'TRUE') NOT NULL DEFAULT 'FALSE',
  `role` varchar(100) NOT NULL DEFAULT 'guest' ,
  `first_name` varchar(100) DEFAULT NULL,
  `last_name` varchar(100) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `domainID` int(11) NOT NULL,
  PRIMARY KEY (`username`)
);


-- Create Initial Users:
--   (In a production Smart database, users should represent individuals, not
--   offices or roles.)

LOCK TABLES `ramp_auth_users` WRITE;
INSERT INTO `ramp_auth_users` (username, active, role, domainID)
VALUES
('dba', 'TRUE', 'smart_dba', 2)
, ('dba2', 'TRUE', 'smart_dba', 25)
, ('hr', 'TRUE', 'hr_staff', 26)
, ('reg', 'TRUE', 'regist_staff', 27)
, ('developer', 'TRUE', 'developer', 1)
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
-- database administrator may view the contents of the Users table
-- (ramp_auth_users) and Person table and view, add, modify, and delete
-- resources and access rules from the Authorizations table (ramp_auth_auths).
-- The "developer" user is provided as a convenience for developers, and has
-- access to everything that the "hr", "reg" and "dba" users do.
--
-- The creation of these developer "users" assumes that the
-- 'smart_dba', 'hr_staff', 'reg_staff', and 'developer' roles have
-- been defined in application.ini.

DROP TABLE IF EXISTS `ramp_auth_auths`;
CREATE TABLE `ramp_auth_auths` (
  `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `role` varchar(100) NOT NULL,
  `resource_type` enum('Activity','Document','Report','Table',
                       'Admin-Table') NOT NULL,
  `resource_name` varchar(100) NOT NULL,
  `action` enum('All','View','AddRecords','ModifyRecords','DeleteRecords',
                'AllButDelete') NOT NULL DEFAULT 'View'
);


-- Create Initial Authorization Rules:

LOCK TABLES `ramp_auth_auths` WRITE;
INSERT INTO `ramp_auth_auths`
(`role`, `resource_type`, `resource_name`, `action`) VALUES
('smart_dba','Activity','Admin','All')
, ('smart_dba','Table','ramp_auth_users','View')
, ('smart_dba','Table','ramp_auth_users','Modify')
, ('smart_dba','Admin-Table','Person','View')
, ('smart_dba','Admin-Table','ramp_auth_users','ALL')
, ('smart_dba','Table','ramp_auth_auths','All')
, ('smart_dba','Table','ramp_lock_relations','All')
, ('smart_dba','Table','ramp_lock_locks','View')
, ('smart_dba','Table','ramp_lock_locks','DeleteRecords')
, ('smart_dba','Table','Person','View')
, ('smart_dba','Document','../..','All')
, ('smart_dba','Document','../../installation','All')
, ('guest','Activity','.','All')
, ('guest','Activity','../docs/rampDocs','All')
, ('guest','Document','.','All')
, ('guest','Document','rampDocs','All')
, ('guest','Activity','Smart','All')
, ('guest','Activity','Smart/Curriculum','All')
, ('guest','Activity','Smart/Person','All')
, ('guest','Table','Terms','View')
, ('guest','Table','AcadProgram','View')
, ('guest','Table','Modules','View')
, ('guest','Table','ModuleOfferings','View')
, ('guest','Table','ModuleType','View')
, ('guest','Table','AddressTypes','View')
, ('guest','Table','JobFunctionCodes','View')
, ('guest','Table','ContractStatusCodes','View')
, ('guest','Table','AcadProgramTypes','View')
, ('guest','Table','AdvisorTypes','View')
, ('guest','Table','StudentProgramStatusCodes','View')
, ('guest','Table','ClassLevelCodes','View')
, ('guest','Table','StudentLeaveTypes','View')
, ('guest','Table','AnnotationOffices','View')
, ('guest','Table','AnnotationTypes','View')
, ('guest','Table','StudentModStatusCodes','View')
, ('guest','Table','TermStandingCodes','View')
, ('guest','Table','TestCodes','View')
, ('hr_or_reg','Activity','Smart/Staff','All')
, ('hr_or_reg','Table','Person','AllButDelete')
, ('hr_staff','Table','Staff','AllButDelete')
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
, ('regist_staff','Table','Student','AllButDelete')
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
