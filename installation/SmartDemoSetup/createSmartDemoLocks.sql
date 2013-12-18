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
-- Smart demo environment).


USE `smart_demo`;

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
, ('Term', 'Term', 'term')
, ('Person', 'Person', 'id')
, ('Address', 'Person', 'personID')
, ('Staff', 'Person', 'staffID')
, ('StaffContract', 'Person', 'staffID')
, ('Student', 'Person', 'studentID')
, ('Advising', 'Person', 'studentID')
, ('StudentAcadProgram', 'Person', 'studentID')
, ('StudentLeaves', 'Person', 'studentID')
, ('StudentAnnotations', 'Person', 'studentID')
, ('Enrollment', 'Person', 'studentID')
, ('TermStanding', 'Person', 'studentID')
, ('TestScores', 'Person', 'studentID')
, ('Modules', 'Modules', 'moduleID')
, ('ModuleOfferings', 'ModuleOfferings', 'pk_id')
, ('ModuleAssignments', 'ModuleOfferings', 'modOfferingID')
, ('ModuleAttributes', 'ModuleAttributes', 'pk_id')
, ('Attributes', 'Attributes', 'pk_id')
, ('AcadProgram', 'AcadProgram', 'programID')
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

