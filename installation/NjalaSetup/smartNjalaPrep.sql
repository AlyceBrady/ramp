--
-- Current Database: `njala_dev`
--

CREATE DATABASE /*!32312 IF NOT EXISTS*/ `njala_dev` /*!40100 DEFAULT CHARACTER SET latin1 */;

USE `njala_dev`;

--
-- Table structure for table `Terms`
--

DROP TABLE IF EXISTS `Terms`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Terms` (
  `term` varchar(15) NOT NULL,
  `acadYear` varchar(10) DEFAULT NULL,
  `startDate` date NOT NULL,
  `censusDate` date NOT NULL,
  `endDate` date NOT NULL,
  `updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`term`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Terms`
--

LOCK TABLES `Terms` WRITE;
/*!40000 ALTER TABLE `Terms` DISABLE KEYS */;
INSERT INTO `Terms` VALUES
('2008-09 Sem 1','2008-09','2008-09-10','2008-09-15','2008-12-15','2012-03-19 21:43:04')
,('2008-09 Sem 2','2008-09','2009-04-01','2009-04-05','2009-06-20','2012-03-19 21:43:04')
,('2009-10 Sem 1','2009-10','2009-09-10','2009-09-15','2009-12-15','2012-03-19 21:43:04')
,('2009-10 Sem 2','2009-10','2010-04-01','2010-04-05','2010-06-20','2012-03-19 21:43:04')
,('2010-11 Sem 1','2010-11','2010-09-10','2010-09-15','2010-12-15','2012-03-19 21:43:04')
,('2010-11 Sem 2','2010-11','2011-04-01','2011-04-05','2011-06-20','2012-03-19 21:43:04')
,('2011-12 Sem 1','2011-12','2011-09-10','2011-09-15','2011-12-15','2012-03-19 21:43:04')
,('2011-12 Sem 2','2011-12','2012-04-01','2012-04-05','2012-06-20','2012-03-19 21:43:04')
,('2012-13 Sem 1','2012-13','2012-09-10','2012-09-15','2012-12-15','2012-03-19 21:43:04')
,('2012-13 Sem 2','2012-13','2013-03-20','2013-03-27','2013-06-20','2012-11-30 02:49:11');
/*!40000 ALTER TABLE `Terms` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `AcadProgram`
--

DROP TABLE IF EXISTS `AcadProgram`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `AcadProgram` (
  `programID` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(30) NOT NULL,
  `type` enum('Coursework','B.A.','B.S.','M.Sc.','Ph.D.','Major','Minor') NOT NULL DEFAULT 'Coursework',
  `school` varchar(30) NOT NULL,
  `division` varchar(30) DEFAULT NULL,
  `department` varchar(30) DEFAULT NULL,
  `startDate` date NOT NULL,
  `endDate` date DEFAULT NULL,
  `updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`programID`)
) ENGINE=InnoDB AUTO_INCREMENT=32 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `AcadProgram`
--

LOCK TABLES `AcadProgram` WRITE;
/*!40000 ALTER TABLE `AcadProgram` DISABLE KEYS */;
INSERT INTO `AcadProgram` VALUES (1,'Bachelor of Arts','B.A.','K',NULL,NULL,'1833-09-01',NULL,'2012-03-21 17:22:16'),(5,'Rhetoric','Major','K',NULL,'Literature/Writing','1870-09-01','1950-09-01','2012-03-21 17:22:16'),(6,'Mathematics','Major','K',NULL,'Mathematics','1870-09-01','1979-09-01','2012-03-21 17:22:16'),(7,'Mathematics','Minor','K',NULL,'Mathematics','1970-09-01','1979-09-01','2012-03-21 17:22:16'),(10,'Computer Science','Major','K',NULL,'Math/CS','1979-09-01',NULL,'2012-03-21 17:22:16'),(11,'Computer Science','Minor','K',NULL,'Math/CS','1979-09-01',NULL,'2012-03-21 17:22:16'),(20,'Literature','Major','K',NULL,'Literature/Writing','1950-09-01',NULL,'2012-03-21 17:22:16'),(21,'Literature','Minor','K',NULL,'Literature/Writing','1970-09-01',NULL,'2012-03-21 17:22:16'),(22,'Creative Writing','Major','K',NULL,'Literature/Writing','1950-09-01',NULL,'2012-03-21 17:22:16'),(23,'Creative Writing','Minor','K',NULL,'Literature/Writing','1970-09-01',NULL,'2012-03-21 17:22:16'),(25,'Psychology','Major','K','Social Science','Psychology','1990-01-01',NULL,'2012-04-26 16:43:32'),(30,'Mathematics','Major','K',NULL,'Math/CS','1979-09-01',NULL,'2012-03-21 17:22:16'),(31,'Mathematics','Minor','K',NULL,'Math/CS','1979-09-01',NULL,'2012-03-21 17:22:16');
/*!40000 ALTER TABLE `AcadProgram` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Modules`
--

DROP TABLE IF EXISTS `Modules`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Modules` (
  `moduleID` int(11) NOT NULL AUTO_INCREMENT,
  `modCode` varchar(6) NOT NULL,
  `modNumber` varchar(6) NOT NULL,
  `status` enum('Proposed','Active','Inactive') NOT NULL,
  `shortTitle` varchar(30) NOT NULL,
  `longTitle` varchar(60) NOT NULL,
  `description` text,
  `credits` double NOT NULL DEFAULT '3',
  `capacity` int(11) DEFAULT NULL,
  `type` enum('Institutional','External Course','Test Equiv.','Other') NOT NULL DEFAULT 'Institutional',
  `startDate` date NOT NULL,
  `endDate` date DEFAULT NULL,
  `updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`moduleID`)
);

# Add catalog VARCHAR ( 30 ) NOT NULL,  ?
# /* Status:
# If current date is before start date: Proposed.
# If end date is null or after current date: Active.
# Else: Inactive.
# */

INSERT INTO Modules (moduleID, modCode, modNumber, status,
    shortTitle, longTitle, startDate)
VALUES
(1, 'COMP', '105', 'Active', 'Intro to CS', 'Introduction to Computer Sci.',
    '2000-01-01')
, (2, 'COMP', '107', 'Active', 'Multimedia Prog', 'Multimedia Programming',
    '2000-01-01')
, (3, 'COMP', '110', 'Active', 'Intro Prog.', 'Introduction to Programming',
    '2000-01-01')
, (4, 'COMP', '210', 'Active', 'Data Struct.', 'Data Structures', '2000-01-01')
, (5, 'COMP', '215', 'Active', 'Algorithms', 'Computer Algorithms',
    '2000-01-01')
