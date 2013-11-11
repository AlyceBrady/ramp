--
-- This file contains SQL code to create a SMART Demo database
-- (`smart_demo`) with corresponding table settings in the
-- settings/demo directory.
--

-- Create the MySQL accounts for a database administrator and for
-- web-based database access via RAMP.  Grant those MySQL accounts
-- appropriate permissions to the RAMP demo database.

SOURCE createSmartDemoMysqlAccts.sql;

-- Define what "guest" users (those who are not logged in) are
-- authorized to do, create a SMART administrator role, and define what
-- administrative users with that role may do.

SOURCE createSmartDemoUsersAuths.sql;

-- Create and populate the demo Smart database.

SOURCE populateSmartDemoData.sql;

