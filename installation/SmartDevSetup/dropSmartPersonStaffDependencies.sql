
-- The Student table has foreign keys to Person
SOURCE dropSmartStudentDependencies.sql
DROP TABLE IF EXISTS Student;

-- The following table(s) have foreign keys to Staff
DROP TABLE IF EXISTS ModuleAssignments;

-- The following additional table(s) have foreign keys to Person
DROP TABLE IF EXISTS StaffContract;
DROP TABLE IF EXISTS Staff;

