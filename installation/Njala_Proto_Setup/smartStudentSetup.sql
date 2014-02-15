--
-- Tables for information about students and their academic progress.
--


USE `njala_proto`;

-- Drop triggers, functions, and procedures referring to tables defined
-- in this file.

DROP TRIGGER IF EXISTS CloseApplicant_Insert;
DROP TRIGGER IF EXISTS CloseApplicant_Update;

DROP TRIGGER IF EXISTS SAPstatusAndDateCheck_insert;
DROP TRIGGER IF EXISTS SAPstatusAndDateCheck_update;

DROP TRIGGER IF EXISTS CreateEnrollmentGradeRecord;

DROP TRIGGER IF EXISTS EndEnrollmentChangesStatus_Insert;
DROP TRIGGER IF EXISTS EndEnrollmentChangesStatus_Update;

DROP TRIGGER IF EXISTS AuthorizeGrade_insert;
DROP TRIGGER IF EXISTS AuthorizeGrade_update;

DROP FUNCTION IF EXISTS TermCensusDate;
DROP FUNCTION IF EXISTS ModOfferingEndDate;
DROP FUNCTION IF EXISTS TermCensusDate;
DROP FUNCTION IF EXISTS CalcCumHrs;
DROP FUNCTION IF EXISTS CalcCumPts;
DROP FUNCTION IF EXISTS GradePts;

DROP PROCEDURE IF EXISTS CancelStudentReg;
DROP PROCEDURE IF EXISTS UpdateSessionStanding;
DROP PROCEDURE IF EXISTS UpdateAllSessionStandings;

-- Before dropping Student, need to drop table(s) that depend on it.
SOURCE dropSmartStudentDependencies.sql;

-- Drop other tables defined in this file.

DROP TABLE IF EXISTS AdmissTestCodes;
DROP TABLE IF EXISTS AdmissTestDescriptors;
DROP TABLE IF EXISTS AdmissExamNames;
DROP TABLE IF EXISTS ApplicationStatusCodes;
DROP TABLE IF EXISTS AdvisorTypes;
DROP TABLE IF EXISTS StudentProgramStatusCodes;
DROP TABLE IF EXISTS ClassLevelCodes;
DROP TABLE IF EXISTS StudentLeaveTypes;
DROP TABLE IF EXISTS AnnotationTypes;
DROP TABLE IF EXISTS AnnotationAuthorities;
DROP TABLE IF EXISTS StudentModStatusCodes;
DROP TABLE IF EXISTS TermStandingCodes;

DROP TABLE IF EXISTS Applicant;
DROP TABLE IF EXISTS Student;

CREATE Table AdmissTestCodes (
    pk_id INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
    testCode VARCHAR ( 20 ) NOT NULL,
    updated TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP
);

INSERT INTO AdmissTestCodes (testCode) VALUES
('Entrance Exam')
, ('WASSCE')
, ('GCE O')
, ('GCE A')
, ('OTHER')
;

CREATE Table AdmissTestDescriptors (
    pk_id INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
    descriptor VARCHAR ( 20 ),
    updated TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP
);

INSERT INTO AdmissTestDescriptors (descriptor) VALUES
(NULL)
, ('GOVT.')
, ('PR.')
, ('GOVT./PR.')
, ('PR./GOVT.')
;

CREATE Table AdmissExamNames (
    pk_id INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
    examName VARCHAR ( 20 ) NOT NULL,
    updated TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP
);

INSERT INTO AdmissExamNames (examName) VALUES
('AGRIC. SCIENCE')
, ('B. MGMT.')
, ('BIOLOGY')
, ('COMMERCE')
, ('COST ACCOUNTS')
, ('ECONOMICS')
, ('ENGLISH')
, ('ENGLISH LIT')
, ('FIN. ACCOUNTS')
, ('FRENCH')
, ('GEOGRAPHY')
, ('HEALTH SC.')
, ('MATHS')
, ('FUR. MATHS')
, ('STATISTICS')
, ('OTHER')
;

CREATE TABLE ApplicationStatusCodes (
    pk_id INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
    statusCode VARCHAR ( 15 ) NOT NULL,
    updated TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP
);

INSERT INTO ApplicationStatusCodes (statusCode) VALUES
('Pending')
, ('Accepted')
, ('WaitList')
, ('Rejected')
;

