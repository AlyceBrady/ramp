--
-- Tables for information about people generally (names, demographic
-- and contact information, etc) and about staff members more
-- specifically (job titles, contract information, etc.)
--


USE `njala_proto`;

-- Drop triggers, functions, and procedures referring to tables defined
-- in this file.

DROP TRIGGER IF EXISTS PrefName_insert;
DROP TRIGGER IF EXISTS PrefName_update;
DROP TRIGGER IF EXISTS ContractEnd_Insert;
DROP TRIGGER IF EXISTS ContractEnd_Update;

DROP FUNCTION IF EXISTS RampConcat;
DROP FUNCTION IF EXISTS FullName;
DROP FUNCTION IF EXISTS LastNameFirst;
DROP FUNCTION IF EXISTS AddrOnOneLine;
DROP FUNCTION IF EXISTS PhNumList;

-- Before dropping Person table, need to drop table(s) that depend on it.
SOURCE dropSmartPersonStaffDependencies.sql

-- Drop other tables defined in this file.

DROP TABLE IF EXISTS NameTypes;
DROP TABLE IF EXISTS AddressTypes;
DROP TABLE IF EXISTS CampusNames;
DROP TABLE IF EXISTS CampusLocations;
DROP TABLE IF EXISTS HoldTypes;
DROP TABLE IF EXISTS HoldAuthorities;
DROP TABLE IF EXISTS JobCategories;
DROP TABLE IF EXISTS ContractStatusCodes;

DROP TABLE IF EXISTS Person;

CREATE TABLE NameTypes (
    pk_id INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
    nameType VARCHAR ( 20 ) NOT NULL,
    updated TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP
);

INSERT INTO NameTypes (nameType) VALUES
('Matriculated')
, ('Transcript')
;

CREATE TABLE AddressTypes (
    pk_id INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
    addressType VARCHAR ( 20 ) NOT NULL,
    updated TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP
);

INSERT INTO AddressTypes (addressType) VALUES
('Current')
, ('Correspondance')
, ('Permanent')
, ('Billing')
, ('Other')
;

