-
-- Tables for information about students and their academic progress.
--


DROP TABLE IF EXISTS Student;

CREATE TABLE Student (
    studentID INT NOT NULL PRIMARY KEY,
    advisor INT NULL DEFAULT NULL,
    transcriptName VARCHAR ( 60 ),
    campusAddress VARCHAR ( 20 ),
    primaryLanguage VARCHAR ( 20 ),
    updated TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (studentID) REFERENCES Person (id) ON UPDATE CASCADE,
    FOREIGN KEY (advisor) REFERENCES Person (id) ON DELETE SET NULL
        ON UPDATE CASCADE,
    INDEX (advisor)
);

INSERT INTO `Student` VALUES (1511,NULL,'Tholley, Sallu',NULL,NULL,'2012-12-12 15:42:39'),(9338,NULL,'Tholley, Sallu',NULL,NULL,'2012-12-12 15:43:30'),(15038,NULL,'Passay, Wundu',NULL,NULL,'2012-12-12 15:41:34'),(15039,NULL,'Wai, Fomba Austin',NULL,NULL,'2012-12-12 15:43:06'),(15584,NULL,'Sheriff, Mohmond S.',NULL,NULL,'2012-12-12 15:44:04');

Insert INTO `Student` (studentID) VALUES
(16123), (16124), (16125), (16126), (16127), (16128), (16129), (16130), (16131), (16132), (16133), (16134), (16135), (16136), (16137), (16138), (16139), (16140);

DROP TABLE IF EXISTS Advising;

CREATE TABLE Advising (
    studentID INT NOT NULL,
    advisorID INT NOT NULL,
    advisorType ENUM('Primary', 'Secondary') NOT NULL,
    startDate DATE NOT NULL,
    endDate DATE,
    updated TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (studentID, advisorID, startDate),
    FOREIGN KEY (studentID) REFERENCES Student (studentID),
    FOREIGN KEY (advisorID) REFERENCES Person (id)
);

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
    term VARCHAR ( 15 ) NOT NULL,
    moduleID INT NOT NULL,
    section VARCHAR ( 3 ) NOT NULL DEFAULT '01',
    status ENUM('Enrolled', 'Canceled', 'Dropped', 'Withdrawn', 'Completed')
        NOT NULL,
    registDate DATE NOT NULL,
    endDate DATE,
    midtermGrade VARCHAR ( 3 ),
    submittedTermGrade VARCHAR ( 3 ),
    finalGrade VARCHAR ( 3 ),
    updated TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (studentID) REFERENCES Student (studentID),
    FOREIGN KEY (term, moduleID, section)
        REFERENCES ModuleOfferings (term, moduleID, section),
    INDEX (studentID),
    INDEX (term),
    INDEX (moduleID)
);

DROP FUNCTION IF EXISTS TermCensusDate;
DROP FUNCTION IF EXISTS ModOfferingEndDate;
DROP PROCEDURE IF EXISTS CancelStudentReg;

DELIMITER //
CREATE FUNCTION TermCensusDate(inTerm VARCHAR(10))
RETURNS DATE DETERMINISTIC
COMMENT 'Returns the census date for the specified term'
BEGIN
DECLARE
    retValue  DATE;
BEGIN
    SELECT censusDate INTO retValue FROM Terms WHERE `term` = inTerm;
    RETURN retValue;
END;
END; //

CREATE FUNCTION ModOfferingEndDate(inTerm VARCHAR(10), inModID INT,
                                   inSect VARCHAR(3))
RETURNS DATE DETERMINISTIC
COMMENT 'Returns the end date for the offering specified by the tri-part key'
BEGIN
DECLARE
    modOffEndDate  DATE;
BEGIN
    SELECT endDate INTO modOffEndDate FROM ModuleOfferings
        WHERE `term` = inTerm AND `moduleID` = inModID AND `section` = inSect;
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
                IF (NEW.endDate < TermCensusDate(NEW.term),
                    'Dropped',  -- End date is before census date
                    IF (NEW.endDate < ModOfferingEndDate(NEW.term, NEW.moduleID,
                                                         NEW.section),
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
                IF (NEW.endDate < TermCensusDate(NEW.term),
                    'Dropped',  -- End date is before census date
                    IF (NEW.endDate < ModOfferingEndDate(NEW.term, NEW.moduleID,
                                                         NEW.section),
                        'Withdrawn',    -- End date is before module end date
                        'Completed')),  -- End date is same or after mod end
                'Enrolled'),    -- End date is NULL or 0
            NEW.status);    -- Status is not 'Enrolled'
  END; //
DELIMITER ;

