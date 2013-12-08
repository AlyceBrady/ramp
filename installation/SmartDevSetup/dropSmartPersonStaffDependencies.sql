
-- The following table(s) have foreign keys to Student and Person
DROP TABLE IF EXISTS Advising;

-- The following table(s) have foreign keys to Student (depends on Person)
DROP TABLE IF EXISTS Enrollment;
DROP TABLE IF EXISTS TermStanding;

-- The following table(s) have foreign keys to Staff
DROP TABLE IF EXISTS ModuleAssignments;

-- The following table(s) have foreign keys to Person

DROP TABLE IF EXISTS Student;
DROP TABLE IF EXISTS StaffContract;
DROP TABLE IF EXISTS Staff;

