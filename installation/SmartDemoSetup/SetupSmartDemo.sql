--
-- This file contains SQL code to create a SMART Demo database
-- (`smart_demo`) with corresponding table settings in the
-- demoSettings/Admin and demoSettings/Smart directories.
--

-- Create the MySQL accounts for a database administrator and for
-- web-based database access via RAMP.  Grant those MySQL accounts
-- appropriate permissions to the SMART demo database.

SOURCE createSmartDemoMysqlAccts.sql;

-- Create the Smart demo database and populate with sample data.

SOURCE setupSmartDemoDB.sql;

