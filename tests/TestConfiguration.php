<?php

TestConfiguration::setup();

class TestConfiguration
{
    static function setup()
    {
    }

    static function setupDatabase()
    {
        $db = Zend_Registry::get('db');

        // The "ramp_auth_users" table must have that name, because used by the 
        // application.
        $db->query(<<<EOT
DROP TABLE IF EXISTS ramp_auth_users;
EOT
        );

        $db->query(<<<EOT
CREATE TABLE ramp_auth_users (
    id INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
    username VARCHAR ( 100 ) NOT NULL ,
    password VARCHAR ( 40 ) NOT NULL ,
    first_name VARCHAR ( 100 ),
    last_name VARCHAR ( 100 ),
    email VARCHAR ( 150 ) NOT NULL,
    role VARCHAR ( 100 )
);
EOT
        );

        $db->query(<<<EOT
INSERT INTO ramp_auth_users (id, first_name, last_name, username,
                             password, email)
VALUES
(1, 'Charlie', 'Brown', 'cbrown', 'cbrown', 'cbrown@univ.edu')
, (2, 'Lucy', 'Van Pelt', 'lvanpelt', 'lvanpelt', 'lvanpelt@univ.edu')
, (3, 'Bart', 'Simpson', 'bsimpson', 'bsimpson', 'bsimpson@univ.edu')
;
EOT
        );

        $db->query(<<<EOT
DROP TABLE IF EXISTS ramp_test_addresses;
EOT
        );

        $db->query(<<<EOT
CREATE TABLE ramp_test_addresses (
    addr_id INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
    userid INT NOT NULL ,
    address1 VARCHAR ( 100 ) NULL ,
    address2 VARCHAR ( 100 ) NULL ,
    town VARCHAR ( 75 ) NULL DEFAULT 'London',
    county VARCHAR ( 75 ) NULL ,
    postcode VARCHAR ( 30 ) NULL ,
    country VARCHAR ( 75 ) NULL DEFAULT 'UK'
)
EOT
        );

        $db->query(<<<EOT
INSERT INTO ramp_test_addresses (addr_id, userid, address1)
VALUES
(1, 1, 'Brown Boulevard')
, (2, 2, 'Lucy Lane')
, (3, 3, 'Simpson Street')
;
EOT
        );

        $db->query(<<<EOT
DROP TABLE IF EXISTS ramp_tabletest1;
EOT
        );

        $db->query(<<<EOT
CREATE TABLE ramp_tabletest1 (
    id INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
    date_created DATETIME NOT NULL ,
    date_updated DATETIME NOT NULL ,
    name VARCHAR ( 100 ) NOT NULL ,
    address1 VARCHAR ( 100 ) NULL ,
    address2 VARCHAR ( 100 ) NULL ,
    address3 VARCHAR ( 100 ) NULL ,
    town VARCHAR ( 75 ) NULL DEFAULT 'London',
    county VARCHAR ( 75 ) NULL ,
    postcode VARCHAR ( 30 ) NULL ,
    country VARCHAR ( 75 ) NULL DEFAULT 'UK'
)
EOT
        );

        $db->query(<<<EOT
INSERT INTO ramp_tabletest1 (name, address1, town, county, postcode,
    date_created, date_updated)
VALUES
('London Zoo', 'Regent''s Park', 'London', '', 'NW1 4RY',
    '2007-02-14 00:00:00', '2007-02-14 00:00:00')
, ('Alton Towers', 'Regent''s Park', 'Alton', 'Staffordshire', 'ST10 4DB',
    '2007-02-20 00:00:00', '2007-02-20 00:00:00')
, ('Coughton Court', '', 'Alcester', 'Warwickshire', 'B49 5JA',
    '2007-02-16 00:00:00', '2007-02-16 00:00:00')
;
EOT
        );

        $db->query(<<<EOT
DROP TABLE IF EXISTS albums;
EOT
        );

        $db->query(<<<EOT
CREATE TABLE albums (
    id INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
    artist VARCHAR ( 100 ) NOT NULL DEFAULT 'The Beatles',
    title VARCHAR ( 100 ) NOT NULL
)
EOT
        );

        $db->query(<<<EOT
INSERT INTO albums (artist, title)
VALUES
('Paolo Nutine', 'Sunny Side Up')
, ('Florence + The Machine', 'Lungs')
, ('Massive Attack', 'Heligoland')
, ('Andre Rieu', 'Forever Vienna')
, ('Sade', 'Soldier of Love')
, ('The Beatles', 'Abbey Road')
, ('The Beatles', 'White Album')
;
EOT
        );

        $db->query(<<<EOT
DROP TABLE IF EXISTS albums_variant;
EOT
        );

        $db->query(<<<EOT
CREATE TABLE albums_variant (
    id INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
    artist VARCHAR ( 100 ) NOT NULL DEFAULT 'The Beatles',
    title VARCHAR ( 100 ) NOT NULL
)
EOT
        );

        $db->query(<<<EOT
DROP TABLE IF EXISTS ramp_enumTesting;
EOT
        );

        $db->query(<<<EOT
CREATE TABLE ramp_enumTesting (
    id INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
    status ENUM('Proposed', 'Active', 'Inactive') NOT NULL,
    gender ENUM('Unknown', 'M', 'F') NOT NULL DEFAULT 'Unknown'
)
EOT
        );

        $db->query(<<<EOT
DROP TABLE IF EXISTS ramp_test_terms;
EOT
        );

        $db->query(<<<EOT
CREATE TABLE ramp_test_terms (
    term VARCHAR( 10 ) NOT NULL PRIMARY KEY,
    acadYear VARCHAR( 10 ),
    startDate DATE NOT NULL,
    censusDate DATE NOT NULL,
    endDate DATE NOT NULL,
    updated TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP
)
EOT
        );

        $db->query(<<<EOT
INSERT INTO ramp_test_terms (acadYear, term, startDate, censusDate, endDate)
VALUES
('2011-12', '2011Q4', '2011-09-10', '2011-09-15', '2011-12-15')
, ('2011-12', '2012Q1', '2012-01-01', '2012-01-05', '2012-03-20')
, ('2011-12', '2012Q2', '2012-04-01', '2012-04-05', '2012-06-20')
, ('2012-13', '2012Q4', '2012-09-10', '2012-09-15', '2012-12-15')
, ('2012-13', '2013Q1', '2012-01-01', '2012-01-05', '2013-03-20')
, ('2012-13', '2013Q2', '2012-04-01', '2012-04-05', '2013-06-20')
;
EOT
        );

        $db->query(<<<EOT
DROP TABLE IF EXISTS ramp_initTesting;
EOT
        );

        $db->query(<<<EOT
CREATE TABLE ramp_initTesting (
    id INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
    userid INT NOT NULL ,
    fname VARCHAR ( 100 ),
    lname VARCHAR ( 100 ),
    term VARCHAR ( 15 ),
    acadYear VARCHAR ( 10 ),
    album_id INT,
    artist VARCHAR ( 100 ) NOT NULL DEFAULT 'The Beatles',
    title VARCHAR ( 100 ) NOT NULL
)
EOT
        );

        $db->query(<<<EOT
DROP TABLE IF EXISTS ramp_valsTableTesting;
EOT
        );

        $db->query(<<<EOT
CREATE TABLE ramp_valsTableTesting (
    term VARCHAR ( 15 ) NOT NULL PRIMARY KEY
)
EOT
        );

        $db->query(<<<EOT
INSERT INTO ramp_valsTableTesting
VALUES
('2008-09 Sem 1')
, ('2008-09 Sem 2')
, ('2009-10 Sem 1')
, ('2009-10 Sem 2')
, ('2010-11 Sem 1')
, ('2010-11 Sem 2')
, ('2011-12 Sem 1')
, ('2011-12 Sem 2')
, ('2012-13 Sem 1')
, ('2012-13 Sem 2')
, ('2013-14 Sem 1')
, ('2013-14 Sem 2')
;
EOT
        );

        $db->query(<<<EOT
DROP TABLE IF EXISTS ModuleAssignments;
EOT
        );

        $db->query(<<<EOT
CREATE TABLE ModuleAssignments (
    pk_id INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
    term VARCHAR ( 10 ) NOT NULL,
    moduleID INT NOT NULL,
    section VARCHAR ( 3 ),
    staffID INT NOT NULL,
    percentage INT NOT NULL DEFAULT 100,
    classroomNumber VARCHAR ( 6 ),
    classroomBuilding VARCHAR ( 20 ),
    weeklySchedule VARCHAR ( 50 ),
    startDate DATE,
    endDate DATE,
    updated TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP
)
EOT
        );

        // still under construction

    }
}


