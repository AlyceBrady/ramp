--
-- Tables for information about course modules and specific offerings.
--


USE `smart_dev`;

-- Drop triggers, functions, and procedures referring to tables defined
-- in this file.

DROP TRIGGER IF EXISTS CancelStudentReg_Update;

-- Before dropping Modules, need to drop table(s) that depend on it.
SOURCE dropTermModuleDependencies.sql

-- Drop other tables defined in this file.

DROP TABLE IF EXISTS ModuleType;

DROP TABLE IF EXISTS Modules;
DROP TABLE IF EXISTS Attributes;
DROP TABLE IF EXISTS ModuleAttributes;

CREATE TABLE ModuleType (
    type VARCHAR ( 20 ) NOT NULL PRIMARY KEY ,
    updated TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP
);

INSERT INTO ModuleType (type) VALUES
('Institutional')
, ('Study Abroad')
, ('External Course')
, ('Test Equiv.')
, ('Other')
;

CREATE TABLE Modules (
    moduleID INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
    modCode VARCHAR ( 6 ) NOT NULL,
    modNumber VARCHAR ( 6 ) NOT NULL,
    status ENUM('Proposed', 'Active', 'Inactive') NOT NULL,
    shortTitle VARCHAR ( 30 ) NOT NULL,
    longTitle VARCHAR ( 60 ) NOT NULL,
    description TEXT,
    credits DOUBLE NOT NULL DEFAULT 3.0,
    capacity INT,
    type VARCHAR ( 20 ) NOT NULL DEFAULT 'Institutional',
    startDate DATE NOT NULL,
    endDate DATE,
    updated TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP
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


-- Table(s) that depend on ModuleOfferings have already been dropped by
-- dropTermModuleDependencies.sql, sourced above.

CREATE TABLE ModuleOfferings (
    pk_id INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
    term VARCHAR ( 10 ) NOT NULL,
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
    type VARCHAR ( 20 ) NOT NULL DEFAULT 'Institutional',
    startDate DATE,
    endDate DATE,
    studentsAtCensusDate INT,
    studentsAtCompletion INT,
    updated TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE (term, moduleID, section),
    FOREIGN KEY (term) REFERENCES Terms (term),
    FOREIGN KEY (moduleID) REFERENCES Modules (moduleID),
    INDEX (term),
    INDEX (moduleID)
);

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

DELIMITER |
CREATE TRIGGER CancelStudentReg_Update AFTER UPDATE ON ModuleOfferings
  FOR EACH ROW BEGIN
    IF NEW.status = 'Cancelled' AND NEW.status <> OLD.status THEN
            CALL CancelStudentReg(NEW.pk_id);
    END IF;
  END;
|
DELIMITER ;

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

INSERT INTO ModuleOfferings (pk_id, moduleID, term, section, startDate, capacity,
    modCode, modNumber, shortTitle, longTitle)
VALUES
(1, 1, '2011Q4', '01', '2011-09-10', 30,
    'COMP', '105', 'Intro to CS', 'Introduction to Computer Sci.')
, (2, 21, '2011Q4', '01', '2011-09-10', 30,
    'MATH', '112', 'Calculus I', 'Calculus I')
, (3, 21, '2011Q4', '02', '2011-09-10', 30,
    'MATH', '112', 'Calculus I', 'Calculus I')
, (4, 60, '2011Q4', '01', '2011-09-10', 18,
    'FREN', '101', 'French I', 'French Lang. and Lit. I')
, (5, 35, '2011Q4', '01', '2011-09-10', 25,
    'ENGL', '210', 'Brit. Lit.', "British Lit: Austen's Novels")
, (6, 70, '2011Q4', '01', '2011-09-10', 15,
    'WRIT', '100', 'First-Year Sem.', 'First-Year Writing Seminar')
, (7, 70, '2011Q4', '02', '2011-09-10', 15,
    'WRIT', '100', 'First-Year Sem.', 'First-Year Writing Seminar')
, (8, 3, '2012Q1', '01', '2012-01-01', 30,
    'COMP', '110', 'Intro Prog.', 'Introduction to Programming')
, (9, 21, '2012Q1', '01', '2012-01-01', 30,
    'MATH', '112', 'Calculus I', 'Calculus I')
, (10, 22, '2012Q1', '02', '2012-01-01', 30,
    'MATH', '112', 'Calculus I', 'Calculus I')
, (11, 40, '2012Q1', '01', '2012-01-01', 25,
    'HIST', '110', 'US History I', 'US History I')
, (12, 42, '2012Q1', '01', '2012-01-01', 25,
    'HIST', '210', 'West Afr. Hist I', 'West African History I')
, (13, 50, '2012Q1', '01', '2012-01-01', 35,
    'ECON', '105', 'Microeconomics', 'Intro. to Microeconomics')
, (14, 61, '2012Q1', '01', '2012-01-01', 18,
    'FREN', '102', 'French II', 'French Lang. and Lit. II')
, (15, 35, '2012Q1', '01', '2012-01-01', 25,
    'ENGL', '210', 'Brit. Lit.', "British Lit: Austen's Novels")
, (16, 70, '2012Q1', '01', '2012-01-01', 15,
    'WRIT', '100', 'First-Year Sem.', 'First-Year Writing Seminar')
, (17, 2, '2012Q2', '01', '2012-04-01', 30,
    'COMP', '107', 'Multimedia Prog', 'Multimedia Programming')
, (18, 4, '2012Q2', '01', '2012-04-01', 20,
    'COMP', '210', 'Data Struct.', 'Data Structures')
, (19, 21, '2012Q2', '01', '2012-04-01', 30,
    'MATH', '112', 'Calculus I', 'Calculus I')
, (20, 22, '2012Q2', '01', '2012-04-01', 30,
    'MATH', '113', 'Calculus II', 'Calculus II')
, (21, 43, '2012Q2', '01', '2012-04-01', 25,
    'HIST', '211', 'West Afr. Hist II', 'West African History II')
, (22, 51, '2012Q2', '01', '2012-04-01', 35,
    'ECON', '106', 'Macroeconomics', 'Intro. to Macroeconomics')
, (23, 62, '2012Q2', '01', '2012-04-01', 18,
    'FREN', '103', 'French III', 'French Lang. and Lit. III')
;

# /* Module attributes used for requirements */

CREATE TABLE Attributes (
    pk_id INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
    category VARCHAR ( 20 ),
    value VARCHAR ( 20 ),
    updated TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,
    INDEX (category)
);

CREATE TABLE ModuleAttributes (
    pk_id INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
    moduleID INT NOT NULL,
    startDate DATE NOT NULL,
    endDate DATE DEFAULT NULL
);

# /*
# Need to implement ability to set pseudo-enums based on values in a table
# in order to be able to enumerate attribute categories.
# Examples: category:  Level  values: 100-level, 200-level, graduate
#           category:  Department  values: Math, CS, Psych, ...
# */
#

CREATE TABLE ModuleAssignments (
    pk_id INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
    modOfferingID INT NOT NULL,
    staffID INT NOT NULL,
    percentage INT NOT NULL DEFAULT 100,
    classroomNumber VARCHAR ( 6 ),
    classroomBuilding VARCHAR ( 20 ),
    weeklySchedule VARCHAR ( 50 ),
    startDate DATE,
    endDate DATE,
    updated TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (modOfferingID) REFERENCES ModuleOfferings (pk_id),
    FOREIGN KEY (staffID) REFERENCES Staff (staffID)
);

INSERT INTO ModuleAssignments (modOfferingID, staffID,
    classroomBuilding, classroomNumber, weeklySchedule)
VALUES
(1, 1, "Olds/Upton", "312", "MWF 1:15-2:30")
, (2, 24, "Olds/Upton", "304", "MWF 8:30-9:45")
, (3, 24, "Olds/Upton", "304", "MWF 8:30-9:45")
, (4, 4, "Dewing", "206", "MWF 8:30-9:45")
, (5, 4, "Humphrey House", "206", "MWF 1:15-2:30")
, (6, 6, "Humphrey House", "Lounge", "MWF 11:50-1:05")
, (7, 6, "Dewing", "114", "MWF 1:15-2:30")
, (8, 1, "Olds/Upton", "312", "MWF 1:15-2:30")
, (9, 24, "Olds/Upton", "304", "MWF 8:30-9:45")
, (10, 24, "Olds/Upton", "304", "MWF 2:40-3:55")
, (11, 3, "Dewing", "302", "TTh 9:00-11:00")
, (12, 3, "Dewing", "302", "TTh 1:00-3:00")
, (13, 6, "Dewing", "316", "MWF 2:40-3:55")
, (14, 4, "Dewing", "206", "MWF 8:30-9:45")
, (15, 4, "Humphrey House", "206", "MWF 1:15-2:30")
, (16, 6, "Humphrey House", "Lounge", "MWF 11:50-1:05")
, (17, 1, DEFAULT, DEFAULT, "MWF 1:15-2:30")
, (18, 1, DEFAULT, DEFAULT, "MWF 10:00-11:15")
, (19, 24, DEFAULT, DEFAULT, "MWF 8:30-9:45")
, (20, 24, DEFAULT, DEFAULT, "MWF 2:40-3:55")
, (21, 3, DEFAULT, DEFAULT, "TTh 9:00-11:00")
, (22, 6, DEFAULT, DEFAULT, "MWF 2:40-3:55")
, (23, 4, DEFAULT, DEFAULT, "MWF 8:30-9:45")
;

# /*
# Usually have one ModuleAssignment per offering, but may sometimes have
# multiple assignments.  (Depending on how times are handled, might almost
# always have multiple assignment.)
# How are assignements populated?
# Option 1: Do a "mass copy" of a selection of assignments, changing just
#   the term field.  NICE IDEA, but CAN'T BE DONE -- offeringID is different.
#   Would have to be part of a larger automated process to copy all offerings
#   and all associated assignments from specified term.
# Option 2: Create new offerings one-by-one.
# */

# /*
# Initialize startDate and endDate from ModuleOfferings.
# */

# /*
# NEED TO HANDLE TIMES !!!
# Could be as easy as string: e.g., "MW 10:00-11:35, F 10:00-10:40"
#   Multiple strings,  e.g., "MW 10:00-11:35", "F 10:00-10:40"
# Or something much more complicated that can actually be used to detect
# time conflicts.
# */

