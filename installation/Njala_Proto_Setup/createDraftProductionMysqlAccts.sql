-- DRAFT file to create Mysql accounts for a Production environment with
-- additional Test and Staged Next Version environments.

--
-- RAMP: Record and Activity Management Program
-- SMART: Software for Managing Academic Records and Transcripts
--
-- Create the basic MySQL user accounts for interacting with the
-- RAMP/SMART databases: smart, smarttest, and smartstage.  There should
-- be an account for a database administrator
-- and one for web-based RAMP access to each database -- and grant
-- those accounts appropriate permissions to access the associated
-- database.
--
-- [Not currently being done...
-- Create similar accounts for the database used for regression testing.
-- Create the regression testing database also.
-- ]


-- AT THE VERY LEAST, change the rampdba and smartuser passwords
-- in this file and change the permissions on the file to be readable
-- only by the owner.

-- You must run MySQL as root (or some other user that has CREATE USER
-- and GRANT permissions to execute the commands found in this file:
--      mysql> SOURCE filename.sql

-- When finished, edit the application/configs/application.ini file
-- to specify the web-based access account and password as properties
-- available to RAMP/SMART.


-- 1a. CREATE DBA ACCOUNT(S):

-- CREATE USER 'rampdba'@'localhost' IDENTIFIED BY 'rampdbapw';

-- 1b. CREATE WEB-BASED RAMP/SMART ACCESS ACCOUNT:

CREATE USER 'smart'@'localhost' IDENTIFIED BY 'sm@4t_Pw';

CREATE USER 'smarttest'@'localhost' IDENTIFIED BY 'sm@4tt3st_Pw';

CREATE USER 'smartstage'@'localhost' IDENTIFIED BY 'sm@4tst@g3_Pw';



-- 2a. SET UP PRIVILEGES FOR DBA ACCOUNT(S):
--     Grant permissions for MySQL access by the database administrator.

-- GRANT ALL ON `njala_proto`.* TO 'rampdba'@'localhost';

-- GRANT ALL ON `smart_automated_tests`.* TO 'rampdba'@'localhost';

-- 2b. SET UP PRIVILEGES FOR WEB-BASED RAMP/SMART ACCESS ACCOUNT:
--     Grant SMART software permission to view, add, edit, and delete
--     data, but not to change table schemas.  The actual access
--     permissions for different types of RAMP users depend on role-based
--     authorization defined in the RAMP authorizations table
--     (ramp_auth_auths); that table might, for example, only allow
--     read-only access to a demo database for non-admin users.

-- NOTE: Smart databases require additional permissions for
-- procedures and functions that allow the database to do some of
-- its own consistency maintenance, but these can't be set up until
-- the database and the relevant procedures and functions have been
-- created.  Therefore, you should execute the MySQL commands in
-- grant_func_proc_privs.sql after the database has been created.
-- (See SetupSmartDevEnv.sql for an example.)

GRANT SELECT, INSERT, UPDATE, DELETE, TRIGGER, EXECUTE ON `smart_production`.*
    TO 'smart'@'localhost';

GRANT SELECT, INSERT, UPDATE, DELETE, TRIGGER, EXECUTE ON `smart_testing`.*
    TO 'smarttest'@'localhost';

GRANT SELECT, INSERT, UPDATE, DELETE, TRIGGER, EXECUTE ON `smart_staging`.*
    TO 'smartstaging'@'localhost';

-- GRANT DROP, CREATE, SELECT, INSERT, UPDATE, DELETE, TRIGGER, EXECUTE 
--     ON `smart_automated_tests`.* TO 'smartuser'@'localhost';


--     Create database for automated regression testing, since we don't
--     have a separate script to create and populate the database.

-- DROP DATABASE IF EXISTS `smart_automated_tests`;
-- CREATE DATABASE `smart_automated_tests`;

