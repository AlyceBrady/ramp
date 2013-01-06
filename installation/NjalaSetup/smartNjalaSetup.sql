--
-- This file invokes a series of other files to create a Smart
-- database (`njala_dev`) with a core set of fundamental tables
-- for an academic records system and several sample users with various
-- levels of access.  Edit the smartAuthsSetup.sql file to create better
-- user accounts.
--

--
-- Current Database: `njala_dev`
--

drop database if exists `njala_dev`;

create database `njala_dev`;
use `njala_dev`;

--
-- Read in various files to set up tables that form the core of an
-- academic records system:
--      - a table for information about academic terms
--      - tables for information about schools, departments, and programs
--      - tables for information about people generally (names,
--          demographic and contact information, etc) and about staff
--          members more specifically (job titles, contract information, etc.)
--      - tables for information about course modules and specific
--          offerings
--      - tables for information about students and their academic progress
--

source smartTermsSetup.sql

source smartProgramSetup.sql

source smartPersonStaffSetup.sql

source smartModuleSetup.sql

source smartStudentSetup.sql

--
-- Read in User Authorization tables.
--

source smartAuthsSetup.sql
