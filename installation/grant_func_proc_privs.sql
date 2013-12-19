--
-- RAMP: Record and Activity Management Program
-- SMART: Software for Managing Academic Records and Transcripts
--
-- Grant the database adminstrator(s) and web-based Smart access
-- accounts privileges to execute functions and procedures that
-- allow the database to do some of its own consistency maintenance.
-- (The database administrator might also need GRANT CREATE ROUTINE
--  to add new stored procedures or functions to the database.)

-- Assumptions: The examples in this file assume that the following
-- MySQL user accounts and databases have been set up.  You should edit
-- this file to refer to the actual MySQL accounts and Ramp/Smart
-- databases you have created.
--
--    MySQL accounts (from create_smart_mysql_acct_examples.sql):
--          ramp_dba1           first database administrator
--          ramp_dba2           second database administrator
--          smartuser           web-based Smart access
--
--    Ramp/Smart databases:
--          smart
--          smart_dev
--
-- Similar privileges would be necessary for any other database that has
-- been set up with functions and procedures.

-- You must run MySQL as root (or some other user that has GRANT
-- permissions to execute the commands found in this file.


-- Set up execution privileges on procedures and functions for both
-- the database administrator(s) and web-based Ramp/Smart access.

GRANT EXECUTE ON PROCEDURE `smart`.`cancelstudentreg` TO
    'ramp_dba1'@'localhost', 'ramp_dba2'@'localhost',
    'smartuser'@'localhost';
GRANT EXECUTE ON FUNCTION `smart`.`termcensusdate` TO
    'ramp_dba1'@'localhost', 'ramp_dba2'@'localhost',
    'smartuser'@'localhost';
GRANT EXECUTE ON FUNCTION `smart`.`modofferingenddate` TO
    'ramp_dba1'@'localhost', 'ramp_dba2'@'localhost',
    'smartuser'@'localhost';

GRANT EXECUTE ON PROCEDURE `smart_dev`.`cancelstudentreg` TO
    'ramp_dba1'@'localhost', 'ramp_dba2'@'localhost',
    'smartuser'@'localhost';
GRANT EXECUTE ON FUNCTION `smart_dev`.`termcensusdate` TO
    'ramp_dba1'@'localhost', 'ramp_dba2'@'localhost',
    'smartuser'@'localhost';
GRANT EXECUTE ON FUNCTION `smart_dev`.`modofferingenddate` TO
    'ramp_dba1'@'localhost', 'ramp_dba2'@'localhost',
    'smartuser'@'localhost';

