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
# Set up privileges for rampdba to access the domain database(s) created
# for this application.
#
# GRANT ALL ON `domain_db`.* TO 'rampdba'@'localhost';

#
# Create user account(s) for Domain User(s).
# Alternatively, the domain user account(s) may be defined in a different
# file, such as createSmartDatabases.  (There may be more than one
# domain user account, possibly with different privileges defined.)
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
# For each database, create the "ramp_auth_users" and "ramp_auth_auths"
# tables, used for authentication and authorization.
#

#
# Users:
# ramp_auth_users: For most databases, there should be one
# (or possibly two) initial user record for the database administrator(s),
# who would then be responsible for creating new user accounts.  All
# roles, including the ramp_dba role (or something similar), should be
# defined in configs/application.ini.  (In the ramp_test example below,
# ramp_dba and ramp_user should both be defined as roles in application.ini.)
#
# The demo database can be a special case, with only a single "guest"
# user account with the default (built-in) 'guest' role.
#

USE ramp_demo;
DROP TABLE IF EXISTS ramp_auth_users;
CREATE TABLE ramp_auth_users (
    id INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
    username VARCHAR ( 100 ) NOT NULL ,
    password VARCHAR ( 40 ) NOT NULL ,
    role VARCHAR ( 100 ) NOT NULL DEFAULT 'guest' ,
    email VARCHAR ( 150 ) NOT NULL ,
    first_name VARCHAR ( 100 ) ,
    last_name VARCHAR ( 100 ) ,
    domainID INT
);
INSERT INTO ramp_auth_users (first_name, last_name, username, password,
    email, role)
VALUES ('Guest', 'Guest', 'guest', 'guest', '', 'guest')
;

USE ramp_test;
DROP TABLE IF EXISTS ramp_auth_users;
CREATE TABLE ramp_auth_users (
    id INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
    username VARCHAR ( 100 ) NOT NULL ,
    password VARCHAR ( 40 ) NOT NULL ,
    role VARCHAR ( 100 ) NOT NULL DEFAULT 'guest' ,
    email VARCHAR ( 150 ) NOT NULL ,
    first_name VARCHAR ( 100 ) ,
    last_name VARCHAR ( 100 ) ,
    domainID INT
);
INSERT INTO ramp_auth_users (first_name, last_name, username, password,
    email, role)
VALUES
('Database', 'Administrator', 'dba', 'ramppass', 'emailAddr@yahoo.com',
    'ramp_dba')
, ('Backup', 'DBA', 'backup_dba', 'backuppass', 'emailAddr2@gmail.com',
    'ramp_dba')
, ('A', 'User', 'user1', 'userpass', 'auser@abc.com', 'ramp_user')
, ('Guest', 'Guest', 'guest', 'guest', '', 'guest')
;

#
# Resources and Authorizations:
# ramp_auth_auths: For most databases, the 'guest' role should have very
# limited privileges, 'ramp_dba' should have all privileges, and other
# roles will have intermediate levels of privileges.
# The demo database is again a special case if it has just a single user.
#
# The ramp_demo example below assumes that there is at least one
# demo-related activity file in a directory called 'demo' and that it
# refers to a defined table in the database called 'places' which guests
# may view but not alter.
#
# The ramp_test example below assumes that users with the default 'guest' 
# role have no special privileges, whereas users with the 'rampuser' role
# may view records in a table called 'readOnlyTable' and view, add, or alter
# (but not delete) records in a table called 'readWriteTable'.  Users
# with the 'ramp_dba' role have permisions to view and alter the
# 'ramp_auth_users' and 'ramp_auth_auths' tables (and may have all
# 'rampuser' permissions as well if the 'ramp_dba' role was defined as
# inheriting from the 'rampuser' role in application.ini).
#

USE ramp_demo;
DROP TABLE IF EXISTS ramp_auth_auths;
CREATE TABLE ramp_auth_auths (
    id INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
    role VARCHAR ( 100 ) NOT NULL ,
    resource_type ENUM ('Activity', 'Table') NOT NULL ,
    resource_name VARCHAR ( 100 ) NOT NULL ,
    action ENUM ('All', 'View', 'AddRecords', 'ModifyRecords', 'DeleteRecords') NOT NULL DEFAULT 'View'
);
INSERT INTO ramp_auth_auths (role, resource_type, resource_name, action)
VALUES
('guest', 'Activity', 'demo', 'All')
, ('guest', 'Table', 'places', 'View')
;

USE ramp_test;
DROP TABLE IF EXISTS ramp_auth_auths;
CREATE TABLE ramp_auth_auths (
    id INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
    role VARCHAR ( 100 ) NOT NULL ,
    resource_type ENUM ('Activity', 'Table') NOT NULL ,
    resource_name VARCHAR ( 100 ) NOT NULL ,
    action ENUM ('All', 'View', 'AddRecords', 'ModifyRecords', 'DeleteRecords') NOT NULL DEFAULT 'View'
);
INSERT INTO ramp_auth_auths (role, resource_type, resource_name, action)
VALUES
('rampuser', 'Activity', 'domainDirectory', 'All')
, ('rampuser', 'Table', 'readOnlyTable', 'View')
, ('rampuser', 'Table', 'readWriteTable', 'View')
, ('rampuser', 'Table', 'readWriteTable', 'AddRecords')
, ('rampuser', 'Table', 'readWriteTable', 'ModifyRecords')
, ('ramp_dba', 'Activity', 'ManageAuths', 'All')
, ('ramp_dba', 'Table', 'ramp_auth_users', 'All')
, ('ramp_dba', 'Table', 'ramp_auth_auths', 'All')
;
