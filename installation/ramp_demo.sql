--
-- Current Database: `ramp_demo`
--

CREATE DATABASE /*!32312 IF NOT EXISTS*/ `ramp_demo` /*!40100 DEFAULT CHARACTER SET latin1 */;

USE `ramp_demo`;

--
-- Table structure for table `ramp_auth_users`
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

--
-- Dumping data for table `ramp_auth_users`
--

LOCK TABLES `ramp_auth_users` WRITE;
/*!40000 ALTER TABLE `ramp_auth_users` DISABLE KEYS */;
INSERT INTO `ramp_auth_users` VALUES (1,'guest','guest','guest', 'Guest','Guest','abrady@kzoo.edu');
/*!40000 ALTER TABLE `ramp_auth_users` ENABLE KEYS */;
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
