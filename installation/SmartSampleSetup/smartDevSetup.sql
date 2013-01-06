--
-- This file invokes a series of other files to create a sample Smart
-- database (`smart_dev`) with a core set of fundamental tables
-- for an academic records system with several sample users with various
-- levels of access.  Edit the Auths setup file to create real or test
-- user accounts with better or more meaningful names.
--

--
-- Current Database: `smart_dev`
--

DROP DATABASE IF EXISTS `smart_dev`;
CREATE DATABASE `smart_dev` /*!40100 DEFAULT CHARACTER SET latin1 */;

USE `smart_dev`;

--
-- Read in User Authorization tables.
--

SOURCE SmartSampleSetup/smartDevAuthsSetup.sql

--
-- Read in various files to set up:
--      - Tables from the simple RAMP demo
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

