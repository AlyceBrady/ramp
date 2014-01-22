--
-- This file contains SQL code to create a RAMP Demo database 
-- (`ramp_demo`) with simple tables ('albums' and 'places') that
-- have corresponding table settings in the demoSettings/rampDemo
-- directory, a single administrative user, and a set of authorization
-- rules giving that user administrative privileges to create users
-- and establish additional authorization rules.
--

-- Create the MySQL accounts for a database administrator and for
-- web-based database access via RAMP.  Grant those MySQL accounts
-- appropriate permissions to the RAMP demo database.

SOURCE createRampDemoMysqlAccts.sql;

-- Create the Ramp demo database and populate with sample data.

SOURCE setupRampDemoDB.sql;
