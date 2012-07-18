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
     undergraduate
       mathematics), courses or modules (e.g., Calculus I), and
       their individual offerings (e.g., the Spring 2012 offering
       of Calculus I)

     - Instructor records (e.g., contract start/end dates, courses or
       modules taught)

     - Student records (e.g., courses of study, test scores, enrollment
       history)

In time, the plan is to separate Ramp and Smart into separate
projects, and to expand Smart to include customized activities
specific to the academic records domain (as opposed to the more
general-purpose activities supported by Ramp).


RELEASE INFORMATION
---------------
RAMP/SMART Release 0.0.0.1.
Released on July 12, 2012.

SYSTEM REQUIREMENTS
-------------------

RAMP (Record and Activity Management Program) was developed using
PHP 5.3 and Zend Framework 1.11.

INSTALLATION
------------

Please see INSTALL.txt.  (To be provided...)

LICENSE
-------

The files in this archive are released under a BSD 2-Clause license.
You can find a copy of this license in LICENSE.txt.

ACKNOWLEDGEMENTS
----------------

The RAMP team would like to thank all the contributors to the RAMP project
and Kalamazoo College.
