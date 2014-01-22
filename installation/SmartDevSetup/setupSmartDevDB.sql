-- Define the Ramp/Smart table schemas and populate with sample data.

--
-- This file contains SQL code to create administrative tables used by
-- Smart and sample Smart tables that have corresponding table settings
-- in the settings/demo directory.
--

--
-- Create Database: `smart_dev`
--

DROP DATABASE IF EXISTS `smart_dev`;
CREATE DATABASE `smart_dev`;

-- Define what "guest" users (those who are not logged in) are
-- authorized to do, create a SMART administrator role, and define what
-- administrative users with that role may do.  As an example, and/or
-- to make development easier, create several test users ("hr", "reg",
-- and "developer") and define what those users may do.

SOURCE createSmartDevUsersAuths.sql;

-- Create and populate the built-in tables used for record locking.

SOURCE createSmartDevLocks.sql;

-- Read in various files to set up tables that form the core of an
-- academic records system::
--    - a table for information about academic terms
--    - tables for information about schools, departments, and programs
--    - tables for information about people generally (names,
--        demographic and contact information, etc) and about
--        staff members more specifically (job titles, contract
--        information, etc.)
--    - tables for information about course modules and specific
--        offerings
--    - tables for information about students and their academic
--        progress
--

SOURCE smartTermsSetup.sql

SOURCE smartProgramSetup.sql

SOURCE smartPersonStaffSetup.sql

SOURCE smartModuleSetup.sql

SOURCE smartStudentSetup.sql

-- SOURCE smartRequirementsSetup.sql

-- Grant the MySQL accounts created in createSmartDevMysqlAccts.sql
-- the ability to execute functions and procedures that allow the
-- database to do some of its own consistency maintenance.
--
--      IS THIS NECESSARY?

-- SOURCE grant_func_proc_privs.sql;