, (6, 'COMP', '230', 'Active', 'Comp. Org.', 'Computer Organization',
    '2000-01-01')
, (7, 'COMP', '300', 'Active', 'Automata', 'Automata, Languages, and Computability',
    '2000-01-01')
, (8, 'COMP', '315', 'Active', 'Prog. Langs', 'Programming Language Concepts',
    '2000-01-01')
, (9, 'COMP', '420', 'Active', 'Op. Sys.', 'Operating Systems', '2000-01-01')
, (10, 'COMP', '483', 'Active', 'Cryptography', 'Cryptography', '2000-01-01')
, (11, 'COMP', '487', 'Active', 'Softw. Eng.', 'Software Engineering',
    '2000-01-01')
, (20, 'MATH', '105', 'Active', 'Quant. Reasoning', 'Quantitative Reasoning',
    '2000-01-01')
, (21, 'MATH', '112', 'Active', 'Calculus I', 'Calculus I', '2000-01-01')
, (22, 'MATH', '113', 'Active', 'Calculus II', 'Calculus II', '2000-01-01')
, (23, 'MATH', '213', 'Active', 'Calculus III', 'Calculus III', '2000-01-01')
, (24, 'MATH', '214', 'Active', 'Linear Algebra', 'Linear Algebra',
    '2000-01-01')
, (25, 'MATH', '300', 'Active', 'Abstract Algebra I', 'Abstract Algebra I',
    '2000-01-01')
, (26, 'MATH', '310', 'Active', 'Real Analysis I', 'Real Analysis I',
    '2000-01-01')
, (27, 'MATH', '350', 'Active', 'Probability', 'Probability', '2000-01-01')
, (28, 'MATH', '360', 'Active', 'Statistics', 'Statistics', '2000-01-01')
, (30, 'MATH', '400', 'Active', 'Abstract Algebra II', 'Abstract Algebra II',
    '2000-01-01')
, (31, 'MATH', '410', 'Active', 'Real Analysis II', 'Real Analysis II',
    '2000-01-01')
, (35, 'ENGL', '210', 'Active', 'Brit. Lit.', "British Lit: Austen's Novels", '2000-01-01')
, (40, 'HIST', '110', 'Active', 'US History I', 'US History I', '2000-01-01')
, (41, 'HIST', '111', 'Active', 'US History II', 'US History II', '2000-01-01')
, (42, 'HIST', '210', 'Active', 'West Afr. Hist I', 'West African History I',
    '2000-01-01')
, (43, 'HIST', '211', 'Active', 'West Afr. Hist II', 'West African History II',
    '2000-01-01')
, (50, 'ECON', '105', 'Active', 'Microeconomics', 'Intro. to Microeconomics',
    '2000-01-01')
, (51, 'ECON', '106', 'Active', 'Macroeconomics', 'Intro. to Macroeconomics',
    '2000-01-01')
, (60, 'FREN', '101', 'Active', 'French I', 'French Lang. and Lit. I',
    '2000-01-01')
, (61, 'FREN', '102', 'Active', 'French II', 'French Lang. and Lit. II',
    '2000-01-01')
, (62, 'FREN', '103', 'Active', 'French III', 'French Lang. and Lit. III',
    '2000-01-01')
, (70, 'WRIT', '100', 'Active', 'First-Year Sem.', 'First-Year Writing Seminar',
    '2000-01-01')
;

--
-- Table structure for table `ModuleOfferings`
--

