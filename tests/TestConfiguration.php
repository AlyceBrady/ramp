<?php

TestConfiguration::setup();

class TestConfiguration
{
    static function setup()
    {
        $lib = realpath(dirname(__FILE__) . '/../../../library/');
        set_include_path(get_include_path()
            . PATH_SEPARATOR . $lib);

        require_once dirname(__FILE__) . '/../application/bootstrap.php';
        self::$bootstrap = new Bootstrap('brim_test');
    }

    static function setupDatabase()
    {
        $db = Zend_Registry::get('db');

        // The "users" table must have that name, because used by the 
        // application.
        $db->query(<<<EOT
DROP TABLE IF EXISTS users;
EOT
        );

        $db->query(<<<EOT
CREATE TABLE users (
    id INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
    username VARCHAR ( 100 ) NOT NULL ,
    password VARCHAR ( 40 ) NOT NULL ,
    first_name VARCHAR ( 100 ),
    last_name VARCHAR ( 100 ),
    email VARCHAR ( 150 ) NOT NULL
)
EOT
        );

        $db->query(<<<EOT
INSERT INTO users (first_name, last_name, username, password, email)
VALUES
('Alyce', 'Brady', 'abrady', 'abrady', 'abrady@kzoo.edu')
, ('Casey', 'Delaney', 'cdelaney', 'cdelaney', 'cdelaney@kzoo.edu')
, ('Lanny', 'Potts', 'lpotts', 'lpotts', 'lpotts@kzoo.edu')
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
DROP TABLE IF EXISTS ramp_tabletest2;
EOT
        );

        $db->query(<<<EOT
CREATE TABLE ramp_tabletest2 (
    id INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
    artist VARCHAR ( 100 ) NOT NULL DEFAULT 'The Beatles',
    title VARCHAR ( 100 ) NOT NULL
)
EOT
        );

        $db->query(<<<EOT
INSERT INTO ramp_tabletest2 (artist, title)
VALUES
('Paolo Nutine', 'Sunny Side Up')
, ('Florence + The Machine', 'Lungs')
, ('Massive Attack', 'Heligoland')
, ('Andre Rieu', 'Forever Vienna')
, ('Sade', 'Soldier of Love')
, ('The Beatles', 'Abbey Road')
;
EOT
        );

        $db->query(<<<EOT
DROP TABLE IF EXISTS ramp_emptytabletest1;
EOT
        );

        $db->query(<<<EOT
CREATE TABLE ramp_emptytabletest1 (
    id INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
    artist VARCHAR ( 100 ) NOT NULL DEFAULT 'The Beatles',
    title VARCHAR ( 100 ) NOT NULL
)
EOT
        );

        // continues (but not yet!)
    }
}


