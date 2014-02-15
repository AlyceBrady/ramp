
-- The Student table has foreign keys to Person
SOURCE dropSmartStudentDependencies.sql
DROP TABLE IF EXISTS Applicant;
DROP TABLE IF EXISTS Student;

-- The following table(s) have foreign keys to Staff
-- DROP TABLE IF EXISTS StaffPersonalInfo;
DROP TABLE IF EXISTS Children;
DROP TABLE IF EXISTS ModuleAssignments;

-- The following additional table(s) have foreign keys to Person

DROP TABLE IF EXISTS RecordHold;
DROP TABLE IF EXISTS RelatedNames;
DROP TABLE IF EXISTS Address;
DROP TABLE IF EXISTS PhoneNumber;
DROP TABLE IF EXISTS InstitutionsAttended;
DROP TABLE IF EXISTS StaffContract;
DROP TABLE IF EXISTS JobFunction;
DROP TABLE IF EXISTS Staff;

