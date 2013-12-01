# RAMP Administrator User Manual #

[ [Introduction](#intro) |
  [Database Administration](#dba) |
  [Ramp Administration](#rdba) |
  [Creating Activity Files][activities] |
  [Creating Table Settings][settings] ]

<div id="intro"></div>

[VERY EARLY, VERY DRAFTY NOTES]

There are different types of administrative roles associated with Ramp.
Need to 
identify who will be playing which roles.  Might be the same small set
of people fulfilling all of these roles, or there might be different people
fulfilling each role.  For example,
the DBA might also be the Ramp user adminstrator, or there might be
people authorized to maintain Ramp accounts who have very little
understanding of MySQL and the underlying database structure.  Similarly,
the DBA might be involved
in helping to implement changes to activity files and table settings,
but the people who know best what activity files and table settings
would be useful are likely to be domain experts rather than database
experts.

> ##### System Administration #####
> System administration (sysadmin) responsibilities include looking after the
> health and welfare of the server on which Ramp runs, including the web
> server, MySQL, and PHP software.
> The sysadmin role overlaps with the database
> administrator role when it comes to implementing backups and restores,
> and coordinating
> upgrades to MySQL and Ramp.
> System administration is a critical role
> that requires
> hardware and operating system level expertise.

> ##### Database Administration #####
> Database administrators (DBAs) are the people who can access the MySQL
> database directly, can create databases and tables, alter the
> structure of tables by adding or removing columns, etc.  DBAs are
> also responsible for backing up the database, restoring the database
> if necessary, staging software updates for users to test, performing
> software upgrades, etc.  This is a critical role that requires MySQL
> database expertise and, preferably, database design skills. Since it
> is so critical, there should be at least two people with DBA
> privileges.  If one person serves as the primary DBA, the secondary
> DBA should possess at least enough skills to do backups and a database
> restore in the case of an emergency.

> ##### Ramp User Administration #####
> The Ramp Administrator is responsible for creating Ramp user accounts,
> adding roles and authorizations, and removing "orphaned" locks.

> ##### Activity File and Table Setting Development #####
> Although it is possible to run the Ramp and Smart demos, and even a
> very vanilla version of the Smart application, without defining any
> new activity files or table settings, to use Ramp for any real
> application will require creating and modifying such files.  This may
> be the responsibility of the database administrator, but domain experts who
> understand the structure of the data and the needs of the users
> accessing that data might also be involved in developing activity
> files, menus, and table settings.  (This is a management and policy
> decision.)
> The development and maintenance of user documents also falls under
> this category.

> (Who is responsible for generating the style sheets and (maybe, in the
> future) the SQL queries for reports: the DBA, the Ramp Admin,
> Activity/Table Setting developers, or users with a certain amount of
> experience?  Who has permissions to put them in place?  Does each one
> require a new authorization rule (or set of rules) written by the Ramp
> Admin?)

<h3 id="dba"> System and Database Administration </h3>

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

<h3 id="rdba"> Ramp Administration </h3>

#### Creating Ramp/Smart user accounts, Changing Passwords ####

  Notes:

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

<h3 id="customization"> Customizing Ramp/Smart Activities and Table
Settings </h3>

Provide links to the documents on [Creating Activity Files][activities]
and [Creating Table Settings and Sequences][settings].  Anyone working
on creating user documents (e.g., help documents) might want to know
more about [Markdown][md].


[install]: /document/index/document/..%252F..%252Finstallation%252FINSTALL.md
[activities]: /document/index/document/rampDocs%252FActivityLists.md
[settings]: /document/index/document/rampDocs%252FTableSettings.md
[md]:  http://michelf.ca/projects/php-markdown/
