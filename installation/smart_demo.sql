-- Created and tested using MySQL Server version 5.5.17, running
-- on osx10.6 (i386).

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Current Database: `smart_demo`
--

CREATE DATABASE /*!32312 IF NOT EXISTS*/ `smart_demo` /*!40100 DEFAULT CHARACTER SET latin1 */;

USE `smart_demo`;

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
-- Table structure for table `Advising`
--

DROP TABLE IF EXISTS `Advising`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Advising` (
  `studentID` int(11) NOT NULL,
  `advisorID` int(11) NOT NULL,
  `advisorType` enum('Primary','Coach','Posse','K Guide') NOT NULL,
  `startDate` date NOT NULL,
  `endDate` date DEFAULT NULL,
  `updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`studentID`,`advisorID`,`startDate`),
  KEY `advisorID` (`advisorID`),
  CONSTRAINT `advising_ibfk_1` FOREIGN KEY (`studentID`) REFERENCES `Student` (`studentID`),
  CONSTRAINT `advising_ibfk_2` FOREIGN KEY (`advisorID`) REFERENCES `Person` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Advising`
--

LOCK TABLES `Advising` WRITE;
/*!40000 ALTER TABLE `Advising` DISABLE KEYS */;
INSERT INTO `Advising` VALUES (8,1,'Primary','2010-08-16',NULL,'2012-03-19 21:44:59'),(8,4,'Coach','2008-09-15',NULL,'2012-03-19 21:44:59'),(8,5,'Primary','2008-09-15','2010-08-15','2012-03-19 21:44:59'),(11,1,'Primary','2012-01-01',NULL,'2012-03-19 21:44:59'),(12,1,'Primary','2011-01-01',NULL,'2012-03-19 21:44:59'),(13,3,'Primary','2011-01-01',NULL,'2012-03-19 21:44:59'),(14,4,'Primary','2011-01-01',NULL,'2012-03-19 21:44:59'),(15,1,'Primary','2011-01-01',NULL,'2012-03-19 21:44:59'),(16,3,'Primary','2011-01-01',NULL,'2012-03-19 21:44:59'),(17,3,'Primary','2011-01-01',NULL,'2012-03-19 21:44:59'),(18,3,'Primary','2011-01-01',NULL,'2012-03-19 21:44:59'),(19,4,'Primary','2011-01-01',NULL,'2012-03-19 21:44:59'),(20,4,'Primary','2011-01-01',NULL,'2012-03-19 21:44:59'),(21,4,'Primary','2011-01-01',NULL,'2012-03-19 21:44:59'),(22,1,'Primary','2011-01-01',NULL,'2012-03-19 21:44:59'),(23,1,'Primary','2011-01-01',NULL,'2012-03-19 21:44:59');
/*!40000 ALTER TABLE `Advising` ENABLE KEYS */;
UNLOCK TABLES;

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
  KEY `term` (`term`,`moduleID`,`section`),
  KEY `studentID` (`studentID`),
  KEY `term_2` (`term`),
  KEY `moduleID` (`moduleID`),
  CONSTRAINT `enrollment_ibfk_1` FOREIGN KEY (`studentID`) REFERENCES `Student` (`studentID`),
  CONSTRAINT `enrollment_ibfk_2` FOREIGN KEY (`term`, `moduleID`, `section`) REFERENCES `ModuleOfferings` (`term`, `moduleID`, `section`)
) ENGINE=InnoDB AUTO_INCREMENT=35 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Enrollment`
--

