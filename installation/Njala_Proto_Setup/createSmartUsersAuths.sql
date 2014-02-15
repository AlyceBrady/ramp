--
-- RAMP: Record and Activity Management Program
-- SMART: Software for Managing Academic Records and Transcripts
--
-- Create Users and Authorizations tables for an initial RAMP/SMART
-- database.  Define a single user, a database administrator, who can
-- create other users and define what they are authorized to do.
-- To make setting up new development environments easier, define
-- additional generic users with different access permissions as well.

-- Prerequisite: The database name (njala_proto) and the database
-- administrator role (smart_dba) must be defined in the 
-- application/configs/application.ini file.

-- You must run MySQL as root (or some other user that has permission
-- to create databases) to execute the commands found in this file.

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


USE `njala_proto`;

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
('abrady', 'TRUE', 'developer', 1)
, ('songu', 'TRUE', 'smart_dba', 2)
, ('pmoseray', 'TRUE', 'smart_dba', 3)
, ('sharvey', 'TRUE', 'developer', 4)
, ('ikamara', 'TRUE', 'developer', 5)
-- , ('tnjohn', 'TRUE', 'smart_dba',  24)
-- , ('akamara', 'FALSE', 'regist_staff', 17)
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
('smart_dba','Activity','Admin','View')
, ('smart_dba','Table','ramp_auth_users','View')
, ('smart_dba','Table','ramp_auth_users','ModifyRecords')
, ('smart_dba','Admin-Table','Person','View')
, ('smart_dba','Admin-Table','ramp_auth_users','ALL')
, ('smart_dba','Table','ramp_auth_auths','All')
, ('smart_dba','Table','ramp_lock_relations','All')
, ('smart_dba','Table','ramp_lock_locks','View')
, ('smart_dba','Table','ramp_lock_locks','DeleteRecords')
, ('smart_dba','Table','Person','View')
, ('smart_dba','Document','../..','View')
, ('smart_dba','Document','../../installation','View')
, ('guest','Activity','.','View')
, ('guest','Activity','../docs/rampDocs','View')
, ('guest','Document','.','View')
, ('guest','Document','rampDocs','View')
, ('guest','Activity','Smart','View')
, ('guest','Activity','Smart/Curriculum','View')
, ('guest','Table','Terms','View')
, ('guest','Table','Schools','View')
, ('guest','Table','Departments','View')
, ('guest','Table','AcadProgram','View')
, ('guest','Table','ProgPlanOfStudy','View')
, ('guest','Table','Modules','View')
, ('guest','Table','ModuleOfferings','View')
, ('guest','Table','AcadProgramTypes','View')
, ('guest','Table','AddressTypes','View')
, ('guest','Table','AdmissExamNames','View')
, ('guest','Table','AdmissTestCodes','View')
, ('guest','Table','AdmissTestDescriptors','View')
, ('guest','Table','AdvisorTypes','View')
, ('guest','Table','AnnotationAuthorities','View')
, ('guest','Table','AnnotationTypes','View')
, ('guest','Table','ApplicationStatusCodes','View')
, ('guest','Table','CampusLocations','View')
, ('guest','Table','CampusNames','View')
, ('guest','Table','ClassLevelCodes','View')
, ('guest','Table','ContractStatusCodes','View')
, ('guest','Table','HoldAuthorities','View')
, ('guest','Table','HoldTypes','View')
, ('guest','Table','JobCategories','View')
, ('guest','Table','ModuleTypes','View')
, ('guest','Table','NameTypes','View')
, ('guest','Table','StudentLeaveTypes','View')
, ('guest','Table','StudentModStatusCodes','View')
, ('guest','Table','StudentProgramStatusCodes','View')
, ('guest','Table','TermStandingCodes','View')
, ('smart_dba','Table','Schools','DeleteRecords')
, ('smart_dba','Table','Departments','DeleteRecords')
, ('smart_dba','Table','AddressTypes','All')
, ('smart_dba','Table','AnnotationAuthorities','All')
, ('smart_dba','Table','AnnotationTypes','All')
, ('smart_dba','Table','CampusNames','AllButDelete')
, ('smart_dba','Table','CampusLocations','AllButDelete')
, ('smart_dba','Table','HoldAuthorities','All')
, ('smart_dba','Table','HoldTypes','All')
, ('smart_dba','Table','NameTypes','All')
, ('any_staff','Activity','Smart/ValidCodeTables','View')
, ('_min_person_access','Activity','Smart/Person','View')
, ('_min_person_access','Table','Person','View')
, ('_min_person_access','Table','Address','View')
, ('_min_person_access','Table','PhoneNumber','View')
, ('_min_person_access','Table','InstitutionsAttended','View')
, ('_min_staff_access','Activity','Smart/Staff','View')
, ('_min_staff_access','Table','Staff','View')
, ('_min_stu_access','Activity','Smart/Student','View')
, ('_min_stu_access','Table','Student','View')
, ('_min_stu_access','Table','RelatedNames','View')
, ('_min_all_people_access','Table','ModuleAssignments','View')
, ('_min_all_people_access','Table','Enrollment','View')
, ('_person_modifier','Table','Person','AllButDelete')
, ('_person_modifier','Table','Address','All')
, ('_person_modifier','Table','PhoneNumber','All')
, ('_person_modifier','Table','InstitutionsAttended','All')
, ('fin_mgmt','Table','Applicant','View')
, ('fin_mgmt','Table','Children','View')
, ('fin_mgmt','Table','JobFunction','View')
, ('fin_mgmt','Table','StaffContract','View')
, ('fin_mgmt','Table','RecordHold','View')
, ('fin_stu_mod','Table','RecordHold','AllButDelete')
, ('hr_view','Table','Children','View')
, ('hr_view','Table','JobFunction','View')
, ('hr_view','Table','StaffContract','View')
, ('hr_view','Table','Accidents','View')
, ('hr_view','Table','StaffDisciplinaryAction','View')
, ('hr_view','Activity','Smart/Staff/Procedures','View')
, ('hr_mod','Table','Schools','AllButDelete')
, ('hr_mod','Table','Departments','AllButDelete')
, ('hr_mod','Table','Staff','AllButDelete')
, ('hr_mod','Table','Children','All')
, ('hr_mod','Table','JobFunction','AllButDelete')
, ('hr_mod','Table','StaffContract','AllButDelete')
, ('hr_mod','Table','Accidents','AllButDelete')
, ('hr_mod','Table','StaffDisciplinaryAction','AllButDelete')
, ('hr_mod','Table','ContractStatusCodes','AllButDelete')
, ('hr_mod','Table','JobCategories','AllButDelete')
, ('curric_mod','Table','Terms','AllButDelete')
, ('curric_mod','Table','Schools','AllButDelete')
, ('curric_mod','Table','Departments','AllButDelete')
, ('curric_mod','Table','AcadProgramTypes','AllButDelete')
, ('curric_mod','Table','AcadProgram','All')
, ('curric_mod','Table','ProgPlanOfStudy','All')
, ('curric_mod','Table','Modules','All')
, ('admiss_view','Table','Applicant','View')
, ('admiss_view','Activity','Smart/Student/Procedures','View')
, ('admiss_view','Table','RecordHold','View')
, ('admiss_mod','Table','Applicant','AllButDelete')
, ('admiss_mod','Table','AdmissExams','All')
, ('admiss_mod','Table','AdmissExamNames','AllButDelete')
, ('admiss_mod','Table','AdmissTestCodes','AllButDelete')
, ('admiss_mod','Table','AdmissTestDescriptors','AllButDelete')
, ('admiss_mod','Table','ApplicationStatusCodes','AllButDelete')
, ('any_ss','Table','AdmissExams','View')
, ('any_ss','Table','StudentAcadProgram','View')
, ('any_ss','Table','Enrollment','View')
, ('any_ss','Table','Advising','View')
, ('any_ss','Table','CourseGrades','View')
, ('any_ss','Table','GradeApproval','View')
, ('any_ss','Table','CompExamScores','View')
, ('any_ss','Table','TermStanding','View')
, ('any_ss','Table','SessionStanding','View')
, ('any_ss','Table','RecordHold','View')
, ('any_ss','Activity','Smart/Student/Procedures','View')
, ('campus_ss_staff','Table','Student','AllButDelete')
, ('campus_ss_staff','Table','RelatedNames','All')
, ('campus_ss_staff','Table','Advising','All')
, ('campus_ss_staff','Table','ModuleOfferings','All')
, ('campus_ss_staff','Table','ModuleAssignments','All')
, ('campus_ss_staff','Table','Enrollment','All')
, ('campus_ss_staff','Table','CourseGrades','All')
, ('campus_ss_staff','Table','GradeApproval','All')
, ('campus_ss_staff','Table','CompExamScores','All')
, ('campus_ss_staff','Table','TermStanding','All')
, ('campus_ss_staff','Table','SessionStanding','All')
, ('sec_ss_staff','Table','AdvisorTypes','AllButDelete')
, ('sec_ss_staff','Table','StudentProgramStatusCodes','AllButDelete')
, ('sec_ss_staff','Table','ClassLevelCodes','AllButDelete')
, ('sec_ss_staff','Table','StudentLeaveTypes','AllButDelete')
, ('sec_ss_staff','Table','StudentModStatusCodes','AllButDelete')
, ('sec_ss_staff','Table','TermStandingCodes','AllButDelete')
, ('sec_ss_staff','Table','StudentAcadProgram','All')
, ('sec_ss_staff','Table','StudentAnnotations','All')
, ('sec_ss_staff','Table','StudentLeaves','All')
, ('sec_ss_staff','Table','RecordHold','AllButDelete')
, ('developer','Activity','tests','View')
, ('developer','Activity','tests/activityTesting','View')
, ('developer','Activity','tests/formTesting','View')
, ('developer','Activity','tests/menuTests','View')
, ('developer','Activity','tests/miscTests','View')
, ('developer','Activity','tests/settingTesting','View')
;
UNLOCK TABLES;

-- Very basic authorizations can alternatively be defined in 
-- the application.ini file, e.g., guest access to the docs/rampDocs
-- directory or smart_dba access to the Admin directory and the
-- ramp_auth_users and ramp_auth_auths tables.