CREATE TABLE CampusNames (
    pk_id INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
    name VARCHAR ( 20 ) NOT NULL,
    updated TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE CampusLocations (
    pk_id INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
    location VARCHAR ( 20 ) NOT NULL,
    updated TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE JobCategories (
    pk_id INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
    category VARCHAR ( 25 ) NOT NULL,
    updated TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE ContractStatusCodes (
    pk_id INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
    statusCode VARCHAR ( 20 ),
    updated TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP
);

INSERT INTO ContractStatusCodes (statusCode) VALUES
(NULL)
, ('Full-time')
, ('Part-time')
, ('On Leave')
, ('Ended')
;

CREATE TABLE HoldTypes (
    pk_id INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
    holdType VARCHAR ( 20 ) NOT NULL,
    updated TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE HoldAuthorities (
    pk_id INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
    authCode VARCHAR ( 20 ) NOT NULL,
    updated TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP
);

INSERT INTO HoldAuthorities (authCode) VALUES
('Dean')
, ('Registrar')
, ('Advisor')
, ('Business')
, ('Vice Chancellor')
;

CREATE TABLE Person (
    personID INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
    title VARCHAR ( 5 ),
    firstname VARCHAR ( 30 ) NOT NULL,
    middlename VARCHAR ( 30 ),
    lastname VARCHAR ( 40 ) NOT NULL,
    suffix VARCHAR ( 10 ),
    specifiedPrefFName VARCHAR ( 30 ),
    prefFirstName VARCHAR ( 30 ),
    previousName VARCHAR ( 100 ),
    gender ENUM('Unknown', 'M', 'F') NOT NULL DEFAULT 'Unknown',
    citizenship VARCHAR ( 30 ),
    nassit VARCHAR (20),
    prefEmail VARCHAR ( 60 ),
    birthDate DATE DEFAULT NULL,
    birthPlace VARCHAR ( 50 ),
    deceasedDate DATE DEFAULT NULL,
    maritalStatus ENUM('Unknown', 'Single', 'Married', 'Widowed', 'Divorced')
        DEFAULT 'Unknown',
    spousePersonID VARCHAR ( 30 ),
    spouseName VARCHAR ( 50 ),
    spouseAddress VARCHAR ( 100 ),
    nextOfKin VARCHAR ( 50 ),
    nextOfKinContact VARCHAR ( 100 ),
    privacy ENUM('F', 'T') NOT NULL DEFAULT 'F',
    updated TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE RelatedNames (
    pk_id INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
    personID INT NOT NULL,
    name VARCHAR (50) NOT NULL,
    nameType VARCHAR ( 20 ) NOT NULL,
    updated TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (personID) REFERENCES Person (personID),
    UNIQUE (personID, name, nameType)
);

CREATE TABLE Address (
    pk_id INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
    personID INT NOT NULL,
    addressType VARCHAR ( 20 ) NOT NULL,
    address1 VARCHAR ( 40 ),
    address2 VARCHAR ( 40 ),
    address3 VARCHAR ( 40 ),
    address4 VARCHAR ( 40 ),
    city VARCHAR ( 40 ),
    stateProvince VARCHAR ( 20 ),
    postalCode VARCHAR ( 10 ),
    country VARCHAR ( 20 ),
    startDate DATE DEFAULT NULL,
    endDate DATE DEFAULT NULL,
    updated TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (personID) REFERENCES Person (personID)
);

CREATE TABLE PhoneNumber (
    pk_id INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
    personID INT NOT NULL,
    phoneNumber VARCHAR (20) NOT NULL,
    updated TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (personID) REFERENCES Person (personID),
    UNIQUE (personID, phoneNumber)
);

CREATE TABLE InstitutionsAttended (
    pk_id INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
    personID INT NOT NULL,
    institutionLoc VARCHAR ( 30 ),
    institutionName VARCHAR ( 40 ),
    degree VARCHAR ( 30 ) DEFAULT NULL,
    date DATE DEFAULT NULL,
    rank INT NOT NULL,
    updated TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (personID) REFERENCES Person (personID),
    UNIQUE(personID, rank)
);


CREATE TABLE Staff (
    personID INT NOT NULL PRIMARY KEY ,
    active ENUM('Active', 'Inactive') NOT NULL DEFAULT 'Active',
    campusLocation varchar(15) DEFAULT NULL,
    officeNumber VARCHAR ( 6 ),
    officeBuilding VARCHAR ( 20 ),
    origApptStartDate DATE DEFAULT NULL,
    lastPromotionDate DATE DEFAULT NULL,
    updated TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (personID) REFERENCES Person (personID)
);

CREATE TABLE Children (
    pk_id INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
    parentID INT NOT NULL,
    personID INT,
    name VARCHAR ( 50 ),
    gender ENUM('Unknown', 'M', 'F') NOT NULL DEFAULT 'Unknown',
    birthDate DATE DEFAULT NULL,
    deceasedDate DATE DEFAULT NULL,
    updated TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (parentID) REFERENCES Staff (personID)
);

CREATE TABLE JobFunction (
    pk_id INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
    personID INT NOT NULL,
    jobTitle VARCHAR ( 40 ),
    jobCategory VARCHAR ( 25 ) NOT NULL DEFAULT 'Other',
    schoolCode VARCHAR (8),
    departmentCode VARCHAR (8),
    campusName varchar(15) DEFAULT NULL,
    campusLocation varchar(15) DEFAULT NULL,
    startDate DATE DEFAULT NULL,
    endDate DATE DEFAULT NULL,
    remark VARCHAR ( 100 ),
    updated TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (personID) REFERENCES Staff (personID),
    INDEX (personID)
);

CREATE TABLE StaffContract (
    pk_id INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
    personID INT NOT NULL,
    apptStartDate DATE DEFAULT NULL,
    lastRenewalDate DATE DEFAULT NULL,
    expirationDate DATE DEFAULT NULL,
    renewalRecommendation VARCHAR(20) DEFAULT NULL,
    recommendationAuthority VARCHAR(20) DEFAULT NULL,
    recommendationDate DATE DEFAULT NULL,
    endDate DATE DEFAULT NULL,
    status VARCHAR ( 20 ) DEFAULT NULL,
    updated TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (personID) REFERENCES Staff (personID),
    INDEX (personID)
);

CREATE TABLE Accidents (
    pk_id INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
    personID INT NOT NULL,
    date DATE DEFAULT NULL,
    type VARCHAR ( 50 ) DEFAULT NULL,
    cause VARCHAR ( 150 ) DEFAULT NULL,
    remark VARCHAR ( 100 ) DEFAULT NULL,
    updated TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (personID) REFERENCES Staff (personID),
    INDEX (personID)
);

CREATE TABLE StaffDisciplinaryAction (
    pk_id INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
    personID INT NOT NULL,
    cause VARCHAR ( 150 ) DEFAULT NULL,
    action VARCHAR ( 40 ) DEFAULT NULL,
    date DATE DEFAULT NULL,
    authority VARCHAR ( 40 ) DEFAULT NULL,
    updated TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (personID) REFERENCES Staff (personID),
    INDEX (personID)
);

CREATE TABLE RecordHold (
    personID INT NOT NULL,
    holdType VARCHAR ( 20 ) NOT NULL,
    remark VARCHAR ( 50 ),
    authority VARCHAR ( 20 ) NOT NULL,
    startDate DATE NOT NULL,
    endDate DATE,
    updated TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (personID, holdType, startDate),
    FOREIGN KEY (personID) REFERENCES Person (personID)
);

-- Define Triggers, Functions, and Procedures that deal with tables
-- defined in this file.

DELIMITER |
CREATE TRIGGER PrefName_insert BEFORE INSERT ON Person
  FOR EACH ROW BEGIN
    SET NEW.prefFirstName = IF (NEW.specifiedPrefFName IS NULL, NEW.firstname,
                                NEW.specifiedPrefFName);
  END;
|
CREATE TRIGGER PrefName_update BEFORE UPDATE ON Person
  FOR EACH ROW BEGIN
    SET NEW.prefFirstName = IF (NEW.specifiedPrefFName IS NULL, NEW.firstname,
                                NEW.specifiedPrefFName);
  END;
|
DELIMITER ;

/*
DELIMITER |
CREATE TRIGGER PrevName_update BEFORE UPDATE ON Person
  FOR EACH ROW BEGIN
    SET @fullName = FullName(NEW.personID);
    SET NEW.previousName = IF (@fullName <> OLD.previousName, @fullName,
                                OLD.previousName);
  END;
|
DELIMITER ;
*/


DELIMITER //
CREATE TRIGGER ContractEnd_Insert BEFORE INSERT ON StaffContract
  FOR EACH ROW BEGIN
    SET NEW.endDate = IF (NEW.status = 'Ended' AND NEW.endDate IS NULL,
                                NOW(), NEW.endDate);
    SET NEW.status = IF ( NEW.endDate IS NOT NULL AND NEW.endDate <= CURDATE(),
                          'Ended', NEW.status);
  END; //
CREATE TRIGGER ContractEnd_Update BEFORE UPDATE ON StaffContract
  FOR EACH ROW BEGIN
    SET NEW.endDate = IF (NEW.status = 'Ended' AND NEW.endDate IS NULL,
                                NOW(), NEW.endDate);
    SET NEW.status = IF ( NEW.endDate IS NOT NULL AND NEW.endDate <= CURDATE(),
                          'Ended', NEW.status);
  END; //
DELIMITER ;

DELIMITER //
CREATE FUNCTION RampConcat(p1 VARCHAR(50), p2 VARCHAR(50), delim VARCHAR(5))
RETURNS VARCHAR(110) DETERMINISTIC
COMMENT "Concatenates p1 and p2, separated by delim unless p1 or p2 is empty"
BEGIN
    SET p1 = IF ( NOT isNULL(p1), p1, '');
    SET p2 = IF ( NOT isNULL(p2), p2, '');
    RETURN IF ( p1='' OR p2='' , CONCAT(p1, p2), CONCAT(p1, delim, p2));
END; //
DELIMITER ;


CREATE FUNCTION FullName(f VARCHAR(30), m VARCHAR(30),
                         l VARCHAR(40), s VARCHAR(10))
RETURNS VARCHAR(115) DETERMINISTIC
COMMENT "Returns the person's full name"
    RETURN RampConcat(RampConcat(RampConcat(f, m, " "),
                l, " "), s, ", ");

CREATE FUNCTION FullNameWTitle(t VARCHAR ( 5 ), f VARCHAR(30), m VARCHAR(30),
                         l VARCHAR(40), s VARCHAR(10))
RETURNS VARCHAR(115) DETERMINISTIC
COMMENT "Returns the person's full name with title"
    RETURN RampConcat(RampConcat(RampConcat(RampConcat(t, f, " "), m, " "),
                l, " "), s, ", ");

CREATE FUNCTION FullLastNameFirst(f VARCHAR(30), m VARCHAR(30), l VARCHAR(40),
                                  s VARCHAR(10))

RETURNS VARCHAR(115) DETERMINISTIC
COMMENT "Returns the person's full name, last name first"
    RETURN RampConcat(RampConcat(RampConcat(l, f, ", "), m, " "), s, ", ");

CREATE FUNCTION FullLNFWTitle(t VARCHAR ( 5 ), f VARCHAR(30), m VARCHAR(30),
                              l VARCHAR(40), s VARCHAR(10))
RETURNS VARCHAR(115) DETERMINISTIC
COMMENT "Returns the person's full name (last name first) with title"
    RETURN RampConcat(RampConcat(RampConcat(RampConcat(l, t, ", "), f, " "),
                        m, " "), s, ", ");


CREATE FUNCTION AppendPrefFName(name VARCHAR(110), f VARCHAR(30), p VARCHAR(30))
RETURNS VARCHAR(150) DETERMINISTIC
COMMENT "Append the preferred first name to 'name' if different from first name"
    RETURN IF ( (f=p), name, RampConcat(name, CONCAT('(',p,')'), " "));


CREATE FUNCTION FullNameWPref(f VARCHAR(30), p VARCHAR(30), m VARCHAR(30),
                              l VARCHAR(40), s VARCHAR(10))
RETURNS VARCHAR(150) DETERMINISTIC
COMMENT "Returns the person's full name"
    RETURN RampConcat(RampConcat(RampConcat(AppendPrefFName(f, f, p), m, " "),
                l, " "), s, ", ");


CREATE FUNCTION FullLNFWPref(f VARCHAR(30), p VARCHAR(30), m VARCHAR(30),
                             l VARCHAR(40), s VARCHAR(10))
RETURNS VARCHAR(150) DETERMINISTIC
COMMENT "Returns the person's full name, last name first (with pref first name)"
    RETURN AppendPrefFName(RampConcat(RampConcat(RampConcat(l, f, ", "),
                m, " "), s, ", "), f, p);


CREATE FUNCTION FirstLastNameWPref(f VARCHAR(30), p VARCHAR(30), l VARCHAR(40))
RETURNS VARCHAR(110) DETERMINISTIC
COMMENT "Returns 'Firstname Lastname' or 'Firstname (Pref) Lastname'"
    RETURN RampConcat(AppendPrefFName(f, f, p), l, " ");


CREATE FUNCTION PrefLastName(p VARCHAR(30), l VARCHAR(40))
RETURNS VARCHAR(75) DETERMINISTIC
COMMENT "Returns 'PreferredFirstname Lastname'"
    RETURN RampConcat(p, l, " ");


DELIMITER //
CREATE FUNCTION NoteHolds(id INT, n VARCHAR(150))
RETURNS VARCHAR(175) DETERMINISTIC
COMMENT "Appends ' -- HOLD' to name if there is a hold"
BEGIN
    SELECT COUNT(holdType) INTO @holdCount FROM RecordHold
            WHERE `personID` = id;
    RETURN IF ( @holdCount > 0, RampConcat(n, "HOLD", " -- "), n);
END; //
DELIMITER ;


CREATE FUNCTION AddrOnOneLine(a1 VARCHAR(40), a2 VARCHAR(40), a3 VARCHAR(40),
                              a4 VARCHAR(40), ci VARCHAR(40), s VARCHAR(20),
                              p VARCHAR(10), co VARCHAR(20))
RETURNS VARCHAR(270) DETERMINISTIC
COMMENT "Returns the address in a single string"
    RETURN RampConcat(RampConcat(RampConcat(RampConcat(
        RampConcat(RampConcat(RampConcat(a1, a2, ", "), a3, ", "), a4, ", "),
            ci, ", "), s, ", "), p, ", "), co, ", ");


CREATE FUNCTION ShortAddrOnOneLine(a1 VARCHAR(40), a2 VARCHAR(40),
                              ci VARCHAR(40), s VARCHAR(20),
                              p VARCHAR(10), co VARCHAR(20))
RETURNS VARCHAR(270) DETERMINISTIC
COMMENT "Returns the address in a single string"
    RETURN RampConcat(RampConcat(RampConcat(RampConcat(
        RampConcat(a1, a2, ", "), ci, ", "), s, ", "), p, ", "), co, ", ");


DELIMITER //
CREATE FUNCTION PhNumList(id INT)
RETURNS VARCHAR(100) DETERMINISTIC
COMMENT "Returns a list of the person's phone numbers as a string"
BEGIN
    DECLARE done INT DEFAULT FALSE;
    DECLARE phNum VARCHAR(20);
    DECLARE phNumList VARCHAR(100);
    DECLARE cur CURSOR FOR SELECT phoneNumber FROM PhoneNumber
        where `personID`=id;
    DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;
    OPEN cur;
        SET phNumList = '';
        read_loop: LOOP
            FETCH cur INTO phNum;
            IF done THEN
              LEAVE read_loop;
            END IF;
            SET phNumList = RampConcat(phNumList, phNum, ', ');
        END LOOP;
    CLOSE cur;
    RETURN phNumList;
END; //
DELIMITER ;


DELIMITER //
CREATE FUNCTION NumChildren(id INT)
RETURNS INT DETERMINISTIC
COMMENT "Computes the number of children for the given person ID"
BEGIN
    SELECT COUNT(name) INTO @childrenCount FROM Children
            WHERE `parentID` = id;
    RETURN @childrenCount;
END; //
DELIMITER ;

/*
DELIMITER //
CREATE FUNCTION HighestQualification(id INT)
RETURNS INT DETERMINISTIC
COMMENT "Returns an 'Institution Attended' record reflecting the highest-ranked qualification"
BEGIN
    SELECT COUNT(name) INTO @childrenCount FROM Children
            WHERE `parentID` = id;
    RETURN @childrenCount;
END; //
DELIMITER ;
*/


/*
DELIMITER //
CREATE FUNCTION MakeList(tableName VARCHAR(20), fieldName VARCHAR(20), keyFieldName VARCHAR(20), key INT)
RETURNS VARCHAR(100) DETERMINISTIC
COMMENT "Returns a list of multiple instances of a field as a string"
BEGIN
    DECLARE done INT DEFAULT FALSE;
    DECLARE fieldVal VARCHAR(20);
    DECLARE listString VARCHAR(100);
    DECLARE cur CURSOR FOR SELECT fieldName FROM tableName
        where keyFieldName=key;
    DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;
    OPEN cur;
        SET listString = '';
        read_loop: LOOP
            FETCH cur INTO fieldVal;
            IF done THEN
              LEAVE read_loop;
            END IF;
            SET listString = RampConcat(listString, fieldVal, ', ');
        END LOOP;
    CLOSE cur;
    RETURN listString;
END; //
DELIMITER ;
CREATE FUNCTION PhNumList(id INT)
RETURNS VARCHAR(100) DETERMINISTIC
COMMENT "Returns a list of the person's phone numbers as a string"
    RETURN MakeList(id, 'PhoneNumber', 'phoneNumber', 'personID');
*/

-- Populate tables.

LOCK TABLES `Person` WRITE;
INSERT INTO `Person`
(personID, title, firstname, middlename, lastname, specifiedPrefFName, gender,
    prefEmail, birthDate, citizenship) VALUES
(1,  'Dr.', 'J.', 'Alyce', 'Brady', 'Alyce', 'F', 'abrady@kzoo.edu', '1961-06-12', 'US')
;
UNLOCK TABLES;

