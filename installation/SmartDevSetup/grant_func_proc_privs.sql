--
-- RAMP: Record and Activity Management Program
-- SMART: Software for Managing Academic Records and Transcripts
--
-- Grant the database adminstrator(s) and web-based Smart access
-- accounts privileges to execute functions and procedures that
-- allow the database to do some of its own consistency maintenance.

-- Assumptions: The examples in this file assume that the following
-- MySQL user accounts and databases have been set up:
--
--    MySQL accounts (from createSmartDevMysqlAccts.sql):
--          smartdevdba        database administrator
--          smartdev           web-based Smart access
--
--    Ramp/Smart database (from createSmartDevUsersAuths.sql):
--          smart_dev
--
-- Similar privileges would be necessary for any other database that has
-- been set up with functions and procedures.

-- You must run MySQL as root (or some other user that has GRANT
-- permissions to execute the commands found in this file.


-- Set up execution privileges on procedures and functions for both
-- the database administrator(s) and web-based Ramp/Smart access.

GRANT EXECUTE ON PROCEDURE `smart_dev`.`cancelstudentreg` TO
    'smartdevdba'@'localhost', 'smartdev'@'localhost';
GRANT EXECUTE ON FUNCTION `smart_dev`.`termcensusdate` TO
    'smartdevdba'@'localhost', 'smartdev'@'localhost';
GRANT EXECUTE ON FUNCTION `smart_dev`.`modofferingenddate` TO
    'smartdevdba'@'localhost', 'smartdev'@'localhost';

