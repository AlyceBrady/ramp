-- Define the RAMP Demo table schemas and populate with sample data.

--
-- This file contains SQL code to create administrative tables used by
-- Ramp and sample Ramp tables ('albums' and 'places') that have corresponding
-- table settings in the settings/demo directory.
--

--
-- Create Database: `ramp_demo`
--

DROP DATABASE IF EXISTS `ramp_demo`;
CREATE DATABASE `ramp_demo`;

-- Define what "guest" users (those who are not logged in) are
-- authorized to do, create a RAMP administrator role, and define what
-- administrative users with that role may do.

SOURCE createRampDemoUsersAuths.sql;

-- Create and populate the built-in tables used for record locking.

SOURCE createRampDemoLocks.sql;

--
-- Table: `albums`
--

DROP TABLE IF EXISTS `albums`;
CREATE TABLE `albums` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `artist` varchar(100) NOT NULL DEFAULT 'The Beatles',
  `title` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
);

LOCK TABLES `albums` WRITE;
INSERT INTO `albums` VALUES
(1,'Paolo Nutine','Sunny Side Up')
,(2,'Florence + The Machine','Lungs')
,(3,'Massive Attack','Heligoland')
,(4,'Andre Rieu','Forever Vienna')
,(5,'Sade','Soldier of Love')
,(9,'The Beatles','Rubber Soul')
,(12,'The Beatles','Abbey Road')
,(13,'Simon and Garfunkel','Parsley, Sage, Rosemary and Thyme')
,(14,'Genesis','We Can\'t Dance')
,(15,'R.E.M.','reckoning')
,(16,'The Beatles','Help!')
,(17,'Beatles','Sgt Pepper\'s Lonely Hearts Club Band')
,(28,'The Beatles','Let It Be')
,(29,'The Beatles','Please, Please Me')
,(32,'The Beatles','Yellow Submarine')
;
UNLOCK TABLES;

--
-- Table: `places`
--   (The concept for this table and some of the places come from
--    _Zend_Framework_in_Action_ by Allen, Lo, and Brown.)
--

DROP TABLE IF EXISTS `places`;
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
);

LOCK TABLES `places` WRITE;
INSERT INTO `places` VALUES
(1, '2007-02-14 00:00:00', '2007-02-14 00:00:00', 'London Zoo',
    'Regent\'s Park', NULL, NULL, 'London', '', 'NW1 4RY', 'UK')
, (2, '2007-02-20 00:00:00', '2007-02-20 00:00:00', 'Alton Towers',
    'Regent\'s Park', NULL, NULL, 'Alton', 'Staffordshire', 'ST10 4DB', 'UK')
, (3, '2007-02-16 00:00:00', '2007-02-16 00:00:00', 'Coughton Court', '',
    NULL, NULL, 'Alcester', 'Warwickshire', 'B49 5JA', 'UK')
, (4, '2012-02-18 15:34:00', '2012-02-21 15:34:00', 'Binder Park Zoo',
    'Exit 100', NULL, NULL, 'Battle Creek', 'MI', '', 'US')
, (5, '2012-02-18 15:36:00',  '2012-02-18 15:36:00', 'Empire State Building',
    'Midtown Manhattan', NULL, NULL, 'New York', 'NY', '', 'US')
, (6, '2012-02-19 01:27:00', '2012-02-19 01:27:00', 'K College',
    '1200 Academy St', NULL, NULL, 'Kalamazoo', '', '', 'US')
, (7, '2010-02-19 15:23:00', '2010-02-19 15:23:00', 'Bowdoin College',
    'Park Row', NULL, NULL, 'Brunswick', 'ME', '', 'US')
, (8, '2010-02-19 15:23:00', '2010-02-19 15:23:00', 'RPI', '8th Street',
    NULL, NULL, 'Troy', 'NY', '', 'US')
, (9, '2010-02-19 15:23:00', '2010-02-19 15:23:00', 'Bronx Zoo',
    '2300 Southern Blvd', NULL, NULL, 'Bronx', 'NY', '', 'US')
, (10, '2010-02-19 15:23:00', '2010-02-19 15:23:00',
    'National Air & Space Museum', 'Independence Ave.',
    NULL, NULL, 'Washington', 'DC', '', 'US')
