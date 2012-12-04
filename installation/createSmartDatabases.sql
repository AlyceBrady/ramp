#
# SMART: Software for Managing Academic Records and Transcripts
#   (built on RAMP, the Record and Activity Management Program)
#
# Set up privileges for the Smart database administrator to access
# Smart production, development, and test databases.
#

#
# Before executing the SQL statements in this file, please read the
# installation instructions in INSTALL_DB.txt and execut the SQL
# statements the createRampDBA.sql.  If the "rampdba" user name has been
# changed to something else in createRampDBA.sql ("smartdba", for example), 
# then it should be changed to the same thing in this file.
#

#
# Set up full set of privileges for the Database Administrator to access
# all Smart databases.  (To minimize the chances of serious errors,
# it is best to keep development and production databases on different
# servers; uncomment only one of these database in this file.)
#

# Demo and test databases:
GRANT ALL ON `smart_demo`.* TO 'rampdba'@'localhost';
# GRANT ALL ON `smart_test`.* TO 'rampdba'@'localhost';
# GRANT ALL ON `smart_user_tests`.* TO 'rampdba'@'localhost';

# Development or Production database:
# GRANT ALL ON `smart_dev`.* TO 'rampdba'@'localhost';
# GRANT ALL ON `smart`.* TO 'rampdba'@'localhost';

#
# Create user account(s) for Domain User(s).  (There may be more than
# one domain user account, possibly with different privileges defined.)
# 
#

CREATE USER 'smartdemo'@'localhost' IDENTIFIED BY 'smartdemopass';
CREATE USER 'smartuser'@'localhost' IDENTIFIED BY 'smartuserpass';
# CREATE USER 'smartadmin'@'localhost' IDENTIFIED BY 'adminpass';
# CREATE USER 'smartstaff'@'localhost' IDENTIFIED BY 'staffpass';
# CREATE USER 'smartstudent'@'localhost' IDENTIFIED BY 'studentpass';

#
# Define appropriate user privileges on the demo database.
#

GRANT SELECT ON `smart_demo`.* TO 'smartdemo'@'localhost', 'smartdemo'@'%';

GRANT ALL ON `smart_demo`.* TO 'rampdba'@'localhost';
GRANT EXECUTE ON PROCEDURE `smart_demo`.`cancelstudentreg` TO 'rampdba'@'localhost';
GRANT EXECUTE ON FUNCTION `smart_demo`.`termcensusdate` TO 'rampdba'@'localhost';
GRANT EXECUTE ON FUNCTION `smart_demo`.`modofferingenddate` TO 'rampdba'@'localhost';

#
# Define appropriate user privileges on testing and development databases
# (other databases would be similar).
#

# GRANT ALL ON `smart_test`.* TO 'rampdba'@'localhost';
# GRANT SELECT, INSERT, UPDATE, DELETE, TRIGGER ON `smart_test`.* TO 'smartuser'@'localhost', 'smartuser'@'%';
# GRANT EXECUTE ON PROCEDURE `smart_test`.`cancelstudentreg` TO 'rampdba'@'localhost', 'smartuser'@'localhost', 'smartuser'@'%';
# GRANT EXECUTE ON FUNCTION `smart_test`.`termcensusdate` TO 'rampdba'@'localhost', 'smartuser'@'localhost', 'smartuser'@'%';
# GRANT EXECUTE ON FUNCTION `smart_test`.`modofferingenddate` TO 'rampdba'@'localhost', 'smartuser'@'localhost', 'smartuser'@'%';

# GRANT ALL ON `smart_dev`.* TO 'rampdba'@'localhost';
# GRANT SELECT, INSERT, UPDATE, DELETE, TRIGGER ON `smart_dev`.* TO 'smartuser'@'localhost', 'smartuser'@'%';
# GRANT EXECUTE ON PROCEDURE `smart_dev`.`cancelstudentreg` TO 'rampdba'@'localhost', 'smartuser'@'localhost', 'smartuser'@'%';
# GRANT EXECUTE ON FUNCTION `smart_dev`.`termcensusdate` TO 'rampdba'@'localhost', 'smartuser'@'localhost', 'smartuser'@'%';
# GRANT EXECUTE ON FUNCTION `smart_dev`.`modofferingenddate` TO 'rampdba'@'localhost', 'smartuser'@'localhost', 'smartuser'@'%';

#
# Define a guest user for the smart_demo database similar to the one defined
# for ramp_demo in createRampDatabases.sql.
#
# Define example users for the smart_dev databases (other databases
# would be more similar to smart_dev than to smart_demo).  These examples
# assume that application.ini includes definitions for at least 4 roles:
# ramp_dba, guest, hr_staff, regist_staff, and manager (who, in this
# example, manages both human resources and registrar staff).
#

USE smart_dev;
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
, ('HumanResources', 'Staff', 'hr1', 'hr1pass', 'hr1@abc.edu', 'hr_staff')
, ('AnotherHR', 'Staff', 'hr2', 'hr2pass', 'hr2@abc.edu', 'hr_staff')
, ('Registrar', 'Staff', 'regist1', 'regist1pass', 'regist1@abc.edu', 'regist_staff')
, ('AnotherRegist', 'Staff', 'regist2', 'regist2pass', 'regist2@abc.edu', 'regist_staff')
, ('A', 'Manager', 'manager', 'managerpass', 'manager@abc.edu', 'manager')
;

#
# Define access control rules for the 4 roles in the example above.
# Note that in this example, the manager has *fewer* permissions than the
# staff (for example, the manger may be viewing tables for review and
# reporting while the staff have responsibility for data entry).  This
# may not reflect the actual responsibilities in a real office.
#

USE smart_dev;
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
('hr_staff', 'Activity', 'Smart/Person', 'All')
, ('hr_staff', 'Activity', 'Smart/Staff', 'All')
, ('hr_staff', 'Table', 'Person', 'View')
, ('hr_staff', 'Table', 'Person', 'AddRecords')
, ('hr_staff', 'Table', 'Staff', 'All')
, ('hr_staff', 'Table', 'StaffContract', 'All')
, ('regist_staff', 'Table', 'Person', 'View')
, ('regist_staff', 'Table', 'Person', 'AddRecords')
, ('regist_staff', 'Table', 'Staff', 'View')
, ('regist_staff', 'Table', 'Student', 'All')
, ('regist_staff', 'Table', 'ModuleOfferings', 'All')
, ('regist_staff', 'Table', 'ModuleAssignments', 'All')
, ('regist_staff', 'Table', 'Enrollment', 'All')
, ('manager', 'Table', 'Person', 'View')
, ('manager', 'Table', 'Staff', 'View')
, ('manager', 'Table', 'StaffContract', 'View')
, ('manager', 'Table', 'Student', 'View')
, ('manager', 'Table', 'ModuleOfferings', 'View')
, ('manager', 'Table', 'ModuleAssignments', 'View')
, ('manager', 'Table', 'Enrollment', 'View')
, ('ramp_dba', 'Activity', 'ManageAuths', 'All')
, ('ramp_dba', 'Table', 'ramp_auth_users', 'All')
, ('ramp_dba', 'Table', 'ramp_auth_auths', 'All')
;
