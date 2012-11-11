Welcome to the (temporarily) combined RAMP/SMART Release 0.0.0.1.
    RAMP: Record and Activity Management Program
    SMART: Software for Managing Academic Records and Transcripts

RAMP
----

    Ramp is a program that supports the easy creation of simple
    activity files (lists of grouped activities with descriptions),
    provides a mechanism for creating database views (called "table
    settings") that is meant to be accessible to domain experts (as
    opposed to database experts), and provides software for populating,
    viewing, and updating database tables through the table settings.
    For any database table for which a table setting has been
    defined, Ramp supports:

     - A search mechanism for finding records based on values in fields

     - A table view for viewing a set of table records with column headings

     - A list view for viewing table records (often just a defined
       subset of columns, useful for selecting a single record from
       a search that yielded multiple results)

     - A single-record view for viewing records in their entirety,
       modifying them, or adding new records to the table; single-record
       views may also contain links to other, related tables or
       records if the relationships are defined in the table setting

    Ramp is set up to work with tables defined in a MySQL database,
    using the Zend Framework.

SMART
-----

    SMART currently consists of a set of activity files and table
    settings to support managing academic records in three broad
    categories:

     - Curriculum records dealing with courses of study (e.g.,
       undergraduate mathematics), courses or modules (e.g., Calculus
       I), and their individual offerings (e.g., the Spring 2012
       offering of Calculus I)

     - Instructor records (e.g., contract start/end dates, courses or
       modules taught)

     - Student records (e.g., courses of study, test scores, enrollment
       history)

In time, the plan is to separate Ramp and Smart into separate
projects, and to expand Smart to include customized activities
specific to the academic records domain (as opposed to the more
general-purpose activities supported by Ramp).


RELEASE INFORMATION
-------------------
RAMP/SMART Release 0.0.0.1.
Released on July 12, 2012.

SYSTEM REQUIREMENTS
-------------------

RAMP (Record and Activity Management Program) was developed using
Apache 2, MySQL 5.5, PHP 5.3 and Zend Framework 1.11.11.  It has
been tested (and seems to work) with MySQL 5.1.44, PHP 5.2.15, and
Zend Framework 1.11.10, as well as later versions up to MySQL 5.5.24,
PHP 5.3.15, and Zend Framework 1.12.  It has not been tested with
earlier versions of MySQL or PHP.  It did not appear to work with
one installation of Zend Framework 1.11.4, nor does it work yet
with Zend 2, which is a completely redesigned version of the Zend
Framework.

INSTALLATION
------------

Please see INSTALL.txt.  (Under construction...)

LICENSE
-------

The source files for Ramp/Smart are released under a BSD 2-Clause license.
You can find a copy of this license in LICENSE.txt.

ACKNOWLEDGEMENTS
----------------

The RAMP team would like to thank all the contributors to the RAMP project
and the institutional supporters who have provided time, expertise, and
money.

Institutional supporters include:
    Kalamazoo College, Kalamazoo, Michigan, USA
    Njala University, Sierra Leone
    The Arcus Center for Socal Justice Leadership, Kalamazoo, Michigan, USA
Individual contributors include:
    Keaton Adams
    Giancarlo Anemone
    Alyce Brady
    Christopher Cain
    Katrina Carlsen
    Chris Clerville
    Ryan Davis
    Ashton Galloway
    Guilherme Guedes
    Simon Haile
    Tristan Kiel
    Justin Leatherwood
    Tendai Mudyiwa
    William Reichle
    Renjie Song
    Kyle Sunden
    Jiakan Wang
    Riley Wetzel

