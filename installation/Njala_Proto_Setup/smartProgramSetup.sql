--
-- Table for information about schools, departments, and academic programs.
--


USE `njala_proto`;

DROP TABLE IF EXISTS AcadProgramTypes;
DROP TABLE IF EXISTS AcadProgram;


CREATE TABLE AcadProgramTypes (
    pk_id INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
    type VARCHAR ( 15 ) NOT NULL,
    updated TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE AcadProgram (
    programID INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
    title VARCHAR ( 100 ) NOT NULL,
    shortTitle VARCHAR ( 38 ) NOT NULL,
    type VARCHAR ( 15 ) NOT NULL DEFAULT 'Coursework',
    schoolCode VARCHAR ( 8 ) DEFAULT NULL,
    deptCode VARCHAR ( 8 ) DEFAULT NULL,
    startDate DATE,
    endDate DATE,
    updated TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP ,
--    FOREIGN KEY (deptID) REFERENCES Departments (deptID),
--    FOREIGN KEY (schoolID) REFERENCES Schools (schoolID),
    INDEX (schoolCode),
    INDEX (deptCode)
);