CREATE TABLE AdvisorTypes (
    pk_id INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
    advisorType VARCHAR ( 20 ) NOT NULL,
    updated TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP
);

INSERT INTO AdvisorTypes (advisorType) VALUES
('Primary')
, ('Secondary')
;

CREATE TABLE StudentProgramStatusCodes (
    pk_id INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
    pgmStatusCode VARCHAR ( 20 ) NOT NULL,
    updated TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP
);

INSERT INTO StudentProgramStatusCodes (pgmStatusCode) VALUES
('Preparatory')
, ('Active')
, ('Withdrawn')
, ('Ended')
, ('Completed')
;

CREATE TABLE ClassLevelCodes (
    pk_id INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
    classLevelCode VARCHAR ( 10 ) NOT NULL,
    updated TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP
);

INSERT INTO ClassLevelCodes (classLevelCode) VALUES
('1st Yr')
, ('2nd Yr')
, ('3rd Yr')
, ('4th Yr')
, ('5th Yr')
, ('Longterm')
;


CREATE TABLE StudentLeaveTypes (
    pk_id INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
    studentLeaveCode VARCHAR ( 20 ) NOT NULL,
    updated TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP
);

INSERT INTO StudentLeaveTypes (studentLeaveCode) VALUES
('Withdrawn')
, ('Medical Leave')
, ('Study Away')
, ('Suspended')
, ('Expelled')
, ('Dismissed')
;

CREATE TABLE AnnotationTypes (
    pk_id INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
    annotationCode VARCHAR ( 20 ) NOT NULL,
    updated TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP
);

INSERT INTO AnnotationTypes (annotationCode) VALUES
('Achievement')
, ('Policy Exception')
, ('TranscriptRemark')
, ('Disciplinary')
, ('Suspended')
, ('Expelled')
, ('Dismissed')
;

CREATE TABLE AnnotationAuthorities (
    pk_id INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
    authCode VARCHAR ( 20 ) NOT NULL,
    updated TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP
);

INSERT INTO AnnotationAuthorities (authCode) VALUES
('Dean')
, ('Registrar')
, ('Advisor')
, ('Vice Chancellor')
;

CREATE TABLE StudentModStatusCodes (
    pk_id INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
    modStatusCode VARCHAR ( 10 ) NOT NULL,
    updated TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP
);

INSERT INTO StudentModStatusCodes (modStatusCode) VALUES
('Enrolled')
-- , ('Dropped')
, ('Withdrawn')
, ('Completed')
, ('Canceled')
;

CREATE Table TermStandingCodes (
    pk_id INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
    termStandingCode VARCHAR ( 20 ) NOT NULL,
    updated TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP
);

INSERT INTO TermStandingCodes (termStandingCode) VALUES
('GOOD')
, ("DEAN'S LIST")
, ('PROBATION')
, ('FINAL PROBATION')
, ('OTHER')
;

CREATE TABLE Applicant (
    personID INT NOT NULL PRIMARY KEY ,
    primaryLanguage VARCHAR ( 20 ),
    chosenProgramID INT NOT NULL,
    secondaryProgramID INT,
    previousApplYears VARCHAR ( 40 ),
    applyingElsewhere VARCHAR ( 150 ),
    extraCurrics VARCHAR ( 150 ),
    interviewLoc VARCHAR ( 20 ),
    hodApproval VARCHAR ( 15 ) NOT NULL DEFAULT 'Pending',
    deanApproval VARCHAR ( 15 ) NOT NULL DEFAULT 'Pending',
    deanRemarks VARCHAR ( 75 ),
    aaApproval VARCHAR ( 15 ) NOT NULL DEFAULT 'Pending',
    status VARCHAR ( 15 ) NOT NULL DEFAULT 'Pending',
    received DATE DEFAULT NULL,
    closed DATE DEFAULT NULL,
    updated TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (personID) REFERENCES Person (personID) ON UPDATE CASCADE
);

