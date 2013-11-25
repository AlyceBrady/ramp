
# SMART: Software for Managing Academic Records and Transcripts #

Smart is a program for managing records needed to support an academic
institution.  It currently supports managing academic records in
three broad categories:

 * Curriculum records dealing with courses of study (e.g.,
   undergraduate mathematics), courses or modules (e.g., Calculus
   I), and their individual offerings (e.g., the Spring 2012
   offering of Calculus I)

 * Instructor records (e.g., contract start/end dates, courses or
   modules taught)

 * Student records (e.g., courses of study, test scores, enrollment
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


### RELEASE INFORMATION ###

RAMP/SMART Release 0.5.2.  
Released on November 24, 2013.


### LICENSE ###

The source files for Smart/Ramp are released under a BSD 2-Clause license.
You can find a copy of this license in [LICENSE.txt] [license].


### ACKNOWLEDGEMENTS ###

The Smart/Ramp team would like to thank all the contributors to the
Smart/Ramp project and the institutional supporters who have provided
time, expertise, and money.

Institutional supporters include:

>   Kalamazoo College, Kalamazoo, Michigan, USA  
>   Njala University, Sierra Leone  
>   The Arcus Center for Socal Justice Leadership, Kalamazoo, Michigan, USA  

Individual contributors include:

>   Keaton Adams  
>   Giancarlo Anemone  
>   Alyce Brady  
>   Christopher Cain  
>   Katrina Carlsen  
>   Chris Clerville  
>   Ryan Davis  
>   Ashton Galloway  
>   Guilherme Guedes  
>   Simon Haile  
>   Tristan Kiel  
>   Lucas Kushner  
>   Justin Leatherwood  
>   Tendai Mudyiwa  
>   William Reichle  
>   Renjie Song  
>   Kyle Sunden  
>   Jiakan Wang  
>   Riley Wetzel  

[license]:  /LICENSE.txt