LOCK TABLES `Enrollment` WRITE;
/*!40000 ALTER TABLE `Enrollment` DISABLE KEYS */;
INSERT INTO `Enrollment` VALUES (1,8,'2011Q4',70,'1','Completed','2011-08-10','2011-12-15',NULL,NULL,'B','2012-03-22 16:37:43'),(2,8,'2011Q4',1,'1','Completed','2011-08-10','2011-12-15',NULL,NULL,'B+','2012-03-22 16:37:43'),(3,8,'2011Q4',21,'1','Completed','2011-08-10','2012-03-22',NULL,NULL,'C','2012-03-22 16:37:43'),(4,8,'2011Q4',60,'1','Completed','2011-08-10','2012-03-22',NULL,NULL,'A','2012-03-22 16:37:43'),(5,8,'2012Q1',3,'1','Enrolled','2011-12-01',NULL,NULL,NULL,NULL,'2012-03-22 16:37:43'),(6,8,'2012Q1',40,'1','Dropped','2011-12-01','2012-01-02',NULL,NULL,NULL,'2012-03-22 16:37:43'),(7,8,'2012Q1',42,'1','Enrolled','2012-01-02',NULL,NULL,NULL,NULL,'2012-03-22 16:37:43'),(8,8,'2012Q1',50,'1','Enrolled','2011-12-01',NULL,NULL,NULL,NULL,'2012-03-22 16:37:43'),(9,8,'2012Q1',61,'1','Enrolled','2011-12-01',NULL,NULL,NULL,NULL,'2012-03-22 16:37:43'),(10,8,'2012Q2',4,'1','Enrolled','2012-03-01',NULL,NULL,NULL,NULL,'2012-03-22 16:37:43'),(11,8,'2012Q2',22,'1','Enrolled','2012-03-01',NULL,NULL,NULL,NULL,'2012-03-22 16:37:43'),(12,8,'2012Q2',51,'1','Enrolled','2012-03-01',NULL,NULL,NULL,NULL,'2012-03-22 16:37:43'),(13,8,'2012Q2',62,'1','Enrolled','2012-03-01',NULL,NULL,NULL,NULL,'2012-03-22 16:37:43'),(14,11,'2012Q1',70,'1','Enrolled','2011-12-01',NULL,NULL,NULL,NULL,'2012-03-22 16:37:43'),(15,11,'2012Q1',35,'1','Enrolled','2011-12-01',NULL,NULL,NULL,NULL,'2012-03-22 16:37:43'),(16,11,'2012Q1',50,'1','Enrolled','2011-12-01',NULL,NULL,NULL,NULL,'2012-03-22 16:37:43'),(17,11,'2012Q1',40,'1','Enrolled','2011-12-01',NULL,NULL,NULL,NULL,'2012-03-22 16:37:43'),(18,11,'2012Q2',21,'1','Enrolled','2012-03-01',NULL,NULL,NULL,NULL,'2012-03-22 16:37:43'),(19,11,'2012Q2',2,'1','Enrolled','2012-03-01',NULL,NULL,NULL,NULL,'2012-03-22 16:37:43'),(20,11,'2012Q2',51,'1','Enrolled','2012-03-01',NULL,NULL,NULL,NULL,'2012-03-22 16:37:43'),(21,11,'2012Q2',43,'1','Enrolled','2012-03-01',NULL,NULL,NULL,NULL,'2012-03-22 16:37:43'),(22,11,'2012Q2',43,'1','Enrolled','2012-03-01',NULL,NULL,NULL,NULL,'2012-03-22 16:37:43'),(23,12,'2011Q4',35,'1','Completed','2011-08-10','2011-12-15',NULL,NULL,'A','2012-03-22 16:37:43'),(24,13,'2011Q4',35,'1','Completed','2011-08-10','2011-12-15',NULL,NULL,'D','2012-03-22 16:37:43'),(25,14,'2011Q4',35,'1','Completed','2011-08-10','2011-12-15',NULL,NULL,'C','2012-03-22 16:37:43'),(26,15,'2011Q4',35,'1','Completed','2011-08-10','2011-12-15',NULL,NULL,'B','2012-03-22 16:37:43'),(27,16,'2011Q4',35,'1','Completed','2011-08-10','2011-12-15',NULL,NULL,'A','2012-03-22 16:37:43'),(28,17,'2011Q4',35,'1','Completed','2011-08-10','2011-12-15',NULL,NULL,'B','2012-03-22 16:37:43'),(29,18,'2011Q4',35,'1','Completed','2011-08-10','2011-12-15',NULL,NULL,'C','2012-03-22 16:37:43'),(30,19,'2011Q4',35,'1','Completed','2011-08-10','2011-12-15',NULL,NULL,'A','2012-03-22 16:37:43'),(31,20,'2011Q4',35,'1','Completed','2011-08-10','2011-12-15',NULL,NULL,'B','2012-03-22 16:37:43'),(32,21,'2011Q4',35,'1','Completed','2011-08-10','2011-12-15',NULL,NULL,'C','2012-03-22 16:37:43'),(33,22,'2011Q4',35,'1','Completed','2011-08-10','2011-12-15',NULL,NULL,'A','2012-03-22 16:37:43'),(34,23,'2011Q4',35,'1','Completed','2011-08-10','2011-12-15',NULL,NULL,'B','2012-03-22 16:37:43');
/*!40000 ALTER TABLE `Enrollment` ENABLE KEYS */;
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
/*!50003 CREATE*/ /*!50017 DEFINER=`rampdba`@`localhost`*/ /*!50003 TRIGGER EnrollmentStatus_Insert BEFORE INSERT ON Enrollment
  FOR EACH ROW BEGIN
    SET NEW.endDate = IF (NEW.finalGrade IS NOT NULL AND NEW.endDate IS NULL,
                                NOW(), NEW.endDate);
    SET NEW.status =
        IF ( NEW.status = 'Enrolled',
            IF ( NEW.endDate IS NOT NULL AND NEW.endDate <> 0,
                IF (NEW.endDate < TermCensusDate(NEW.term),
                    'Dropped',  
                    IF (NEW.endDate < ModOfferingEndDate(NEW.term, NEW.moduleID,
                                                         NEW.section),
                        'Withdrawn',    
                        'Completed')),  
                'Enrolled'),    
            NEW.status);    
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
/*!50003 CREATE*/ /*!50017 DEFINER=`rampdba`@`localhost`*/ /*!50003 TRIGGER EnrollmentStatus_Update BEFORE UPDATE ON Enrollment
  FOR EACH ROW BEGIN
    SET NEW.endDate = IF (NEW.finalGrade IS NOT NULL AND NEW.endDate IS NULL,
                                NOW(), NEW.endDate);
    SET NEW.status =
        IF ( NEW.status = 'Enrolled',
            IF ( NEW.endDate IS NOT NULL AND NEW.endDate <> 0,
                IF (NEW.endDate < TermCensusDate(NEW.term),
                    'Dropped',  
                    IF (NEW.endDate < ModOfferingEndDate(NEW.term, NEW.moduleID,
                                                         NEW.section),
                        'Withdrawn',    
                        'Completed')),  
                'Enrolled'),    
            NEW.status);    
  END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

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
  KEY `term` (`term`,`moduleID`,`section`),
  KEY `staffID` (`staffID`),
  CONSTRAINT `moduleassignments_ibfk_1` FOREIGN KEY (`term`, `moduleID`, `section`) REFERENCES `ModuleOfferings` (`term`, `moduleID`, `section`),
  CONSTRAINT `moduleassignments_ibfk_2` FOREIGN KEY (`staffID`) REFERENCES `Staff` (`staffID`)
) ENGINE=InnoDB AUTO_INCREMENT=126 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ModuleAssignments`
--

LOCK TABLES `ModuleAssignments` WRITE;
/*!40000 ALTER TABLE `ModuleAssignments` DISABLE KEYS */;
INSERT INTO `ModuleAssignments` VALUES (103,'2011Q4',1,'1',1,100,'312','Olds/Upton','MWF 1:15-2:30',NULL,NULL,'2012-03-21 13:26:37'),(104,'2011Q4',21,'1',24,100,'304','Olds/Upton','MWF 8:30-9:45',NULL,NULL,'2012-03-21 13:26:37'),(105,'2011Q4',21,'2',24,100,'304','Olds/Upton','MWF 8:30-9:45',NULL,NULL,'2012-03-21 13:26:37'),(106,'2011Q4',60,'1',4,100,'206','Dewing','MWF 8:30-9:45',NULL,NULL,'2012-03-21 13:26:37'),(107,'2011Q4',35,'1',4,100,'206','Humphrey House','MWF 1:15-2:30',NULL,NULL,'2012-03-21 13:26:37'),(108,'2011Q4',70,'1',6,100,'Lounge','Humphrey House','MWF 11:50-1:05',NULL,NULL,'2012-03-21 13:26:37'),(109,'2011Q4',70,'2',6,100,'114','Dewing','MWF 1:15-2:30',NULL,NULL,'2012-03-21 13:26:37'),(110,'2012Q1',3,'1',1,100,'312','Olds/Upton','MWF 1:15-2:30',NULL,NULL,'2012-03-21 13:27:19'),(111,'2012Q1',21,'1',24,100,'304','Olds/Upton','MWF 8:30-9:45',NULL,NULL,'2012-03-21 13:27:19'),(112,'2012Q1',22,'1',24,100,'304','Olds/Upton','MWF 2:40-3:55',NULL,NULL,'2012-03-21 13:27:19'),(113,'2012Q1',40,'1',3,100,'302','Dewing','TTh 9:00-11:00',NULL,NULL,'2012-03-21 13:27:19'),(114,'2012Q1',42,'1',3,100,'302','Dewing','TTh 1:00-3:00',NULL,NULL,'2012-03-21 13:27:19'),(115,'2012Q1',50,'1',6,100,'316','Dewing','MWF 2:40-3:55',NULL,NULL,'2012-03-21 13:27:19'),(116,'2012Q1',61,'1',4,100,'206','Dewing','MWF 8:30-9:45',NULL,NULL,'2012-03-21 13:27:19'),(117,'2012Q1',35,'1',4,100,'206','Humphrey House','MWF 1:15-2:30',NULL,NULL,'2012-03-21 13:27:19'),(118,'2012Q1',70,'1',6,100,'Lounge','Humphrey House','MWF 11:50-1:05',NULL,NULL,'2012-03-21 13:27:19'),(119,'2012Q2',2,'1',1,100,NULL,NULL,'MWF 1:15-2:30',NULL,NULL,'2012-03-21 13:27:19'),(120,'2012Q2',4,'1',1,100,NULL,NULL,'MWF 10:00-11:15',NULL,NULL,'2012-03-21 13:27:19'),(121,'2012Q2',21,'1',24,100,NULL,NULL,'MWF 8:30-9:45',NULL,NULL,'2012-03-21 13:27:19'),(122,'2012Q2',22,'1',24,100,NULL,NULL,'MWF 2:40-3:55',NULL,NULL,'2012-03-21 13:27:19'),(123,'2012Q2',43,'1',3,100,NULL,NULL,'TTh 9:00-11:00',NULL,NULL,'2012-03-21 13:27:19'),(124,'2012Q2',51,'1',6,100,NULL,NULL,'MWF 2:40-3:55',NULL,NULL,'2012-03-21 13:27:19'),(125,'2012Q2',62,'1',4,100,NULL,NULL,'MWF 8:30-9:45',NULL,NULL,'2012-03-21 13:27:19');
/*!40000 ALTER TABLE `ModuleAssignments` ENABLE KEYS */;
UNLOCK TABLES;

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
  KEY `moduleID` (`moduleID`),
  CONSTRAINT `moduleofferings_ibfk_1` FOREIGN KEY (`term`) REFERENCES `Terms` (`term`),
  CONSTRAINT `moduleofferings_ibfk_2` FOREIGN KEY (`moduleID`) REFERENCES `Modules` (`moduleID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ModuleOfferings`
--

LOCK TABLES `ModuleOfferings` WRITE;
/*!40000 ALTER TABLE `ModuleOfferings` DISABLE KEYS */;
INSERT INTO `ModuleOfferings` VALUES ('2011Q4',1,'1','COMP','105','Intro to CS','Introduction to Computer Sci.',NULL,3,30,'Offered','Institutional','2011-09-10',NULL,NULL,NULL,'2012-03-21 01:03:51'),('2011Q4',21,'1','MATH','112','Calculus I','Calculus I',NULL,3,30,'Offered','Institutional','2011-09-10',NULL,NULL,NULL,'2012-03-21 01:03:51'),('2011Q4',21,'2','MATH','112','Calculus I','Calculus I',NULL,3,30,'Offered','Institutional','2011-09-10','2011-12-15',NULL,NULL,'2012-03-21 01:19:13'),('2011Q4',35,'1','ENGL','210','Brit. Lit.','British Lit: Austen\'s Novels',NULL,3,25,'Offered','Institutional','2011-09-10',NULL,NULL,NULL,'2012-03-21 01:03:51'),('2011Q4',60,'1','FREN','101','French I','French Lang. and Lit. I',NULL,3,18,'Offered','Institutional','2011-09-10',NULL,NULL,NULL,'2012-03-21 01:03:51'),('2011Q4',70,'1','WRIT','100','First-Year Sem.','First-Year Writing Seminar',NULL,3,15,'Offered','Institutional','2011-09-10',NULL,NULL,NULL,'2012-03-21 01:03:51'),('2011Q4',70,'2','WRIT','100','First-Year Sem.','First-Year Writing Seminar',NULL,3,15,'Offered','Institutional','2011-09-10','2011-12-15',NULL,NULL,'2012-03-21 01:20:15'),('2012Q1',3,'1','COMP','110','Intro Prog.','Introduction to Programming',NULL,3,30,'Offered','Institutional','2012-01-01',NULL,NULL,NULL,'2012-03-21 01:04:42'),('2012Q1',21,'1','MATH','112','Calculus I','Calculus I',NULL,3,30,'Offered','Institutional','2012-01-01',NULL,NULL,NULL,'2012-03-21 13:25:41'),('2012Q1',22,'1','MATH','112','Calculus I','Calculus I',NULL,3,30,'Offered','Institutional','2012-01-01',NULL,NULL,NULL,'2012-03-21 13:25:41'),('2012Q1',35,'1','ENGL','210','Brit. Lit.','British Lit: Austen\'s Novels',NULL,3,25,'Offered','Institutional','2012-01-01',NULL,NULL,NULL,'2012-03-21 01:05:16'),('2012Q1',40,'1','HIST','110','US History I','US History I',NULL,3,25,'Offered','Institutional','2012-01-01',NULL,NULL,NULL,'2012-03-21 01:05:16'),('2012Q1',42,'1','HIST','210','West Afr. Hist I','West African History I',NULL,3,25,'Offered','Institutional','2012-01-01',NULL,NULL,NULL,'2012-03-21 01:05:16'),('2012Q1',50,'1','ECON','105','Microeconomics','Intro. to Microeconomics',NULL,3,35,'Offered','Institutional','2012-01-01',NULL,NULL,NULL,'2012-03-21 01:05:16'),('2012Q1',61,'1','FREN','102','French II','French Lang. and Lit. II',NULL,3,18,'Offered','Institutional','2012-01-01',NULL,NULL,NULL,'2012-03-21 01:05:16'),('2012Q1',70,'1','WRIT','100','First-Year Sem.','First-Year Writing Seminar',NULL,3,15,'Offered','Institutional','2012-01-01',NULL,NULL,NULL,'2012-03-21 01:05:16'),('2012Q2',2,'1','COMP','107','Multimedia Prog','Multimedia Programming',NULL,3,30,'Offered','Institutional','2012-04-01',NULL,NULL,NULL,'2012-03-21 01:06:57'),('2012Q2',4,'1','COMP','210','Data Struct.','Data Structures',NULL,3,20,'Offered','Institutional','2012-04-01',NULL,NULL,NULL,'2012-03-21 01:06:57'),('2012Q2',21,'1','MATH','112','Calculus I','Calculus I',NULL,3,30,'Offered','Institutional','2012-04-01',NULL,NULL,NULL,'2012-03-21 01:06:57'),('2012Q2',22,'1','MATH','113','Calculus II','Calculus II',NULL,3,30,'Offered','Institutional','2012-04-01',NULL,NULL,NULL,'2012-03-21 01:06:57'),('2012Q2',43,'1','HIST','211','West Afr. Hist II','West African History II',NULL,3,25,'Offered','Institutional','2012-04-01',NULL,NULL,NULL,'2012-03-21 01:06:57'),('2012Q2',51,'1','ECON','106','Macroeconomics','Intro. to Macroeconomics',NULL,3,35,'Offered','Institutional','2012-04-01',NULL,NULL,NULL,'2012-03-21 01:06:57'),('2012Q2',62,'1','FREN','103','French III','French Lang. and Lit. III',NULL,3,18,'Offered','Institutional','2012-04-01',NULL,NULL,NULL,'2012-03-21 01:06:57');
/*!40000 ALTER TABLE `ModuleOfferings` ENABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=71 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Modules`
--

LOCK TABLES `Modules` WRITE;
/*!40000 ALTER TABLE `Modules` DISABLE KEYS */;
INSERT INTO `Modules` VALUES (1,'COMP','105','Active','Intro to CS','Introduction to Computer Sci.',NULL,3,NULL,'Institutional','2000-01-01',NULL,'2012-03-19 23:58:38'),(2,'COMP','107','Active','Multimedia Prog','Multimedia Programming',NULL,3,NULL,'Institutional','2000-01-01',NULL,'2012-03-19 23:58:38'),(3,'COMP','110','Active','Intro Prog.','Introduction to Programming',NULL,3,NULL,'Institutional','2000-01-01',NULL,'2012-03-19 23:58:38'),(4,'COMP','210','Active','Data Struct.','Data Structures',NULL,3,NULL,'Institutional','2000-01-01',NULL,'2012-03-19 23:58:38'),(5,'COMP','215','Active','Algorithms','Computer Algorithms',NULL,3,NULL,'Institutional','2000-01-01',NULL,'2012-03-19 23:58:38'),(6,'COMP','230','Active','Comp. Org.','Computer Organization',NULL,3,NULL,'Institutional','2000-01-01',NULL,'2012-03-19 23:58:38'),(7,'COMP','300','Active','Automata','Automata, Languages, and Computabili\nty',NULL,3,NULL,'Institutional','2000-01-01',NULL,'2012-03-19 23:58:38'),(8,'COMP','315','Active','Prog. Langs','Programming Language Concepts',NULL,3,NULL,'Institutional','2000-01-01',NULL,'2012-03-19 23:58:38'),(9,'COMP','420','Active','Op. Sys.','Operating Systems',NULL,3,NULL,'Institutional','2000-01-01',NULL,'2012-03-19 23:58:38'),(10,'COMP','483','Active','Cryptography','Cryptography',NULL,3,NULL,'Institutional','2000-01-01',NULL,'2012-03-19 23:58:38'),(11,'COMP','487','Active','Softw. Eng.','Software Engineering',NULL,3,NULL,'Institutional','2000-01-01',NULL,'2012-03-19 23:58:38'),(20,'MATH','105','Active','Quant. Reasoning','Quantitative Reasoning',NULL,3,NULL,'Institutional','2000-01-01',NULL,'2012-03-19 23:58:38'),(21,'MATH','112','Active','Calculus I','Calculus I',NULL,3,NULL,'Institutional','2000-01-01',NULL,'2012-03-19 23:58:38'),(22,'MATH','113','Active','Calculus II','Calculus II',NULL,3,NULL,'Institutional','2000-01-01',NULL,'2012-03-19 23:58:38'),(23,'MATH','213','Active','Calculus III','Calculus III',NULL,3,NULL,'Institutional','2000-01-01',NULL,'2012-03-19 23:58:38'),(24,'MATH','214','Active','Linear Algebra','Linear Algebra',NULL,3,NULL,'Institutional','2000-01-01',NULL,'2012-03-19 23:58:38'),(25,'MATH','300','Active','Abstract Algebra I','Abstract Algebra I',NULL,3,NULL,'Institutional','2000-01-01',NULL,'2012-03-19 23:58:38'),(26,'MATH','310','Active','Real Analysis I','Real Analysis I',NULL,3,NULL,'Institutional','2000-01-01',NULL,'2012-03-19 23:58:38'),(27,'MATH','350','Active','Probability','Probability',NULL,3,NULL,'Institutional','2000-01-01',NULL,'2012-03-19 23:58:38'),(28,'MATH','360','Active','Statistics','Statistics',NULL,3,NULL,'Institutional','2000-01-01',NULL,'2012-03-19 23:58:38'),(30,'MATH','400','Active','Abstract Algebra II','Abstract Algebra II',NULL,3,NULL,'Institutional','2000-01-01',NULL,'2012-03-19 23:58:38'),(31,'MATH','410','Active','Real Analysis II','Real Analysis II',NULL,3,NULL,'Institutional','2000-01-01',NULL,'2012-03-19 23:58:38'),(35,'ENGL','210','Active','Brit. Lit.','British Lit: Austen\'s Novels',NULL,3,NULL,'Institutional','2000-01-01',NULL,'2012-03-19 23:58:38'),(40,'HIST','110','Active','US History I','US History I',NULL,3,NULL,'Institutional','2000-01-01',NULL,'2012-03-19 23:58:38'),(41,'HIST','111','Active','US History II','US History II',NULL,3,NULL,'Institutional','2000-01-01',NULL,'2012-03-19 23:58:38'),(42,'HIST','210','Active','West Afr. Hist I','West African History I',NULL,3,NULL,'Institutional','2000-01-01',NULL,'2012-03-19 23:58:38'),(43,'HIST','211','Active','West Afr. Hist II','West African History II',NULL,3,NULL,'Institutional','2000-01-01',NULL,'2012-03-19 23:58:38'),(50,'ECON','105','Active','Microeconomics','Intro. to Microeconomics',NULL,3,NULL,'Institutional','2000-01-01',NULL,'2012-03-19 23:58:38'),(51,'ECON','106','Active','Macroeconomics','Intro. to Macroeconomics',NULL,3,NULL,'Institutional','2000-01-01',NULL,'2012-03-19 23:58:38'),(60,'FREN','101','Active','French I','French Lang. and Lit. I',NULL,3,NULL,'Institutional','2000-01-01',NULL,'2012-03-19 23:58:38'),(61,'FREN','102','Active','French II','French Lang. and Lit. II',NULL,3,NULL,'Institutional','2000-01-01',NULL,'2012-03-19 23:58:38'),(62,'FREN','103','Active','French III','French Lang. and Lit. III',NULL,3,NULL,'Institutional','2000-01-01',NULL,'2012-03-19 23:58:38'),(70,'WRIT','100','Active','First-Year Sem.','First-Year Writing Seminar',NULL,3,NULL,'Institutional','2000-01-01',NULL,'2012-03-19 23:58:38');
/*!40000 ALTER TABLE `Modules` ENABLE KEYS */;
UNLOCK TABLES;

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
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Person`
--

LOCK TABLES `Person` WRITE;
/*!40000 ALTER TABLE `Person` DISABLE KEYS */;
INSERT INTO `Person` VALUES (1,'Dr.','Jill','Alyce','Brady',NULL,'Alyce','Alyce','Faulstich','F','abrady@kzoo.edu','123-456-7890','1961-06-12',NULL,'US',NULL,NULL,'F','2012-03-22 17:10:42'),(2,'Mr.','Aly','Harry','Patrick',NULL,NULL,'Aly','Lisa','M','njala.reg@yahoo.com','23276615200','1959-03-14',NULL,'Sierra Leone',NULL,NULL,'F','2012-03-22 17:15:13'),(3,'Mrs.','Beth','Anne','Stork',NULL,'Beth Anne','Beth Anne','Faulstich','F','bastork@pretend.com','222-222-2222','1963-01-27',NULL,'US',NULL,NULL,'F','2012-03-22 17:10:51'),(4,'Mr.','Paul','Stuart','Faulstich',NULL,NULL,'Paul',NULL,'M','psf@pretend.com','333-333-3333','1968-11-02',NULL,'US',NULL,NULL,'F','2012-03-22 17:15:13'),(5,'Sir','Walter',NULL,'Elliot',NULL,'Sir Walter','Sir Walter',NULL,'M','sir_walter@persuasion.com','333-333-3333','1968-11-02',NULL,'UK',NULL,NULL,'F','2012-03-22 17:15:13'),(6,'Mrs.','Mary',NULL,'Brady',NULL,NULL,'Mary','Filardi','F','mbrady@pretend.com','444-444-4444','1925-08-04',NULL,'US',NULL,NULL,'F','2012-03-22 17:10:35'),(7,'Mrs.','Mary',NULL,'Musgrove',NULL,NULL,'Mary','Elliot','F','mm@persuasion@com','555-555-5555',NULL,NULL,'UK',NULL,NULL,'F','2012-03-22 17:10:35'),(8,NULL,'Charles',NULL,'Brown',NULL,'Charlie','Charlie',NULL,'M','cb@peanutscomicstrip.com',NULL,NULL,NULL,'US',NULL,NULL,'F','2012-03-22 17:15:13'),(9,NULL,'Sally',NULL,'Brown',NULL,NULL,'Sally',NULL,'F','sb@peanutscomicstrip.com',NULL,NULL,NULL,'US',NULL,NULL,'F','2012-03-22 17:11:00'),(10,NULL,'Linus',NULL,'van Pelt',NULL,NULL,'Linus',NULL,'M','lvp2@peanutscomicstrip.com',NULL,NULL,NULL,'US',NULL,NULL,'F','2012-03-22 17:15:13'),(11,NULL,'Lucy',NULL,'van Pelt',NULL,NULL,'Lucy',NULL,'F','lvp1@peanutscomicstrip.com',NULL,NULL,NULL,'US',NULL,NULL,'F','2012-03-22 17:11:06'),(12,'','Elizabeth','','Bennet','',NULL,'Elizabeth','','F','eb@prideandprejudice.com','',NULL,NULL,'UK','','','F','2012-03-22 18:18:54'),(13,NULL,'Mary',NULL,'Bennet',NULL,NULL,'Mary',NULL,'F','mb@prideandprejudice.com',NULL,NULL,NULL,'UK',NULL,NULL,'T','2012-03-22 17:10:35'),(14,NULL,'Katherine',NULL,'Bennet',NULL,'Kitty','Kitty',NULL,'F','kb@prideandprejudice.com',NULL,NULL,NULL,'UK',NULL,NULL,'F','2012-03-22 17:11:36'),(15,NULL,'Fitzwilliam',NULL,'Darcy',NULL,NULL,'Fitzwilliam',NULL,'M','fd@prideandprejudice.com',NULL,NULL,NULL,'UK',NULL,NULL,'F','2012-03-22 17:15:13'),(16,NULL,'Elinor',NULL,'Dashwood',NULL,NULL,'Elinor',NULL,'F','ed@senseandsensibility.com',NULL,NULL,NULL,'UK',NULL,NULL,'F','2012-03-22 17:11:43'),(17,NULL,'Edward',NULL,'Ferrars',NULL,NULL,'Edward',NULL,'M','ef@senseandsensibility.com',NULL,NULL,NULL,'UK',NULL,NULL,'F','2012-03-22 17:15:13'),(18,NULL,'Anne',NULL,'Elliot',NULL,NULL,'Anne',NULL,'F','ae@persuasion.com',NULL,NULL,NULL,'UK',NULL,NULL,'F','2012-03-22 17:11:50'),(19,NULL,'Frederick',NULL,'Wentworth',NULL,NULL,'Frederick',NULL,'M','fw@persuasion.com',NULL,NULL,NULL,'UK',NULL,NULL,'F','2012-03-22 17:15:13'),(20,NULL,'Emma',NULL,'Woodhouse',NULL,NULL,'Emma',NULL,'F','ew@emma.com',NULL,NULL,NULL,'UK',NULL,NULL,'F','2012-03-22 17:11:55'),(21,NULL,'George',NULL,'Knightly',NULL,NULL,'George',NULL,'M','gk@emma.com',NULL,NULL,NULL,'UK',NULL,NULL,'F','2012-03-22 17:15:13'),(22,NULL,'Frances',NULL,'Price',NULL,'Fanny','Fanny',NULL,'F','fp@mansfieldpark.com',NULL,NULL,NULL,'UK',NULL,NULL,'F','2012-03-22 17:12:06'),(23,NULL,'Edmund',NULL,'Bertram',NULL,NULL,'Edmund',NULL,'M','eb@mansfieldpark.com',NULL,NULL,NULL,'UK',NULL,NULL,'F','2012-03-22 17:15:13'),(24,'Mr.','Henry',NULL,'Weston',NULL,NULL,'Henry',NULL,'M','gk@emma.com','555-555-5555','1957-04-23',NULL,'UK',NULL,NULL,'F','2012-03-22 17:15:13');
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
  `staffID` int(11) NOT NULL,
  `officeNumber` varchar(6) DEFAULT NULL,
  `officeBuilding` varchar(20) DEFAULT NULL,
  `updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`staffID`),
  CONSTRAINT `staff_ibfk_1` FOREIGN KEY (`staffID`) REFERENCES `Person` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Staff`
--

LOCK TABLES `Staff` WRITE;
/*!40000 ALTER TABLE `Staff` DISABLE KEYS */;
INSERT INTO `Staff` VALUES (1,'203G','Olds/Upton Hall','2012-03-19 21:21:23'),(2,'1','Secretariat','2012-03-19 21:21:23'),(3,'123','Greenville','2012-03-19 21:21:23'),(4,'123','Olds/Upton Hall','2012-03-19 21:21:23'),(5,NULL,NULL,'2012-03-19 21:21:23'),(6,'123','OU','2012-03-20 17:23:00'),(24,'203B','Olds/Upton Hall','2012-03-21 13:09:07');
/*!40000 ALTER TABLE `Staff` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `StaffContract`
--

DROP TABLE IF EXISTS `StaffContract`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `StaffContract` (
  `pk_id` int(11) NOT NULL AUTO_INCREMENT,
  `staffID` int(11) NOT NULL,
  `school` varchar(30) DEFAULT NULL,
  `department` varchar(30) DEFAULT NULL,
  `jobFunction` enum('Teaching','Administrative','Service','Other') NOT NULL DEFAULT 'Other',
  `jobTitle` varchar(40) NOT NULL,
  `status` enum('','Full-time','Part-time','On Leave','Ended') DEFAULT NULL,
  `startDate` date NOT NULL,
  `endDate` date DEFAULT NULL,
  `updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`pk_id`),
  KEY `staffID` (`staffID`),
  CONSTRAINT `staffcontract_ibfk_1` FOREIGN KEY (`staffID`) REFERENCES `Person` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `StaffContract`
