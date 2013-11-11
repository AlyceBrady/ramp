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
-- c) the smartuser password.

-- You must run MySQL as root (or some other user that has CREATE USER
-- and GRANT permissions to execute the commands found in this file:
--      mysql> SOURCE filename.sql

-- NOTE: Smart databases require additional permissions for
-- procedures and functions that allow the database to do some of
-- its own consistency maintenance, but these can't be set up until
-- the database and the relevant procedures and functions have been
-- created.  Therefore, you should execute the MySQL commands in
-- grant_func_proc_privs.sql after the database has been created.

-- ------------------------
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

CREATE USER 'smartuser'@'localhost' IDENTIFIED BY 'smartuser_passwd';

--
-- Examples if you create separate accounts for different databases:
--
-- CREATE USER 'smarttester'@'localhost' IDENTIFIED BY 'smarttester_passwd';
-- CREATE USER 'smartdemo'@'localhost' IDENTIFIED BY 'smartdemo_passwd';

-- ------------------------
--
-- Set up privileges for database administrators to access all databases
-- that have been (or will be) set up.
--
-- Examples:
--    These examples assume the dba accounts are ramp_dba1 and ramp_dba2
--    from the examples above, and that there are two databases: smart
--    and smart_dev.  Smart databases require additional permissions
--    for procedures and functions that allow the database to do
--    some of its own consistency maintenance.  The database
--    administrator might also need GRANT CREATE ROUTINE to
--    add new stored procedures or functions to the database.
--
--    Additional databases (e.g., smart_user_tests, smart_automated_tests)
--    would need similar statements.

GRANT ALL ON `smart`.* TO 'ramp_dba1'@'localhost', 'ramp_dba2'@'localhost';

GRANT ALL ON `smart_dev`.* TO 'ramp_dba1'@'localhost', 'ramp_dba2'@'localhost';

--
-- Set up appropriate privileges for Ramp/Smart access account.
--
-- Examples:
--    The first two examples below grant appropriate privileges to two
--    different databases, smart and smart_dev, assuming that
--    there is a single smartuser access account for both databases.
--    For both databases, the examples give permissions to
--    the Smart application to view, add, edit, and delete data,
--    but not to change table schemas.  Additional permissions for
--    procedures, functions, and triggers allow the database to
--    do some of its own consistency maintenance.  Whether actual
--    users can do all the things allowed by the permissions set
--    here depends on role-based authorization, defined in the
--    Ramp/Smart authorizations table (ramp_auth_auths).
--
--    The final example below grants appropriate privileges for
--    automated regression testing.

GRANT SELECT, INSERT, UPDATE, DELETE, TRIGGER ON `smart`.* TO
                    'smartuser'@'localhost';

GRANT SELECT, INSERT, UPDATE, DELETE, TRIGGER ON `smart_dev`.* TO
                    'smartuser'@'localhost';

--
-- SMART Automated Regression Testing Example:
-- Note: Automated tests include dropping and re-creating tables entirely.
--

GRANT DROP, CREATE, SELECT, INSERT, UPDATE, DELETE, TRIGGER
        ON `smart_automated_tests`.* TO 'smartuser'@'localhost';

