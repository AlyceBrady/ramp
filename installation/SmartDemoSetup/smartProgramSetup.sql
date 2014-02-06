--
-- Table for information about academic programs.
--


USE `smart_demo`;

-- NOTE: Once the Requirements table & its relatives have been set up,
-- dropping AcadProgram will require dropping some Requirements tables
-- first.

DROP TABLE IF EXISTS AcadProgram;

CREATE TABLE AcadProgram (
    programID INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
    title VARCHAR ( 30 ) NOT NULL,
    type ENUM('Coursework', 'B.A.', 'B.S.', 'M.Sc.', 'Ph.D.', 'Major', 'Minor')
        NOT NULL DEFAULT 'Coursework',
    school VARCHAR ( 30 ) NOT NULL,
    division VARCHAR ( 30 ),
    department VARCHAR ( 30 ),
    startDate DATE NOT NULL,
    endDate DATE,
    updated TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP
);

INSERT INTO AcadProgram (programID, title, type,
    school, department, startDate, endDate)
VALUES
(1, "Bachelor of Arts", "B.A.", "K", DEFAULT, "1833-09-01", NULL)
, (10, "Computer Science", "Major", "K", "Math/CS", "1979-09-01", NULL)
, (11, "Computer Science", "Minor", "K", "Math/CS", "1979-09-01", NULL)
, (20, "Literature", "Major", "K", "Literature/Writing", "1950-09-01", NULL)
, (21, "Literature", "Minor", "K", "Literature/Writing", "1970-09-01", NULL)
, (22, "Creative Writing", "Major", "K", "Literature/Writing", "1950-09-01", NULL)
, (23, "Creative Writing", "Minor", "K", "Literature/Writing", "1970-09-01", NULL)
, (05, "Rhetoric", "Major", "K", "Literature/Writing", "1870-09-01", "1950-09-01")
, (06, "Mathematics", "Major", "K", "Mathematics", "1870-09-01", "1979-09-01")
, (07, "Mathematics", "Minor", "K", "Mathematics", "1970-09-01", "1979-09-01")
, (30, "Mathematics", "Major", "K", "Math/CS", "1979-09-01", NULL)
, (31, "Mathematics", "Minor", "K", "Math/CS", "1979-09-01", NULL)
;

