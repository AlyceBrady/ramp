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
-- The demo database could be a special case, with only a single "guest"
-- user account with the default (built-in) 'guest' role, although the table
-- below illustrates users corresponding to three of the sample roles
-- defined in application/configs/smartApplicationTemplate.ini.
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
(first_name, last_name, username, password, email, role)
VALUES
('Database', 'Administrator', 'dba', 'ramppass', 'emailAddr@yahoo.com',
    'ramp_dba')
, ('Backup', 'DBA', 'backup_dba', 'backup_pass', 'emailAddr2@gmail.com',
    'ramp_dba')
, ('HR', 'Staff', 'hrstaff', 'hrstaffpass', 'hrstaff@kzoo.edu', 'hr_staff')
, ('Guest', 'Guest', 'guest', 'guest', '', 'guest')
;
/*!40000 ALTER TABLE `ramp_auth_users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Resources and Authorizations:
-- ramp_auth_auths: For most databases, the 'guest' role should have very
-- limited privileges, 'ramp_dba' should have all privileges, and other
-- roles will have intermediate levels of privileges.
-- The demo database is a special case that could have just a single
-- user ('guest') or a single role (also 'guest').
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
('guest','Activity','demo','All')
,('guest','Table','albums','View')
,('guest','Report','places','View')
,('guest','Table','places','View')
,('guest','Activity','ManageAuths','All')
,('guest','Activity','Smart','All')
,('guest','Activity','Smart/Curriculum','All')
,('guest','Activity','Smart/Person','All')
,('guest','Activity','Smart/Staff','All')
,('guest','Activity','Smart/Student','View')
,('guest','Table','Terms','View')
,('guest','Table','AcadProgram','View')
,('guest','Table','Modules','View')
,('guest','Table','ModuleAssignments','View')
,('guest','Table','ModuleOfferings','View')
,('guest','Table','Person','View')
,('guest','Table','Staff','View')
,('guest','Table','StaffContract','View')
,('guest','Table','Student','View')
,('guest','Table','Advising','View')
,('guest','Table','StudentAcadProgram','View')
,('guest','Table','Enrollment','View')
,('guest','Table','TermStanding','View')
,('guest','Table','TestScores','View')
;
/*!40000 ALTER TABLE `ramp_auth_auths` ENABLE KEYS */;
UNLOCK TABLES;

