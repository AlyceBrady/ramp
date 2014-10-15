
# Installing a Customized RAMP/SMART Application #

(under construction!  So far, this document is mostly a previous
version of much of the regular INSTALL document.)

[ [Planning](#planning) | [Addressing Security Concerns](#security) |
[Configuration](#configuration) ]

This document covers creating and configuring a customized Ramp/Smart
application.  It assumes that you have already installed the Ramp/Smart
software along with the AMP software services on which it depends, as
described in the [Ramp/Smart Installation Guide][install].

As described in the [Ramp/Smart Installation Guide][install],
installing and configuring Ramp/Smart consists of three steps:

1. __Planning:__ The first step is to decide which application you will set up
and then plan your customizations.

2. __Addressing Security Concerns:__ You should check or change permissions on
files that contain passwords and confidential data.

3. __Configuration:__ This step includes configuring the database
(setting up MySQL accounts and defining the tables the application
will use) and setting up a configuration file that defines parameters
used by Ramp/Smart.  If you plan to install one of the three
pre-defined applications, this step consists primarily of editing
a script provided in the appropriate subdirectory to change the
MySQL account names and passwords, running the MySQL script from
the appropriate directory, and creating a configuration file from
templates and editing it to refer to your own specific accounts and
passwords.  If you are creating a customized application, you will
first need to define the database schemas for your tables and create
activity files and table settings for your application.

The [Ramp/Smart Installation Guide][install] covers these three
steps for two demo programs and for a pre-defined, default development
environment.  Even if you plan to create a customized Ramp or Smart
application, as covered in this document, you may wish to set up
one of the three pre-defined applications first, to test your
software installation and to become familiar with some of the basic
functionality of Ramp or Smart.


<h2 id='planning'> 1. Planning </h2>

This section is meant to guide you through some planning stages in
preparation for creating one or more Ramp/Smart databases.  Do not,
however,  make changes to your MySQL database, the files in this
directory, or your `application.ini` file in the `application/configs`
directory before reading the [Addressing Security
Concerns](#security) section.

The planning phase addresses four key questions.

### 1a. Internal or External Authentication? ###
At the moment, Ramp only supports internal authentication (_i.e.,_
authentication against user accounts and passwords created within Ramp).
Support for LDAP authentication, including Active Directory and
OpenLDAP, is, however, being implemented.  Once that support is in
place, you will need to decide whether you plan to create user
accounts within Ramp and use them to authenticate Ramp users, or
whether you plan to authenticate against an existing server, such
as an Active Directory or OpenLDAP server.

### 1b. What databases to set up? ###
The first significant planning step is to decide which database
environments are to be
set up.  For example, one might just set up Ramp and Smart demo
databases to learn more about the application.  A Ramp/Smart
developer, on the other hand, might create development and regression
testing environments, while someone running Smart as a production
system might create production, user testing, and regression testing
environments.

Example databases might be:

       ramp_demo   (a demo of basic RAMP functions)  
       smart_demo  (a demo of typical Smart activities and functions)  

       smart_dev   (a version of Smart for active developers)  

       smart       (a production Smart environment)  
       smart_user_tests      (a production variant containing test data)  
       smart_automated_tests (a database created, recreated, and  
                              populated by automated test cases)  

The "Creating Databases and Setting MySQL Account Permissions"
section below provides information on actually creating Ramp/Smart
databases once you have taken the protective measures outlined in the
"Addressing Security Concerns" section.

### 1c. What MySQL accounts to set up? ###
You will also need two types of MySQL user accounts for your various
databases:  database administrator accounts and Ramp/Smart application
access accounts.  Database administrators are staff with database
expertise who may be creating new tables, adding new fields to
tables, or otherwise changing the structure of the database.  They
do this by logging in and accessing MySQL directly or using database
software such as phpMyAdmin.  If they do not already have MySQL
accounts, you will create them below.  You will also need a MySQL
account for the Ramp/Smart software to use to access (read and write)
data in the database.  If you are going to have multiple databases, you
may use a single Ramp/Smart access account for all of them, or you may
create separate accounts for each database.

Identify a list of accounts you plan to create.  For example, you
might decide you need 4 accounts:

    dba_person_1    (dba account for one of two database admins)
    dba_person_2    (dba account for 2nd of two database admins)
    ramp_demo       (access account for Ramp demo only)
    smart_user      (access account for several Smart databases)

Guidance on actually creating MySQL accounts is provided in the
"Creating MySQL Accounts" section below.

### 1d. What authorization roles to set up? ###
Permission, or authorization, to read tables, add new data, modify
or delete existing data, or carry out other activities in Ramp/Smart
is achieved, not by using different MySQL accounts with specified
privileges, but through a set of roles defined in Ramp/Smart.  For
example, there might be a "ramp_dba" role for users who have permission to
add and delete other users.  There might be a "guest" role for users who
are allowed to read, but not change, a subset of tables in the database.
In Smart, there might be separate roles for staff who update academic
records and staff who update employee records.  There might even be
a role that combines the permissions of the other two by "inheriting"
the privileges of both.

There is one role, 'guest', built into Ramp.  Identify the other,
more meaningful roles you plan to use for your application.  Start
with the simplest roles and then identify more privileged roles
that will inherit all the privileges from one or more simpler roles
and add more privileges to them.  For example,  you might define
the following roles:

    hr_staff role has all guest privileges (plus additional ones)
    regist_staff role has all guest privileges (plus additional ones)
    dept_mgmt role has all hr_staff privileges and all regist_staff
        privileges (plus additional ones, perhaps)

More detailed examples are provided in the `ramp_defaults.ini`
and `smart_defaults.ini` files in the `application/configs`
directory. The "Setting Up Ramp/Smart User Accounts and Authorization"
section below provides information on actually creating Ramp/Smart
roles.


<h2 id="security"> 2. Addressing Security Concerns </h2>

To protect the security of the Ramp/Smart database, the database
administrator responsible for creating and maintaining the database
must take several steps before creating the users and databases
used by Ramp.

* First, make a copy of one of the example files in the installation
  directory for creating mysql accounts.  (These files start
  with create_ramp_mysql_... or create_smart_mysql_....)  You
  could call your copy create_mysql_accts.sql, for example.
  You will edit this later to create your actual MySQL accounts.
  (Instructions for doing this are in the next section.)
  Change the permissions of your new file so that it can be
  read only by yourself or whoever will be responsible for the
  initial installation.  (If you plan to setup and run one of
  the Ramp/Smart demos, you should copy the appropriate
  create...MysqlAccountsTemplate.sql file in the RampDemoSetup or
  SmartDemoSetup directory to a file with the same name
  except without Template (e.g., create...MysqlAccounts.sql) and
  then change the permissions of the new ...MysqlAccount.sql file
  so that it can be read only by yourself or whatever database
  administrator will be setting up the demo.)

* The .mysql_history file for each database administrator should
  also be readable only by its owner.

* Ramp's `application/configs/application.ini` file should also be
  protected, but must be readable by the web server.  The best way
  to achieve this could be, for example, to have the `application.ini`
  owned by the database administrator but part of the same group as
  the httpd web server (or vice versa).  (The
  [Customizing application.ini][custom]
  file has additional information on using and customizing the
  `application.ini` file.)

* Once the database administrator MySQL accounts have been created
  below, each database administrator should create a MySQL option
  file, again readable only by its owner, containing the MySQL
  account password.  This could be a .my.cnf file in the home
  directory or an options file somewhere else.  The file would
  include:

    [client]
    password=their_mysql_passwd

  It might also include the user, host, or other options.  Files
  like this provide a convenience to database administrators,
  cutting down on the need to type in passwords, and simultaneously
  serve to protect those passwords from plain-text transmission.


<h2 id='configuration'> 3. Configuration </h2>

### Setting Up "As-Is" Demos ###

[This "As-Is" sub-section will soon disappear, as it has been
superceded by similar (improved) information in the [Ramp/Smart
Installation Guide][install].]

Very little configuration is necessary if you just want to set up
the Ramp or Smart demos and are willing to use the database names,
MySQL accounts, and Ramp/Smart usernames in the example setup files
provided with the demos.

  -  In the `application/configs` directory, build an `application.ini`
     using the sample components provided in that directory, as
     described in the [Customizing application.ini][custom]
     document.
  - If you have not already done so, copy the appropriate
    `create[...]MysqlAccountsTemplate.sql` to `create[...]MysqlAccounts.sql`
    in the `RampDemoSetup` or `SmartDemoSetup` directory.
  - Next, take the protective security measures outlined above,
    including changing the permissions of your `application.ini` file
    and your new `create[...]DemoMysqlAccounts.sql` file.
  - Then, as a miminum precaution, change the MySQL passwords in
    the appropriate `create[...]DemoMysqlAccts.sql` file.  One of those
    accounts (the web-based Ramp/Smart access account) is also
    represented in the `application.ini` file, so its password must be
    changed there as well.  (If you already have MySQL accounts for the
    DBA(s), you can comment out the statements that create those
    accounts and comment out or change the user account name in the
    GRANT statements for the DBA accounts.  Whether you comment those
    lines out or change them to reflect the actual user account names
    depends on whether those accounts already have the relevant
    privileges.)
  - Set up virtual hosts in your web server's vhosts.conf file for the
    Ramp and/or Smart demos.  The [INSTALL][install] document describes
    more fully how to do this.  The `vhost-configs` directory contains
    files with examples for both demos.
  - Finally, from within the appropriate demo directory, log in to
    MySQL as root or another user with the ability to create new
    users and new databases, and run the appropriate Setup script.

        Ramp Demo:    mysql> SOURCE SetupRampDemo.sql
        Smart Demo:   mysql> SOURCE SetupSmartDemo.sql

### Configuring Ramp/Smart (Beyond Demos) ###
The rest of this section covers the basic tasks necessary to set up
a Ramp/Smart application other than the provided demos, or to set up
up the demos to use customized database or MySQL account names.
These tasks include creating one or more MySQL databases for the
Ramp/Smart application, creating MySQL accounts for database
administration and for web-based Ramp/Smart interaction with the
database, defining the right database permissions for the MySQL
accounts, and setting up Ramp/Smart users.

[Notes from setting up Njala & USL prototypes:  I copied from SmartDev
wherever possible.
  - I created application.ini first (using ramp_basics,
    smart_defaults, smartApplicationTemplate, deleting production,
    testing, etc, renaming smart_development to sl_prototypes, created
    a new njala_prototype section, moved db.params, applicationShortName,
    and menu/activities/settings directory to njala section and finally
    copied njala section to usl_prototype.
    For USL, I added roles for exams operator, senior exams officer,
    student & academic affairs, financial office (exams_op, sr_exams_off,
    st_acad_aff, finance).
  - Then I made new installation/USL_Setup directory and copied
    SmartDevSetup/* to it.
    * I edited create..MysqlAccts and commented out the line to
      create a DBA account since I will use an existing one.  Edited
      GRANT lines in 2a to use existing DBA account.  Also commented
      out access account to use an existing one and edited GRANT
      lines in 2b to reflect that.  Deleted creation of automated
      tests since the prototypes won't do regression testing.
    * I edited create..UsersAuths to change name of database.  For USL,
      I also added new users: exop (exams op), sreo (Sr exams officer),
      and saa (student & acad affairs), fo (finance officer).
    * USL: I edited smartPersonStaffSetup.sql to add details for
      new sample users (exop, sreo, saa, fo).
NOTE: Need to make schema mods before knowing what data changes to
make to ramp_auth_auths.
    SCHEMA CHANGES:
      QUESTION: why aren't studentID and programID foreign keys in
          StudentAcadProgram???
    * Terms:  YYYY-YY N, e.g., 2012-13 1
    * Made schema changes to:  FinancialHold (new table),
      Colleges, Departments, AcadProgram,
      StudentAcadProgram, Enrollment, UnverifiedGrades/VerifiedGrades
              (new tables),
=>      NEED to add functions to calculate grade (and letter grade?),
                update triggers for enrollment status
            add academic holds?  (e.g., advising, "authorized",
            "registered" (difference between registered & enrolled?))
    * Changed createSmartDevLocks and createSmartDevUserAuths to update
      locks and auths to reflect schema changes.
    * Updateed the drop scripts
    SETTINGS CHANGES:
    * For any new tables, can start with a simple setting that shows all
      columns by default, then build an appropriate setting from there.
    * Make col heading for Student.studentID be Registry #?
    * Make col heading for ModuleOfferings.section be Shift?
]


### 3a. Creating MySQL Accounts and Granting MySQL Privileges ###
* If you have not already decided on the name(s) of the database(s)
  you are going to create, do so now.  (See the planning section
  "What databases to set up?" above.)  You can create your
  databases now or later; the example files provided in this
  directory create database(s) later when creating Ramp/Smart
  user accounts.  Either way, you need to know the names now.

* After the protective measures above have been taken, create any
  new dba or Ramp/Smart access accounts necessary in MySQL.
  The easiest way to do this is to use a copy of either the
  create_ramp_mysql_acct_examples.sql file or the equivalent file
  of Smart examples, having changed the file permissions as
  described above.  Alternatively, you can merely use one of
  these ...mysql_acct_examples.sql files as a guide.  These files
  also provide examples of granting the new accounts appropriate
  MySQL privileges, which is the next step.

* Each of the database administrator and Ramp/Smart access
  accounts needs to be given appropriate permissions on each
  database you decide to create.  Database administrators will
  generally have ALL permissions granted to them.  The access
  account permissions, on the other hand, might vary from database
  to database.  For example, a demo database might be set up as
  read-only from Ramp/Smart software, a "normal" database might
  provide read and modify permissions, while a database set up for
  automated testing might grant Ramp/Smart software the ability to
  drop and create tables as part of its testing.

  You will need to be logged in to MySQL as root or another
  user with the ability to create new users and grant MySQL
  privileges, whether you copy and edit one of the examples
  files provided or type similar commands at the MySQL prompt
  or using phyMyAdmin.  To read in a SQL file, use the SOURCE
  command (e.g., SOURCE create_mysql_accts.sql).

  [ FUTURE:  
  NOTE: In Smart, there are some special privileges 
  that allow the database to do some automatic processing when
  certain tables have been modified, and these cannot be granted
  until the database functions that do that processing have been
  defined.  Thus, if you will be using Smart table schemas, you will
  have to do one more privilege-granting step below, after reading
  in the table schemas.]

* In order for the Ramp/Smart software to know what MySQL accounts
  to use to access appropriate databases, follow the directions
  in [Customizing application.ini][custom] to set the database, username, and
  password properties in the `application.ini` file appropriately.

### 3b. Setting Up Ramp/Smart User Accounts and Authorizations ###
Ramp and Smart use two tables to keep track of eligible users and what
they are authorized to do.  These two tables are repeated in each
database, since the set of eligible users and authorizations might be
different from one database to another.  The first table, called
ramp_auth_users, defines the eligible users, associates a role with
each user, and identifies users as active or inactive.  The second
table, ramp_auth_auths, defines Access Control List (ACL) rules
that specify the activity and table privileges associated with each role.

In addition to associating users with authorization roles, the
ramp_auth_users file may be used for user authentication ("internal
authentication") if an external mechanism such as Active Directory
is not used.  In this case, it would track passwords, as well as
usernames and roles.

The ramp_auth_users file can also store identifying and contact
information about Ramp/Smart users, such as names and email addresses.
If, on the other hand, the application stores such information for
its own purposes (as Smart does in the Person table, since Smart
users are also institutional staff), then it is better practice to
merely store a reference to the user's key from the application
table (e.g., their Person ID) in ramp_auth_users rather than to
duplicate information that is in that table, such as name and email
address.  The ramp_auth_users.ini and smart_auth_users.ini files
in the settings/Admin directory provide examples of table
settings covering both situations (storing all necessary user
information vs. referencing such information from another table).

* __Specify Internal or External Authentication__  
  In order for the Ramp/Smart software to know whether to use
  internal or external authentication, follow the directions
  in [Customizing application.ini][custom] to set the authentication
  type and a
  default password for new Ramp/Smart user accounts.

      ramp.authenticationType  (e.g., "internal")
      ramp.defaultPassword     (required for internal authentication)

* __Define Roles__  
  Ramp/Smart has a built-in "guest" role that defines Ramp/Smart
  access that is allowed without logging in at all.  Other roles
  must be specified in the `application/configs/application.ini`
  file.  Follow the instructions
  in [Customizing application.ini][custom] to
  update `application.ini` to specify the non-guest roles you
  want to define for each database.  Example roles are defined in
  the `rampApplicationTemplate.ini` file in `application/configs`,
  which is set up to configure a simple `ramp_demo` database, and
  the `smartApplicationTemplate.ini` file, which provides more
  complex examples.  Both include a `ramp_dba` role for database
  administrators who have authorization to manage the Users and
  Authorizations tables.  You should define such a role, whether you
  call it `ramp_dba` or something else.

* __Create and Populate the Users Table__  
  For each database you wish to set up, create the database if you
  have not done so already, and then create the users and
  authorizations tables for that database.  Most new users and
  authorizations can be added using Ramp/Smart itself, but
  you will need to first create at least one new administrative
  user using MySQL who has authorization to work with those tables.

  You may use the contents of create_users_auths_example.sql as
  a guide in creating and populating the users and authorizations
  tables or you may copy and edit it to create your own tables.
  The example in that file creates a single database, its Users
  and Authorizations tables, and a single administrative user with
  authorization to manage those two tables.  The paragraphs
  below provide more information on customizing this example,
  while the Ramp and Smart Demo installations provide additional
  examples.  (See the RampDemoSetup and SmartDemoSetup directories.)

  __Defining the Users Table Schema__  
  For each user, the Users table should specify a unique username,
  a role, and an indication of whether the user is active or
  inactive.  In general, it makes sense to create users as inactive
  and then activate them when appropriate.

        ------------------------------------
        | username:  required in all cases |
        | role:      required in all cases |
        | active:    required in all cases |
        ------------------------------------

  If you are using internal authentication, the Users table
  should also include the user's password, which should initially
  be set to the default value set in the `application.ini` file.
  When a new user tries to log in and the password is still set
  to the default password, Ramp/Smart will redirect the user to a
  Set Password screen that will encrypt the new password correctly.

        -------------------------------------------------------------
        | password:  required only if using internal authentication |
        -------------------------------------------------------------

  You should store basic identifying or contact information about
  the Ramp/Smart Users unless your application is storing it
  separately.  For example, Smart has a Person table that would
  include information about all Ramp/Smart users, as they are
  institutional staff, as well as information about other people
  associated with the institution.  In this case, it makes sense to
  provide a column that contains a key to the other table (e.g., the
  PersonID).  The Ramp and Smart Demos provide an example of each
  case: Ramp Demo stores identifying/contact information about its
  user(s), while Smart Demo does not.

        ------------------------------------------------------------------
        | identifying/contact information: helpful if application does   |
        |                                  not track it separately       |
        | OR domainID:   key from domain table containing identifying or |
        |                contact information (if there is one)           |
        ------------------------------------------------------------------

  __Populating the Users Table__  
  You only need to create one user at this stage, who should have
  a database administrator role that provides authorization to
  create additional users, modify user information, and define
  user authorizations.  The username, role, and active field
  for this user should all be set.  The password should be left
  as the default password, causing Ramp/Smart to prompt the
  user for a password that will be properly encrypted the first
  time the user attempts to log in.  If your application will
  store identifying or contact information in the Users table,
  you can add that information now or later using Ramp/Smart.

  Note that even though both the Ramp and Smart demos utilize
  internal authentication, their table settings for the
  Users table in the Admin directory specifically do not
  include the password, leaving it hidden.  Password changes
  must happen through a separate Change Password mechanism, not
  by modifying the Users Table directly, in order for passwords
  to be encrypted correctly.

* __Create and Populate the Authorizations Table__  
  The Authorizations table schema should be defined exactly as
  in create_users_auths_example.sql.  To allow the initial
  administrative user to create additional users and define
  their access permissions using Ramp/Smart itself, the
  Authorizations table should include a rule that provides the
  administrative user's role with access to an activity directory
  containing table settings for the Users and Authorizations
  tables.  It should also include rules that provide access to
  the tables themselves.  The create_users_auths_example.sql
  file does all this.  (Alternatively, the same authorization
  rules can be defined in the `application.ini` file.)

  NOTE: To provide database administrators the ability to run
  checks of the roles, resources, and rules that have been set
  up in Ramp/Smart, you need to also define authorization rules
  that provide access to the validate-roles and validate-acl-rules
  actions in the Authorization Controller.  This cannot be done
  in the Authorizations table; it must be done in the `application.ini`
  file.  See [Customizing application.ini][custom]
  for more information.

* __Defining Additional Users__  
  Additional users can be added to Ramp/Smart after the initial
  installation is complete, using the Ramp/Smart software itself.

  [ We need a user's manual for using Ramp, and it should
  probably have a separate chapter or section for administrators
  on managing the Users and Authorizations tables. ]

## 4. Setting Up Your Application ##

RAMP can be used as the underpinnings for the SMART application
(Software for Managing Academic Records and Transcripts), or as a means
of managing records and activities for another application of your own.
If you plan to use RAMP to work with a database of your own devising,
you will need to define the structure of your tables and possibly some
initial data as well.  The populateRampDemoData.sql file provides an
example of setting up a small application with just [ for now ] two
tables.

The SMART application consists of a well-designed set of tables and
activities to support interacting with academic records.  You may use
this infrastructure as-is, or customize it for your own particular
context.  Before deploying Smart, therefore, you should verify whether the
set of tables and fields provided meets your situation, and, if not,
customize them as necessary.

 >    [ FUTURE:  
 >    NOTE: In Smart, there are some special privileges 
 >    that allow the database to do some automatic processing when
 >    certain tables have been modified, and these cannot be granted
 >    until the database functions that do that processing have been
 >    defined.  Thus, if you will be using Smart table schemas, you will
 >    have to do one more privilege-granting step below, after reading
 >    in the table schemas.]

### 4a. Minimal Behavior ###
If you are customizing SMART in some way or developing an application
of your own, you will generally want, at the very least, to provide
a starting activity, a menu, and a mechanism for creating user
accounts and authorization.  The RAMP and SMART demos provide
illustrations of these: the settings/demo directory contains the
files that represent the starting activities and menus for both
demos (index.act and rampDemoMenu.ini for RAMP, smartDemoIndex.act
and smartDemoMenu.ini for Smart), and the settings/Admin directory
contains the activity files and table settings that support creating
new users and authorizations.  Your Authorizations table should
carefully restrict access to the activities and table settings for
managing users and authorizations to a very small number of trusted
database administrators; this can be specified in the
`configs/application.ini` file or in MySQL when the Authorizations
table is created.  (Note that if you specify the access control
rule giving access to the activity directory (e.g., Admin) in the
`configs/application.ini` file, you must also define that directory
as a resource in the same file.)

### 4b. Public Activities and Table Settings ###
You may also wish to allow "guest" users (those who are not logged in)
access to certain activities or tables, such as the ability to read an
About document or view public information in a particular table.
The `application/settings/PublicActivities` directory provides an
example of this.  `application.ini` defines this directory as a
resource (ramp.aclResources[] = "activity::index::PublicActivities")
and provides an ACL rule that allows the guest role access to the
activity files in the directory.

   EVERYTHING AFTER THIS IS STILL UNDER CONSTRUCTION

#### More Information about Smart ####
If you want to use Smart with the exact same tables and table settings
as are provided in the Smart Demo, [ all you need to do is change the
authorizations for various roles so they are no longer read-only?  Will
there be a provided file that defines roles such as might be used at K
or at Njala? ]

[Some of the following may be obsolete:]  
The SmartDemoSetup/smartDev.... file defines the structure of the base
Smart tables along with sample data for development or testing purposes.
Edit this file as necessary and then read it into MySQL as a Ramp/Smart
database administrator.  You and your users may wish to work with the
sample setup for a while, to identify changes that you wish to make for
your own installation.  You can then modify the data setup file and
associated table settings, re-initialize the tables and sample data for
additional testing, and continue with this process until the database
structures and table settings appear appropriate for your situation.
Alternatively, you can create and modify table structures in MySQL,
dumping the database to a file if you want to capture the new structures
for future use (e.g., to create a clone of the database for testing
purposes).

* Copy the sample file (ramp demo, smart demo, other?)
  that is closest to your needs and edit it to reflect the table
  structures you need.  For demo, development, or test databases,
  you may wish to keep or redefine the initial data in these
  source files; for a production database, you will probably
  want to remove all, or nearly all, of the sample data provided.

  When you are ready, log in to MySQL as a database administrator
  and read in the appropriate file (SOURCE filename.sql).


============ Verbiage that is no longer used? =========

Ramp and Smart each use a single MySQL account to access a database;
authorization is achieved not by using different MySQL accounts with
different privileges but through a set of roles defined in Ramp/Smart
with different privileges defined through access rules.  A separate
mechanism associates each Ramp or Smart user with a role.  Since
the MySQL account used by Ramp/Smart for web access to a particular
database must accomodate all users of that database, it will have
very wide-ranging privileges (although not as wide-ranging as the
rampdba database administrator account, which can add, delete, and
re-structure tables).

The installation instructions in this file refer to two SQL script
files, createRampDatabases.sql (for both Ramp and Smart) and
createSmartDatabases.sql (Smart only), to be edited and run.  To
prevent the possibility that local customizations in these files
might be overwritten by future software updates, the initial download
contains files called createRampDatabasesTemplate.sql and
createSmartDatabasesTemplate.sql in the installation directory
instead.  For an initial installation, therefore:
    * Copy createRampDatabasesTemplate.sql to createRampDatabases.sql.
    * Copy createSmartDatabasesTemplate.sql to createSmartDatabases.sql.
In the instructions that follow, edit the new files, leaving the
original template files untouched.

The installation directory also contains sample files for initializing
the database schema(s) that will be used for different database
environments (e.g., demonstration, development, test, and production
environments), and for populating demonstration or test tables with some 
initial data.  To know which sample files to use, and how to change them
for the specific needs of the application, the first step is to decide
which database environments are to be setup.  For example, one might
just set up Ramp and Smart demo databases to learn more about the the
application.  A Ramp/Smart developer, on the other hand, might create
development and regression testing environments, while someone running
Smart as a production system might create production, user testing,
and regression testing environments.

      rampdba password in the createRampDatabases.sql file to the
      actual password that will be used.  (This MySQL account is
      designed to be used by the database administrator when working
      in MySQL or phpMyAdmin directly.)  The user name may also
      be changed if desired; for example, rampdba could be changed
      to smartdba.  If there will be multiple database administrators,
      create a user account for each similar to the provided rampdba
      account.  Administrators should each have a .my.cnf file and should
      protect their .mysql_history files.

Since
the MySQL account used by Ramp/Smart for web access to a particular
database must accomodate all users of that database, it will have
very wide-ranging privileges (although not as wide-ranging as the
rampdba database administrator account, which can add, delete, and
re-structure tables).

    * Update the createRampDatabases.sql and createSmartDatabases.sql
      files, uncommenting, modifying, and copying lines as necessary,
      to create the databases and web-access MySQL accounts that you
      want.  For example, for a minimal, demo version of Ramp,
      simply uncomment the SQL lines referring to rampdemo.  (See
      the next bullet for more information if you are using Smart.)

      Appropriate permissions for the rampdba and sample user accounts
      are illustrated in createRampDatabases.sql and
      createSmartDatabases.sql.

    * If you are planning to use Smart, you probably do not need to make
      many changes to createRampDatabases.sql except to set the password
      for the rampdba account, create additional dba accounts if
      necessary, and, perhaps, to uncomment commands dealing with
      the ramp_demo database if you want to run that.  To create
      development and test Smart databases, you need only uncomment
      the appropriate commands in createSmartDatabases.sql.  To
      create other databases, such as a production database or other
      test databases, you will need to copy and modify the appropriate
      commands to create the databases, their associated Smart MySQL
      accounts, and the appropriate privileges.

      For example, a developer might create the following databases
      and MySQL accounts:
            smart_dev   'smartuser'@'localhost', 'smartuser'@'%'
            smart_test  'smartuser'@'localhost', 'smartuser'@'%'
      while a production environment might use the following:
            smart             'smartuser'@'localhost', 'smartuser'@'%'
            smart_user_tests  'smartuser'@'localhost', 'smartuser'@'%'
            smart_automated_tests  'smartuser'@'localhost', 'smartuser'@'%'

    * Update the `application/configs/application.ini` file to
      reflect the databases, web-access MySQL accounts, and passwords
      defined in createRampDatabases.sql and createSmartDatabases.sql.

Each Ramp or Smart user must have a Ramp/Smart user account that
is associated with a role for authorization.  A group of users may
have the same role within the system; it is also possible for a
single user to play multiple roles by creating a new role that
inherits privileges from two or more existing roles.
There is one
role, 'guest', built into Ramp; other, more meaningful roles must
be defined in Ramp's `application/configs/application.ini` file.
(Examples are provided in rampApplicationTemplate.ini and
smartApplicationTemplate.ini.)  A special table within a Ramp/Smart
database, ramp_auth_users, associates users with their roles.  A
second table, ramp_auth_auths, defines role-based access control
rules to the activities and tables the comprise the Ramp application.


[install]: /INSTALL.md
[custom]:  /document/index/document/..%252F..%252Finstallation%252FApplication_Ini.md
