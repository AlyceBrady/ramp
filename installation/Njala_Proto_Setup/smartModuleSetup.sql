--
-- Tables for information about course modules and specific offerings.
--


USE `njala_proto`;

-- Drop triggers, functions, and procedures referring to tables defined
-- here.

-- Before dropping Modules, need to drop table(s) that depend on it.
SOURCE dropTermModuleDependencies.sql

-- Drop other tables defined in this file.

DROP TABLE IF EXISTS Modules;
DROP TABLE IF EXISTS ModuleDescriptions;
DROP TABLE IF EXISTS ModuleTypes;

CREATE TABLE ModuleTypes (
    pk_id INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
    type VARCHAR ( 20 ) NOT NULL,
    updated TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP
);

INSERT INTO ModuleTypes (type) VALUES
('Institutional')
, ('External Course')
, ('Test Equiv.')
, ('Other')
;

CREATE TABLE ModuleDescriptions (
    pk_id INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
    description TEXT,
    updated TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE Modules (
    moduleID INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
    modCode VARCHAR ( 6 ) NOT NULL,
    modNumber VARCHAR ( 6 ) NOT NULL,
    deptCode VARCHAR ( 8 ) NOT NULL,
    status ENUM('Proposed', 'Active', 'Inactive') NOT NULL,
    shortTitle VARCHAR ( 30 ) NOT NULL,
    longTitle VARCHAR ( 60 ) NOT NULL,
    descriptionKey INT,
    creditHours DOUBLE NOT NULL DEFAULT 3.0,
    capacity INT,
    type VARCHAR ( 20 ) NOT NULL DEFAULT 'Institutional',
    startDate DATE NOT NULL,
    endDate DATE,
    updated TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (descriptionKey) REFERENCES ModuleDescriptions (pk_id)
);

# /* Add catalog VARCHAR ( 30 ) NOT NULL,  ? */
# /* Status:
# If current date is before start date: Proposed.
# If end date is null or after current date: Active.
# Else: Inactive.
# */


CREATE TABLE ModuleOfferings (
    pk_id INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
    term VARCHAR( 15 ) NOT NULL,
    moduleID INT NOT NULL,
    section VARCHAR ( 3 ) NOT NULL,
    modCode VARCHAR ( 6 ) NOT NULL,
    modNumber VARCHAR ( 6 ) NOT NULL,
    shortTitle VARCHAR ( 30 ) NOT NULL,
    longTitle VARCHAR ( 60 ) NOT NULL,
    descriptionKey INT,
    deptCode VARCHAR ( 8 ) NOT NULL,
    creditHours DOUBLE NOT NULL DEFAULT 3.0,
    capacity INT,
    status ENUM('Offered', 'Canceled') NOT NULL DEFAULT 'Offered',
    type VARCHAR ( 20 ) NOT NULL DEFAULT 'Institutional',
    startDate DATE,
    endDate DATE,
    studentsAtCensusDate INT DEFAULT 0,
    studentsAtCompletion INT DEFAULT 0,
    updated TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE (term, moduleID, section),
    FOREIGN KEY (term) REFERENCES Terms (term),
    FOREIGN KEY (moduleID) REFERENCES Modules (moduleID),
    FOREIGN KEY (descriptionKey) REFERENCES ModuleDescriptions (pk_id),
    INDEX (term),
    INDEX (moduleID)
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
# Initialize modCode, modNumber, shortTitle, longTitle,
# creditHours, type, and cap from module.
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


CREATE TABLE ModuleAssignments (
    pk_id INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
    modOfferingID INT NOT NULL,
    personID INT NOT NULL,
    percentage INT NOT NULL DEFAULT 100,
    classroomNumber VARCHAR ( 6 ),
    classroomBuilding VARCHAR ( 20 ),
    weeklySchedule VARCHAR ( 50 ),
    startDate DATE,
    endDate DATE,
    updated TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (modOfferingID) REFERENCES ModuleOfferings (pk_id),
    FOREIGN KEY (personID) REFERENCES Staff (personID)
);

CREATE TABLE ProgPlanOfStudy (
    pk_id INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
    programID INT NOT NULL,
    year ENUM('Year 1', 'Year 2', 'Year 3', 'Year 4', 'Year 5',
        'Year 6') NOT NULL,
    semester ENUM('Sem. 1', 'Sem. 2') NOT NULL,
    moduleID INT NOT NULL,
    required ENUM('Required', 'Elective') DEFAULT 'Required';
    updated TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (programID) REFERENCES AcadProgram (programID),
    FOREIGN KEY (moduleID) REFERENCES Modules (moduleID),
    INDEX (programID)
);

