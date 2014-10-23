# RAMP Administrator User Manual #

[ [Introduction](#intro) |
  [Database Administration](#sys_dba) |
  [Ramp Administration](#rdba) |
  [Application Development](#devel) ]

<h2 id="intro">Introduction</h2>

Ramp (Record and Activity Management Program) is a program that
supports the easy creation of simple activity files (lists of
activities) and table settings (database views), which together
create a web-based application.  It treats activity files and table
settings generically, which means that it could be used for a variety
of different applications.  One application that has been developed
on top of Ramp is Smart (Software for Managing Academic Records and
Transcripts).

This document covers administrative tasks that apply to any Ramp
application (including Smart), such as installing the
application, creating user accounts, doing backups, and ..., 

There are different types of administrative roles associated with
Ramp, including [System Administration](#sysadmin), [Database
Administration](#dba), [Ramp User Administration](#ramp_dba), and
[Application Development](#development).
Depending on the size and structure of the organization,
there may be overlapping responsibilities, or there may not.
For example, the database administrator (DBA) might also be the
Ramp user administrator, or there might be people authorized to
maintain Ramp accounts who have very little understanding of MySQL
and the underlying database structure.  Similarly, the DBA might
be involved in helping to implement changes to activity files and
table settings, but the people who would best understand what
activity files and table settings would be useful are likely to be
domain experts rather than database experts.

<h3 id="sysadmin">System Administration</h3>
System administration (sysadmin) responsibilities include installing and
looking after the health and welfare of the server on which Ramp
runs, including the web server, MySQL, PHP, and Ramp software.  The
sysadmin role overlaps with the database administrator role when
it comes to implementing backups and restores, and coordinating
upgrades to MySQL and Ramp.  System administration is a critical
role that requires hardware and operating system level expertise.

<h3 id="dba">Database Administration</h3>
Database administrators (DBAs) are the people who can access the MySQL
database directly, can create databases and tables, alter the
structure of tables by adding or removing columns, etc.  DBAs are
also responsible for backing up the database, restoring the database
if necessary, staging software updates for users to test, performing
software upgrades, etc.  This is a critical role that requires MySQL
database expertise and, preferably, database design skills. Since it
is so critical, there should be at least two people with DBA
privileges.  If one person serves as the primary DBA, the secondary
DBA should possess at least enough skills to do backups and a database
restore in the case of an emergency.

<h3 id="ramp_dba">Ramp User Administration</h3>
The Ramp Administrator is responsible for creating Ramp user accounts,
resetting passwords, adding roles and authorizations, and removing "orphaned" locks.

<h3 id="development">Ramp Application Development</h3>
##### Activity File and Table Setting Development #####
Although it is possible to run the Ramp and Smart demos, and even an
uncustomized version of the Smart application, without defining any
new activity files or table settings, to get the most benefit from
Smart will require creating and modifying such files to suit the needs
of the given context.  This may be the responsibility
of the database administrator, but domain experts who
understand the structure of the data and the needs of the users
accessing that data might also be involved in developing activity
files, menus, and table settings.  (This is a management and policy
decision.)
The development and maintenance of user documents also falls under
this category.

Defining a new Ramp application or customizing a Smart application
involves designing the structure of the database tables, identifying
appropriate authorization roles for the users, and developing activity
files and tables settings that match the defined tables structures and
authorizations.

Also, updating the Authorizations and Lock Relations tables.

If you create a new role, you may need to add the first rule using that
role to the db manually (mysql command line or phpMyAdmin).

NOTES:  TODO:
A related question that may come up later in the system implementation is:
who is responsible for generating the style sheets and (maybe, in the
future) the SQL queries for reports: the DBA, the Ramp Admin,
Activity/Table Setting developers, or users with a certain amount of
experience?  Who has permissions to put them in place?  Does each one
require a new authorization rule (or set of rules) written by the Ramp
Admin?

<h2 id="sys_dba"> System and Database Administration </h2>

Ramp is a software application that provides a way to interact with an
existing database or a database under development.  This document
focuses on the administrative responsibilities related to Ramp itself,
not with the system administrator responsibilities involved
in maintaining the Ramp server, including the installation
and upgrading of Apache, MySQL, and PHP (AMP), nor the database
administrator responsibilities involved in designing, implementing, or
improving the underlying database.  Both of those, while important, are
beyond the scope of this document.  

Instead, this section will focus on
  - Installing RAMP
  - Backing up the database
  - Restoring Ramp from backups (an emergency activity, since almost any
    restore will involve the loss of data)
  - Cloning the database to create a test environment
  - Staging a new release of Ramp/Smart software for testing purposes
  - Upgrading the production version of a Ramp/Smart application to a
    new release

#### Installing Ramp/Smart ####

[VERY EARLY, VERY DRAFTY NOTES]

Two steps: installing the software (including addressing any security
concerns, setting up appropriate vhost(s)) and setting up the initial
table structures and data for a Ramp application.  The 
[Installation Instructions][install] document covers both of these.

Note: The Installation scripts
create sample MySQL user accounts corresponding to the accounts needed for
DBAs (for working in MySQL or phpMyAdmin, not Ramp); if MysQL accounts
for the DBAs already exist, the lines creating analogous accounts
should be removed from those scripts.  If the DBAs do not already have
MySQL accounts, the scripts should be edited to create appropriate
accounts with appropriate passwords.  If more than one installation
script is going to be run (e.g., to create one or more demos in
addition to the "real" application, then the MySQL account creation
should be removed from all the scripts except one.  In any case, for
whichever script(s) are run, the GRANTS commands that grant
appropriate user privileges to the DBAs should be edited to refer to
the actual DBA accounts.

Note 2: If you are doing development (whether with the default development
application or a customized one), you may find yourself wanting to
re-initialize your database from time to time.  You will generally
not, however, need to recreate the MySQL dba or web-based access
accounts.  Therefore, you may wish to comment out the call to the
`create[...]MysqlAccts.sql` in your setup script
(`SmartDevSetup/SetupSmartDevEnv.sql` or a script you created yourself)
after running it the first time.

#### Backing Up the Ramp Database ####

Need to write this section.  Would also be nice to provide a RampAdmin
activity, or at least a MySQL script, to help with this.  Might also
mention setting up a cron job (or whatever the Windows equivalent is).

#### Restoring from Backups ####

Need to write this section.

#### Creating Clones or Staging Areas for New Versions ####

Some similarities with restoring from backups, but this is planned and
less dangerous.  (For test clones, it does not matter if there is some
data loss.  For staged versions, need to make sure the final
installation of an upgrade uses the current, production copy of the
database, not the clone made for testing.  (Make a good backup
immediately before any upgrade!))

Notes: New version should be put in a new directory
structure and should use a cloned version of the database.  Once a new
version has been tested and is ready
to go into production, would be good to include script that tars up
the version being replaced -- code & settings, including application.ini
-- and saves it along with a snapshot of the database at that moment.

<h2 id="rdba"> Ramp Administration </h2>

#### Understanding the Ramp/Smart File System Structure

Need to create a pretty picture for this, but for now...  The Ramp/Smart
directory has a number of important subdirectories and file.  Some of
the most important are:

    ramp/
     |
     |--application/
     |   |
     |   |--configs/application.ini
     |   |
     |   |--docs/
     |   |   |
     |   |   |--rampDocs/       general documentation (admin, settings, etc)
     |   |   |
     |   |   |--smartDocs/      Smart documentation
     |   |
     |   |--NjalaSettings/      (menus, activity pages & table settings)
     |   |   |
     |   |   |--Admin/          settings for administrative functions
     |   |   |
     |   |   |--Smart/
     |   |       |
     |   |       |--Core/        settings for schools, depts, etc.
     |   |       |
     |   |       |--Curriculum/  settings for programs, courses, offerings
     |   |       |
     |   |       |--Person/      settings for Person, Addresses, etc
     |   |       |
     |   |       |--Staff/       settings for HR tables
     |   |       |
     |   |       |--Student/     settings for Admiss & Student Services tables
     |   |       |
     |   |       |--Valid.../    settings for Valid Code tables
     |   |
     |   |--controllers: directory with code for controllers
     |   |
     |   |--library/Ramp:  dir & subdirs with lots of Ramp code
     |   |
     |   |--library/Zend:  dir & subdirs with all Zend Framework code
     |
     |--installation/

#### Creating Ramp/Smart user accounts, Changing Passwords ####

  Notes:

  - New staff and students should be added to Smart by various sub-units
    within the Secretariat.  When new staff should also be Smart
    _users_, then the Ramp administrator must add the person to the
    Smart users table.  (This should be done AFTER HR has created the
    Person/Staff record for the new staff member.)  To add a new user,
    choose the "Add New User" item under the Admin menu.  The Smart
    username is the name they will use to log in to the Smart system.
    If the staff member has a Njala email account (e.g., pmoseray) then
    that is a good choice as it will be easy for the person to remember.
    If not, you may want to follow the same convention (and maybe even
    set up a Njala email account for them at the same time).

    You can make the new user Active if they will start using the system
    right away, or Inactive if you are setting up an account that will
    not be used for a month or two.  Once you set an account to Active,
    the user should change their password right away (see next bullet).

    You will also need to choose an appropriate role for the new staff
    member.  The pull-down menu of roles has all the roles that are
    available, but avoid roles beginning with an underscore ('_').
    Those roles are for use in the inheritance hierarchy only
    (to greatly reduce duplication of rules) but are not meant to be
    "visible" roles to be given to actual staff members.  The
    roles you are most likely to use are:

        fin_stu_mod:  Finance people who apply/release Student holds
        fin_mgmt:     Finance management who may view select Student info
        hr_mod:       Amy and anyone else who might enter/edit HR info
        hr_view:      HR managers or people who need to view HR info
        curric_mod:   whoever will enter program/course data
        admiss_mod:   Musu and anyone else who might enter/edit Applicant info
        admiss_view:  AA managers or people who need to view Applicant info
        sec_ss_staff: Theresa & other Secretariat staff with modify privileges
        sec_ss_mgmt:  Secretariat senior staff who may view all Student info
        campus_ss_staff: Campus registry staff with modify privileges
                         on students and course assignments
        campus_ss_mgmt:  Campus Reg. senior staff who may view info
        acad_aff_mgmt:   Dr. Johnson or anyone who can view all AA/SS/Reg info
        registrar:       Registrar, VC, or anyone who can view ALL data

        smart_dba:    Ramp/Smart administrators (ICT staff)
        developer:    You should probably not give this to anyone (it
                      conveys privileges to add and modify all data).
                      Your usual activities should fall under the
                      smart_dba role, and for advanced functions
                      (like changing the database schema) you will
                      need to go directly into mysql or phpMyAdmin.

    Note that anyone can view Curriculum (Program/Course) information,
    even without logging in at all, so there is no `curric_view` role.

  - New users (including DBAs when accounts first created) are given a
  default password.  This causes RAMP to redirect the user to a Set
  Password screen the first time they try to log in.  There is now an
  Active? field in the Users table, so new users can be created as
  Inactive to reduce the chance that a malicious user will come in and
  set the password before the new user has a chance to do so.  The new
  user should be set to Active just before they are expected to use the
  system for the first time.

#### Adding Roles and Authorizations ####

Roles get added to application.ini file.  (Save a copy so you have them
whenever there is a Ramp upgrade!)  Need to explain roles and role
inheritance.  application.ini has some good verbiage for this section,
as does library/Ramp/Acl.php.

Authorization rules: Need to explain the different kinds of
authorizations (e.g., activity and document auths are directory-based,
table and report auths are based on the database table (not the table
setting)).  Need to explain how to create new authorization rules.  

#### Removing Orphaned Locks ####

<h2 id="devel"> Developing Ramp Applications </h2>
<h2 id="devel"> Customizing Ramp/Smart Activities, Table
Settings, and `application.ini` </h2>

Provide links to the documents on [Creating Activity Files][activities]
and [Creating Table Settings and Sequences][settings].  Anyone working
on creating user documents (e.g., help documents) might want to know
more about [Markdown][md].

------------

<a rel="license" href="http://creativecommons.org/licenses/by/4.0/"><img
alt="Creative Commons License" style="border-width:0"
src="https://i.creativecommons.org/l/by/4.0/88x31.png" /></a><br /><span
xmlns:dct="http://purl.org/dc/terms/"
href="http://purl.org/dc/dcmitype/Text" property="dct:title"
rel="dct:type">RAMP Documentation</span> is licensed under a <a
rel="license"
href="http://creativecommons.org/licenses/by/4.0/">Creative Commons
Attribution 4.0 International License</a>.


[install]: /INSTALL.md
[activities]: /document/index/document/ActivityLists.md
[settings]: /document/index/document/TableSettings.md
[md]:  http://michelf.ca/projects/php-markdown/
