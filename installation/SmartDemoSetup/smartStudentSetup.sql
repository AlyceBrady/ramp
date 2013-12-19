--
-- Tables for information about students and their academic progress.
--


USE `smart_dev`;

-- Before dropping Student, need to drop table(s) that depend on it.
SOURCE dropSmartStudentDependencies.sql

DROP TABLE IF EXISTS Student;

CREATE TABLE Student (
    studentID INT NOT NULL PRIMARY KEY,
    advisorID INT NULL DEFAULT NULL,
    transcriptName VARCHAR ( 60 ),
    campusAddress VARCHAR ( 20 ),
    primaryLanguage VARCHAR ( 20 ),
    updated TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (studentID) REFERENCES Person (id) ON UPDATE CASCADE,
    FOREIGN KEY (advisorID) REFERENCES Person (id) ON DELETE SET NULL
        ON UPDATE CASCADE,
    INDEX (advisorID)
);

INSERT INTO Student (studentID, advisorID, transcriptName, campusAddress,
    primaryLanguage)
VALUES
(8, 1, 'Charles Brown', 'MU 248', 'English')
, (9, DEFAULT, DEFAULT, 'Hicks 1234', 'English')
, (11, DEFAULT, DEFAULT, 'Hicks 789', 'Bossy English')
, (12, DEFAULT, DEFAULT, 'Hicks 1212', 'English')
, (13, DEFAULT, DEFAULT, 'Hicks 1313', 'English')
, (14, DEFAULT, DEFAULT, 'Hicks 1414', 'English')
, (15, DEFAULT, DEFAULT, 'Hicks 1515', 'English')
, (16, DEFAULT, DEFAULT, 'Hicks 1616', 'English')
, (17, DEFAULT, DEFAULT, 'Hicks 1717', 'English')
, (18, DEFAULT, DEFAULT, 'Hicks 1818', 'English')
, (19, DEFAULT, DEFAULT, 'Hicks 1919', 'English')
, (20, DEFAULT, DEFAULT, 'Hicks 2020', 'English')
, (21, DEFAULT, DEFAULT, 'Hicks 2121', 'English')
, (22, DEFAULT, DEFAULT, 'Hicks 2222', 'English')
, (23, DEFAULT, DEFAULT, 'Hicks 2323', 'English')
;

DROP TABLE IF EXISTS Advising;

CREATE TABLE Advising (
    studentID INT NOT NULL,
    advisorID INT NOT NULL,
    advisorType ENUM('Primary', 'Coach', 'Posse', 'K Guide') NOT NULL,
    startDate DATE NOT NULL,
    endDate DATE,
    updated TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (studentID, advisorID, startDate),
    FOREIGN KEY (studentID) REFERENCES Student (studentID),
    FOREIGN KEY (advisorID) REFERENCES Person (id)
);

INSERT INTO Advising (studentID, advisorID, advisorType, startDate, endDate)
VALUES
(8, 5, 'Primary', '2008-09-15', '2010-8-15')
, (8, 4, 'Coach', '2008-09-15', DEFAULT)
, (8, 1, 'Primary', '2010-08-16', DEFAULT)
, (11, 1, 'Primary', '2012-01-01', DEFAULT)
, (12, 1, 'Primary', '2011-01-01', DEFAULT)
, (13, 3, 'Primary', '2011-01-01', DEFAULT)
, (14, 4, 'Primary', '2011-01-01', DEFAULT)
, (15, 1, 'Primary', '2011-01-01', DEFAULT)
, (16, 3, 'Primary', '2011-01-01', DEFAULT)
, (17, 3, 'Primary', '2011-01-01', DEFAULT)
, (18, 3, 'Primary', '2011-01-01', DEFAULT)
, (19, 4, 'Primary', '2011-01-01', DEFAULT)
, (20, 4, 'Primary', '2011-01-01', DEFAULT)
, (21, 4, 'Primary', '2011-01-01', DEFAULT)
, (22, 1, 'Primary', '2011-01-01', DEFAULT)
, (23, 1, 'Primary', '2011-01-01', DEFAULT)
;

