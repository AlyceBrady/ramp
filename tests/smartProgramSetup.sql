DROP TABLE IF EXISTS AcadProgram;

CREATE TABLE AcadProgram (
    programID INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
    title VARCHAR ( 30 ) NOT NULL,
    type ENUM('Coursework', 'B.A.', 'B.S.', 'M.Sc.', 'Ph.D.', 'Major', 'Minor')
        NOT NULL DEFAULT 'Coursework',
    school VARCHAR ( 30 ) NOT NULL,
    division VARCHAR ( 30 ),
    department VARCHAR ( 30 ),
    startDate DATE NOT NULL,
    endDate DATE,
    updated TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP
);

INSERT INTO AcadProgram (programID, title, type,
    school, department, startDate, endDate)
VALUES
(1, "Bachelor of Arts", "B.A.", "K", DEFAULT, "1833-09-01", NULL)
, (10, "Computer Science", "Major", "K", "Math/CS", "1979-09-01", NULL)
, (11, "Computer Science", "Minor", "K", "Math/CS", "1979-09-01", NULL)
, (20, "Literature", "Major", "K", "Literature/Writing", "1950-09-01", NULL)
, (21, "Literature", "Minor", "K", "Literature/Writing", "1970-09-01", NULL)
, (22, "Creative Writing", "Major", "K", "Literature/Writing", "1950-09-01", NULL)
, (23, "Creative Writing", "Minor", "K", "Literature/Writing", "1970-09-01", NULL)
, (05, "Rhetoric", "Major", "K", "Literature/Writing", "1870-09-01", "1950-09-01")
, (06, "Mathematics", "Major", "K", "Mathematics", "1870-09-01", "1979-09-01")
, (07, "Mathematics", "Minor", "K", "Mathematics", "1970-09-01", "1979-09-01")
, (30, "Mathematics", "Major", "K", "Math/CS", "1979-09-01", NULL)
, (31, "Mathematics", "Minor", "K", "Math/CS", "1979-09-01", NULL)
;

DROP TABLE IF EXISTS Requirements;

CREATE TABLE Requirements (
    requirementID INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
    requirementName VARCHAR ( 30 ) NOT NULL,
    parentRequirementID INT NULL,
    startDate DATE NOT NULL,
    endDate DATE,
    updated TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (parentRequirementID) REFERENCES Requirements (requirementID)
        ON UPDATE CASCADE ON DELETE RESTRICT,
);

DROP TABLE IF EXISTS ProgramRequirements;

CREATE TABLE ProgramRequirements (
    programID INT NOT NULL,
    requirementID INT NOT NULL,
    startDate DATE NOT NULL,
    endDate DATE,
    updated TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (programID, requirementID, startDate)
    FOREIGN KEY (programID) REFERENCES Program (programID),
    FOREIGN KEY (requirementID) REFERENCES Requirements (requirementID)
);

# /*
# Start date is part of primary key in case of a rare situation
# where a program is associated with requirement set 1 for a while,
# then with requirement set 2, then back with requirement set 1.
# Program requirements should only change at clean boundaries,
# e.g., the start of an academic year.
# A program should only have one active requirement set at any one
# time.  There might be different students complete different
# requirements, based on the requirement set that was active when
# they started the program, but there should be no ambiguity about
# the requirements associated with a particular program at a particular
# date.
# */
