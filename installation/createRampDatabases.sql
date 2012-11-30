#
# RAMP: Record and Activity Management Program
#
# Create the basic user account for the RAMP database administrator and,
# optionally, one or more domain user accounts.
#

#
# Please read the installation instructions in INSTALL_DB.txt
# BEFORE executing these SQL instructions in your database.
#

#
# Create rampdba account for RAMP Database Administrator.
# 
CREATE USER 'rampdba'@'localhost' IDENTIFIED BY 'ramppass';

#
# Set up privileges for rampdba to access the demonstration and test
# databases, ramp_demo and ramp_test.
#

GRANT ALL ON `ramp_demo`.* TO 'rampdba'@'localhost';
GRANT ALL ON `ramp_test`.* TO 'rampdba'@'localhost';

#
# Set up privileges for rampdba to access the domain database created
# for this application.
#
# GRANT ALL ON `domain_db`.* TO 'rampdba'@'localhost';

#
# Create user account(s) for Domain User(s).  (There may be more than
# one domain user account, possibly with different privileges defined.
# Or the domain user account(s) may be defined in a different file,
# such as createSmartDatabases.)
# 
# CREATE USER 'rampdemo'@'localhost' IDENTIFIED BY 'rampdemopass';
# CREATE USER 'rampuser'@'localhost' IDENTIFIED BY 'rampuserpass';

#
# As an example, the rampdemo user might have privileges to view data
# in tables, but not add, modify, or delete it.  A normal domain user
# might have privileges to view, add, edit, and delete data in tables,
# but not to change table schemas.
#
# GRANT SELECT ON `ramp_demo`.* TO 'rampdemo'@'localhost', 'rampdemo'@'%';
# GRANT SELECT ON `ramp_demo`.* TO 'rampuser'@'localhost', 'rampuser'@'%';
# GRANT SELECT, INSERT, UPDATE, DELETE ON `ramp_test`.* TO 'rampuser'@'localhost';

#
# For each database, create the "users" table, used for authentication and
# authorization.  For most databases, there should be one (or possibly two)
# initial user record for the database administrator(s), who would then
# be responsible for creating new user accounts.  The demo database is a
# special case, needing only a single "guest" user account.
#
# Note that the user accounts and passwords in the users table are separate
# from the SQL accounts created above; the similarities in the
# examples below are due to lack of creativity.
#

USE ramp_demo;
DROP TABLE IF EXISTS users;
CREATE TABLE users (
    id INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
    username VARCHAR ( 100 ) NOT NULL ,
    password VARCHAR ( 40 ) NOT NULL ,
    first_name VARCHAR ( 100 ),
    last_name VARCHAR ( 100 ),
    email VARCHAR ( 150 ) NOT NULL,
    role VARCHAR ( 100 )
);
INSERT INTO users (first_name, last_name, username, password, email, role)
VALUES
('Guest', 'Guest', 'guest', 'guest', '', 'guest')
;

USE ramp_test;
DROP TABLE IF EXISTS users;
CREATE TABLE users (
    id INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
    username VARCHAR ( 100 ) NOT NULL ,
    password VARCHAR ( 40 ) NOT NULL ,
    first_name VARCHAR ( 100 ),
    last_name VARCHAR ( 100 ),
    email VARCHAR ( 150 ) NOT NULL,
    role VARCHAR ( 100 )
);
INSERT INTO users (first_name, last_name, username, password, email, role)
VALUES
('FirstName', 'LastName', 'rampdba', 'ramppass', 'emailAddr@yahoo.com', 'dba')
;
