--
-- This file contains SQL code to create a SMART Developer database
-- (`smart_dev`) with corresponding table settings in the
-- devSettings/Admin and devSettings/Smart directories.
--

-- Create the MySQL accounts for a database administrator and for
-- web-based database access via RAMP.  Grant those MySQL accounts
-- appropriate permissions to the RAMP/SMART developer database.

SOURCE createSmartDevMysqlAccts.sql;

-- Create the Ramp/Smart developer database and populate with sample data.

SOURCE setupSmartDevDB.sql;

