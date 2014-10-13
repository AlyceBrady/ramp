--
-- RAMP: Record and Activity Management Program
-- SMART: Software for Managing Academic Records and Transcripts
--
-- Create the basic MySQL user accounts for interacting with a simple
-- RAMP Demo -- one for a database administrator and one for web-based
-- RAMP access to the RAMP Demo -- and grant those accounts appropriate
-- permissions to access the RAMP Demo database (whose name is assumed
-- to be 'ramp_demo').


-- Please read the installation instructions in INSTALL_DB.txt,
-- particularly the section on "Addressing Security Concerns,"
-- BEFORE using this file or executing these SQL instructions.

-- AT THE VERY LEAST, change the rampdba and rampdemo passwords
-- in this file and change the permissions on the file to be readable
-- only by the owner.

-- You must run MySQL as root (or some other user that has CREATE USER
-- and GRANT permissions to execute the commands found in this file:
--      mysql> SOURCE filename.sql

-- When finished, edit the application/configs/application.ini file
-- to specify the web-based access account and password as properties
-- available to RAMP.


-- 1a. CREATE DBA ACCOUNT(S):

CREATE USER 'rampdba'@'localhost' IDENTIFIED BY 'need_password';

-- 1b. CREATE WEB-BASED RAMP/SMART ACCESS ACCOUNT:

CREATE USER 'rampdemo'@'localhost' IDENTIFIED BY 'need_password';



-- 2a. SET UP PRIVILEGES FOR DBA ACCOUNT(S):
--     Grant permissions for MySQL access by the database administrator.

GRANT ALL ON `ramp_demo`.* TO 'rampdba'@'localhost';

-- 2b. SET UP PRIVILEGES FOR WEB-BASED RAMP/SMART ACCESS ACCOUNT:
--     Grant RAMP software permission to view, add, edit, and delete
--     data, but not to change table schemas.  The actual access
--     permissions for different types of RAMP users depend on role-based
--     authorization defined in the RAMP authorizations table
--     (ramp_auth_auths); that table might, for example, only allow
--     read-only access to the demo database for non-admin users.

GRANT SELECT, INSERT, UPDATE, DELETE ON `ramp_demo`.* TO 'rampdemo'@'localhost';