DROP TRIGGER IF EXISTS SAPstatusAndDateCheck_insert;
DROP TRIGGER IF EXISTS SAPstatusAndDateCheck_update;
DROP TABLE IF EXISTS StudentAcadProgram;

CREATE TABLE StudentAcadProgram (
    pk_id INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
    studentID INT NOT NULL,
    programID INT NOT NULL,
    title VARCHAR ( 30 ) NOT NULL,
    type ENUM('Coursework', 'B.A.', 'B.S.', 'M.Sc.', 'Ph.D.', 'Major', 'Minor')
        NOT NULL DEFAULT 'Coursework',
    -- requirementSet INT NOT NULL,
    parentProgramID INT NULL,
    status ENUM('Preparatory', 'Active', 'Withdrawn', 'Ended',
        'Completed') NOT NULL,
    prepStartDate DATE NOT NULL,
    startDate DATE NOT NULL,
    anticipatedCompletionDate DATE,
    completionDate DATE,
    endDate DATE,
    classLevel ENUM('1st Yr', '2nd Yr', '3rd Yr', '4th Yr', '5th Year',
        'Longterm') NOT NULL,
    updated TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (parentProgramID) REFERENCES StudentAcadProgram (pk_id)
        ON UPDATE CASCADE ON DELETE RESTRICT
);

DELIMITER //
CREATE TRIGGER SAPstatusAndDateCheck_insert BEFORE INSERT ON StudentAcadProgram
  FOR EACH ROW BEGIN
    SET NEW.status = IF (NEW.completionDate IS NOT NULL AND
                         NEW.completionDate <> 0, 'Completed', 
                        IF (NEW.status = 'Ended', 'Ended',
                        IF (NEW.endDate IS NOT NULL AND
                         NEW.endDate <> 0, 'Withdrawn',
                        IF (NOW() > NEW.startDate, 'Active', NEW.status))));
    SET NEW.endDate = IF (NEW.completionDate IS NOT NULL,
                                NEW.completionDate, NEW.endDate);
  END; //
CREATE TRIGGER SAPstatusAndDateCheck_update BEFORE UPDATE ON StudentAcadProgram
  FOR EACH ROW BEGIN
    SET NEW.status = IF (NEW.completionDate IS NOT NULL AND
                         NEW.completionDate <> 0, 'Completed', 
                        IF (NEW.status = 'Ended', 'Ended',
                        IF (NEW.endDate IS NOT NULL AND
                         NEW.endDate <> 0, 'Withdrawn',
                        IF (NOW() > NEW.startDate, 'Active', NEW.status))));
    SET NEW.endDate = IF (NEW.completionDate IS NOT NULL,
                                NEW.completionDate, NEW.endDate);
  END; //
DELIMITER ;


INSERT INTO StudentAcadProgram (pk_id, studentID, programID, title, type,
    parentProgramID, status, prepStartDate, startDate,
    anticipatedCompletionDate, endDate, classLevel)
VALUES
(1, 8, 1, "Bachelor of Arts", "B.A.", NULL, 'Active',
    '2011-06-01', '2011-09-01', '2015-06-15', NULL, '1st Yr')
, (2, 11, 1, "Bachelor of Arts", "B.A.", NULL, 'Active',
    '2011-06-01', '2011-09-01', '2015-06-15', NULL, '1st Yr')
, (3, 12, 1, "Bachelor of Arts", "B.A.", NULL, 'Active',
    '2008-06-01', '2008-09-01', '2012-06-15', NULL, '4th Yr')
, (4, 13, 1, "Bachelor of Arts", "B.A.", NULL, 'Active',
    '2009-06-01', '2009-09-01', '2013-06-15', NULL, '3rd Yr')
, (5, 14, 1, "Bachelor of Arts", "B.A.", NULL, 'Withdrawn',
    '2010-06-01', '2010-09-01', '2014-06-15', '2012-01-31', '2nd Yr')
, (6, 15, 1, "Bachelor of Arts", "B.A.", NULL, 'Active',
    '2008-06-01', '2008-09-01', '2012-06-15', NULL, '4th Yr')
, (7, 16, 1, "Bachelor of Arts", "B.A.", NULL, 'Active',
    '2008-06-01', '2008-09-01', '2012-06-15', NULL, '4th Yr')
