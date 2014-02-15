<h1> User Manual </h1>
<h2> SMART: Software for Managing Academic Records and Transcripts </h2>

[ [Introduction](#intro) | ... ]

<div id="intro"></div>

Smart is a program for managing records needed to support an academic
institution.  It currently supports managing academic records in
three broad categories:

 * __Curriculum records__ dealing with courses of study (e.g.,
   undergraduate mathematics), courses or modules (e.g., Calculus
   I), and their individual offerings (e.g., the Spring 2012
   offering of Calculus I)

 * __Instructor records__ (e.g., contract start/end dates, courses or
   modules taught)

 * __Student records__ (e.g., courses of study, test scores, enrollment
   history)

Smart allows a database administrator to create various views of
the tables in the database, called "table settings." For any database
table for which a table setting has been defined, Smart supports:

 * A search mechanism for finding records based on values in fields

 * A table view for viewing a set of table records with column headings

 * A list view for viewing table records (often just a defined
   subset of columns, useful for selecting a single record from
   a search that yielded multiple results)

 * A single-record view for viewing records in their entirety,
   modifying them, or adding new records to the table; single-record
   views may also contain links to other, related tables or
   records if the relationships are defined in the table setting

In time, the plan is to expand Smart to include customized activities
specific to the academic records domain, such as student registration
and generation of transcripts.

Smart is built on top of Ramp (Record and Activity Management Program),
which provides generic methods for creating pages for choosing among
various activities and for viewing and updating database tables.  Ramp,
in turn, uses MySQL, PHP, and the Zend Framework.

See the [Ramp User Manual][ruserman] for instructions on setting and changing
passwords, navigating and using activity files, accessing data records and
other basic functionality.


[VERY EARLY, VERY DRAFTY NOTES]


[ruserman]: /document/index/document/rampDocs%252FRampUserManual.md
