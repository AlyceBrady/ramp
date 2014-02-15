--
-- RAMP: Record and Activity Management Program
-- SMART: Software for Managing Academic Records and Transcripts
--
-- Create pre-defined Ramp/Smart tables to manage record locking.

-- You must run MySQL as root (or some other user that has permission
-- to create databases) to execute the commands found in this file.


-- Create Lock Relations Table (ramp_lock_relations):
--
-- Create a Ramp/Smart Lock Relations table that, for each table in the
-- application, identifies a table and field to use for locking.  The
-- table might use its own primary key for locking (i.e., to lock a
-- record in this table, acquire the lock for this record's key), or it
-- might use a key or other unique field from another table (for
-- example, several tables related to a single, central table might all
-- use their foreign key references to the central table for record
-- locking, which would prevent people from simultaneously modifying
-- different aspects of the same multi-table "record").

-- If the relationships among different tables is known when the
-- database is being created, the lock relations table can be
-- initialized here with known table relations (as it is for this
-- Smart development environment).


USE `njala_proto`;

DROP TABLE IF EXISTS `ramp_lock_relations`;
CREATE TABLE `ramp_lock_relations` (
  `db_table` varchar(50) NOT NULL,
  `lock_table` varchar(50) NOT NULL,
  `locking_key_name` varchar(150) NOT NULL,
  PRIMARY KEY (`db_table`)
);


-- Create Initial Lock Relations:

LOCK TABLES `ramp_lock_relations` WRITE;
INSERT INTO `ramp_lock_relations` (`db_table`, `lock_table`, `locking_key_name`)
VALUES
('ramp_auth_users', 'ramp_auth_users', 'username')
, ('ramp_auth_auths', 'ramp_auth_auths', 'id')
, ('ramp_lock_relations', 'ramp_lock_relations', 'db_table')
, ('Schools', 'Schools', 'schoolID')
, ('Departments', 'Departments', 'deptID')
, ('Term', 'Term', 'term')
, ('Person', 'Person', 'personID')
, ('Address', 'Person', 'personID')
, ('PhoneNumber', 'Person', 'personID')
, ('InstitutionsAttended', 'Person', 'personID')
, ('Staff', 'Person', 'personID')
, ('StaffPersonalInfo', 'Person', 'personID')
, ('Children', 'Person', 'parentID')
, ('JobFunction', 'Person', 'personID')
, ('StaffContract', 'Person', 'personID')
, ('Accidents', 'Person', 'personID')
, ('StaffDisciplinaryAction', 'Person', 'personID')
, ('Applicant', 'Person', 'personID')
, ('AdmissExams', 'Person', 'personID')
, ('Student', 'Student', 'studentID')
, ('RecordHolds', 'Student', 'studentID')
, ('Advising', 'Student', 'studentID')
, ('StudentAcadProgram', 'Student', 'studentID')
, ('StudentLeaves', 'Student', 'studentID')
, ('StudentAnnotations', 'Student', 'studentID')
, ('Enrollment', 'Student', 'studentID')
, ('CourseGrades', 'Student', 'studentID')
, ('CompExamScores', 'Student', 'studentID')
, ('TermStanding', 'Student', 'studentID')
, ('SessionStanding', 'Student', 'studentID')
, ('AcadProgram', 'AcadProgram', 'programID')
, ('ProgPlanOfStudy', 'AcadProgram', 'programID')
, ('Modules', 'Modules', 'moduleID')
, ('ModuleOfferings', 'ModuleOfferings', 'pk_id')
, ('ModuleAssignments', 'ModuleOfferings', 'modOfferingID')
, ('ModuleAttributes', 'ModuleAttributes', 'pk_id')
, ('Attributes', 'Attributes', 'pk_id')
, ('AcadProgramTypes', 'AcadProgramTypes', 'pk_id')
, ('AddressTypes', 'AddressTypes', 'pk_id')
, ('AdmissExamNames', 'ExamNames', 'pk_id')
, ('AdmissTestCodes', 'AdmissTestCodes', 'pk_id')
, ('AdmissTestDescriptors', 'AdmissTestDescriptors', 'pk_id')
, ('AdvisorTypes', 'AdvisorTypes', 'pk_id')
, ('AnnotationTypes', 'AnnotationTypes', 'pk_id')
, ('AnnotationAuthorities', 'AnnotationAuthorities', 'pk_id')
, ('ApplicationStatusCodes', 'ApplicationStatusCodes', 'pk_id')
, ('CampusNames', 'CampusNames', 'pk_id')
, ('CampusLocations', 'CampusLocations', 'pk_id')
, ('ClassLevelCodes', 'ClassLevelCodes', 'pk_id')
, ('ContractStatusCodes', 'ContractStatusCodes', 'pk_id')
, ('HoldAuthorities', 'HoldAuthorities', 'pk_id')
, ('HoldTypes', 'HoldTypes', 'pk_id')
, ('JobCategories', 'JobCategories', 'pk_id')
, ('ModuleTypes', 'ModuleTypes', 'pk_id')
, ('NameTypes', 'NameTypes', 'pk_id')
, ('StudentLeaveTypes', 'StudentLeaveTypes', 'pk_id')
, ('StudentModStatusCodes', 'StudentModStatusCodes', 'pk_id')
, ('StudentProgramStatusCodes', 'StudentProgramStatusCodes', 'pk_id')
, ('TermStandingCodes', 'TermStandingCodes', 'pk_id')
;
UNLOCK TABLES;


--
-- Create Locks Table (ramp_lock_locks):
--
-- Create a Ramp/Smart Locks table that holds lock information for every
-- locked record.

DROP TABLE IF EXISTS `ramp_lock_locks`;
CREATE TABLE `ramp_lock_locks` (
  `lock_table` varchar(50) NOT NULL,
  `locking_key` varchar(100) NOT NULL,
  `username` varchar(100) NOT NULL,
  `lock_time` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`lock_table`, `locking_key`)
);


-- Do Not Create Any Initial Locks:

