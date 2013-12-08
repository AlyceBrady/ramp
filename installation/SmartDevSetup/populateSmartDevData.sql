-- Populate the Smart Demo database.

--
-- This file contains SQL code to create sample Smart tables that
-- have corresponding table settings in the settings/demo directory.
--

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

