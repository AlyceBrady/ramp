--
-- RAMP: Record and Activity Management Program
-- SMART: Software for Managing Academic Records and Transcripts
--
-- Create the basic MySQL user accounts for interacting with the
-- RAMP/SMART Developer database -- one for a database administrator
-- and one for web-based RAMP access to the database -- and grant
-- those accounts appropriate permissions to access the Developer
-- database (whose name is assumed to be 'smart_dev') and the database
-- for automated regression testing.  (The actual database is created
-- later.)
--
-- Create similar accounts for the database used for regression testing.
-- Create the regression testing database also.


-- Please read the installation instructions in INSTALL_DB.txt,
-- particularly the section on "Addressing Security Concerns,"
-- BEFORE using this file or executing these SQL instructions.

-- AT THE VERY LEAST, change the smartdevdba and smartdev passwords
-- in this file and change the permissions on the file to be readable
-- only by the owner.

-- You must run MySQL as root (or some other user that has CREATE USER
-- and GRANT permissions to execute the commands found in this file:
--      mysql> SOURCE filename.sql

-- When finished, edit the application/configs/application.ini file
-- to specify the web-based access account and password as properties
-- available to RAMP/SMART.

-- NOTE: Smart databases require additional permissions for
-- procedures and functions that allow the database to do some of
-- its own consistency maintenance, but these can't be set up until
-- the database and the relevant procedures and functions have been
-- created.  Therefore, you should execute the MySQL commands in
-- grant_func_proc_privs.sql after the database has been created.
-- (See SetupSmartDevEnv.sql for an example.)


-- 1a. CREATE DBA ACCOUNT(S):

CREATE USER 'smartdevdba'@'localhost' IDENTIFIED BY 'smartdbapw';

-- 1b. CREATE WEB-BASED RAMP/SMART ACCESS ACCOUNT:

CREATE USER 'smartdev'@'localhost' IDENTIFIED BY 'smartdev_passwd';



-- 2a. SET UP PRIVILEGES FOR DBA ACCOUNT(S):
--     Grant permissions for MySQL access by the database administrator.

GRANT ALL ON `smart_dev`.* TO 'smartdevdba'@'localhost';

GRANT ALL ON `smart_automated_tests`.* TO 'smartdevdba'@'localhost';

-- 2b. SET UP PRIVILEGES FOR WEB-BASED RAMP/SMART ACCESS ACCOUNT:
--     Grant SMART software permission to view, add, edit, and delete
--     data, but not to change table schemas.  The actual access
--     permissions for different types of RAMP users depend on role-based
--     authorization defined in the RAMP authorizations table
--     (ramp_auth_auths); that table might, for example, only allow
--     read-only access to a demo database for non-admin users.

GRANT SELECT, INSERT, UPDATE, DELETE, TRIGGER ON `smart_dev`.*
    TO 'smartdev'@'localhost';

GRANT DROP, CREATE, SELECT, INSERT, UPDATE, DELETE, TRIGGER
    ON `smart_automated_tests`.* TO 'smartdev'@'localhost';


--     Create database for automated regression testing, since we don't
--     have a separate script to create and populate the database.

DROP DATABASE IF EXISTS `smart_automated_tests`;
CREATE DATABASE `smart_automated_tests`;

