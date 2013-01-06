--
-- Tables for information about people generally (names, demographic
-- and contact information, etc) and about staff members more
-- specifically (job titles, contract information, etc.)
--


DROP TRIGGER IF EXISTS prefName_insert;
DROP TRIGGER IF EXISTS prefName_update;
DROP TABLE IF EXISTS Person;

CREATE TABLE Person (
    id INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
    title VARCHAR ( 5 ),
    firstname VARCHAR ( 30 ) NOT NULL,
    middlename VARCHAR ( 30 ),
    lastname VARCHAR ( 40 ) NOT NULL,
    suffix VARCHAR ( 10 ),
    specifiedPrefFName VARCHAR ( 30 ),
    prefFirstName VARCHAR ( 30 ),
    previousName VARCHAR ( 40 ),
    gender ENUM('Unknown', 'M', 'F') NOT NULL DEFAULT 'Unknown',
    prefEmail VARCHAR ( 30 ),
    prefPhone VARCHAR ( 20 ),
    birthDate DATE NULL,
    deceasedDate DATE NULL,
    citizenship VARCHAR ( 30 ),
    ethnicGroup VARCHAR ( 30 ),
    ssn VARCHAR (9),
    privacy ENUM('F', 'T') NOT NULL DEFAULT 'F',
    updated TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP
);

DELIMITER |
CREATE TRIGGER prefName_insert BEFORE INSERT ON Person
  FOR EACH ROW BEGIN
    SET NEW.prefFirstName = IF (NEW.specifiedPrefFName IS NULL, NEW.firstName,
                                NEW.specifiedPrefFName);
  END;
|
CREATE TRIGGER prefName_update BEFORE UPDATE ON Person
  FOR EACH ROW BEGIN
    SET NEW.prefFirstName = IF (NEW.specifiedPrefFName IS NULL, NEW.firstName,
                                NEW.specifiedPrefFName);
  END;
|
DELIMITER ;

LOCK TABLES `Person` WRITE;
/*!40000 ALTER TABLE `Person` DISABLE KEYS */;
INSERT INTO `Person`
(title, firstname, middlename, lastname, specifiedPrefFName, gender,
    prefEmail, prefPhone, birthDate, citizenship) VALUES
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
,('Mr.','Patrick',NULL,'Unknown',NULL,'M',NULL,NULL,NULL,'Sierra Leone')
;
/*!40000 ALTER TABLE `Person` ENABLE KEYS */;
UNLOCK TABLES;

Insert into `Person` (id, firstname, lastname, gender) VALUES
(16123, 'Daniel', 'Sesay', 'M')
,(16124, 'Rosie', 'Chaytor', 'F')
,(16125, 'Idrissa', 'Manseray', 'M')
,(16126, 'Festus', 'Brown', 'M')
,(16127, 'Mohammed', 'Bangura', 'M')
,(16128, 'Idrissa', 'Manseray', 'M')
,(16129, 'Edmund', 'Songo', 'M')
,(16130, 'Henry', 'Kamara', 'M')
,(16131, 'Joseph', 'Brown', 'M')
,(16132, 'Ibrahim', 'Songu', 'M')
,(16133, 'Theresa', 'Bangura', 'M')
,(16134, 'Henry', 'Foday', 'M')
,(16135, 'Joseph', 'Jalloh', 'M')
,(16136, 'Allieu', 'Chaytor', 'M')
,(16137, 'Thomas', 'Sheriff', 'M')
,(16138, 'Anthony', 'Noni', 'M')
,(16139, 'Memunatu', 'Tollay', 'M')
,(16140, 'Edwin', 'Faulkner', 'M')
;

DROP TABLE IF EXISTS Staff;

CREATE TABLE Staff (
    pk_id INT NOT NULL PRIMARY KEY,
    staffPF INT,
    campus varchar(20) DEFAULT NULL,
    officeNumber VARCHAR ( 6 ),
    officeBuilding VARCHAR ( 20 ),
    updated TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (staffID) REFERENCES Person (id) ON UPDATE CASCADE
);

LOCK TABLES `Staff` WRITE;
/*!40000 ALTER TABLE `Staff` DISABLE KEYS */;
INSERT INTO `Staff` (staffPF, campus, officeNumber, officeBuilding) VALUES
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

DROP TRIGGER IF EXISTS curTitle_insert;
DROP TRIGGER IF EXISTS curTitle_update;
DROP TABLE IF EXISTS StaffContract;

CREATE TABLE StaffContract (
    pk_id INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
    staffID INT NOT NULL,
    school VARCHAR (30),
    department VARCHAR (30),
    jobFunction ENUM('Academic', 'Administrative', 'Service', 'Other')
        NOT NULL DEFAULT 'Other',
    origJobTitle VARCHAR ( 40 ) NOT NULL,
    apptStartDate DATE NOT NULL,
    updatedJobTitle VARCHAR ( 40 ) NOT NULL,
    currentJobTitle VARCHAR ( 40 ) NOT NULL,
    lastRenewalDate DATE NULL,
    expirationDate DATE NOT NULL,
    endDate DATE NULL,
    status ENUM('', 'Full-time', 'Part-time', 'On Leave', 'Ended'),
    updated TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (staffID) REFERENCES Person (id),
    INDEX (staffID)
);

DELIMITER |
CREATE TRIGGER curTitle_insert BEFORE INSERT ON StaffContract
  FOR EACH ROW BEGIN
    SET NEW.currentJobTitle = IF (NEW.updatedJobTitle IS NULL, NEW.origJobTitle,
                                  NEW.updatedJobTitle);
  END;
|
CREATE TRIGGER curTitle_update BEFORE UPDATE ON StaffContract
  FOR EACH ROW BEGIN
    SET NEW.currentJobTitle = IF (NEW.updatedJobTitle IS NULL, NEW.origJobTitle,
                                  NEW.updatedJobTitle);
  END;
|
DELIMITER ;

INSERT INTO StaffContract (staffID, department, jobFunction, origJobTitle,
    apptStartDate, updatedJobTitle, lastRenewalDate, expirationDate,
    endDate, status)
VALUES
(1, 'Math/CS', 'Academic', 'Asst. Prof. of Computer Science', '1994-09-26', NULL, NULL, '2001-03-15', '2001-03-15', 'Ended')
, (1, 'Math/CS', 'Academic', 'Assoc. Prof. of Computer Science', '2001-03-15', 'Professor of Computer Science', '2010-03-15', '2012-03-15', NULL, 'Full-time')
;

