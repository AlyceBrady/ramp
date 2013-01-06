--
-- RAMP: Record and Activity Management Program
--
-- Create the basic user account for the RAMP database administrator and,
-- optionally, one or more accounts for web-based RAMP access.
--

--
-- Please read the installation instructions in INSTALL_DB.txt
-- BEFORE executing these SQL instructions in your database.
--

--
-- Create rampdba account for RAMP Database Administrator.
-- 

CREATE USER 'rampdba'@'localhost' IDENTIFIED BY 'rampdba_password';

--
-- Set up privileges for rampdba to access the demonstration and test
-- databases, ramp_demo and ramp_test.
--

GRANT ALL ON `ramp_demo`.* TO 'rampdba'@'localhost';
GRANT ALL ON `ramp_test`.* TO 'rampdba'@'localhost';

--
-- Set up privileges for rampdba to access the domain database(s) created
-- for this application.
--

-- GRANT ALL ON `domain_db`.* TO 'rampdba'@'localhost';

--
-- Create a MySQL account for web-based RAMP access to each database.
-- Alternatively, these account(s) may be defined in a different
-- file, such as createSmartDatabases.
-- 

-- CREATE USER 'rampdemo'@'localhost' IDENTIFIED BY 'rampdemopass';
-- CREATE USER 'rampdemo'@'%' IDENTIFIED BY 'rampdemopass';
-- CREATE USER 'rampuser'@'localhost' IDENTIFIED BY 'rampuserpass';
-- CREATE USER 'rampuser'@'%' IDENTIFIED BY 'rampuserpass';

--
-- Set up privileges for the RAMP accounts to access the domain database(s)
-- created for this application.
--
-- As an example, the rampdemo user might have privileges to view data
-- in tables, but not add, modify, or delete data.  A normal web access
-- account might have privileges to view, add, edit, and delete data in
-- tables, but not to change table schemas.
--

-- GRANT SELECT ON `ramp_demo`.* TO 'rampdemo'@'localhost', 'rampdemo'@'%';
-- GRANT SELECT ON `ramp_demo`.* TO 'rampuser'@'localhost', 'rampuser'@'%';
-- GRANT SELECT, INSERT, UPDATE, DELETE ON `ramp_test`.* TO 'rampuser'@'localhost';

--
-- Users, Resources, Roles, and Authorizations:
--
-- See rampDemoSetup.sql and smartDemoSetup.sql for examples on how to set up the
-- table schemas, domain users, and access control rules for RAMP databases.
--
