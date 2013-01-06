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
-- The table below illustrates users corresponding to several of
-- the sample roles defined in
-- application/configs/smartApplicationTemplate.ini.
--

DROP TABLE IF EXISTS `ramp_auth_users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ramp_auth_users` (
  `pk_id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `username` varchar(100) NOT NULL,
  `password` varchar(40) NOT NULL,
  `role` varchar(100) NOT NULL DEFAULT 'guest',
  `domainID` int(11) DEFAULT NULL
);
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `ramp_auth_users` WRITE;
/*!40000 ALTER TABLE `ramp_auth_users` DISABLE KEYS */;
INSERT INTO `ramp_auth_users` (username, password, role, domainID)
VALUES ('guest','guest','guest',NULL)
,('abrady','abrady','ramp_dba',1)
,('tnjohn','tnjohn','ramp_dba', 4)
,('songu','songu','ramp_dba',5)
,('akamara','akamara','regist_staff',7)
;
/*!40000 ALTER TABLE `ramp_auth_users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Resources and Authorizations:
-- ramp_auth_auths: The 'guest' role should have few if any privileges.
-- The 'ramp_dba' role has all privileges, with other roles having
-- intermediate levels of privileges.
--
-- The rules provide access to different sets of tables for two different
-- groups of users, HR staff and management and Registrar's Office staff
-- and management.  
--
-- Note that in this authorization table, managers have *fewer* permissions
-- than the staff (for example, managers may be viewing tables for review
-- and reporting while the staff have responsibility for data entry).
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
,('guest','Report','places','View')
,('guest','Table','places','View')
,('guest','Table','albums','View')
,('hr_mgmt','Activity','Smart','All')
,('hr_mgmt','Activity','Smart/Curriculum','All')
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
,('regist_mgmt','Activity','Smart/Curriculum','All')
,('regist_mgmt','Activity','Smart/Person','All')
,('regist_mgmt','Activity','Smart/Staff','View')
,('regist_mgmt','Activity','Smart/Student','View')
,('regist_mgmt','Activity','ManageAuths','All')
,('regist_mgmt','Table','AcadProgram','View')
,('regist_mgmt','Table','Modules','View')
,('regist_mgmt','Table','ModuleAssignments','View')
,('regist_mgmt','Table','ModuleOfferings','View')
,('regist_mgmt','Table','Person','View')
,('regist_mgmt','Table','Advising','View')
,('regist_mgmt','Table','Student','View')
,('regist_mgmt','Table','StudentAcadProgram','View')
,('regist_mgmt','Table','Enrollment','View')
,('regist_mgmt','Table','TermStanding','View')
,('regist_mgmt','Table','TestScores','View')
,('regist_mgmt','Table','Terms','All')
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
,('regist_staff','Table','Person','View')
,('regist_staff','Table','Person','AddRecords')
,('regist_staff','Table','Person','ModifyRecords')
,('regist_staff','Table','Staff','View')
,('regist_staff','Table','Student','All')
,('regist_staff','Table','Advising','All')
,('regist_staff','Table','StudentAcadProgram','All')
,('regist_staff','Table','Enrollment','All')
,('regist_staff','Table','TermStanding','All')
,('regist_staff','Table','TestScores','All')
;
/*!40000 ALTER TABLE `ramp_auth_auths` ENABLE KEYS */;
UNLOCK TABLES;
