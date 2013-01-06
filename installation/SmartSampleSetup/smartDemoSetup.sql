--
-- This file invokes a series of other files to create a sample Smart
-- database (`smart_demo`) with a core set of fundamental tables
-- for an academic records system.  The user authorization tables define
-- several sample users but none have more than 'guest' (view-only)
-- privileges in the smart_demo database.
--

--
-- Current Database: `smart_demo`
--

DROP DATABASE IF EXISTS `smart_demo`;
CREATE DATABASE `smart_demo` /*!40100 DEFAULT CHARACTER SET latin1 */;

USE `smart_demo`;

--
-- Read in User Authorization tables.
--

SOURCE SmartSampleSetup/smartDemoAuthsSetup.sql

--
-- Read in various files to set up:
--      - Tables from the simpler RAMP demo
--      - Tables that form the core of an academic records system:
--          - a table for information about academic terms
--          - tables for information about schools, departments, and programs
--          - tables for information about people generally (names,
--              demographic and contact information, etc) and about
--              staff members more specifically (job titles, contract
--              information, etc.)
--          - tables for information about course modules and specific
--              offerings
--          - tables for information about students and their academic
--              progress
--

SOURCE rampDemoData.sql

SOURCE SmartSampleSetup/smartTermsSetup.sql

SOURCE SmartSampleSetup/smartProgramSetup.sql

SOURCE SmartSampleSetup/smartPersonStaffSetup.sql

SOURCE SmartSampleSetup/smartModuleSetup.sql

SOURCE SmartSampleSetup/smartStudentSetup.sql