/*  NOT TESTED YET !!!
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

INSERT INTO `Enrollment` VALUES (1,15038,'2011-12 Sem 1',75,'01','Completed','2011-09-01','2012-12-12',NULL,NULL,'C','2012-12-12 22:04:39'),(2,15038,'2011-12 Sem 1',73,'01','Completed','2011-09-01','2012-12-12',NULL,NULL,'C','2012-12-12 22:09:26'),(3,15038,'2011-12 Sem 2',76,'01','Completed','2012-04-01','2012-06-15','','','B','2012-12-12 22:09:26'),(4,15039,'2011-12 Sem 1',75,'01','Completed','2011-09-01','2012-12-12','','','B','2012-12-12 22:04:39'),(5,15039,'2011-12 Sem 1',73,'01','Completed','2011-09-01','2012-12-12',NULL,NULL,'C','2012-12-12 22:09:26'),(6,15039,'2011-12 Sem 2',76,'01','Completed','2012-04-01','2012-06-15',NULL,NULL,'B','2012-12-12 22:09:26'),(7,9338,'2011-12 Sem 1',75,'01','Completed','2011-09-01','2012-12-12',NULL,NULL,'C','2012-12-12 22:04:39'),(8,9338,'2011-12 Sem 1',73,'01','Completed','2011-09-01','2012-12-12',NULL,NULL,'C','2012-12-12 22:09:26'),(9,9338,'2011-12 Sem 2',76,'01','Completed','2012-04-01','2012-06-15',NULL,NULL,'F','2012-12-12 22:09:26'),(10,9338,'2011-12 Sem 1',74,'01','Completed','2011-09-01','2012-02-15',NULL,NULL,'B','2012-12-12 22:09:26');

Insert INTO `Enrollment` (studentID, term, moduleID, status, registDate, endDate, finalGrade) VALUES
(16123, '2011-12 Sem 1', 75, 'Completed', '2011-09-01', '2012-02-12', 'C')
, (16124, '2011-12 Sem 1', 75, 'Completed', '2011-09-01', '2012-02-12', 'C')
, (16125, '2011-12 Sem 1', 75, 'Completed', '2011-09-01', '2012-02-12', 'A')
, (16126, '2011-12 Sem 1', 75, 'Completed', '2011-09-01', '2012-02-12', 'C')
, (16127, '2011-12 Sem 1', 75, 'Completed', '2011-09-01', '2012-02-12', 'B')
, (16128, '2011-12 Sem 1', 75, 'Completed', '2011-09-01', '2012-02-12', 'C')
, (16129, '2011-12 Sem 1', 75, 'Completed', '2011-09-01', '2012-02-12', 'E')
, (16130, '2011-12 Sem 1', 75, 'Completed', '2011-09-01', '2012-02-12', 'C')
, (16131, '2011-12 Sem 1', 75, 'Completed', '2011-09-01', '2012-02-12', 'D')
, (16132, '2011-12 Sem 1', 75, 'Completed', '2011-09-01', '2012-02-12', 'C')
, (16133, '2011-12 Sem 1', 75, 'Completed', '2011-09-01', '2012-02-12', 'D')
, (16134, '2011-12 Sem 1', 75, 'Completed', '2011-09-01', '2012-02-12', 'C')
, (16135, '2011-12 Sem 1', 75, 'Completed', '2011-09-01', '2012-02-12', 'B')
, (16136, '2011-12 Sem 1', 75, 'Completed', '2011-09-01', '2012-02-12', 'B')
, (16137, '2011-12 Sem 1', 75, 'Completed', '2011-09-01', '2012-02-12', 'C')
, (16138, '2011-12 Sem 1', 75, 'Completed', '2011-09-01', '2012-02-12', 'C')
, (16139, '2011-12 Sem 1', 75, 'Completed', '2011-09-01', '2012-02-12', 'A')
, (16140, '2011-12 Sem 1', 75, 'Completed', '2011-09-01', '2012-02-12', 'C');


DROP TABLE IF EXISTS TermStanding;

CREATE TABLE TermStanding (
    pk_id INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
    studentID INT NOT NULL,
    term VARCHAR( 15 ) NOT NULL,
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

DROP TABLE IF EXISTS TestScores;

CREATE TABLE TestScores (
    pk_id INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
    studentID INT NOT NULL,
    date_taken DATE,
    title VARCHAR ( 30 ) NOT NULL,
    category ENUM('Entrance Exam', 'Comprehensive Exam',
        'OTHER') NOT NULL,
    testing_agency VARCHAR ( 50 ),
    score DOUBLE NOT NULL,
    percentile INT,
    equivalency VARCHAR ( 50 ),
    comments VARCHAR ( 100 )
);

