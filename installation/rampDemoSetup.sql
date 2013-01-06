--
-- This file contains SQL code to create a sample RAMP database
-- (`ramp_demo`) with simple tables ('albums' and 'places') that have
-- corresponding table settings in the settings/demo directory, a single
-- user ('guest'), and a set of authorization rules giving that user
-- viewing permissions on the tables in the database.
--

--
-- Create Database: `ramp_demo`
--

DROP DATABASE IF EXISTS `ramp_demo`;
CREATE DATABASE `ramp_demo` /*!40100 DEFAULT CHARACTER SET latin1 */;

USE `ramp_demo`;

--
-- Read in sample table structures and data from another file.
--

SOURCE rampDemoData.sql


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
-- below illustrates users corresponding to the three sample roles defined
-- in application/configs/rampApplicationTemplate.ini.
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
('Database', 'Administrator', 'dba', 'ramppass', 'emailAddr@yahoo.com', 'ramp_dba')
, ('Backup', 'DBA', 'backup_dba', 'backup_pass', 'emailAddr2@gmail.com', 'ramp_dba')
, ('A', 'User', 'user1', 'userpass', 'auser@abc.com', 'ramp_user')
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
-- The ramp_demo example below assumes that there is at least one
-- demo-related activity file in a directory called 'demo' and that it
-- refers to two defined tables in the database called 'albums' and
-- 'places' that guests may view but not alter.
-- Note that it does not define additional access rules for the ramp_user
-- or ramp_dba roles.
--

DROP TABLE IF EXISTS `ramp_auth_auths`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ramp_auth_auths` (
  `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `role` varchar(100) NOT NULL,
  `resource_type` enum('Activity','Report','Table') NOT NULL,
  `resource_name` varchar(100) NOT NULL,
  `action` enum('All','View','AddRecords','ModifyRecords','DeleteRecords')
        NOT NULL DEFAULT 'View'
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
;
/*!40000 ALTER TABLE `ramp_auth_auths` ENABLE KEYS */;
UNLOCK TABLES;

--
-- The additional example below assumes that users with the default 'guest' 
-- role have no special privileges, whereas users with the 'ramp_user' role
-- may view records in a table called 'readOnlyTable' and view, add, or alter
-- (but not delete) records in a table called 'readWriteTable'.  Users
-- with the 'ramp_dba' role have permisions to view and alter the
-- 'ramp_auth_users' and 'ramp_auth_auths' tables (and may have all
-- 'ramp_user' permissions as well if the 'ramp_dba' role was defined as
-- inheriting from the 'ramp_user' role in application.ini).
--

-- INSERT INTO ramp_auth_auths (role, resource_type, resource_name, action)
-- VALUES
-- ('ramp_user', 'Activity', 'domainDirectory', 'All')
-- , ('ramp_user', 'Table', 'readOnlyTable', 'View')
-- , ('ramp_user', 'Table', 'readWriteTable', 'View')
-- , ('ramp_user', 'Table', 'readWriteTable', 'AddRecords')
-- , ('ramp_user', 'Table', 'readWriteTable', 'ModifyRecords')
-- , ('ramp_dba', 'Activity', 'ManageAuths', 'All')
-- , ('ramp_dba', 'Table', 'ramp_auth_users', 'All')
-- , ('ramp_dba', 'Table', 'ramp_auth_auths', 'All')
-- ;