DELIMITER //
CREATE TRIGGER CloseApplicant_Insert BEFORE INSERT ON Applicant
  FOR EACH ROW BEGIN
    SET NEW.received = IF ( ( isNull(NEW.received) AND NEW.status = 'Pending' ),
                     NOW(), NEW.received);
    SET NEW.closed = IF ( (NEW.status = 'Accepted' OR NEW.status = 'Denied')
                          AND NEW.closed IS NULL,
                     NOW(), NEW.closed);
  END; //
CREATE TRIGGER CloseApplicant_Update BEFORE UPDATE ON Applicant
  FOR EACH ROW BEGIN
    SET NEW.closed = IF ( (NEW.status = 'Accepted' OR NEW.status = 'Denied')
                          AND NEW.closed IS NULL,
                     NOW(), NEW.closed);
  END; //
DELIMITER ;

CREATE TABLE Student (
    studentID INT NOT NULL AUTO_INCREMENT PRIMARY Key,
    personID INT NOT NULL,
    advisorID INT NULL DEFAULT NULL,
    campusAddress VARCHAR ( 20 ),
    primaryLanguage VARCHAR ( 20 ),
    updated TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (personID) REFERENCES Person (personID) ON UPDATE CASCADE,
    FOREIGN KEY (advisorID) REFERENCES Person (personID) ON DELETE SET NULL
        ON UPDATE CASCADE,
    UNIQUE(personID),
    INDEX (advisorID)
);

CREATE TABLE AdmissExams (
    pk_id INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
    personID INT NOT NULL,
    date_taken DATE,
    type VARCHAR ( 15 ) NOT NULL,
    qualifier VARCHAR ( 15 ),
    examName VARCHAR ( 15 ) NOT NULL,
    score VARCHAR ( 5 ) NOT NULL,
    percentile INT,
    equivalency VARCHAR ( 50 ),
    comments VARCHAR ( 100 ),
    FOREIGN KEY (personID) REFERENCES Person (personID)
);

CREATE TABLE Advising (
    adviseeID INT NOT NULL,
    advisorID INT NOT NULL,
    advisorType VARCHAR ( 20 ) NOT NULL,
    startDate DATE NOT NULL,
    endDate DATE,
    updated TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (adviseeID, advisorID, startDate),
    FOREIGN KEY (adviseeID) REFERENCES Student (studentID),
    FOREIGN KEY (advisorID) REFERENCES Person (personID)
);

-- Initialize title, type, dept, and college from AcadProgram (keep a
-- historical copy here in case any of those change in the future).

