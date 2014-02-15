--
-- This file contains SQL code to create a SMART database
-- (`njala_proto`) with corresponding table settings in the
-- NjalaSettings/Admin and NjalaSettings/Smart directories.
--

-- Create the MySQL accounts for a database administrator and for
-- web-based database access via RAMP.  Grant those MySQL accounts
-- appropriate permissions to the RAMP/SMART developer database.

SOURCE createSmartMysqlAccts.sql;

-- Create the Ramp/Smart database and populate with sample data.

SOURCE setupSmartDB.sql;

