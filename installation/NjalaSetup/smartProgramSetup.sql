-
-- Table for information about schools, departments, and academic programs.
--



DROP TABLE IF EXISTS Schools;

CREATE TABLE Schools (
    schoolID INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
    code VARCHAR ( 6 ) NOT NULL,
    name VARCHAR ( 30 ) NOT NULL,
    startDate DATE NOT NULL,
    endDate DATE,
    updated TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP
);

INSERT INTO Schools (code, name)
VALUES
("AGRIC", "School of Agriculture")
,("ENVSC", "School of Environmental Sciences")
,("SOC", "School of Social Sciences")
,("FOH", "School of Forestry & Horticulture")
,("EDUC", "School of Education")
,("TECH", "School of Technology")
,("SCHS", "School of Community Health Sciences")
,("POST", "School of Postgraduate Studies")
;

DROP TABLE IF EXISTS Departments;

CREATE TABLE Departments (
    deptID INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
    code VARCHAR ( 6 ) NOT NULL,
    name VARCHAR ( 30 ) NOT NULL,
    schoolID INT,
    division VARCHAR ( 30 ),
    startDate DATE NOT NULL,
    endDate DATE,
    updated TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP
);

INSERT INTO Departments (code, name, schoolID)
VALUES
("ANS", "Animal Science", 1)
,("FOR", "Forestry", 4)
,("HORT", "Horticulture", 4)
,("SOILS", "Soil Science", 1)
,("AEX", "Agriculture Extension", 1)
,("FEX", "Forestry Extension", 4)
,("COMS", "Communication Skills", 5)
,("BIOL", "Biology", 2)
,("CHEM", "Chemistry", 2)
,("COMPS", "Computer Science", 6)
,("COMPB", "Business & Information Technology", 6)
,("ELTT", "Electronics & Telecommunication", 6)
,("ENG", "Agric. Engineering", 6)
,("ENE", "Energy Studies", 6)
,("STAT", "Statistics", 6)
,("PHYS", "Physics", 6)
;

DROP TABLE IF EXISTS AcadProgram;

CREATE TABLE AcadProgram (
    programID INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
    title VARCHAR ( 30 ) NOT NULL,
    type ENUM('Coursework', 'B.A.', 'B.Sc.', 'M.A.', 'M.Sc.', 'Ph.D.', 'Major', 'Minor')
        NOT NULL DEFAULT 'Coursework',
    schoolID INT,
    deptID INT,
    startDate DATE NOT NULL,
    endDate DATE,
    updated TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP
);

INSERT INTO AcadProgram (title, type, schoolID, deptID, startDate)
VALUES
("B.A. in Literature (Honors)", "B.A.", 5, DEFAULT, "1833-09-01")
, ("B.A. in Linguistics (Honors)", "B.A.", 5, DEFAULT, "1833-09-01")
, ("B.A. in Language Arts (Honors)", "B.A.", 5, DEFAULT, "1833-09-01")
, ("B.Ed. in Education", "B.Sc.", 5, DEFAULT, "1965-09-01")
, ("B.Sc. in Economics", "B.Sc.", 5, DEFAULT, "1965-09-01")
, ("B.Sc. in Banking and Finance (Honors)", "B.Sc.", 3, DEFAULT, "1965-09-01")
, ("B.Sc. in Agriculture (Honors)", "B.Sc.", 1, DEFAULT, "1955-09-01")
, ("M.Sc. in Agriculture", "M.Sc.", 1, DEFAULT, "1955-09-01")
, ("B.Sc. Forestry (Honors)", "Major", DEFAULT, 2, "1975-09-01")
, ("B.Sc. Biology Education", "B.Sc.", DEFAULT, 2, "1975-09-01")
, ("B.Sc. Biology (Honors)", "B.Sc.", DEFAULT, 2, "1975-09-01")
, ("Animal Science", "Major", DEFAULT, 1, "1975-09-01")
, ("Horticulture", "Major", DEFAULT, 3, "1975-09-01")
, ("B.Sc. Soil Science (Honors)", "Major", DEFAULT, 4, "1975-09-01")
, ("Biology", "Major", DEFAULT, 8, "1975-09-01")
, ("Biology", "Major", DEFAULT, 8, "1975-09-01")
, ("Chemistry", "Major", DEFAULT, 9, "1975-09-01")
, ("Physics", "Major", DEFAULT, 11, "1975-09-01")
, ("Computer Science", "Major", DEFAULT, 10, "2005-09-01")
;
