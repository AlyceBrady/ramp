# Ramp/Smart Installation Guide #

[ [Dependencies](#dependencies) | [Installation and
Configuration](#installation) |
[Installing Pre-Defined Applications](#pre-defined) ]

Ramp is a program that supports domain expert access to a relational
database.  Whereas a program like phpMyAdmin is aimed at database
administrators who need to update and maintain the structure of a
database, Ramp is aimed at the store owner, librarian, registrar,
or other professional who needs to access and update the data in
it.  Smart is an application built on top of Ramp for managing academic
records, specifically curriculum records, instructor records, and
student records.

Ramp works with tables defined in a MySQL database using PHP and the Zend
Framework.  Ramp also uses PHP Markdown and Twitter Bootstrap.
Installing and running Ramp (or Smart), therefore, first requires
installing an AMP Stack (Apache Web Server, MySQL, and PHP), the
Zend Framework, PHP Markdown, and the Twitter Bootstrap ZF library
(a version of Twitter Bootstrap that works with the Zend Framework).

Ramp also includes dependencies on HTML 5, so the browsers used to
interact with Ramp on your server must support HTML 5.

<h2 id='dependencies'> Dependencies </h2>

### AMP ###

This document does not give detailed instructions for setting up a working
AMP implementation.  Your operating system may come with an Apache Web
Server, MySQL,
and PHP pre-installed, you may download and install the three components
individually, or you may choose to install a bundled, integrated unit
(for example, LAMP, MAMP, WAMP, or XAMPP). If you
do not have one of these, see the following websites for installation
and tutorials:

- Apache HTTP Server: [http://httpd.apache.org/] [apache]  
- MySQL:  [http://www.mysql.com/][mysql]  
- PHP:  [http://www.php.net/][php]  
- LAMP: Various sites provide instructions for setting up Apache,
  MySQL, and PHP on the different Linux distibutions.  Search for LAMP
  and the distribution name, _e.g.,_ "installing LAMP on Ubuntu Mint".
- Mac-specific:
  Several sites provide instructions for activating native Apache
  and PHP and for installing MySQL on OS X; for example, search for
  "installing AMP on Mac OS X".  Alternatively,
  [MAMP] [mamp] is an integrated AMP stack for Macs.
- Windows-specific:
  Search for "installing AMP on Windows" or for [WampServer] [wamp].
- [XAMPP] [xampp] is an integrated cross-platform AMP and Perl package
  for Linux, Mac OS X, & Windows.

Depending on the system you are using, you may need to update your `httpd`
configuration (_e.g.,_ `/etc/apache2/httpd.conf` on some systems) to load the
Rewrite module, the PHP module, turn on virtual hosting, and allow
overrides to all in your `DocumentRoot` directory.  This document
provides only very general information about setting virtual hosts
(vhosts) for Ramp/Smart; the specifics are system-dependent, so you will
need to determine how to do that for your specific system.

Ramp requires at least PHP 5.3, because it uses the modified ternary
operator ( `"? :"` with the middle part left out).  The PHP Markdown
Library also depends on PHP 5.3.  In order to get better encryption,
PHP recommends 5.3.7 or later.

If you make a link from your server's document root to folders or
directories in personal space, make sure the permissions on the link and
target directory will allow your web server to follow the link.

### Other dependencies ###
Ramp is built upon Zend Framework 1, a framework of PHP classes for
developing web applications.  Its style attributes extend
Twitter Bootstrap, using the `twitter-bootstrap-zf1` library to
integrate Twitter Bootstrap with Zend Framework 1.  Finally, Ramp uses
Michel Fortin's PHP Markdown Lib 1.3 to provide support for Markdown in
documents and activities files, which is then converted to HTML.

The current version of Ramp includes bundled versions of
Zend Framework 1, the Markdown library, the `twitter-bootstrap-zf1`
library, and a subset of Twitter Bootstrap 2.  (See the Ramp [README]
[readme] file for more information about these software dependencies and
for license information about these components.)

If you want to use Git to download Ramp (and future updates), 
the [Getting Started - Installing Git][git]
and [Set Up Git][git-setup] online documents are helpful.

If you will be doing Ramp/Smart development, you should also download
a version of PHP unit tests that works with Zend Framework 1 (_e.g.,_
phpunit34).


<h2 id='installation'> Installing and Configuring Ramp/Smart </h2>

Before installing Ramp/Smart, you will have to decide where you want to
install it, and under what user name.  Since it runs as a web
application, you will probably want to install it either under the
server's document root or under personal web space.

Ramp and Smart are currently available as a single, bundled package on
GitHub, along with the Zend Framework, PHP Markdown, and Twitter
Bootstrap libraries on which they depend.  If you plan to use Ramp/Smart
but not to modify it, you can download a read-only
zip version of it from the GitHub Ramp site using a browser
([https://github.com/AlyceBrady/ramp][ramp]) or a command-line tool such
as `curl`:

        curl -L -o ramp.zip \  
            https://api.github.com/repos/AlyceBrady/ramp/zipball/master

If you want to use Git for version control, or if you want (or might
want) to contribute to the Ramp/Smart project in the future, create a
fork on GitHub (use the Fork button on Ramp's GitHub web page) and then
install a clone on your local server:

        git clone git://github.com/yourUserName/ramp.git

Once you have downloaded the software, you may set up one of the two
provided demo programs, set up a pre-defined, default Ramp/Smart
development environment, create a customized Smart environment, or
create a new, custom application by defining new activities and
table settings.  Even if you plan to create a customized Smart
environment or new application, you may wish to set up one of the
three pre-defined applications first, to test your installation and
to become familiar with some of the basics.
The pre-defined applications available to you are:

 * RAMP Demo:  A small demo consisting of just a few tables.  

 * SMART Demo: A more advanced demo consisting of an abridged version of
   the Smart system for managing academic records.

 * Default Development Environment: Based on Smart Demo but gives the
   pre-defined "users" authorization to make changes to the tables in
   the development environment.  This environment can be used for
   developing and testing Ramp functionality, or as a starting point for
   a full Smart environment.

Installing and configuring Ramp/Smart consists of three steps:

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

The rest of this document covers these three steps for the pre-defined
demos and default development environment.  The [Installing a Customized
Ramp/Smart Application][customInstall] document [under construction!] 
will guide you through these steps for customized Smart environments
or new Ramp-based applications.

<h2 id='pre-defined'> Installing Pre-Defined Demos or Development
Environment </h2>

[ [Planning](#planning) | [Addressing Security Concerns](#security) |
[Configuration](#configuration) ]

<h3 id='planning'> 1. Planning </h3>

Installing Ramp/Smart generally requires some planning first, although
if you are installing only pre-defined applications (demos or the default
development environment) much of the planning has been done for
you.  There are some changes you can, and should, make to the
provided scripts that generate the pre-defined applications, but
you should read this section and the next, "Addressing Security
Concerns", before making any of those changes.

The planning phase addresses four key questions.

#### 1a. Internal or External Authentication?
The pre-defined demos and development environment use internal
authentication (_i.e.,_
authentication against user accounts and passwords created within Ramp).

#### 1b. What databases to set up?
#### 1c. What MySQL accounts to set up?
#### 1d. What authorization roles to set up?

The `installation` directory contains a subdirectory for each
pre-defined application with scripts that set up a database (or two
databases, in the case of the development environment),
two MySQL accounts, and a short set of authorization roles.
Later sections of this document provide instructions for modifying and
running these scripts.

The following table shows the script directory, database, MySQL accounts,
and Access Control List (Authorization) roles for each pre-defined
application.

<table>
<tr>
<th></th>
<th>Ramp Demo</th>
<th>Smart Demo</th>
<th>Development Env</th>
</tr>

<tr>
<td>Script Directory</td>
<td><code>installation/RampDemoSetup</code></td>
<td><code>installation/SmartDemoSetup</code></td>
<td><code>installation/SmartDevSetup</code></td>
</tr>

<tr>
<td>Database(s)</td>
<td><code>ramp_demo</code></td>
<td><code>smart_demo</code></td>
<td><code>smart_dev</code></td>
</tr>

<tr>
<td valign='top'>MySQL Accounts * <br/>
&nbsp;&nbsp;&nbsp;DBA account <br/>
&nbsp;&nbsp;&nbsp;Ramp/Smart access account
</td>
<td valign='top'>&nbsp;<br />
<code>rampdemodba</code> <br />
<code>rampdemo</code>
</td>
<td valign='top'>&nbsp;<br />
<code>smartdemodba</code> <br />
<code>smartdemo</code>
</td>
<td valign='top'>&nbsp;<br />
<code>smartdevdba</code> <br />
<code>smartdev</code>
</td>
</tr>

<tr>
<td valign='top'>Authorization Roles ** </td>
<td valign='top'><code>guest</code> <br />
<code>ramp_dba</code>
</td>
<td valign='top'><code>guest</code> <br />
<code>hr_staff</code> <br />
<code>regist_staff</code> <br />
<code>smart_dba</code>
</td>
<td valign='top'><code>guest</code> <br />
<code>hr_or_reg</code> <br />
<code>hr_staff</code> <br />
<code>regist_staff</code> <br />
<code>smart_dba</code> <br />
<code>developer</code>
</td>
</tr>
</table>

##### * Why does each pre-defined application have two MySQL accounts?
You will need two types of MySQL user accounts for your various
databases:  database administrator accounts (which you may already have)
and Ramp/Smart application
access accounts.  Database administrators are staff with database
expertise who may be creating new tables, adding new fields to
tables, or otherwise changing the structure of the database.  They
do this by logging in and accessing MySQL directly or using database
software such as phpMyAdmin.  The Ramp/Smart application access account,
on the other hand, allows the Ramp/Smart software to use to access
(read and write) data in the database.

DBA accounts should be individual accounts, preferably with account
names that identify the individual (as opposed to the generic dba
accounts above).  Furthermore, if you plan to have a back-up DBA,
especially for a development environment, that person should have their
own DBA account.  Instructions for editing the pre-defined scripts, if
you choose to do so, are provided below.

##### ** What are ACL or Authorization roles?
Permission, or authorization, to read tables, add new data, modify
or delete existing data, or carry out other activities in Ramp/Smart
is achieved, not by using different MySQL accounts with specified
privileges, but through a set of roles defined in Ramp/Smart.  For
example, there might be a "ramp_dba" role for users who have permission to
add and delete other users.  The default "guest" role might allow users
to read, but not change, a subset of tables in the database.

The authorization roles defined for the pre-defined applications are:

  * `guest` (all applications):  default role built into Ramp software

  * `ramp_dba` (Ramp Demo):  Ramp DBA user, allowed to view/add users and
    release locks

  * `hr_staff` (Smart Demo & Dev. Env.): role with extra permissions for
    accessing HR-related records

  * `regist_staff` (Smart Demo & Dev. Env.): role with extra permissions
    for accessing curriculum and student records

  * `smart_dba` (Smart Demo & Dev. Env.): DBA users, allowed to view/add
    users, add or modify authorization rules, and release locks

  * `hr_or_reg` (Dev. Env.): role defining permissions shared by hr and
    regist staff

  * `developer` (Dev. Env.): role with the permissions of all other roles


<h3 id="security"> 2. Addressing Security Concerns </h3>

There are several steps to take to protect the security of the
Ramp/Smart database.  The first two should be taken before running
the scripts that create the users and databases used by Ramp.

* In the appropriate `installation` directory (`RampDemoSetup`,
  `SmartDemoSetup`, or `SmartDevSetup`), change the permissions of
  the `create...MysqlAccts.sql` file so that it can be read only
  by yourself or whoever will be responsible for the initial
  installation.

* The .mysql_history file for each database administrator should
  also be readable only by its owner.  (MySQL should set these
  permissions correctly on its own, but you may want to double-check
  them.)

* Once the database administrator MySQL accounts have been created
  (see below), each database administrator should create a MySQL option
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

* Once 
  Ramp's `application/configs/application.ini` file has been created
  (and before it is edited to contain actual passwords), it should
  also be protected, but in a way that allows it to be readable by
  the web server.  The best way to achieve this could be, for
  example, to have the `application.ini` owned by the database
  administrator but part of the same group as the httpd web server
  (or vice versa).  (The [Customizing application.ini][applIni] file
  has additional information on creating and customizing an
  `application.ini` file.)

<h3 id='configuration'> 3. Configuration </h3>

Very little configuration is necessary if you just want to set up
the Ramp or Smart demos or default development environment and are
willing to use the database names, MySQL accounts, and Ramp/Smart
usernames in the setup files provided with them.

  - Find the appropriate script directories for the demos or development
    environment you want to set up (but do not run the scripts yet).
       * `ramp/installation/RampDemoSetup`
       * `ramp/installation/SmartDemoSetup`
       * `ramp/installation/SmartDevSetup`

  - If you have not done so already, change the permissions of the
    create[...]MysqlAccts.sql file in each appropriate directory 
    so that it can be read only by yourself.

  - After changing the permissions on the create[...]MysqlAccts.sql file
    or files, edit them to change their MySQL passwords.  If you
    already have appropriate MySQL user accounts for your database
    administrator(s), you can delete or comment-out the line in
    each file that creates the DBA account (labeled '1a') and then
    change the account name used when granting that user all
    permissions (labeled '2a').

  - From within the appropriate directory, log in to
    MySQL as root or another user with the ability to create new
    users and new databases, and run the appropriate Setup script.

        Ramp Demo:        mysql> SOURCE SetupRampDemo.sql
        Smart Demo:       mysql> SOURCE SetupSmartDemo.sql
        Development Env:  mysql> SOURCE SetupSmartDevEnv.sql

  - Ramp expects to find a file called `application.ini` in its
    `application/configs/` directory.  This file should contain
    Ramp configuration information, some of which you may have to
    customize for your environment.  To prevent the possibility
    that local customizations in an `application.ini` file might
    be overwritten by future Ramp updates, the Ramp download contains
    a set of small files containing uncustomized components or
    sections of a possible `application.ini` file, which can be put
    together in different ways depending on whether you are planning
    to set up a demo, the Smart development environment, or a customized
    environment.

    Create your `application.ini` file in the `application/configs`
    directory using the sample components provided there.  Some
    components provide general settings (_e.g.,_ time zone, a
    session timeout value, what database you are using, _etc._), while
    others define settings that are specific to the demo or application
    you plan to use.  The list below shows the components required
    for the three pre-defined applications.  For
    example, if you are setting up the default development environment,
    your `application.ini` file will need the contents of three files:
    `ramp_basics.ini` (general settings), `smart_defaults.ini` (settings
    common to most Smart applications), and `smartApplicationTemplate.ini`
    (additional settings for production or development environments; the
    two relevant sections are the `smart_development` and
    `smart_regressiontesting` sections).  As you create your
    `application.ini` file, be sure to include the components in the
    order provided below.

        Ramp Demo:        ramp_basics.ini, ramp_defaults.ini, rampDemo.ini
        Smart Demo:       ramp_basics.ini, smart_defaults.ini, smartDemo.ini
        Development Env:  ramp_basics.ini, smart_defaults.ini, smartApplicationTemplate.ini
        All of the Above: ramp_basics.ini, ramp_defaults.ini, rampDemo.ini,
                          smart_defaults.ini, smartDemo.ini, smartApplicationTemplate.ini

    In a UNIX or Linux environment, you can create the file easily, as
    in the following example:

        cat ramp_basics.ini ramp_defaults.ini rampDemo.ini >application.ini

  - Change the permissions of your new `application.ini` file and then edit
    it to set the relevant passwords to match the ones you defined in
    the create[...]MysqlAccts.sql files.  Look for occurrences of
    `resources.db.params.password`.
    There should be one account and password for each section in your
    `application.ini` file corresponding to a demo, development, or
    regression testing environment.  (Note, only the Ramp/Smart access
    accounts have their account and password information in
    `application.ini`, not the DBA accounts.)

  - Set up virtual hosts or subdirectories for different Ramp
    environments.  The instructions for setting up vhosts provided
    here are very general, since the specifics differ from system to
    system.

    If you do not want to (or cannot) set up virtual hosts on your
    server, you can create sub-directories for various Ramp/Smart
    environments.  For example, if you have installed Ramp/Smart in a
    directory called `smart` under your server's document root, you
    can create subdirectories (such as `rampdemo`) under the `public`
    directory and then access them with URLs such as
    `.../smart/rampdemo/`.
    A few things will not work with this setup, however.
    The images within the RAMP User Manual will only work if they
    are in the server's (or virtual host's) document root directory,
    and the same is true for the links from the RAMP README file
    to the license files for RAMP and its dependent software.

    ##### Vhosts

    * On some systems, you may need to first enable virtual hosting.
      Depending on the system, this might be done, for example,
      by uncommenting the `Include` line for `httpd-vhosts.conf` in
      `/etc/apache2/httpd.conf`.

    * Define the virtual host(s) you are going to use.  On some
      machines, this involves editing a file in the apache
      directory structure (_e.g.,_
      `/etc/apache2/extra/httpd-vhosts.conf`) and adding as
      many virtual host definitions as you need to that file.  On
      others (Debian, for example), you will need to provide a
      separate file for each virtual host in a directory such as
      `/etc/apache2/sites-available`.  On Debian, you then need to
      enabble the new virtual hosts using `a2ensite`.

      __Templates:__ The `ramp/installation/vhost-configs`
      directory contains a set of example files that could be
      used as templates for individual files on Debian-based
      machines, or whose contents could be added to an
      `httpd-vhosts.conf` file on other architectures.

    * Reload or restart the Apache server.

    * Unless the new virtual host is being served by DNS (and,
      therefore, publicly accessible), you will need to
      make changes on the client machines to see it.
      For example, this might be a matter of editing `/etc/hosts` on the
      client machines and adding lines that resolve the virtual server names
      from the appropriate machine.  For example,

            123.45.0.67     rampdemo

    ##### Environment Subdirectories

    * Make a subdirectory (such as `rampdemo`) under the `public`
      directory for each Ramp/Smart application you have installed.

    * Copy the `index.php` and `.htaccess` files from `public` to your
      new subdirectories.  Edit the `index.php` files in your
      subdirectories to change the setting of the `APPICATION_PATH` from
      `'/../application'` to `'/../../application'`.  Edit the
      `.htaccess` files in the subdirectories to uncomment the line that
      sets the `APPLICATION_ENV` environment variable to set it to the
      appropriate section name in your `application.ini` file (_e.g.,_
      `rampdemo`, `smartdemo`, `smart_development`, or
      `smart_regressiontesting`).

    * Each subdirectory needs to have the same `css`, `images`, and
      `tb_assets`
      directories that the `public` directory has.  You can achieve this
      by creating symbolic links, aliases, or copies of the `css`,
      `images`, and `tb_assets` directories in each subdirectory.

[readme]: /README.md
[customInstall]: /INSTALL_CUSTOM.md
[applIni]: /document/index/document/..%252F..%252Finstallation%252FApplication_Ini.md
[git]: http://git-scm.com/book/en/Getting-Started-Installing-Git
[git-setup]: https://help.github.com/articles/set-up-git#platform-all
[apache]: http://httpd.apache.org/
[mysql]:  http://www.mysql.com/
[php]:  http://www.php.net/
[mamp]: http://www.mamp.info/en/index.html
[wamp]: http://www.wampserver.com/
[xampp]: http://www.apachefriends.org/en/xampp.html
[ramp]: https://github.com/AlyceBrady/ramp

