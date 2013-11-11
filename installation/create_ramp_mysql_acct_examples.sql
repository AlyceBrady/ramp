--
-- RAMP: Record and Activity Management Program
-- SMART: Software for Managing Academic Records and Transcripts
--
-- Create the basic MySQL user accounts for administering and accessing
-- Ramp/Smart databases and grant those accounts appropriate
-- permissions to work with those databases.

-- Please read the installation instructions in INSTALL_DB.txt,
-- particularly the section on "Addressing Security Concerns,"
-- BEFORE using this file or executing these SQL instructions.

-- This file, as provided, is meant to be a TEMPLATE for creating dba
-- accounts.  You may use the commands here as examples for actual
-- commands to be typed in at the MySQL prompt, or you may edit this
-- file to contain the actual commands you will need for your own
-- installation and then read the file in as source from the MySQL
-- prompt (SOURCE this_file_name.sql).  If you edit this file to
-- create actual dba accounts with passwords, be sure to read the
-- section on Addressing Security Concerns in INSTALL_DB.txt first,
-- then change a) the file permissions of this file and your
-- .mysql_history file, b) the dba usernames and passwords, and
-- c) the rampuser password.

-- You must run MySQL as root (or some other user that has permission
-- to create users) to execute the commands found in this file.


--
-- Create an account for each person who will be a Ramp/Smart
-- Database Administrator.
-- 
-- Examples:

CREATE USER 'rampdba_1'@'localhost' IDENTIFIED BY 'password_1';
CREATE USER 'rampdba_2'@'localhost' IDENTIFIED BY 'password_2';

--
-- Create a MySQL account for web-based Ramp/Smart access to
-- each database. You may use one account for all databases or create
-- separate ones for different databases.
-- [When finished, you should also edit the application.ini file in
-- the application/configs/ directory to specify the web-based
-- access account(s) and password(s) as properties available to Ramp.]
--
-- Example:

CREATE USER 'rampuser'@'localhost' IDENTIFIED BY 'rampuser_passwd';

--
-- Examples if you create separate accounts for different databases:
--
-- CREATE USER 'ramptester'@'localhost' IDENTIFIED BY 'ramptester_passwd';
-- CREATE USER 'rampdemo'@'localhost' IDENTIFIED BY 'rampdemo_passwd';


--
-- Set up privileges for database administrators to access all databases
-- that have been (or will be) set up.
--
-- Examples:
--    These examples assume the dba accounts are ramp_dba1 and ramp_dba2
--    from the examples above, and that there are two databases:
--    ramp_appl and ramp_test.

GRANT ALL ON `ramp_appl`.* TO 'ramp_dba1'@'localhost', 'ramp_dba2'@'localhost';

GRANT ALL ON `ramp_test`.* TO 'ramp_dba1'@'localhost', 'ramp_dba2'@'localhost';

--
-- Set up appropriate privileges for Ramp/Smart access account.
--
-- Examples:
--    The examples below grant appropriate privileges to two
--    different databases, smart and smart_dev, assuming that
--    there is a single smartuser access account for both databases.
--    For both databases, the examples give permissions to
--    the Smart application to view, add, edit, and delete data,
--    but not to change table schemas.  Additional permissions for
--    procedures, functions, and triggers allow the database to
--    do some of its own consistency maintenance.  Whether actual
--    users can do all the things allowed by the permissions set
--    here depends on role-based authorization, defined in the
--    Ramp/Smart authorizations table (ramp_auth_auths); that table
--    might, for example, only allow read-only access to a demo
--    database.
--
--    The final example below grants appropriate privileges for
--    automated regression testing.

GRANT SELECT, INSERT, UPDATE, DELETE ON `ramp_appl`.* TO 'rampuser'@'localhost';

GRANT SELECT, INSERT, UPDATE, DELETE ON `ramp_test`.* TO 'rampuser'@'localhost';