, (8, 17, 1, "Bachelor of Arts", "B.A.", NULL, 'Active',
    '2010-06-01', '2010-09-01', '2014-06-15', NULL, '2nd Yr')
, (9, 18, 1, "Bachelor of Arts", "B.A.", NULL, 'Active',
    '2011-06-01', '2011-09-01', '2015-06-15', NULL, '1st Yr')
, (10, 19, 1, "Bachelor of Arts", "B.A.", NULL, 'Active',
    '2011-06-01', '2011-09-01', '2015-06-15', NULL, '1st Yr')
, (11, 20, 1, "Bachelor of Arts", "B.A.", NULL, 'Active',
    '2009-06-01', '2009-09-01', '2013-06-15', NULL, '3rd Yr')
, (12, 21, 1, "Bachelor of Arts", "B.A.", NULL, 'Active',
    '2011-06-01', '2011-09-01', '2015-06-15', NULL, '1st Yr')
, (13, 22, 1, "Bachelor of Arts", "B.A.", NULL, 'Active',
    '2010-06-01', '2010-09-01', '2014-06-15', NULL, '2nd Yr')
, (14, 23, 1, "Bachelor of Arts", "B.A.", NULL, 'Active',
    '2009-06-01', '2009-09-01', '2013-06-15', NULL, '3rd Yr')
, (15, 12, 20, "Literature", "Major", 3, 'Active',
    '2010-06-01', '2010-06-01', '2012-06-15', NULL, '4th Yr')
, (16, 16, 20, "Literature", "Major", 7, 'Active',
    '2010-06-01', '2010-06-01', '2012-06-15', NULL, '4th Yr')
, (17, 16, 11, "Computer Science", "Minor", 7, 'Active',
    '2010-06-01', '2010-06-01', '2012-06-15', NULL, '4th Yr')
, (18, 15, 10, "Computer Science", "Major", 6, 'Active',
    '2010-06-01', '2010-06-01', '2012-06-15', NULL, '4th Yr')
, (19, 15, 20, "Literature", "Major", 6, 'Active',
    '2010-06-01', '2010-06-01', '2012-06-15', NULL, '4th Yr')
, (20, 15, 31, "Mathematics", "Minor", 6, 'Active',
    '2010-06-01', '2010-06-01', '2012-06-15', NULL, '4th Yr')
, (21, 13, 30, "Mathematics", "Major", 4, 'Active',
    '2011-06-01', '2011-06-01', '2013-06-15', NULL, '3rd Yr')
, (22, 20, 10, "Computer Science", "Major", 11, 'Active',
    '2011-06-01', '2011-06-01', '2013-06-15', NULL, '3rd Yr')
, (23, 20, 21, "Literature", "Minor", 11, 'Active',
    '2011-06-01', '2011-06-01', '2013-06-15', NULL, '3rd Yr')
, (24, 23, 20, "Literature", "Major", 14, 'Active',
    '2011-06-01', '2011-06-01', '2013-06-15', NULL, '3rd Yr')
;

# /*
# CAREFUL!!!  If a parent program ID is provided, then student ID
# is redundant; have to be careful to keep these two things synchronized.
# (Also anticipated completion date and class year, at least at K.)
# */

# /*
# Initialize program title and type from AcadProgram.
# Initialize requirement set from ProgramRequirements table.
# Important to specify both title and requirement set here, because
# majors can change names and requirements.
# Initialize school, division, and department from AcadProgram?
# (Do we really need to
# capture school, division, and department here?  Do we need a record of
# what they were when the student was enrolled in the program? Or can
# we just import them from the current program information with a join?)
# */

# /*
# Is there any chance that a student might be following a program
# that is being used to satisfy multiple parent programs?  E.g., a
# major or minor counting towards a degree and a certificate?  If so,
# need to drop parentProgramID field and either make StudentAcadProgram
# be a container class, or create a program/subprogram relationship
# table.
# */