CREATE TABLE StudentAcadProgram (
    pk_id INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
    studentID INT NOT NULL,
    programID INT NOT NULL,
    title VARCHAR ( 100 ) NOT NULL,
    shortTitle VARCHAR ( 38 ) NOT NULL,
    type VARCHAR ( 15 ) NOT NULL DEFAULT 'Coursework',
    schoolCode VARCHAR ( 8 ) DEFAULT NULL,
    deptCode VARCHAR ( 8 ) DEFAULT NULL,
    -- requirementSet INT NOT NULL,
    parentProgramID INT NULL,
    status VARCHAR ( 20 ) NOT NULL,
    prepStartDate DATE NOT NULL,
    startDate DATE NOT NULL,
    anticipatedCompletionDate DATE,
    completionDate DATE,
    endDate DATE,
    classLevel VARCHAR ( 10 ) NOT NULL,
    updated TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (studentID) REFERENCES Student (studentID),
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


CREATE TABLE StudentLeaves (
    pk_id INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
    studentID INT NOT NULL,
    type VARCHAR ( 20 ) NOT NULL,
    comment VARCHAR ( 100 ) NOT NULL,
    startDate DATE NOT NULL,
    anticipatedEndDate DATE,
    endDate DATE,
    prepStartDate DATE,
    updated TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (studentID) REFERENCES Student (studentID)
);


CREATE TABLE StudentAnnotations (
    pk_id INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
    studentID INT NOT NULL,
    authority VARCHAR ( 20 ) NOT NULL,
    annotationType VARCHAR ( 20 ) NOT NULL,
    annotation VARCHAR ( 100 ) NOT NULL,
    enteredBy VARCHAR ( 20 ) NOT NULL,
    date DATE NOT NULL,
    updated TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (studentID) REFERENCES Student (studentID)
);


CREATE TABLE Enrollment (
    pk_id INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
    studentID INT NOT NULL,
    modOfferingID INT NOT NULL,
    status VARCHAR ( 10 ) NOT NULL DEFAULT 'Enrolled',
    registDate DATE NOT NULL,
    endDate DATE,
    updated TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (studentID) REFERENCES Student (studentID),
    FOREIGN KEY (modOfferingID) REFERENCES ModuleOfferings (pk_id),
    INDEX (studentID),
    INDEX (modOfferingID)
);

CREATE TABLE CourseGrades (
    enrollmentID INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    continuousAssessment INT,
    examScore INT,
    calcFinalGrade VARCHAR ( 5 ),
--    continuousAssessment VARCHAR ( 3 ),
--    examScore VARCHAR ( 3 ),
--    submittedTermGrade VARCHAR ( 3 ),
    approved ENUM('F', 'T') NOT NULL DEFAULT 'F',
    authority VARCHAR ( 20 ),
--    authority VARCHAR ( 20 ) NOT NULL,
    transcriptGrade VARCHAR ( 5 ),
    updated TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (enrollmentID) REFERENCES Enrollment (pk_id),
    INDEX (enrollmentID)
);

/*
CREATE TABLE GradeApproval (
    enrollmentID INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    updated TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (enrollmentID) REFERENCES Enrollment (pk_id),
    INDEX (enrollmentID)
);
*/


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
COMMENT 'Returns the end date for the specified offering'
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

CREATE TRIGGER CreateEnrollmentGradeRecord AFTER INSERT ON Enrollment
  FOR EACH ROW BEGIN
    INSERT INTO `CourseGrades` (enrollmentID)
    VALUES (NEW.pk_id);
  END; //

CREATE TRIGGER EndEnrollmentChangesStatus_Insert BEFORE INSERT ON Enrollment
  FOR EACH ROW BEGIN
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
CREATE TRIGGER EndEnrollmentChangesStatus_Update BEFORE UPDATE ON Enrollment
  FOR EACH ROW BEGIN
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

CREATE TRIGGER GradeEndsEnrollment_Update AFTER UPDATE ON CourseGrades
  FOR EACH ROW BEGIN
    UPDATE Enrollment SET endDate = 
                IF (NEW.calcFinalGrade IS NOT NULL AND endDate IS NULL,
                    NOW(), endDate)
        WHERE pk_id = NEW.enrollmentID;
  END; //

DELIMITER ;

DELIMITER //
CREATE TRIGGER AuthorizeGrade_insert BEFORE INSERT ON CourseGrades
  FOR EACH ROW BEGIN
    SET NEW.transcriptGrade = IF (NEW.approved = 'T' AND
                                  NEW.transcriptGrade IS NULL,
                               NEW.calcFinalGrade,
                               NEW.transcriptGrade);
  END; //
CREATE TRIGGER AuthorizeGrade_update BEFORE UPDATE ON CourseGrades
  FOR EACH ROW BEGIN
    SET NEW.transcriptGrade = IF (NEW.approved = 'T' AND
                                  NEW.transcriptGrade IS NULL,
                               NEW.calcFinalGrade,
                               NEW.transcriptGrade);
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


CREATE TABLE CompExamScores (
    pk_id INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
    studentID INT NOT NULL,
    examName VARCHAR ( 30 ) NOT NULL,
    date_taken DATE DEFAULT NULL,
    grade VARCHAR ( 3 ) NOT NULL,
    FOREIGN KEY (studentID) REFERENCES Student (studentID)
);

CREATE TABLE TermStanding (
    pk_id INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
    studentID INT NOT NULL,
    term VARCHAR( 15 ) NOT NULL,
    standing VARCHAR ( 20 ) NOT NULL DEFAULT 'GOOD',
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

CREATE TABLE SessionStanding (
    pk_id INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
    studentID INT NOT NULL,
    acadYear VARCHAR( 10 ),
    sessionTotalHrs INT,
    sessionTotalPts INT,
    sessionGPA DECIMAL(6,3),
    cumTotalHrs INT,
    cumTotalPts INT,
    cumGPA DECIMAL(6,3),
    standing VARCHAR ( 20 ) NOT NULL DEFAULT 'GOOD',
    comment VARCHAR ( 100 ),
    updated TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (studentID) REFERENCES Student (studentID),
    INDEX (studentID),
    INDEX (acadYear),
    INDEX (standing)
);

DELIMITER //
CREATE FUNCTION CalcCumHrs(id INT)
RETURNS INT DETERMINISTIC
COMMENT "Computes the cumulative hours earned by the given student"
BEGIN
    SELECT SUM(sessionTotalHrs) INTO @cumHrs FROM SessionStanding
        WHERE studentID = id;
    RETURN IF (isNull(@cumHrs), 0, @cumHrs);
END; //
DELIMITER ;

DELIMITER //
CREATE FUNCTION CalcCumPts(id INT)
RETURNS INT DETERMINISTIC
COMMENT "Computes the cumulative points earned by the given student"
BEGIN
    SELECT SUM(sessionTotalPts) INTO @cumPts FROM SessionStanding
        WHERE studentID = id;
    RETURN IF (isNull(@cumPts), 0, @cumPts);
END; //
DELIMITER ;

DELIMITER //
CREATE FUNCTION GradePts(grade VARCHAR ( 5 ), hrs INT)
RETURNS INT DETERMINISTIC
COMMENT "Computes the points earned for the given grade (grade * hrs)"
BEGIN
    RETURN hrs *
            ( CASE grade WHEN 'A' THEN 5 WHEN 'B' THEN 4
                         WHEN 'C' THEN 3 WHEN 'D' THEN 2
                         WHEN 'D' THEN 1 ELSE 0 END );
END; //
DELIMITER ;

DELIMITER //
-- should use transcriptGrade, not calcFinalGrade
CREATE Procedure UpdateSessionStanding(in stuID INT, in sessID INT,
                                       in acadYear VARCHAR(10))
COMMENT "Calculates the latest session GPA and cumulative values for this student"
BEGIN
    -- Do calculations.
    SELECT SUM(ModuleOfferings.creditHours),
           SUM(GradePts(CourseGrades.calcFinalGrade,
                        ModuleOfferings.creditHours))
        INTO @totalHrs, @totalPts
        FROM CourseGrades
        LEFT JOIN Enrollment
                ON CourseGrades.enrollmentID = Enrollment.pk_id
        LEFT JOIN ModuleOfferings
                ON Enrollment.modOfferingID=ModuleOfferings.pk_id
        LEFT JOIN Terms ON ModuleOfferings.term = Terms.term
        WHERE Enrollment.studentID=stuID AND Terms.acadYear=acadYear;
    SET @sessGPA = IF ( ( NOT isNULL(@totalHrs) AND @totalHrs <> 0 ),
                        (@totalPts / @totalHrs), 0);
    SET @newCumHrs = CalcCumHrs(stuID) + @totalHrs;
    SET @newCumPts = CalcCumPts(stuID) + @totalPts;
    SET @cumGPA = IF ( ( NOT isNULL(@newCumHrs) AND @newCumHrs <> 0 ),
                        (@newCumPts / @newCumHrs), 0);

    -- Do update..
    UPDATE SessionStanding SET sessionTotalHrs = @totalHrs,
                               sessionTotalPts = @totalPts,
                               sessionGPA = @sessGPA,
                               cumTotalHrs = @newCumHrs,
                               cumTotalPts = @newCumPts,
                               cumGPA = @cumGPA
        WHERE pk_id = sessID;
END; //
DELIMITER ;

DELIMITER //
CREATE PROCEDURE UpdateAllSessionStandings (IN inAcadYear VARCHAR(10))
COMMENT 'Updates student session standings for the given acad. year'
BEGIN
    DECLARE student_id INT;
    DECLARE sess_id INT;
    DECLARE done INT DEFAULT FALSE;
    DECLARE cur CURSOR FOR SELECT Student.studentID,
                                  SessionStanding.pk_id
        FROM Student LEFT JOIN SessionStanding
          ON Student.studentID = SessionStanding.studentID
         AND SessionStanding.acadYear = inAcadYear;
    DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;
    OPEN cur;
        read_loop: LOOP
            FETCH cur INTO student_id, sess_id;
            IF done THEN
              LEAVE read_loop;
            END IF;
            CALL UpdateSessionStanding(student_id, sess_id, inAcadYear);
        END LOOP;
    CLOSE cur;
END; //
DELIMITER ;

