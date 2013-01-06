--
-- Tables for information about course modules and specific offerings.
--

DROP TABLE IF EXISTS Modules;

CREATE TABLE Modules (
    moduleID INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
    modCode VARCHAR ( 6 ) NOT NULL,
    modNumber VARCHAR ( 6 ) NOT NULL,
    deptID INT NOT NULL,
    status ENUM('Proposed', 'Active', 'Inactive') NOT NULL,
    shortTitle VARCHAR ( 30 ) NOT NULL,
    longTitle VARCHAR ( 60 ) NOT NULL,
    description TEXT,
    credits DOUBLE NOT NULL DEFAULT 3.0,
    capacity INT,
    type ENUM('Institutional', 'External Course',
        'Test Equiv.', 'Other') NOT NULL DEFAULT 'Institutional',
    startDate DATE NOT NULL,
    endDate DATE,
    updated TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP
);

# /* Add catalog VARCHAR ( 30 ) NOT NULL,  ? */
# /* Status:
# If current date is before start date: Proposed.
# If end date is null or after current date: Active.
# Else: Inactive.
# */

INSERT INTO Modules (moduleID, modCode, modNumber, deptID, status,
    shortTitle, longTitle, description, credits, capacity, type, startDate)
VALUES
(1,'COMP','105',10,'Active','Intro to CS','Introduction to Computer Sci.',NULL,3,NULL,'Institutional','2000-01-01')
,(3,'COMP','110',10,'Active','Intro Prog.','Introduction to Programming',NULL,3,NULL,'Institutional','2000-01-01')
,(4,'COMP','210',10,'Active','Data Struct.','Data Structures',NULL,3,NULL,'Institutional','2000-01-01')
,(71,'COMP','487',10,'Active','Dynamic Internet Apps','Dynamic Internet Apps',NULL,3,NULL,'Institutional','2012-09-01')
,(72,'AFST','101',7, 'Active','Intro to African Studies','Introduction to African Studies','This module provides an introduction to African Studies.',3,NULL,'Institutional','2000-10-01')
,(73,'COMS','211',7, 'Active','Comm. Skills','Communication Skills','This module provides an introduction to Intermediate Communication Skills',3,NULL,'Institutional','1985-10-01')
,(74,'AEX','211',5,'Active','Intermed Ag. Ext.','Intermediate Agriculture Extension','This module provides an introduction to Intermediate Agriculture Extension.',3,NULL,'Institutional','1980-10-01')
,(75,'FOR','211',5,'Active','Intermed Forestry Practices','Intermediate Forestry Practices','This module provides an introduction to Intermediate Forestry Practices.',3,NULL,'Institutional','2008-08-01')
,(76,'FOR','213',5,'Active','Intermed. Wood Forestry','Intermediate Wood Forestry','This module provides an introduction to Intermediate Wood Forestry.',3,NULL,'Institutional','2008-08-01')
;

DROP TABLE IF EXISTS ModuleOfferings;

CREATE TABLE ModuleOfferings (
    term VARCHAR ( 15 ) NOT NULL,
    moduleID INT NOT NULL,
    section VARCHAR ( 3 ) NOT NULL,
    modCode VARCHAR ( 6 ) NOT NULL,
    modNumber VARCHAR ( 6 ) NOT NULL,
    shortTitle VARCHAR ( 30 ) NOT NULL,
    longTitle VARCHAR ( 60 ) NOT NULL,
    description TEXT,
    credits DOUBLE NOT NULL DEFAULT 3.0,
    capacity INT,
    status ENUM('Offered', 'Canceled') NOT NULL DEFAULT 'Offered',
    type ENUM('Institutional', 'External Course', 'Test Equiv.',
        'Other') NOT NULL DEFAULT 'Institutional',
    startDate DATE,
    endDate DATE,
    studentsAtCensusDate INT,
    studentsAtCompletion INT,
    updated TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (term, moduleID, section),
    FOREIGN KEY (term) REFERENCES Terms (term),
    FOREIGN KEY (moduleID) REFERENCES Modules (moduleID)
);

DELIMITER |
CREATE TRIGGER CancelStudentReg_Update AFTER UPDATE ON ModuleOfferings
  FOR EACH ROW BEGIN
    IF NEW.status = 'Cancelled' AND NEW.status <> OLD.status THEN
            CALL CancelStudentReg(NEW.term, NEW.moduleID, NEW.section);
    END IF;
  END;
|
DELIMITER ;

# /*
# Initialize modCode, modNumber, shortTitle, longTitle, description, credits,
# type, and cap from module.
# Initialize startDate and endDate from term.
# */

# /*
# Must also copy all the module attributes as offering attributes.
# */

# /* Status:
# If end date before (term end date? term census date?): Canceled.
# Else: Offered.
# */