# /*
# There should be a requirement set defined for every program.
# Programs could be: a course of study, a degree, a major, a minor
# A student might be signed up for multiple independent programs and maybe
# degrees or credential can be awarded as each program is finished.
# A student can also be signed up for one or more programs that
# serve as sub-programs for an over-arching program, e.g., the K undergrad
# program requires a general education and a major, and could also
# have other sub-programs, such as additional major(s), minor(s).
# */

DROP TABLE IF EXISTS StudentLeaves;

CREATE TABLE StudentLeaves (
    pk_id INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
    studentID INT NOT NULL,
    type ENUM('Withdrawn', 'Medical Leave', 'Study Away',
        'Suspended', 'Expelled', 'Dismissed') NOT NULL,
    comment VARCHAR ( 100 ) NOT NULL,
    startDate DATE NOT NULL,
    anticipatedEndDate DATE,
    endDate DATE,
    prepStartDate DATE,
    updated TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP
);

DROP TABLE IF EXISTS StudentAnnotations;

CREATE TABLE StudentAnnotations (
    pk_id INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
    studentID INT NOT NULL,
    office ENUM('Dean', 'Registrar', 'Advisor',
        'Vice Chancellor') NOT NULL,
    annotationType ENUM('Disciplinary', 'Policy Exception', 'Achievement',
        'Suspended', 'Expelled', 'Dismissed') NOT NULL,
    annotation VARCHAR ( 100 ) NOT NULL,
    date DATE NOT NULL,
    updated TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP
);

DROP TRIGGER IF EXISTS EnrollmentStatus_Insert;
DROP TRIGGER IF EXISTS EnrollmentStatus_Update;
DROP TABLE IF EXISTS Enrollment;

CREATE TABLE Enrollment (
    pk_id INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
    studentID INT NOT NULL,
    modOfferingID INT NOT NULL,
    status ENUM('Enrolled', 'Canceled', 'Dropped', 'Withdrawn', 'Completed')
        NOT NULL DEFAULT 'Enrolled',
    registDate DATE,
    endDate DATE,
    midtermGrade VARCHAR ( 3 ),
    submittedTermGrade VARCHAR ( 3 ),
    finalGrade VARCHAR ( 3 ),
    updated TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (studentID) REFERENCES Student (studentID),
    FOREIGN KEY (modOfferingID) REFERENCES ModuleOfferings (pk_id),
    INDEX (studentID),
    INDEX (modOfferingID)
);

DROP FUNCTION IF EXISTS TermCensusDate;
DROP FUNCTION IF EXISTS ModOfferingEndDate;
DROP PROCEDURE IF EXISTS CancelStudentReg;

DELIMITER //
CREATE FUNCTION TermCensusDate(inOffering INT)
RETURNS DATE DETERMINISTIC
COMMENT "Returns the census date for the module offering's term"
BEGIN
DECLARE
    retValue  DATE;
BEGIN
    SELECT term INTO @modTerm FROM ModuleOfferings WHERE `pk_id` = inOffering;
    SELECT censusDate INTO retValue FROM Terms WHERE `term` = @modTerm;
    RETURN retValue;
END;
END; //

CREATE FUNCTION ModOfferingEndDate(inOffering INT)
RETURNS DATE DETERMINISTIC
COMMENT 'Returns the end date for the offering specified by the tri-part key'
BEGIN
DECLARE
    modOffEndDate  DATE;
BEGIN
    SELECT endDate INTO modOffEndDate FROM ModuleOfferings
        WHERE `pk_id` = inOffering;
    RETURN modOffEndDate;
END;
END; //

DELIMITER ;

DELIMITER //

CREATE TRIGGER EnrollmentStatus_Insert BEFORE INSERT ON Enrollment
  FOR EACH ROW BEGIN
    SET NEW.endDate = IF (NEW.finalGrade IS NOT NULL AND NEW.endDate IS NULL,
                                NOW(), NEW.endDate);
    SET NEW.status =
        IF ( NEW.status = 'Enrolled',
            IF ( NEW.endDate IS NOT NULL AND NEW.endDate <> 0,
                IF (NEW.endDate < TermCensusDate(NEW.modOfferingID),
                    'Dropped',  -- End date is before census date
                    IF (NEW.endDate < ModOfferingEndDate(NEW.modOfferingID),
                        'Withdrawn',    -- End date is before module end date
                        'Completed')),  -- End date is same or after mod end
                'Enrolled'),    -- End date is NULL or 0
            NEW.status);    -- Status is not 'Enrolled'
  END; //