--

LOCK TABLES `StaffContract` WRITE;
/*!40000 ALTER TABLE `StaffContract` DISABLE KEYS */;
INSERT INTO `StaffContract` VALUES (1,1,NULL,'Math/CS','Teaching','Asst. Prof. of Computer Science','Ended','1994-09-26','2001-03-15','2012-03-19 21:22:25'),(2,1,NULL,'Math/CS','Teaching','Assoc. Prof. of Computer Science','Ended','2001-03-15','2010-03-15','2012-03-19 21:22:25'),(3,1,NULL,'Math/CS','Teaching','Professor of Computer Science','Full-time','2010-03-15',NULL,'2012-03-19 21:22:25'),(4,2,NULL,'Secretariat','Administrative','Asst. Registrar','Full-time','2007-09-15',NULL,'2012-03-19 21:22:25'),(5,3,NULL,'History','Teaching','Teacher','Full-time','2001-08-15',NULL,'2012-03-19 21:22:25'),(6,4,NULL,'Lang. and Lit.','Teaching','Teacher','Full-time','2001-08-15',NULL,'2012-03-19 21:22:25'),(7,5,NULL,'Gentry','Other','Narcissistic Baronet','Ended','2000-08-15','2010-08-15','2012-03-19 21:22:25'),(8,6,NULL,'Economics','Teaching','Teacher','Full-time','2001-08-15',NULL,'2012-03-19 21:22:25'),(9,24,NULL,'Math/CS','Teaching','Asst. Prof. of Mathematics','Full-time','2008-09-15',NULL,'2012-03-21 13:10:31');
/*!40000 ALTER TABLE `StaffContract` ENABLE KEYS */;
UNLOCK TABLES;

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
INSERT INTO `Student` VALUES (8,1,'Charles Brown','MU 248','English','2012-03-19 21:22:52'),(9,NULL,NULL,'Hicks 1234','English','2012-03-19 21:22:52'),(11,NULL,NULL,'Hicks 789','Bossy English','2012-03-19 21:22:52'),(12,NULL,NULL,'Hicks 1212','English','2012-03-19 21:22:52'),(13,NULL,NULL,'Hicks 1313','English','2012-03-19 21:22:52'),(14,NULL,NULL,'Hicks 1414','English','2012-03-19 21:22:52'),(15,NULL,NULL,'Hicks 1515','English','2012-03-19 21:22:52'),(16,NULL,NULL,'Hicks 1616','English','2012-03-19 21:22:52'),(17,NULL,NULL,'Hicks 1717','English','2012-03-19 21:22:52'),(18,NULL,NULL,'Hicks 1818','English','2012-03-19 21:22:52'),(19,NULL,NULL,'Hicks 1919','English','2012-03-19 21:22:52'),(20,NULL,NULL,'Hicks 2020','English','2012-03-19 21:22:52'),(21,NULL,NULL,'Hicks 2121','English','2012-03-19 21:22:52'),(22,NULL,NULL,'Hicks 2222','English','2012-03-19 21:22:52'),(23,NULL,NULL,'Hicks 2323','English','2012-03-19 21:22:52');
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
INSERT INTO `StudentAcadProgram` VALUES (1,8,1,'Bachelor of Arts','B.A.',NULL,'Active','2011-06-01','2011-09-01','2015-06-15',NULL,NULL,'1st Yr','2012-03-21 18:58:34'),(2,11,1,'Bachelor of Arts','B.A.',NULL,'Active','2011-06-01','2011-09-01','2015-06-15',NULL,NULL,'1st Yr','2012-03-21 18:58:34'),(3,12,1,'Bachelor of Arts','B.A.',NULL,'Active','2008-06-01','2008-09-01','2012-06-15',NULL,NULL,'4th Yr','2012-03-21 18:58:34'),(4,13,1,'Bachelor of Arts','B.A.',NULL,'Active','2009-06-01','2009-09-01','2013-06-15',NULL,NULL,'3rd Yr','2012-03-21 18:58:34'),(5,14,1,'Bachelor of Arts','B.A.',NULL,'Withdrawn','2010-06-01','2010-09-01','2014-06-15',NULL,'2012-01-31','2nd Yr','2012-03-21 18:58:34'),(6,15,1,'Bachelor of Arts','B.A.',NULL,'Active','2008-06-01','2008-09-01','2012-06-15',NULL,NULL,'4th Yr','2012-03-21 18:58:34'),(7,16,1,'Bachelor of Arts','B.A.',NULL,'Active','2008-06-01','2008-09-01','2012-06-15',NULL,NULL,'4th Yr','2012-03-21 18:58:34'),(8,17,1,'Bachelor of Arts','B.A.',NULL,'Active','2010-06-01','2010-09-01','2014-06-15',NULL,NULL,'2nd Yr','2012-03-21 18:58:34'),(9,18,1,'Bachelor of Arts','B.A.',NULL,'Active','2011-06-01','2011-09-01','2015-06-15',NULL,NULL,'1st Yr','2012-03-21 18:58:34'),(10,19,1,'Bachelor of Arts','B.A.',NULL,'Active','2011-06-01','2011-09-01','2015-06-15',NULL,NULL,'1st Yr','2012-03-21 18:58:34'),(11,20,1,'Bachelor of Arts','B.A.',NULL,'Active','2009-06-01','2009-09-01','2013-06-15',NULL,NULL,'3rd Yr','2012-03-21 18:58:34'),(12,21,1,'Bachelor of Arts','B.A.',NULL,'Active','2011-06-01','2011-09-01','2015-06-15',NULL,NULL,'1st Yr','2012-03-21 18:58:34'),(13,22,1,'Bachelor of Arts','B.A.',NULL,'Active','2010-06-01','2010-09-01','2014-06-15',NULL,NULL,'2nd Yr','2012-03-21 18:58:34'),(14,23,1,'Bachelor of Arts','B.A.',NULL,'Active','2009-06-01','2009-09-01','2013-06-15',NULL,NULL,'3rd Yr','2012-03-21 18:58:34'),(15,12,20,'Literature','Major',3,'Active','2010-06-01','2010-06-01','2012-06-15','2012-03-21','2012-03-21','4th Yr','2012-03-21 19:00:43'),(16,16,20,'Literature','Major',7,'Active','2010-06-01','2010-06-01','2012-06-15',NULL,NULL,'4th Yr','2012-03-21 19:00:43'),(17,16,11,'Computer Science','Minor',7,'Active','2010-06-01','2010-06-01','2012-06-15',NULL,NULL,'4th Yr','2012-03-21 19:00:43'),(18,15,10,'Computer Science','Major',6,'Completed','2010-06-01','2010-06-01','2012-06-15','2012-03-21','2012-03-21','4th Yr','2012-03-21 19:00:43'),(19,15,20,'Literature','Major',6,'Completed','2010-06-01','2010-06-01','2012-06-15','2012-03-21','2012-03-21','4th Yr','2012-03-21 19:00:43'),(20,15,31,'Mathematics','Minor',6,'Completed','2010-06-01','2010-06-01','2012-06-15','2012-03-21','2012-03-21','4th Yr','2012-03-21 19:00:43'),(21,13,30,'Mathematics','Major',4,'Active','2011-06-01','2011-06-01','2013-06-15','0000-00-00','0000-00-00','3rd Yr','2012-03-21 19:00:43'),(22,20,10,'Computer Science','Major',11,'Active','2011-06-01','2011-06-01','2013-06-15',NULL,NULL,'3rd Yr','2012-03-21 19:00:43'),(23,20,21,'Literature','Minor',11,'Active','2011-06-01','2011-06-01','2013-06-15',NULL,NULL,'3rd Yr','2012-03-21 19:00:43'),(24,23,20,'Literature','Major',14,'Active','2011-06-01','2011-06-01','2013-06-15',NULL,NULL,'3rd Yr','2012-03-21 19:00:43');
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
INSERT INTO `TermStanding` VALUES (4,8,'2011Q4','PROBATION',3,3,2.000,3,3,2.000,NULL,'2012-03-22 03:50:04'),(5,8,'2012Q1','GOOD',3.6,NULL,NULL,6.6,3,2.000,NULL,'2012-03-22 03:50:04'),(6,11,'2012Q1','GOOD',3.8,NULL,NULL,NULL,NULL,NULL,NULL,'2012-03-22 03:50:04');
/*!40000 ALTER TABLE `TermStanding` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Terms`
--

DROP TABLE IF EXISTS `Terms`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Terms` (
  `term` varchar(10) NOT NULL,
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
INSERT INTO `Terms` VALUES ('2008Fall','2008-09','2008-09-10','2008-09-15','2008-12-15','2012-03-19 21:43:04'),('2009Fall','2009-10','2009-09-10','2009-09-15','2009-12-15','2012-03-19 21:43:04'),('2009Spring','2008-09','2009-04-01','2009-04-05','2009-06-20','2012-03-19 21:43:04'),('2009Winter','2008-09','2009-01-01','2009-01-05','2009-03-20','2012-03-19 21:43:04'),('2010Q1','2009-10','2010-01-01','2010-01-05','2010-03-20','2012-03-19 21:43:04'),('2010Q2','2009-10','2010-04-01','2010-04-05','2010-06-20','2012-03-19 21:43:04'),('2010Q4','2010-11','2010-09-10','2010-09-15','2010-12-15','2012-03-19 21:43:04'),('2011Q1','2010-11','2011-01-01','2011-01-05','2011-03-20','2012-03-19 21:43:04'),('2011Q2','2010-11','2011-04-01','2011-04-05','2011-06-20','2012-03-19 21:43:04'),('2011Q4','2011-12','2011-09-10','2011-09-15','2011-12-15','2012-03-19 21:43:04'),('2012Q1','2011-12','2012-01-01','2012-01-05','2012-03-20','2012-03-19 21:43:04'),('2012Q2','2011-12','2012-04-01','2012-04-05','2012-06-20','2012-03-19 21:43:04'),('2012Q4','2012-13','2012-09-10','2012-09-15','2012-12-15','2012-03-19 21:43:04');
/*!40000 ALTER TABLE `Terms` ENABLE KEYS */;
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
INSERT INTO `TestScores` VALUES (3,8,'2011-06-15','Entrance Exam',NULL,'West Africa Exam Agency',120.5,80,NULL,NULL),(4,11,'2011-09-15','Entrance Exam',NULL,'West Africa Exam Agency',142.5,95,NULL,NULL),(5,11,'2011-09-15','AP Calculus BC','AP','West Africa Exam Agency',5,99,'MATH 113',NULL);
/*!40000 ALTER TABLE `TestScores` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(100) NOT NULL,
  `password` varchar(40) NOT NULL,
  `first_name` varchar(100) DEFAULT NULL,
  `last_name` varchar(100) DEFAULT NULL,
  `email` varchar(150) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'abrady','abrady','Alyce','Brady','abrady@kzoo.edu'),(2,'cdelaney','cdelaney','Casey','Delaney','cdelaney@kzoo.edu'),(3,'lpotts','lpottslpottslp','Lanny','Potts','lpotts@kzoo.edu');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

