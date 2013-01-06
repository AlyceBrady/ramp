--
-- SMART: Software for Managing Academic Records and Transcripts
--   (built on RAMP, the Record and Activity Management Program)
--
-- Set up privileges for the Smart database administrator to access
-- Smart production, development, and test databases.
--

--
-- Before executing the SQL statements in this file, please read the
-- installation instructions in INSTALL_DB.txt and execute the SQL
-- statements in createRampDBA.sql.  If the "rampdba" user name has been
-- changed to something else in createRampDBA.sql ("smartdba", for example), 
-- then it should be changed to the same thing in this file.
--

--
-- Set up full set of privileges for the Database Administrator to access
-- all Smart databases.  (To minimize the chances of serious errors,
-- it is best to keep development and production databases on different
-- servers; uncomment only one of these database in this file.)
--

-- Demo and test databases:
GRANT ALL ON `smart_demo`.* TO 'rampdba'@'localhost';
-- GRANT ALL ON `smart_test`.* TO 'rampdba'@'localhost';
-- GRANT ALL ON `smart_regression_tests`.* TO 'rampdba'@'localhost';

-- Development or Production database:
-- GRANT ALL ON `smart_dev`.* TO 'rampdba'@'localhost';
-- GRANT ALL ON `smart`.* TO 'rampdba'@'localhost';

--
-- Create a MySQL account for web-based Smart access to each database.
-- 

CREATE USER 'smartdemo'@'localhost' IDENTIFIED BY 'smartdemopass';
CREATE USER 'smartuser'@'localhost' IDENTIFIED BY 'smartuserpass';

--
-- Define appropriate user privileges on the demo database (rampdba has all
-- privileges, the smartdemo user for web access to the demo has read-only
-- privileges).
--

GRANT SELECT ON `smart_demo`.* TO 'smartdemo'@'localhost', 'smartdemo'@'%';

GRANT ALL ON `smart_demo`.* TO 'rampdba'@'localhost';
GRANT EXECUTE ON PROCEDURE `smart_demo`.`cancelstudentreg` TO 'rampdba'@'localhost';
GRANT EXECUTE ON FUNCTION `smart_demo`.`termcensusdate` TO 'rampdba'@'localhost';
GRANT EXECUTE ON FUNCTION `smart_demo`.`modofferingenddate` TO 'rampdba'@'localhost';

--
-- Set up privileges for the Smart accounts to access the testing and
-- development databases created for this application (other databases
-- would be similar).
--
-- As an example, the smartdemo user might have privileges to view data
-- in tables, but not add, modify, or delete data.  A normal web access
-- account might have privileges to view, add, edit, and delete data in
-- tables, but not to change table schemas.
--

-- GRANT ALL ON `smart_test`.* TO 'rampdba'@'localhost';
-- GRANT SELECT, INSERT, UPDATE, DELETE, TRIGGER ON `smart_test`.* TO
--      'smartuser'@'localhost', 'smartuser'@'%';
-- GRANT EXECUTE ON PROCEDURE `smart_test`.`cancelstudentreg` TO
--      'rampdba'@'localhost', 'smartuser'@'localhost', 'smartuser'@'%';
-- GRANT EXECUTE ON FUNCTION `smart_test`.`termcensusdate` TO
--      'rampdba'@'localhost', 'smartuser'@'localhost', 'smartuser'@'%';
-- GRANT EXECUTE ON FUNCTION `smart_test`.`modofferingenddate` TO
--      'rampdba'@'localhost', 'smartuser'@'localhost', 'smartuser'@'%';

-- GRANT ALL ON `smart_dev`.* TO 'rampdba'@'localhost';
-- GRANT SELECT, INSERT, UPDATE, DELETE, TRIGGER ON `smart_dev`.* TO
--      'smartuser'@'localhost', 'smartuser'@'%';
-- GRANT EXECUTE ON PROCEDURE `smart_dev`.`cancelstudentreg` TO
--      'rampdba'@'localhost', 'smartuser'@'localhost', 'smartuser'@'%';
-- GRANT EXECUTE ON FUNCTION `smart_dev`.`termcensusdate` TO
--      'rampdba'@'localhost', 'smartuser'@'localhost', 'smartuser'@'%';
-- GRANT EXECUTE ON FUNCTION `smart_dev`.`modofferingenddate` TO
--      'rampdba'@'localhost', 'smartuser'@'localhost', 'smartuser'@'%';

--
-- Users, Resources, Roles, and Authorizations:
--
-- See rampDemoSetup.sql and smartDemoSetup.sql for examples on how to
-- set up the table schemas, domain users, and access control rules for
-- RAMP and Smart databases.
--