CREATE TRIGGER EnrollmentStatus_Update BEFORE UPDATE ON Enrollment
  FOR EACH ROW BEGIN
    SET NEW.endDate = IF (NEW.finalGrade IS NOT NULL AND NEW.endDate IS NULL,
                                NOW(), NEW.endDate);
    SET NEW.status =
        IF ( NEW.status = 'Enrolled',
            IF ( NEW.endDate IS NOT NULL AND NEW.endDate <> 0,
                IF (NEW.endDate < TermCensusDate(NEW.modOfferingID),
                    'Dropped',  -- End date is before census date
                    IF (NEW.endDate < ModOfferingEndDate(NEW.modOfferingID),
                        'Withdrawn',    -- End date is before module end date
                        'Completed')),  -- End date is same or after mod end
                'Enrolled'),    -- End date is NULL or 0
            NEW.status);    -- Status is not 'Enrolled'
  END; //
DELIMITER ;

/*  NOT TESTED YET !!!  Nor is it updated after change in primary key.
DELIMITER //

CREATE PROCEDURE CancelStudentReg (IN inTerm VARCHAR(10),
                                   IN inModuleID INT, IN inSection VARCHAR(3))
COMMENT 'Cancels the registration for every student enrolled in the
specified module offering'
BEGIN
    UPDATE Enrollment SET status = 'Cancelled', endDate = NOW()
        WHERE term = inTerm AND moduleID = inModuleID AND section = inSection
        AND status = 'Enrolled';
END; //

DELIMITER ;
*/

# /* Status:  (Note: status is dependant on module offering dates/status.)
# If offering status is canceled and end date is offering end date: Canceled.
#   (trigger on module offering: if offering canceled, cancel student reg)
# If end date not null and before offering's: Dropped.
# If end date not null and before offering's end date: Withdrawn.
# If end date not null and equal to (or after) offering's end date: Completed.
# Else: Enrolled.
# */


INSERT INTO Enrollment (studentID, modOfferingID, status, 
    registDate, endDate, finalGrade)
VALUES
(8, 6, 'Completed', '2011-08-10', '2011-12-15', 'B')
, (8, 1, 'Completed', '2011-08-10', '2011-12-15', 'B+')
, (8, 2, 'Completed', '2011-08-10', DEFAULT, 'C')
, (8, 4, DEFAULT, '2011-08-10', DEFAULT, 'A')
, (8, 8, 'Enrolled', '2011-12-01', DEFAULT, DEFAULT)
, (8, 11, DEFAULT, '2011-12-01', '2012-01-02', DEFAULT)
, (8, 12, DEFAULT, '2012-01-02', DEFAULT, DEFAULT)
, (8, 13, 'Enrolled', '2011-12-01', DEFAULT, DEFAULT)
, (8, 14, 'Enrolled', '2011-12-01', DEFAULT, DEFAULT)
, (8, 18, 'Enrolled', '2012-03-01', DEFAULT, DEFAULT)
, (8, 20, 'Enrolled', '2012-03-01', DEFAULT, DEFAULT)
, (8, 22, 'Enrolled', '2012-03-01', DEFAULT, DEFAULT)
, (8, 23, 'Enrolled', '2012-03-01', DEFAULT, DEFAULT)
, (11, 16, 'Enrolled', '2011-12-01', DEFAULT, DEFAULT)
, (11, 15, 'Enrolled', '2011-12-01', DEFAULT, DEFAULT)
, (11, 13, 'Enrolled', '2011-12-01', DEFAULT, DEFAULT)
, (11, 11, 'Enrolled', '2011-12-01', DEFAULT, DEFAULT)
, (11, 19, 'Enrolled', '2012-03-01', DEFAULT, DEFAULT)
, (11, 17, 'Enrolled', '2012-03-01', DEFAULT, DEFAULT)
, (11, 22, 'Enrolled', '2012-03-01', DEFAULT, DEFAULT)
, (11, 21, 'Enrolled', '2012-03-01', DEFAULT, DEFAULT)
, (11, 21, 'Enrolled', '2012-03-01', DEFAULT, DEFAULT)
, (12, 5, DEFAULT, '2011-08-10', '2011-12-15', 'A')
, (13, 5, 'Completed', '2011-08-10', '2011-12-15', 'D')
, (14, 5, 'Completed', '2011-08-10', '2011-12-15', 'C')
, (15, 5, 'Completed', '2011-08-10', '2011-12-15', 'B')
, (16, 5, 'Completed', '2011-08-10', '2011-12-15', 'A')
, (17, 5, 'Completed', '2011-08-10', '2011-12-15', 'B')
, (18, 5, 'Completed', '2011-08-10', '2011-12-15', 'C')
, (19, 5, 'Completed', '2011-08-10', '2011-12-15', 'A')
, (20, 5, 'Completed', '2011-08-10', '2011-12-15', 'B')
, (21, 5, 'Completed', '2011-08-10', '2011-12-15', 'C')
, (22, 5, 'Completed', '2011-08-10', '2011-12-15', 'A')
, (23, 5, 'Completed', '2011-08-10', '2011-12-15', 'B')
;