# /*
# Need manual triggers to set studentsAtCensusDate and studentsAtCompletion.
# */

# /*
# Populating Offerings:
# Option 1: Do a "mass copy" of a selection of offerings, changing just
#   the term field.
# Option 2: Create new offerings one-by-one, initializing most fields from
#   Module and Term tables.
# Since the only non-derived fields are term, modCode, modNumber, and section,
# populating offerings are not a big deal.  The big deal is populating
# ASSIGNMENTS.
# */

INSERT INTO `ModuleOfferings` VALUES ('2011-12 Sem 1',1,'1','COMP','105','Intro to CS','Introduction to Computer Sci.',NULL,3,30,'Offered','Institutional','2011-09-10','2011-12-01',0,0,'2012-12-12 21:59:30')
,('2011-12 Sem 1',72,'01','AFST','101','Intro to African Studies','Introduction to African Studies','This module provides an introduction to African Studies.',3,NULL,'Offered','Institutional','2011-09-10','2011-12-15',NULL,NULL,'2012-12-12 19:31:34')
,('2011-12 Sem 1',72,'02','AFST','101','Intro to African Studies','Introduction to African Studies','This module provides an introduction to African Studies.',3,NULL,'Offered','Institutional','2011-09-10','2011-12-15',NULL,NULL,'2012-12-12 19:31:49')
,('2011-12 Sem 1',73,'01','COMS','211','Comm. Skills','Communication Skills','This module provides an introduction to Intermediate Communication Skills',3,NULL,'Offered','Institutional','2011-09-10','2011-12-15',NULL,NULL,'2012-12-12 19:30:52')
,('2011-12 Sem 1',74,'01','AEX','211','Intermed Ag. Ext.','Intermediate Agriculture Extension','This module provides an introduction to Intermediate Agriculture Extension.',3,NULL,'Offered','Institutional','2011-09-10','2011-12-15',NULL,NULL,'2012-12-12 19:35:42')
,('2011-12 Sem 1',75,'01','FOR','211','Intermed Forestry Practices','Intermediate Forestry Practices','This module provides an introduction to Intermediate Forestry Practices.',3,NULL,'Offered','Institutional','2011-09-10','2011-12-15',NULL,NULL,'2012-12-12 19:30:14')
,('2011-12 Sem 1',76,'01','FOR','213','Intermed. Wood Forestry','Intermediate Wood Forestry','This module provides an introduction to Intermediate Wood Forestry.',3,0,'Canceled','Institutional','2011-09-10','2011-09-11',0,0,'2012-12-12 19:37:06')
,('2011-12 Sem 2',3,'1','COMP','110','Intro Prog.','Introduction to Programming',NULL,3,30,'Offered','Institutional','2012-04-01','2012-06-15',0,0,'2012-12-12 22:00:18')
,('2011-12 Sem 2',76,'01','FOR','213','Intermed. Wood Forestry','Intermediate Wood Forestry','This module provides an introduction to Intermediate Wood Forestry.',3,NULL,'Offered','Institutional','2012-04-01','2012-06-20',NULL,NULL,'2012-12-12 19:37:45')
,('2012-13 Sem 1',4,'1','COMP','210','Data Struct.','Data Structures',NULL,3,20,'Offered','Institutional','2012-10-01','2013-02-15',0,0,'2012-12-12 21:58:40')
,('2012-13 Sem 2',1,'01','COMP','105','Intro to CS','Introduction to Computer Sci.',NULL,3,30,'Offered','Institutional','2013-04-01','2013-06-15',0,0,'2012-12-12 22:01:21')
,('2012-13 Sem 2',71,'01','COMP','487','Dynamic Internet Apps','Dynamic Internet Applications','An indescribable course, on so many fronts!',3,30,'Offered','Institutional','2012-09-10','2012-12-15',NULL,NULL,'2012-11-30 09:42:54')
;

DROP TABLE IF EXISTS ModuleAssignments;

CREATE TABLE ModuleAssignments (
    pk_id INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
    term VARCHAR ( 15 ) NOT NULL,
    moduleID INT NOT NULL,
    section VARCHAR ( 3 ),
    staffID INT NOT NULL,
    percentage INT NOT NULL DEFAULT 100,
    classroomNumber VARCHAR ( 6 ),
    classroomBuilding VARCHAR ( 20 ),
    weeklySchedule VARCHAR ( 50 ),
    startDate DATE,
    endDate DATE,
    updated TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (term, moduleID, section) REFERENCES ModuleOfferings (term, moduleID, section),
    FOREIGN KEY (staffID) REFERENCES Staff (staffID)
);

INSERT INTO `ModuleAssignments` VALUES
(1,'2011-12 Sem 1',73,'01',15585,100,'243','Faculty Building','MWF 10:00-11:00','2011-10-01','2012-02-15','2012-12-12 21:53:38')
;