, (11, '2010-02-19 15:23:00', '2010-02-19 15:23:00', 'Tour Eiffel',
    'Champ de Mars', NULL, NULL, 'Paris', 'France', '', 'US')
, (14, '2012-02-20 10:09:00', '2012-02-20 10:09:00', 'WMU', 'Michigan Ave',
    NULL, NULL, 'Kalamazoo', NULL, NULL, 'US')
, (16, '2012-02-20 10:09:00', '2012-02-20 10:09:00', 'Fundy National Park',
    'Test Address', NULL, NULL, 'Brunswick', NULL, NULL, 'CA')
, (17, '2012-02-20 10:09:00', '2012-02-20 10:09:00', 'Colonial Williamsburg',
    'Duke of Gloucester St', NULL, NULL, 'Williamsburg', 'VA', NULL, 'US')
, (18, '2012-02-20 10:09:00', '2012-02-20 10:09:00', 'George St Playhouse',
    '9 Livingstone Ave', NULL, NULL, 'New Brunswick', 'NJ', NULL, 'US')
, (20, '2012-02-19 01:27:00', '2012-02-21 15:34:00', 'Mount Tom State Park',
    'Cherry Street', NULL, NULL, 'Washington', 'CT', NULL, 'US')
, (21, '2012-02-18 15:34:00', '2012-02-20 10:09:00', 'Museum London',
    '421 Ridout Street North', NULL, NULL, 'London', "Ontario", "", 'CA')
, (22, '2010-02-19 15:23:00', '2010-02-19 15:23:00', 'Rutgers Univ.',
    'College Ave', NULL, NULL, 'New Brunswick', 'NJ', NULL, 'US')
, (23, '2012-02-20 10:09:00', '2012-02-21 15:34:00', 'Tower of London',
    '', NULL, NULL, 'London', NULL, NULL, 'UK')
;
UNLOCK TABLES;

--
-- Table: `reviews`
--   (The concept for this table and some of the reviews come from
--    _Zend_Framework_in_Action_ by Allen, Lo, and Brown.)
--

DROP TABLE IF EXISTS `reviews`;
CREATE TABLE `reviews` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date_created` datetime NOT NULL,
  `date_updated` datetime,
  `place_id` int(11) NOT NULL,
  `user_name` varchar(50) NOT NULL,
  `body` mediumtext NOT NULL,
  `rating` int(11) DEFAULT NULL,
  `helpful_yes` int(11) NOT NULL DEFAULT '0',
  `helpful_total` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
);

LOCK TABLES `reviews` WRITE;
INSERT INTO `reviews` (place_id, user_name, body, rating, date_created) VALUES
(1, 'rob',
    'The facilities here are really good.  All the family enjoyed it.', 4,
    '2007-02-14 00:00:00')
, (1, 'rob', 'Good day out, but not so many big animals now.', 2,
    '2007-02-14 00:00:00')
, (1, 'rob',
    'Excellent food in the cafeteria.  Even my 2 year old ate her lunch.',
    4, '2007-02-14 00:00:00')
;
UNLOCK TABLES;

--
-- Table: `reviewers`
--   (The concept for this table and the first reviewer comes from
--    the 'users' table in _Zend_Framework_in_Action_ by Allen, Lo, and Brown.)
--

DROP TABLE IF EXISTS `reviewers`;
CREATE TABLE `reviewers` (
  `id` int NOT NULL AUTO_INCREMENT,
  `date_created` datetime,
  `date_updated` datetime,
  `username` varchar(100) NOT NULL,
  `password` varchar(40) NOT NULL,
  `first_name` varchar(100),
  `last_name` varchar(100),
  `email` varchar(150) NOT NULL,
  `town` varchar(100),
  `country` varchar(100),
  `date_of_birth` datetime,
  `sex` char(1),
  `postcode` varchar(30),
  PRIMARY KEY (`id`)
);

LOCK TABLES `reviewers` WRITE;
INSERT INTO `reviewers`
(first_name, last_name, username, password, email, town, country) VALUES
('Rob', 'Allen', 'rob', 'rob', 'rob@akrabat.com', 'London', 'UK')
, ('Alyce', 'Brady', 'abrady', 'alyce', 'abrady@kzoo.edu', 'Kalamazoo', 'US')
;
UNLOCK TABLES;

