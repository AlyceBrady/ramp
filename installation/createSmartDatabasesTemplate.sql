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

#
# Define appropriate user privileges on testing, development, and
# production databases.
#

# GRANT SELECT, INSERT, UPDATE, DELETE, TRIGGER ON `smart_test`.* TO 'smartuser'@'localhost', 'smartuser'@'%';
# GRANT EXECUTE ON PROCEDURE `smart_test`.`cancelstudentreg` TO 'smartuser'@'localhost', 'smartuser'@'%';
# GRANT EXECUTE ON FUNCTION `smart_test`.`termcensusdate` TO 'smartuser'@'localhost', 'smartuser'@'%';
# GRANT EXECUTE ON FUNCTION `smart_test`.`modofferingenddate` TO 'smartuser'@'localhost', 'smartuser'@'%';

# GRANT SELECT, INSERT, UPDATE, DELETE, TRIGGER ON `smart_dev`.* TO 'smartuser'@'localhost', 'smartuser'@'%';
# GRANT EXECUTE ON PROCEDURE `smart_dev`.`cancelstudentreg` TO 'smartuser'@'localhost', 'smartuser'@'%';
# GRANT EXECUTE ON FUNCTION `smart_dev`.`termcensusdate` TO 'smartuser'@'localhost', 'smartuser'@'%';
# GRANT EXECUTE ON FUNCTION `smart_dev`.`modofferingenddate` TO 'smartuser'@'localhost', 'smartuser'@'%';