# /*
# Expected Results:
#   End date for row 3 should be set to today.
#   End date for row 4 should be set to day, status should be 'Completed'
#   Grade and end date for row 5 should be null
#   Status for row 6 should be 'Dropped'; grade should be null
#   Status for row 7 should be 'Enrolled'; grade & end date should be null
#   Status for course 35 for student 12 (E. Bennet) should be 'Completed'
# */


DROP TABLE IF EXISTS TermStanding;

CREATE TABLE TermStanding (
    pk_id INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
    studentID INT NOT NULL,
    term VARCHAR( 10 ) NOT NULL,
    standing ENUM('GOOD', "DEAN'S LIST", 'PROBATION', 'FINAL PROBATION',
        'OTHER') NOT NULL DEFAULT 'GOOD',
    creditsAttempted DOUBLE,
    creditsEarned DOUBLE,
    termGPA DECIMAL(6,3),
    cumulativeCreditsAttempted DOUBLE,
    cumulativeCreditsEarned DOUBLE,
    cumulativeGPA DECIMAL(6,3),
    comment VARCHAR ( 100 ),
    updated TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (studentID) REFERENCES Student (studentID),
    INDEX (studentID),
    INDEX (term),
    INDEX (standing)
);

INSERT INTO TermStanding (studentID, term, standing, creditsAttempted,
    creditsEarned, termGPA, cumulativeCreditsAttempted,
    cumulativeCreditsEarned, cumulativeGPA)
VALUES
(8, '2011Q4', 'PROBATION', 3.0, 3.0, 2.0, 3.0, 3.0, 2.0)
, (8, '2012Q1', 'GOOD', 3.6, DEFAULT, DEFAULT, 6.6, 3.0, 2.0)
, (11, '2012Q1', DEFAULT, 3.8, DEFAULT, DEFAULT, DEFAULT, DEFAULT, DEFAULT)
;

DROP TABLE IF EXISTS TestScores;

CREATE TABLE TestScores (
    pk_id INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
    studentID INT NOT NULL,
    date_taken DATE,
    title VARCHAR ( 30 ) NOT NULL,
    category VARCHAR ( 6 ),
    testing_agency VARCHAR ( 50 ),
    score DOUBLE NOT NULL,
    percentile INT,
    equivalency VARCHAR ( 50 ),
    comments VARCHAR ( 100 )
);

INSERT INTO TestScores (studentID, date_taken, title, category,
    testing_agency, score, percentile, equivalency)
VALUES
(8, '2011-06-15', 'Entrance Exam', 'WAEC', 'West Africa Exam Agency', '120.5', '80', NULL)
, (11, '2011-09-15', 'Entrance Exam', 'WAEC', 'West Africa Exam Agency', '142.5', '95', NULL)
, (11, '2011-09-15', 'AP Calculus BC', 'AP', 'West Africa Exam Agency', '5', '99', 'MATH 113')
;

