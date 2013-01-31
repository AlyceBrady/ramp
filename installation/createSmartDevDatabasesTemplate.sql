--
-- SMART: Software for Managing Academic Records and Transcripts
--   (built on RAMP, the Record and Activity Management Program)
--
-- Set up privileges for the Smart database administrator to access
-- Smart development and automated test databases:
--      `smart_dev`
--      `automated_tests`
--

--
-- Before executing the SQL statements in this file, please read the
-- installation instructions in INSTALL_DB.txt.  After addressing the
-- security concerns outlined there, you should:
--      Change the name of the RAMP Database Administrator account
--          and create additional accounts if there will be more than
--          one person fulfilling that role (e.g., a DBA and a backup).
--          This means changing all instances of 'rampdba' to the new
--          name and copying all statements involving 'rampdba' to apply
--          to additional DBA accounts as well.
--      Set the passwords for the MySQL user accounts to appropriate
--          values, either in the lines below or through MySQL directly.
--

--
-- Create rampdba account for RAMP Database Administrator.
-- Set up full set of privileges for the Database Administrator
-- for the development and test databases.
-- 

CREATE USER 'rampdba'@'localhost' IDENTIFIED BY 'rampdba_password';

GRANT ALL ON `smart_dev`.* TO 'rampdba'@'localhost';
GRANT ALL ON `automated_tests`.* TO 'rampdba'@'localhost';

--
-- Create a MySQL account for web-based Smart access to Smart databases.
-- Set up web access privileges for the development and test databases.
-- 

CREATE USER 'smartuser'@'localhost' IDENTIFIED BY 'smartuserpass';

GRANT SELECT, INSERT, UPDATE, DELETE, TRIGGER ON `smart_dev`.* TO
    'smartuser'@'localhost', 'smartuser'@'%';
GRANT DROP, CREATE, SELECT, INSERT, UPDATE, DELETE, TRIGGER
    ON `automated_tests`.* TO 'smartuser'@'localhost', 'smartuser'@'%';

--
-- Read in:
--    Authorization user and ACL tables for `smart_dev`
--    Database table schemas and initial data for `smart_dev`
--    Authorization user and ACL tables for `automated_tests`
--

SOURCE SmartSampleSetup/smartDevSetup.sql;
SOURCE rampTestsSetup.sql;

--
-- Set up privileges for procedures and functions defined for the Smart
-- development database.  (Will the automated tests database use these
-- also?  Only if there are some automated tests that test or depend on
-- the procedures & functions.)
--

GRANT EXECUTE ON PROCEDURE `smart_dev`.`cancelstudentreg` TO
    'rampdba'@'localhost', 'smartuser'@'localhost', 'smartuser'@'%';
GRANT EXECUTE ON FUNCTION `smart_dev`.`termcensusdate` TO
    'rampdba'@'localhost', 'smartuser'@'localhost', 'smartuser'@'%';
GRANT EXECUTE ON FUNCTION `smart_dev`.`modofferingenddate` TO
    'rampdba'@'localhost', 'smartuser'@'localhost', 'smartuser'@'%';

-- GRANT EXECUTE ON PROCEDURE `automated_tests`.`cancelstudentreg` TO
--      'rampdba'@'localhost', 'smartuser'@'localhost', 'smartuser'@'%';
-- GRANT EXECUTE ON FUNCTION `automated_tests`.`termcensusdate` TO
--      'rampdba'@'localhost', 'smartuser'@'localhost', 'smartuser'@'%';
-- GRANT EXECUTE ON FUNCTION `automated_tests`.`modofferingenddate` TO
--      'rampdba'@'localhost', 'smartuser'@'localhost', 'smartuser'@'%';
