--
-- Authorization users and access control lists / rules for Smart Demo
--

--
-- Users:
-- ramp_auth_users: For most databases, there should be one
-- (or possibly two) initial user record for the database administrator(s),
-- who would then be responsible for adding other user with their roles.
-- All roles, including the ramp_dba role (or something similar), should be
-- defined in application/configs/application.ini.
--
-- The table below illustrates users corresponding to a variety of
-- the sample roles defined in
-- application/configs/smartApplicationTemplate.ini.
--

DROP TABLE IF EXISTS `ramp_auth_users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ramp_auth_users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(100) NOT NULL,
  `password` varchar(40) NOT NULL,
  `role` varchar(100) NOT NULL DEFAULT 'guest' ,
  `first_name` varchar(100) DEFAULT NULL,
  `last_name` varchar(100) DEFAULT NULL,
  `email` varchar(150) NOT NULL,
  `domainID` int,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `ramp_auth_users` WRITE;
/*!40000 ALTER TABLE `ramp_auth_users` DISABLE KEYS */;
INSERT INTO `ramp_auth_users`
(role, first_name, last_name, username, password, email, domainID)
VALUES
('guest','Guest','Guest','guest','guest','',NULL)
,('guest','Charlie','Brown','cbrown','cbrown','cbrown@gmail.com',8)
,('ramp_dba','Alyce','Brady','abrady','abrady','abrady@kzoo.edu',1)
,('hr_staff','HR','Staff','hrstaff','hrstaff','hrstaff@kzoo.edu',NULL)
,('hr_mgmt','HR','Management','hrmgmt','hrmgmt','hrmgmt@kzoo.edu',NULL)
,('regist_staff','Regist','Staff','rstaff','rstaff','rstaff@kzoo.edu',0)
,('regist_mgmt','Regist','Management','rmgmt','rmgmt','rmgmt@kzoo.edu',0)
,('ramp_dba','RAMP','DBA','dba','dba','dba@kzoo.edu',NULL)
,('ramp_dba','RAMP','Backup_DBA','backup_dba','backup_dba',
    'backup_dba@kzoo.edu',NULL)
;
/*!40000 ALTER TABLE `ramp_auth_users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Resources and Authorizations:
-- ramp_auth_auths: For most databases, the 'guest' role should have very
-- limited privileges, 'ramp_dba' should have all privileges, and other
-- roles will have intermediate levels of privileges, as shown in this
-- file.
--
-- The sample access control (authorization) rules below define access
-- to a core set of fundamental tables for an academic records system.
-- The rules provide access to different sets of tables for two different
-- groups of users, HR staff and management and Registrar's Office staff
-- and management.  
--
-- Note that in this example, managers have *fewer* permissions than the
-- staff (for example, managers may be viewing tables for review and
-- reporting while the staff have responsibility for data entry).  This
-- may not reflect the actual responsibilities in a real office.
--

DROP TABLE IF EXISTS `ramp_auth_auths`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ramp_auth_auths` (
  `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `role` varchar(100) NOT NULL,
  `resource_type` enum('Activity','Report','Table') NOT NULL,
  `resource_name` varchar(100) NOT NULL,
  `action` enum('All','View','AddRecords','ModifyRecords','DeleteRecords') NOT NULL DEFAULT 'View'
) ENGINE=InnoDB AUTO_INCREMENT=79 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `ramp_auth_auths` WRITE;
/*!40000 ALTER TABLE `ramp_auth_auths` DISABLE KEYS */;
INSERT INTO `ramp_auth_auths`
(`role`, `resource_type`, `resource_name`, `action`) VALUES
('ramp_dba','Activity','ManageAuths','All')
,('ramp_dba','Table','ramp_auth_auths','All')
,('ramp_dba','Table','ramp_auth_users','All')
,('ramp_dba','Table','Person','DeleteRecords')
,('ramp_dba','Activity','Typo','View')
,('guest','Activity','demo','All')
,('guest','Table','albums','View')
,('guest','Report','places','View')
,('guest','Table','places','View')
,('guest','Activity','Smart/Curriculum','All')
,('guest','Table','Terms','View')
,('guest','Table','AcadProgram','View')
,('guest','Table','Modules','View')
,('guest','Table','ModuleOfferings','View')
,('hr_mgmt','Activity','Smart','All')
,('hr_mgmt','Activity','Smart/Person','All')
,('hr_mgmt','Activity','Smart/Staff','All')
,('hr_mgmt','Table','Staff','View')
,('hr_mgmt','Report','StaffContract','View')
,('hr_mgmt','Table','StaffContract','View')
,('hr_staff','Table','Person','View')
,('hr_staff','Table','Person','AddRecords')
,('hr_staff','Table','Person','ModifyRecords')
,('hr_staff','Table','Staff','View')
,('hr_staff','Table','Staff','AddRecords')
,('hr_staff','Table','Staff','ModifyRecords')
,('hr_staff','Table','StaffContract','All')
,('hr_staff','Table','ModuleAssignments','View')
,('regist_mgmt','Activity','Smart','All')
,('regist_mgmt','Activity','Smart/Person','All')
,('regist_mgmt','Activity','Smart/Staff','View')
,('regist_mgmt','Activity','Smart/Student','View')
,('regist_mgmt','Activity','ManageAuths','All')
,('regist_mgmt','Table','ModuleAssignments','View')
,('regist_mgmt','Table','Person','View')
,('regist_mgmt','Table','Staff','View')
,('regist_mgmt','Table','Advising','View')
,('regist_mgmt','Table','Student','View')
,('regist_mgmt','Table','StudentAcadProgram','View')
,('regist_mgmt','Table','Enrollment','View')
,('regist_mgmt','Table','TermStanding','View')
,('regist_mgmt','Table','TestScores','View')
,('regist_staff','Activity','tests','All')
,('regist_staff','Activity','tests/activityTesting','All')
,('regist_staff','Activity','tests/formTesting','All')
,('regist_staff','Activity','tests/miscTests','All')
,('regist_staff','Activity','tests/settingTesting','All')
,('regist_staff','Table','places','All')
,('regist_staff','Table','albums','All')
,('regist_staff','Table','AcadProgram','All')
,('regist_staff','Table','Modules','All')
,('regist_staff','Table','ModuleAssignments','All')
,('regist_staff','Table','ModuleOfferings','All')
,('regist_staff','Table','Person','AddRecords')
,('regist_staff','Table','Person','ModifyRecords')
,('regist_staff','Table','Student','AddRecords')
,('regist_staff','Table','Student','ModifyRecords')
,('regist_staff','Table','Advising','All')
,('regist_staff','Table','StudentAcadProgram','All')
,('regist_staff','Table','Enrollment','All')
,('regist_staff','Table','TermStanding','All')
,('regist_staff','Table','TestScores','All')
;
/*!40000 ALTER TABLE `ramp_auth_auths` ENABLE KEYS */;
UNLOCK TABLES;

