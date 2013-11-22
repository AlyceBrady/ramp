--
-- This file contains SQL code to create a SMART Developer database
-- (`smart_dev`) with corresponding table settings in the
-- devSettings/Admin and devSettings/Smart directories.
--

-- Create the MySQL accounts for a database administrator and for
-- web-based database access via RAMP.  Grant those MySQL accounts
-- appropriate permissions to the RAMP/SMART developer database.

SOURCE createSmartDevMysqlAccts.sql;

-- Define what "guest" users (those who are not logged in) are
-- authorized to do, create a SMART administrator role, and define what
-- administrative users with that role may do.

SOURCE createSmartDevUsersAuths.sql;

-- Create and populate the Ramp/Smart developer database.

SOURCE populateSmartDevData.sql;

-- Grant the MySQL accounts created in createSmartDevMysqlAccts.sql
-- the ability to execute functions and procedures that allow the
-- database to do some of its own consistency maintenance.

-- SOURCE grant_func_proc_privs.sql;