DROP TABLE IF EXISTS `ModuleOfferings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ModuleOfferings` (
  `term` varchar(10) NOT NULL,
  `moduleID` int(11) NOT NULL,
  `section` varchar(3) NOT NULL,
  `modCode` varchar(6) NOT NULL,
  `modNumber` varchar(6) NOT NULL,
  `shortTitle` varchar(30) NOT NULL,
  `longTitle` varchar(60) NOT NULL,
  `description` text,
  `credits` double NOT NULL DEFAULT '3',
  `capacity` int(11) DEFAULT NULL,
  `status` enum('Offered','Canceled') NOT NULL DEFAULT 'Offered',
  `type` enum('Institutional','External Course','Test Equiv.','Other') NOT NULL DEFAULT 'Institutional',
  `startDate` date DEFAULT NULL,
  `endDate` date DEFAULT NULL,
  `studentsAtCensusDate` int(11) DEFAULT NULL,
  `studentsAtCompletion` int(11) DEFAULT NULL,
  `updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`term`,`moduleID`,`section`),
    FOREIGN KEY (term) REFERENCES Terms (term),
    FOREIGN KEY (moduleID) REFERENCES Modules (moduleID)
);
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ModuleOfferings`
--

LOCK TABLES `ModuleOfferings` WRITE;
/*!40000 ALTER TABLE `ModuleOfferings` DISABLE KEYS */;
INSERT INTO `ModuleOfferings` VALUES ('2011-12 Sem 1',1,'1','COMP','105','Intro to CS','Introduction to Computer Sci.',NULL,3,30,'Offered','Institutional','2011-09-10',NULL,NULL,NULL,'2012-03-21 01:03:51'),('2011-12 Sem 1',21,'1','MATH','112','Calculus I','Calculus I',NULL,3,30,'Offered','Institutional','2011-09-10',NULL,NULL,NULL,'2012-03-21 01:03:51'),('2011-12 Sem 1',21,'2','MATH','112','Calculus I','Calculus I',NULL,3,30,'Offered','Institutional','2011-09-10','2011-12-15',NULL,NULL,'2012-03-21 01:19:13'),('2011-12 Sem 1',35,'1','ENGL','210','Brit. Lit.','British Lit: Austen\'s Novels',NULL,3,25,'Offered','Institutional','2011-09-10',NULL,NULL,NULL,'2012-03-21 01:03:51'),('2011-12 Sem 1',60,'1','FREN','101','French I','French Lang. and Lit. I',NULL,3,18,'Offered','Institutional','2011-09-10',NULL,NULL,NULL,'2012-03-21 01:03:51'),('2011-12 Sem 1',70,'1','WRIT','100','First-Year Sem.','First-Year Writing Seminar',NULL,3,15,'Offered','Institutional','2011-09-10',NULL,NULL,NULL,'2012-03-21 01:03:51'),('2011-12 Sem 1',70,'2','WRIT','100','First-Year Sem.','First-Year Writing Seminar',NULL,3,15,'Offered','Institutional','2011-09-10','2011-12-15',NULL,NULL,'2012-03-21 01:20:15'),('2011-12 Sem 2',3,'1','COMP','110','Intro Prog.','Introduction to Programming',NULL,3,30,'Offered','Institutional','2012-01-01',NULL,NULL,NULL,'2012-03-21 01:04:42'),('2011-12 Sem 2',21,'1','MATH','112','Calculus I','Calculus I',NULL,3,30,'Offered','Institutional','2012-01-01',NULL,NULL,NULL,'2012-03-21 13:25:41'),('2011-12 Sem 2',22,'1','MATH','112','Calculus I','Calculus I',NULL,3,30,'Offered','Institutional','2012-01-01',NULL,NULL,NULL,'2012-03-21 13:25:41'),('2011-12 Sem 2',35,'1','ENGL','210','Brit. Lit.','British Lit: Austen\'s Novels',NULL,3,25,'Offered','Institutional','2012-01-01',NULL,NULL,NULL,'2012-03-21 01:05:16'),('2011-12 Sem 2',40,'1','HIST','110','US History I','US History I',NULL,3,25,'Offered','Institutional','2012-01-01',NULL,NULL,NULL,'2012-03-21 01:05:16'),('2011-12 Sem 2',42,'1','HIST','210','West Afr. Hist I','West African History I',NULL,3,25,'Offered','Institutional','2012-01-01',NULL,NULL,NULL,'2012-03-21 01:05:16'),('2011-12 Sem 2',50,'1','ECON','105','Microeconomics','Intro. to Microeconomics',NULL,3,35,'Offered','Institutional','2012-01-01',NULL,NULL,NULL,'2012-03-21 01:05:16'),('2011-12 Sem 2',61,'1','FREN','102','French II','French Lang. and Lit. II',NULL,3,18,'Offered','Institutional','2012-01-01',NULL,NULL,NULL,'2012-03-21 01:05:16'),('2011-12 Sem 2',70,'1','WRIT','100','First-Year Sem.','First-Year Writing Seminar',NULL,3,15,'Offered','Institutional','2012-01-01',NULL,NULL,NULL,'2012-03-21 01:05:16'),('2012-13 Sem 1',2,'1','COMP','107','Multimedia Prog','Multimedia Programming',NULL,3,30,'Offered','Institutional','2012-04-01',NULL,NULL,NULL,'2012-03-21 01:06:57'),('2012-13 Sem 1',4,'1','COMP','210','Data Struct.','Data Structures',NULL,3,20,'Offered','Institutional','2012-04-01',NULL,NULL,NULL,'2012-03-21 01:06:57'),('2012-13 Sem 1',21,'1','MATH','112','Calculus I','Calculus I',NULL,3,30,'Offered','Institutional','2012-04-01',NULL,NULL,NULL,'2012-03-21 01:06:57'),('2012-13 Sem 1',22,'1','MATH','113','Calculus II','Calculus II',NULL,3,30,'Offered','Institutional','2012-04-01',NULL,NULL,NULL,'2012-03-21 01:06:57'),('2012-13 Sem 1',43,'1','HIST','211','West Afr. Hist II','West African History II',NULL,3,25,'Offered','Institutional','2012-04-01',NULL,NULL,NULL,'2012-03-21 01:06:57'),('2012-13 Sem 1',51,'1','ECON','106','Macroeconomics','Intro. to Macroeconomics',NULL,3,35,'Offered','Institutional','2012-04-01',NULL,NULL,NULL,'2012-03-21 01:06:57'),('2012-13 Sem 1',62,'1','FREN','103','French III','French Lang. and Lit. III',NULL,3,18,'Offered','Institutional','2012-04-01',NULL,NULL,NULL,'2012-03-21 01:06:57'),('2012-13 Sem 2',1,'01','COMP','105','Intro to CS','Introduction to Computer Sci.',NULL,3,30,'Offered','Institutional','2011-09-10','2012-12-15',NULL,NULL,'2012-11-30 01:44:09')
,('2012-13 Sem 2',71,'01','COMP','487','Dynamic Internet Apps','Dynamic Internet Applications','An indescribable course, on so many fronts!',3,30,'Offered','Institutional','2012-09-10','2012-12-15',NULL,NULL,'2012-11-30 04:42:54'),('2012-13 Sem 2',2,'01','COMP','107','Multimedia Prog','Multimedia Programming',NULL,3,NULL,'Offered','Institutional','2013-01-07','2013-03-15',NULL,NULL,'2012-11-30 01:53:27'),('2012-13 Sem 2',2,'01','COMP','107','Multimedia Prog','Multimedia Programming',NULL,3,NULL,'Offered','Institutional','2013-03-20','2013-06-20',NULL,NULL,'2012-11-30 04:16:02');
/*!40000 ALTER TABLE `ModuleOfferings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ModuleAssignments`
--

DROP TABLE IF EXISTS `ModuleAssignments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ModuleAssignments` (
  `pk_id` int(11) NOT NULL AUTO_INCREMENT,
  `term` varchar(10) NOT NULL,
  `moduleID` int(11) NOT NULL,
  `section` varchar(3) DEFAULT NULL,
  `staffID` int(11) NOT NULL,
  `percentage` int(11) NOT NULL DEFAULT '100',
  `classroomNumber` varchar(6) DEFAULT NULL,
  `classroomBuilding` varchar(20) DEFAULT NULL,
  `weeklySchedule` varchar(50) DEFAULT NULL,
  `startDate` date DEFAULT NULL,
  `endDate` date DEFAULT NULL,
  `updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`pk_id`),
    FOREIGN KEY (term, moduleID, section) REFERENCES ModuleOfferings (term, moduleID, section),
    FOREIGN KEY (staffID) REFERENCES Staff (staffID)
);
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ModuleAssignments`
--

--
-- Table structure for table `Person`
--

DROP TABLE IF EXISTS `Person`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Person` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(5) DEFAULT NULL,
  `firstname` varchar(30) NOT NULL,
  `middlename` varchar(30) DEFAULT NULL,
  `lastname` varchar(40) NOT NULL,
  `suffix` varchar(10) DEFAULT NULL,
  `specifiedPrefFName` varchar(30) DEFAULT NULL,
  `prefFirstName` varchar(30) DEFAULT NULL,
  `previousName` varchar(40) DEFAULT NULL,
  `gender` enum('Unknown','M','F') NOT NULL DEFAULT 'Unknown',
  `prefEmail` varchar(30) DEFAULT NULL,
  `prefPhone` varchar(20) DEFAULT NULL,
  `birthDate` date DEFAULT NULL,
  `deceasedDate` date DEFAULT NULL,
  `citizenship` varchar(30) DEFAULT NULL,
  `ethnicGroup` varchar(30) DEFAULT NULL,
  `ssn` varchar(9) DEFAULT NULL,
  `privacy` enum('F','T') NOT NULL DEFAULT 'F',
  `updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Person`
--

LOCK TABLES `Person` WRITE;
/*!40000 ALTER TABLE `Person` DISABLE KEYS */;
INSERT INTO `Person` (title, firstname, middlename, lastname, specifiedPrefFName, gender, prefEmail, prefPhone, birthDate, citizenship) VALUES
('Dr.','Jill','Alyce','Brady','Alyce','F','abrady@kzoo.edu','269-217-8507','1961-06-12','US')
,('Mr.','Aly',NULL,'Turay',NULL,'M','alyturay@yahoo.com',NULL,NULL,'Sierra Leone')
,('Mrs.','Theresa','Njabu','John',NULL,'F',NULL,NULL,NULL,'Sierra Leone')
,('Mr.','Thomas',NULL,'Songu',NULL,'M','thomas_songu@yahoo.co.uk','079453322',NULL,'Sierra Leone')
,('Mr.','Unknown',NULL,'Songa',NULL,'M',NULL,NULL,NULL,'Sierra Leone')
,('Mr.','Unknown',NULL,'Lebbie',NULL,'M',NULL,NULL,NULL,'Sierra Leone')
,('Mr.','Anthony',NULL,'Kamara',NULL,'M',NULL,NULL,NULL,'Sierra Leone')
,('Mr.','Harry',NULL,'Nyandemoh',NULL,'M',NULL,NULL,NULL,'Sierra Leone')
,('Mr.','Antony',NULL,'Jalloh',NULL,'M',NULL,NULL,NULL,'Sierra Leone')
,('Mr.','Sallieu',NULL,'Jalloh',NULL,'M',NULL,NULL,NULL,'Sierra Leone')
,('Mr.','Jeremiah',NULL,'Jozie',NULL,'M',NULL,NULL,NULL,'Sierra Leone')
,('Mr.','Unknown',NULL,'Patrick',NULL,'M',NULL,NULL,NULL,'Sierra Leone')
;
/*!40000 ALTER TABLE `Person` ENABLE KEYS */;
UNLOCK TABLES;

/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`rampdba`@`localhost`*/ /*!50003 TRIGGER prefName_insert BEFORE INSERT ON Person
  FOR EACH ROW BEGIN
    SET NEW.prefFirstName = IF (NEW.specifiedPrefFName IS NULL, NEW.firstName,
                                NEW.specifiedPrefFName);
  END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`rampdba`@`localhost`*/ /*!50003 TRIGGER prefName_update BEFORE UPDATE ON Person
  FOR EACH ROW BEGIN
    SET NEW.prefFirstName = IF (NEW.specifiedPrefFName IS NULL, NEW.firstName,
                                NEW.specifiedPrefFName);
  END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Table structure for table `Staff`
--

DROP TABLE IF EXISTS `Staff`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Staff` (
  `staffID` int(11) NOT NULL PRIMARY KEY,
  `campus` varchar(20) DEFAULT NULL,
  `officeNumber` varchar(6) DEFAULT NULL,
  `officeBuilding` varchar(20) DEFAULT NULL,
  `updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (staffID) REFERENCES Person (id) ON UPDATE CASCADE
);
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Staff`
--

LOCK TABLES `Staff` WRITE;
/*!40000 ALTER TABLE `Staff` DISABLE KEYS */;
INSERT INTO `Staff` (staffID, campus, officeNumber, officeBuilding) VALUES
(1,'Kalamazoo','203G','Olds/Upton Hall')
,(2,'Njala','?','Secretariat')
,(3,'Njala','???','Secretariat')
,(4,'Njala','???','Secretariat')
,(5,'Njala',NULL,'Secretariat')
,(6,'Njala','???','Secretariat')
,(7,'Njala','??','Secretariat')
,(8,'Njala',NULL,'Secretariat')
,(9,'Njala',NULL,'Secretariat')
,(10,'Njala',NULL,'Secretariat')
,(11,'Njala',NULL,'Secretariat')
,(12,'Njala',NULL,'Secretariat')
;
/*!40000 ALTER TABLE `Staff` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `StaffContract`
--

DROP TABLE IF EXISTS `StaffContract`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `StaffContract` (
  `pk_id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `staffID` int(11) NOT NULL,
  `school` varchar(30) DEFAULT NULL,
  `department` varchar(30) DEFAULT NULL,
  `jobFunction` enum('Teaching','Administrative','Service','Other') NOT NULL DEFAULT 'Other',
  `jobTitle` varchar(40) NOT NULL,
  `status` enum('','Full-time','Part-time','On Leave','Ended') DEFAULT NULL,
  `startDate` date NOT NULL,
  `endDate` date DEFAULT NULL,
  `updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (staffID) REFERENCES Person (id),
    INDEX (staffID)
);
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `StaffContract`
--

LOCK TABLES `StaffContract` WRITE;
/*!40000 ALTER TABLE `StaffContract` DISABLE KEYS */;
INSERT INTO `StaffContract` VALUES
(1,1,NULL,'Math/CS','Teaching','Asst. Prof. of Computer Science','Ended','1994-09-26','2001-03-15','2012-03-19 21:22:25')
,(2,1,NULL,'Math/CS','Teaching','Assoc. Prof. of Computer Science','Ended','2001-03-15','2010-03-15','2012-03-19 21:22:25')
,(3,1,NULL,'Math/CS','Teaching','Professor of Computer Science','Full-time','2010-03-15',NULL,'2012-03-19 21:22:25')
;
/*!40000 ALTER TABLE `StaffContract` ENABLE KEYS */;
UNLOCK TABLES;
/*
('Dr.','Jill','Alyce','Brady','Alyce','F','abrady@kzoo.edu','269-217-8507','1961-06-12','US'),
,('Mr.','Aly',NULL,'Turay',NULL,'M','alyturay@yahoo.com',NULL,NULL,'Sierra Leone')
,('Mrs.','Theresa','Njabu','John',NULL,'F',NULL,NULL,NULL,'Sierra Leone')
,('Mr.','Thomas',NULL,'Songu',NULL,'M','thomas_songu@yahoo.co.uk','079453322',NULL,'Sierra Leone')
,('Mr.','Unknown',NULL,'Songa',NULL,'M',NULL,NULL,NULL,'Sierra Leone')
,('Mr.','Unknown',NULL,'Lebbie',NULL,'M',NULL,NULL,NULL,'Sierra Leone')
,('Mr.','Anthony',NULL,'Kamara',NULL,'M',NULL,NULL,NULL,'Sierra Leone')
,('Mr.','Harry',NULL,'Nyandemoh',NULL,'M',NULL,NULL,NULL,'Sierra Leone')
,('Mr.','Antony',NULL,'Jalloh',NULL,'M',NULL,NULL,NULL,'Sierra Leone')
,('Mr.','Sallieu',NULL,'Jalloh',NULL,'M',NULL,NULL,NULL,'Sierra Leone')
,('Mr.','Jeremiah',NULL,'Jozie',NULL,'M',NULL,NULL,NULL,'Sierra Leone')
,('Mr.','Unknown',NULL,'Patrick',NULL,'M',NULL,NULL,NULL,'Sierra Leone')
*/

--
-- Table structure for table `Student`
--

DROP TABLE IF EXISTS `Student`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Student` (
  `studentID` int(11) NOT NULL,
  `advisor` int(11) DEFAULT NULL,
  `transcriptName` varchar(60) DEFAULT NULL,
  `campusAddress` varchar(20) DEFAULT NULL,
  `primaryLanguage` varchar(20) DEFAULT NULL,
  `updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`studentID`),
  KEY `advisor` (`advisor`),
  CONSTRAINT `student_ibfk_1` FOREIGN KEY (`studentID`) REFERENCES `Person` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `student_ibfk_2` FOREIGN KEY (`advisor`) REFERENCES `Person` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Student`
--

LOCK TABLES `Student` WRITE;
/*!40000 ALTER TABLE `Student` DISABLE KEYS */;
/*
INSERT INTO `Student` VALUES (8,1,'Charles Brown','MU 248','English','2012-03-19 21:22:52'),(9,NULL,NULL,'Hicks 1234','English','2012-03-19 21:22:52'),(11,NULL,NULL,'Hicks 789','Bossy English','2012-03-19 21:22:52'),(12,NULL,NULL,'Hicks 1212','English','2012-03-19 21:22:52'),(13,NULL,NULL,'Hicks 1313','English','2012-03-19 21:22:52'),(14,NULL,NULL,'Hicks 1414','English','2012-03-19 21:22:52'),(15,NULL,NULL,'Hicks 1515','English','2012-03-19 21:22:52'),(16,NULL,NULL,'Hicks 1616','English','2012-03-19 21:22:52'),(17,NULL,NULL,'Hicks 1717','English','2012-03-19 21:22:52'),(18,NULL,NULL,'Hicks 1818','English','2012-03-19 21:22:52'),(19,NULL,NULL,'Hicks 1919','English','2012-03-19 21:22:52'),(20,NULL,NULL,'Hicks 2020','English','2012-03-19 21:22:52'),(21,NULL,NULL,'Hicks 2121','English','2012-03-19 21:22:52'),(22,NULL,NULL,'Hicks 2222','English','2012-03-19 21:22:52'),(23,NULL,NULL,'Hicks 2323','English','2012-03-19 21:22:52');
*/
/*!40000 ALTER TABLE `Student` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `StudentAcadProgram`
--

DROP TABLE IF EXISTS `StudentAcadProgram`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `StudentAcadProgram` (
  `pk_id` int(11) NOT NULL AUTO_INCREMENT,
  `studentID` int(11) NOT NULL,
  `programID` int(11) NOT NULL,
  `title` varchar(30) NOT NULL,
  `type` enum('Coursework','B.A.','B.S.','M.Sc.','Ph.D.','Major','Minor') NOT NULL DEFAULT 'Coursework',
  `parentProgramID` int(11) DEFAULT NULL,
  `status` enum('Preparatory','Active','Withdrawn','Ended','Completed') NOT NULL,
  `prepStartDate` date NOT NULL,
  `startDate` date NOT NULL,
  `anticipatedCompletionDate` date DEFAULT NULL,
  `completionDate` date DEFAULT NULL,
  `endDate` date DEFAULT NULL,
  `classLevel` enum('1st Yr','2nd Yr','3rd Yr','4th Yr','5th Year','Longterm') NOT NULL,
  `updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`pk_id`),
  KEY `parentProgramID` (`parentProgramID`),
  CONSTRAINT `studentacadprogram_ibfk_1` FOREIGN KEY (`parentProgramID`) REFERENCES `StudentAcadProgram` (`pk_id`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `StudentAcadProgram`
--

LOCK TABLES `StudentAcadProgram` WRITE;
/*!40000 ALTER TABLE `StudentAcadProgram` DISABLE KEYS */;
/*
INSERT INTO `StudentAcadProgram` VALUES (1,8,1,'Bachelor of Arts','B.A.',NULL,'Active','2011-06-01','2011-09-01','2015-06-15',NULL,NULL,'1st Yr','2012-03-21 18:58:34'),(2,11,1,'Bachelor of Arts','B.A.',NULL,'Active','2011-06-01','2011-09-01','2015-06-15',NULL,NULL,'1st Yr','2012-03-21 18:58:34'),(3,12,1,'Bachelor of Arts','B.A.',NULL,'Active','2008-06-01','2008-09-01','2012-06-15',NULL,NULL,'4th Yr','2012-03-21 18:58:34'),(4,13,1,'Bachelor of Arts','B.A.',NULL,'Active','2009-06-01','2009-09-01','2013-06-15',NULL,NULL,'3rd Yr','2012-03-21 18:58:34'),(5,14,1,'Bachelor of Arts','B.A.',NULL,'Withdrawn','2010-06-01','2010-09-01','2014-06-15',NULL,'2012-01-31','2nd Yr','2012-03-21 18:58:34'),(6,15,1,'Bachelor of Arts','B.A.',NULL,'Active','2008-06-01','2008-09-01','2012-06-15',NULL,NULL,'4th Yr','2012-03-21 18:58:34'),(7,16,1,'Bachelor of Arts','B.A.',NULL,'Active','2008-06-01','2008-09-01','2012-06-15',NULL,NULL,'4th Yr','2012-03-21 18:58:34'),(8,17,1,'Bachelor of Arts','B.A.',NULL,'Active','2010-06-01','2010-09-01','2014-06-15',NULL,NULL,'2nd Yr','2012-03-21 18:58:34'),(9,18,1,'Bachelor of Arts','B.A.',NULL,'Active','2011-06-01','2011-09-01','2015-06-15',NULL,NULL,'1st Yr','2012-03-21 18:58:34'),(10,19,1,'Bachelor of Arts','B.A.',NULL,'Active','2011-06-01','2011-09-01','2015-06-15',NULL,NULL,'1st Yr','2012-03-21 18:58:34'),(11,20,1,'Bachelor of Arts','B.A.',NULL,'Active','2009-06-01','2009-09-01','2013-06-15',NULL,NULL,'3rd Yr','2012-03-21 18:58:34'),(12,21,1,'Bachelor of Arts','B.A.',NULL,'Active','2011-06-01','2011-09-01','2015-06-15',NULL,NULL,'1st Yr','2012-03-21 18:58:34'),(13,22,1,'Bachelor of Arts','B.A.',NULL,'Active','2010-06-01','2010-09-01','2014-06-15',NULL,NULL,'2nd Yr','2012-03-21 18:58:34'),(14,23,1,'Bachelor of Arts','B.A.',NULL,'Active','2009-06-01','2009-09-01','2013-06-15',NULL,NULL,'3rd Yr','2012-03-21 18:58:34'),(15,12,20,'Literature','Major',3,'Active','2010-06-01','2010-06-01','2012-06-15','2012-03-21','2012-03-21','4th Yr','2012-03-21 19:00:43'),(16,16,20,'Literature','Major',7,'Active','2010-06-01','2010-06-01','2012-06-15',NULL,NULL,'4th Yr','2012-03-21 19:00:43'),(17,16,11,'Computer Science','Minor',7,'Active','2010-06-01','2010-06-01','2012-06-15',NULL,NULL,'4th Yr','2012-03-21 19:00:43'),(18,15,10,'Computer Science','Major',6,'Completed','2010-06-01','2010-06-01','2012-06-15','2012-03-21','2012-03-21','4th Yr','2012-03-21 19:00:43'),(19,15,20,'Literature','Major',6,'Completed','2010-06-01','2010-06-01','2012-06-15','2012-03-21','2012-03-21','4th Yr','2012-03-21 19:00:43'),(20,15,31,'Mathematics','Minor',6,'Completed','2010-06-01','2010-06-01','2012-06-15','2012-03-21','2012-03-21','4th Yr','2012-03-21 19:00:43'),(21,13,30,'Mathematics','Major',4,'Active','2011-06-01','2011-06-01','2013-06-15','0000-00-00','0000-00-00','3rd Yr','2012-03-21 19:00:43'),(22,20,10,'Computer Science','Major',11,'Active','2011-06-01','2011-06-01','2013-06-15',NULL,NULL,'3rd Yr','2012-03-21 19:00:43'),(23,20,21,'Literature','Minor',11,'Active','2011-06-01','2011-06-01','2013-06-15',NULL,NULL,'3rd Yr','2012-03-21 19:00:43'),(24,23,20,'Literature','Major',14,'Active','2011-06-01','2011-06-01','2013-06-15',NULL,NULL,'3rd Yr','2012-03-21 19:00:43');
*/
/*!40000 ALTER TABLE `StudentAcadProgram` ENABLE KEYS */;
UNLOCK TABLES;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`rampdba`@`localhost`*/ /*!50003 TRIGGER SAPstatusAndDateCheck_insert BEFORE INSERT ON StudentAcadProgram
  FOR EACH ROW BEGIN
    SET NEW.status = IF (NEW.completionDate IS NOT NULL AND
                         NEW.completionDate <> 0, 'Completed',
                        IF (NEW.status = 'Ended', 'Ended',
                        IF (NEW.endDate IS NOT NULL AND
                         NEW.endDate <> 0, 'Withdrawn',
                        IF (NOW() > NEW.startDate, 'Active', NEW.status))));
    SET NEW.endDate = IF (NEW.completionDate IS NOT NULL,
                                NEW.completionDate, NEW.endDate);
  END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`rampdba`@`localhost`*/ /*!50003 TRIGGER SAPstatusAndDateCheck_update BEFORE UPDATE ON StudentAcadProgram
  FOR EACH ROW BEGIN
    SET NEW.status = IF (NEW.completionDate IS NOT NULL AND
                         NEW.completionDate <> 0, 'Completed',
                        IF (NEW.status = 'Ended', 'Ended',
                        IF (NEW.endDate IS NOT NULL AND
                         NEW.endDate <> 0, 'Withdrawn',                        IF (NOW() > NEW.startDate, 'Active', NEW.status))));
    SET NEW.endDate = IF (NEW.completionDate IS NOT NULL,
                                NEW.completionDate, NEW.endDate);
  END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Table structure for table `TermStanding`
--

DROP TABLE IF EXISTS `TermStanding`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `TermStanding` (
  `pk_id` int(11) NOT NULL AUTO_INCREMENT,
  `studentID` int(11) NOT NULL,
  `term` varchar(10) NOT NULL,
  `standing` enum('GOOD','DEAN''S LIST','PROBATION','FINAL PROBATION','OTHER') NOT NULL DEFAULT 'GOOD',
  `creditsAttempted` double DEFAULT NULL,
  `creditsEarned` double DEFAULT NULL,
  `termGPA` decimal(6,3) DEFAULT NULL,
  `cumulativeCreditsAttempted` double DEFAULT NULL,
  `cumulativeCreditsEarned` double DEFAULT NULL,
  `cumulativeGPA` decimal(6,3) DEFAULT NULL,
  `comment` varchar(100) DEFAULT NULL,
  `updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`pk_id`),
  KEY `studentID` (`studentID`),
  KEY `term` (`term`),
  KEY `standing` (`standing`),
  CONSTRAINT `termstanding_ibfk_1` FOREIGN KEY (`studentID`) REFERENCES `Student` (`studentID`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `TermStanding`
--

LOCK TABLES `TermStanding` WRITE;
/*!40000 ALTER TABLE `TermStanding` DISABLE KEYS */;
/*
INSERT INTO `TermStanding` VALUES (4,8,'2011-12 Sem 1','PROBATION',3,3,2.000,3,3,2.000,NULL,'2012-03-22 03:50:04'),(5,8,'2011-12 Sem 2','GOOD',3.6,NULL,NULL,6.6,3,2.000,NULL,'2012-03-22 03:50:04'),(6,11,'2011-12 Sem 2','GOOD',3.8,NULL,NULL,NULL,NULL,NULL,NULL,'2012-03-22 03:50:04');
*/
/*!40000 ALTER TABLE `TermStanding` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `TestScores`
--

DROP TABLE IF EXISTS `TestScores`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `TestScores` (
  `pk_id` int(11) NOT NULL AUTO_INCREMENT,
  `studentID` int(11) NOT NULL,
  `date_taken` date DEFAULT NULL,
  `title` varchar(30) NOT NULL,
  `category` varchar(6) DEFAULT NULL,
  `testing_agency` varchar(50) DEFAULT NULL,
  `score` double NOT NULL,
  `percentile` int(11) DEFAULT NULL,
  `equivalency` varchar(50) DEFAULT NULL,
  `comments` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`pk_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `TestScores`
--

LOCK TABLES `TestScores` WRITE;
/*!40000 ALTER TABLE `TestScores` DISABLE KEYS */;
/*
INSERT INTO `TestScores` VALUES (3,8,'2011-06-15','Entrance Exam',NULL,'West Africa Exam Agency',120.5,80,NULL,NULL),(4,11,'2011-09-15','Entrance Exam',NULL,'West Africa Exam Agency',142.5,95,NULL,NULL),(5,11,'2011-09-15','AP Calculus BC','AP','West Africa Exam Agency',5,99,'MATH 113',NULL);
*/
/*!40000 ALTER TABLE `TestScores` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `albums`
--

DROP TABLE IF EXISTS `albums`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `albums` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `artist` varchar(100) NOT NULL DEFAULT 'The Beatles',
  `title` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=33 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `albums`
--

LOCK TABLES `albums` WRITE;
/*!40000 ALTER TABLE `albums` DISABLE KEYS */;
INSERT INTO `albums` VALUES (1,'Paolo Nutine','Sunny Side Up'),(2,'Florence + The Machine','Lungs'),(3,'Massive Attack','Heligoland'),(4,'Andre Rieu','Forever Vienna'),(5,'Sade','Soldier of Love'),(9,'The Beatles','Rubber Soul'),(12,'The Beatles','Abbey Road'),(13,'Simon and Garfunkel','Parsley, Sage, Rosemary and Thyme'),(14,'Genesis','We Can\'t Dance'),(15,'R.E.M.','reckoning'),(16,'The Beatles','Help!'),(17,'Beatles','Sgt Pepper\'s Lonely Hearts Club Band'),(28,'The Beatles','Let It Be'),(29,'The Beatles','Please, Please Me'),(32,'The Beatles','Yellow Submarine');
/*!40000 ALTER TABLE `albums` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `places`
--

DROP TABLE IF EXISTS `places`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `places` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date_created` datetime NOT NULL,
  `date_updated` datetime NOT NULL,
  `name` varchar(100) NOT NULL,
  `address1` varchar(100) DEFAULT NULL,
  `address2` varchar(100) DEFAULT NULL,
  `address3` varchar(100) DEFAULT NULL,
  `town` varchar(75) DEFAULT 'London',
  `county` varchar(75) DEFAULT NULL,
  `postcode` varchar(30) DEFAULT NULL,
  `country` varchar(75) DEFAULT 'UK',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `places`
--

LOCK TABLES `places` WRITE;
/*!40000 ALTER TABLE `places` DISABLE KEYS */;
INSERT INTO `places` VALUES (1,'2007-02-14 00:00:00','2007-02-14 00:00:00','London Zoo','Regent\'s Park',NULL,NULL,'London','','NW1 4RY','UK'),(2,'2007-02-20 00:00:00','2007-02-20 00:00:00','Alton Towers','Regent\'s Park',NULL,NULL,'Alton','Staffordshire','ST10 4DB','UK'),(3,'2007-02-16 00:00:00','2007-02-16 00:00:00','Coughton Court','',NULL,NULL,'Alcester','Warwickshire','B49 5JA','UK'),(4,'2012-02-18 15:34:00','2012-02-21 15:34:00','Binder Park Zoo','Exit 100',NULL,NULL,'Battle Creek','MI','','US'),(5,'2012-02-18 15:36:00','2012-02-18 15:36:00','Empire State Building','Midtown Manhattan',NULL,NULL,'New York','NY','','US'),(6,'2012-02-19 01:27:00','2012-02-19 01:27:00','K College','1200 Academy St',NULL,NULL,'Kalamazoo','','','US'),(7,'2010-02-19 15:23:00','2010-02-19 15:23:00','Bowdoin College','Park Row',NULL,NULL,'Brunswick','ME','','US'),(8,'2010-02-19 15:23:00','2010-02-19 15:23:00','RPI','8th Street',NULL,NULL,'Troy','NY','','US'),(9,'2010-02-19 15:23:00','2010-02-19 15:23:00','Home','64th Ave',NULL,NULL,'Mattawan','MI','','US'),(10,'2010-02-19 15:23:00','2010-02-19 15:23:00','Brady Cottage','77 Mapleridge Road',NULL,NULL,'Nobleboro','ME','','US'),(11,'2010-02-19 15:23:00','2010-02-19 15:23:00','Faulstich Cottage','83 Mapleridge Road',NULL,NULL,'Nobleboro','ME','','US'),(14,'2012-02-20 10:09:00','2012-02-20 10:09:00','WMU','Michigan Ave',NULL,NULL,'Kalamazoo',NULL,NULL,'US'),(16,'2012-02-20 10:09:00','2012-02-20 10:09:00','Junk','junk',NULL,NULL,'Brunswick',NULL,NULL,'CA'),(17,'2012-02-20 10:09:00','2012-02-20 10:09:00','Colonial Williamsburg','Duke of Gloucester St',NULL,NULL,'Williamsburg','VA',NULL,'US'),(18,'2012-02-20 10:09:00','2012-02-20 10:09:00','SIGCSE','Sheraton',NULL,NULL,'Raleigh','NC',NULL,'US'),(20,'2012-02-19 01:27:00','2012-02-21 15:34:00','Kazoo School','Cherry Street',NULL,NULL,'Kalamazoo',NULL,NULL,'US'),(21,'2012-02-18 15:34:00','2012-02-20 10:09:00','MHS','McGillen',NULL,NULL,'Mattawan',NULL,NULL,'US'),(22,'2010-02-19 15:23:00','2010-02-19 15:23:00','ARU','238 Maine Street',NULL,NULL,'Brunswick','ME',NULL,'US'),(23,'2012-02-20 10:09:00','2012-02-21 15:34:00','Real Junk','64th Ave',NULL,NULL,'London',NULL,NULL,'UK');
/*!40000 ALTER TABLE `places` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Advising`
--

DROP TABLE IF EXISTS `Advising`;
CREATE TABLE `Advising` (
  `studentID` int(11) NOT NULL,
  `advisorID` int(11) NOT NULL,
  `advisorType` enum('Primary','Coach','Posse','K Guide') NOT NULL,
  `startDate` date NOT NULL,
  `endDate` date DEFAULT NULL,
  `updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`studentID`,`advisorID`,`startDate`),
  FOREIGN KEY (studentID) REFERENCES Student (studentID),
  FOREIGN KEY (advisorID) REFERENCES Person (id)
);

--
-- Dumping data for table `Advising`
--

--
-- Table structure for table `Enrollment`
--

DROP TABLE IF EXISTS `Enrollment`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Enrollment` (
  `pk_id` int(11) NOT NULL AUTO_INCREMENT,
  `studentID` int(11) NOT NULL,
  `term` varchar(10) NOT NULL,
  `moduleID` int(11) NOT NULL,
  `section` varchar(3) DEFAULT NULL,
  `status` enum('Enrolled','Canceled','Dropped','Withdrawn','Completed') NOT NULL,
  `registDate` date NOT NULL,
  `endDate` date DEFAULT NULL,
  `midtermGrade` varchar(3) DEFAULT NULL,
  `submittedTermGrade` varchar(3) DEFAULT NULL,
  `finalGrade` varchar(3) DEFAULT NULL,
  `updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`pk_id`),
    FOREIGN KEY (studentID) REFERENCES Student (studentID),
    FOREIGN KEY (term, moduleID, section)
        REFERENCES ModuleOfferings (term, moduleID, section),
    INDEX (studentID),
    INDEX (term),
    INDEX (moduleID)
);

--
-- Dumping data for table `Enrollment`
--

--
-- Table structure for table `ramp_auth_auths`
--

DROP TABLE IF EXISTS `ramp_auth_auths`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ramp_auth_auths` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `role` varchar(100) NOT NULL,
  `resource_type` enum('Activity','Table') NOT NULL,
  `resource_name` varchar(100) NOT NULL,
  `action` enum('All','View','AddRecords','ModifyRecords','DeleteRecords') NOT NULL DEFAULT 'View',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=79 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ramp_auth_auths`
--

LOCK TABLES `ramp_auth_auths` WRITE;
/*!40000 ALTER TABLE `ramp_auth_auths` DISABLE KEYS */;
INSERT INTO `ramp_auth_auths` VALUES (12,'ramp_dba','Activity','ManageAuths','All'),(14,'ramp_dba','Table','ramp_auth_auths','All'),(15,'ramp_dba','Table','ramp_auth_users','All'),(16,'regist_mgmt','Activity','Smart','All'),(17,'regist_mgmt','Activity','Smart/Curriculum','All'),(19,'regist_mgmt','Activity','Smart/Person','All'),(20,'regist_mgmt','Activity','Smart/Staff','View'),(21,'regist_mgmt','Activity','Smart/Student','View'),(22,'ramp_dba','Activity','Typo','View'),(23,'regist_staff','Table','Student','All'),(24,'regist_mgmt','Activity','ManageAuths','All'),(25,'guest','Activity','demo','All'),(26,'regist_staff','Activity','tests','All'),(27,'regist_staff','Activity','tests/activityTesting','All'),(28,'regist_staff','Activity','tests/formTesting','All'),(29,'regist_staff','Activity','tests/miscTests','All'),(30,'regist_staff','Activity','tests/settingTesting','All'),(31,'regist_staff','Table','places','All'),(32,'regist_staff','Table','albums','All'),(33,'guest','Table','places','View'),(34,'guest','Table','albums','View'),(35,'regist_staff','Table','AcadProgram','All'),(36,'regist_mgmt','Table','AcadProgram','View'),(37,'regist_staff','Table','Modules','All'),(38,'regist_mgmt','Table','Modules','View'),(39,'regist_mgmt','Table','ModuleAssignments','View'),(40,'regist_staff','Table','ModuleAssignments','All'),(41,'regist_staff','Table','ModuleOfferings','All'),(42,'regist_mgmt','Table','ModuleOfferings','View'),(43,'regist_mgmt','Table','Person','View'),(44,'regist_staff','Table','Person','View'),(45,'regist_staff','Table','Person','AddRecords'),(46,'regist_staff','Table','Staff','View'),(47,'regist_staff','Table','Advising','All'),(48,'regist_mgmt','Table','Advising','View'),(49,'regist_mgmt','Table','Student','View'),(50,'regist_staff','Table','Student','View'),(51,'regist_mgmt','Table','StudentAcadProgram','View'),(52,'regist_staff','Table','StudentAcadProgram','All'),(53,'regist_staff','Table','Enrollment','All'),(54,'regist_mgmt','Table','Enrollment','View'),(55,'regist_mgmt','Table','TermStanding','View'),(56,'regist_staff','Table','TermStanding','All'),(57,'regist_staff','Table','TestScores','All'),(58,'regist_mgmt','Table','TestScores','View'),(59,'regist_mgmt','Table','Terms','All'),(60,'hr_staff','Table','ModuleAssignments','View'),(61,'hr_staff','Table','Person','View'),(62,'hr_staff','Table','Person','AddRecords'),(63,'regist_staff','Table','Person','ModifyRecords'),(64,'hr_staff','Table','Person','ModifyRecords'),(65,'hr_staff','Table','Staff','View'),(66,'hr_mgmt','Activity','Smart','All'),(67,'hr_mgmt','Activity','Smart/Curriculum','All'),(68,'hr_mgmt','Activity','Smart/Person','All'),(69,'hr_mgmt','Activity','Smart/Staff','All'),(71,'hr_mgmt','Table','Staff','View'),(72,'hr_mgmt','Table','StaffContract','View'),(73,'hr_staff','Table','StaffContract','All'),(74,'regist_staff','Table','Student','AddRecords'),(75,'regist_staff','Table','Student','ModifyRecords'),(76,'hr_staff','Table','Staff','AddRecords'),(77,'hr_staff','Table','Staff','ModifyRecords'),(78,'ramp_dba','Table','Person','DeleteRecords');
/*!40000 ALTER TABLE `ramp_auth_auths` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ramp_auth_users`
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

--
-- Dumping data for table `ramp_auth_users`
--

LOCK TABLES `ramp_auth_users` WRITE;
/*!40000 ALTER TABLE `ramp_auth_users` DISABLE KEYS */;
INSERT INTO `ramp_auth_users` (username, password, role, domainID)
VALUES ('guest','guest','guest',NULL),('abrady','abrady','ramp_dba',1),('tnjohn','tnjohn','ramp_dba', 4),('songu','songu','ramp_dba',5),('akamara','akamara','regist_staff',7);
/*!40000 ALTER TABLE `ramp_auth_users` ENABLE KEYS */;
UNLOCK TABLES;
