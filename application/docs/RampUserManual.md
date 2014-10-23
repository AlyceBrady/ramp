<h1> RAMP: Record and Activity Management Program </h1>
<h2> User Manual </h2>

[ 
  [Logging In/Passwords](#login) |
  [Activity Pages](#activities) |
  [Data Searches](#search) |
  [Lists and Tables](#lists) |
  [Viewing/Editing Records](#records) |
  [New Records](#add) |
<!--  [Reports](#reports) | -->
  [Table Settings](#settings) ]

<div id="intro"></div>

Ramp is a program that supports the easy creation of simple _activity
files_ (lists of activities) and _table settings_ (database views),
which together create a web-based application.  The application could
keep track of a company's suppliers and inventory, the volunteers
and donors involved in a charitable organization, or students and
courses at an academic institution.  The most important feature of
Ramp is that changing the nature of the application does not require
a new program.  It requires new database tables, of course, to
represent the application's data, but the customization of what
data should appear on what screen, and how tables should be grouped
together, is all defined in simple text files that can be understood
and manipulated by domain experts rather than programmers.
The program -- the underlying engine for populating tables, displaying
tables in several different formats, and updating data -- remains
the same.

In particular, for any database
table for which a table setting has been defined, Ramp supports:

 * A search mechanism for finding records based on values in fields,

 * A table view for viewing a set of table records with column headings,

 * A list view for viewing table records (often just a defined
   subset of columns, useful for selecting a single record from
   a search that yielded multiple results),

 * A single-record view for viewing records in their entirety,
   modifying them, or adding new records to the table. Single-record
   views may also contain links to other, related tables or
   records if the relationships are defined in the table setting.

Although Ramp can be used to manage records and activities tied to many
types of applications, most of the examples in this document will come
from a _Ramp_ demo or from _Smart_, an academic records system that
keeps tracks of students, teaching staff, and the classes that they
take or teach.

<h3 id="login">
Logging In
</h3>

Ramp applications may make some activities and tables available to
anyone, but other activities and tables will probably require logging in
first.  The login page prompts for your username and password.

<img src="/images/RampUserManual/Login.png"  width="600px" />

The first time you login, you need only provide your username.  The
login process will automatically redirect you to a page where you
can set your password.  If you forget your password there is no way
to recover it, but your Ramp administrator can reset your password
back to its initial state, after which you can set it again the
next time you login in.

<img src="/images/RampUserManual/InitPassword.png"  width="600px" />

You can also change your password at any time.  The menu items for
changing your password and logging out appear in the upper-right
corner of any Ramp page once you are logged in.

<!--
<img src="/images/RampUserManual/LoggedIn.png"  width="250px" />
-->

<h3 id="activities">
Activity Pages and Menus
</h3>

Activity pages provide a way of grouping or structuring related data and
activities into a sort of "home page" for that set.  Each activity has a
button that you can click on to go to that activity and a brief
description.  Activities can be grouped together under sub-headings, as
in the example below.

<img src="/images/RampUserManual/SmartInitialActivity.png"  width="600px" />

The common activity types include:

  - going to another activity page,
  - viewing a document (such as this one),
  - viewing or modifying a table,
  - generating a report.

In the example above, which is the initial activity page for the Smart
application, the Terms and Person File activities go to
individual tables showing a list of academic terms or person records.
The other activities go to additional activity pages that provide
activities for viewing or modifying academic program tables or staff
records, for example.  The Help menu, shown below, provides links to
various documents, like this one, about Ramp and Smart.  The first item
in the menu ("Help") goes to an activity page with the same set of
options but more description.

<div class="row">
<div class="span4">
<img src="/images/RampUserManual/HelpMenu.png"  />
</div>

<div class="span8">
<img src="/images/RampUserManual/HelpActPage.png" width="600px" />
</div>
</div>

In addition, some menu items or activity pages may include links to
specialized activities, such as adding a new Ramp user account or
running a syntax checker against a table setting file.

<h3 id="search">
Data Searches
</h3>

When you choose a table activity, you may go directly to a listing of
all the records in the table, if it is a short one, but it is more
likely that clicking on the activity will take you to a Search page.  A
search page provides blank text entry boxes for many or all of the
fields in the table, which you can fill in
for the search.  If you fill in several fields, you can choose whether
to search for records that match __all__ of the fields you provided, or
__any one__ of those fields, by selecting the "Search on All Fields" or
"Match Against Any Field" buttons.  In the example below, the user is
about to search for all course module offerings with the course
number "MATH 112".  If the user were to click on "Match Against Any
Field" instead, then the search would look for any offerings whose
code is "MATH" (regardless of the number) or whose number is "112"
(regardless of the code).  The "Reset Fields" button clears all the text
boxes in the form, while the "Display All Entries" button brings up a
list of all the records in the table (useful only if the table is small).

<img src="/images/RampUserManual/SimpleSearch.png"  width="600px" />

Some fields, such as Term and Status in the example above, have a
pre-defined set of legal values, in which case the form provides
a drop-down menu for that field rather than a text entry box.  The first
value in the drop-down menu is always "ANY VALUE" in case you do not
want to search based on that field.

It is also possible to search for fields that are less than a given
value, greater than a value, not equal to a given value, or similar to a
given value.  Just pick a comparison operator from the drop-down menu
next to the field.  The default option, HAS, causes the search to match
any field that contains the string you provide; this is useful if you
only want to type part of a long name, or are not sure of the spelling.
The full list of comparison operators available is:
<dl class='dl-horizontal'>
<dt>HAS</dt>    <dd>any field that contains the given string</dd>
<dt>=</dt>      <dd>match the field exactly</dd>
<dt>&lt;</dt>   <dd>look for fields whose value is less than the given
                value</dd>
<dt>&lt;=</dt>  <dd>look for fields whose value is less than or equal
                to the given value</dd>
<dt>&gt;</dt>   <dd>look for fields whose value is greater than
                the given value</dd>
<dt>&gt;=</dt>  <dd>look for fields whose value is greater than or equal
                to the given value</dd>
<dt>!=</dt>     <dd>match any field whose value is __not__ the given value</dd>
<dt>IS NULL</dt>      <dd>match any field that is `NULL`</dd>
<dt>IS NOT NULL</dt>  <dd>match any field that is not `NULL`</dd>
<dt>LIKE</dt>   <dd>match using SQL regular expression comparisons</dd>
</dl>

In the example below, the user is about to search for all "MATH"
offerings before 2010 (the Start Date is before 1 January, 2010).

<img src="/images/RampUserManual/SearchExample2.png"  width="600px" />


<h3 id="lists">
Lists and Tables
</h3>

The results of a search are displayed as a list of matching
records, as in the example below.  The list does not
include column headings, but if you hover the cursor over a column the
column heading will appear as a hover note ("tooltip"), sometimes with a
brief explanation.  For example, hovering the cursor over the date column
in the example below would result in a note that says "Start Date:
yyyy-mm-dd".

<img src="/images/RampUserManual/ListView.png"  width="600px" />

The three icons at the right of each record require more explanation.
Starting with the rightmost, the "magnifying glass" icon
(<i class='icon-search'></i>) is a button that allows the user to zoom
in and view that one record in greater detail (see [below](#records)).
The "deletion" icon (<i class='icon-remove'></i>)
allows the user to delete a record (bringing up a confirmation page first).
The leftmost icon indicates to what extent the record is "complete", where
completion indicates how many recommended fields have been filled in.
(This is explained in more detail [below](#completion).)  An "OK"
icon (<i class='icon-ok'></i>) indicates that all recommended fields
have values, a half-filled icon (<i class='icon-adjust'></i>) indicates
that some, but not all, recommended fields have values, while a dashed
line icon (<i class='icon-minus'></i>) indicates that no recommended
fields have values.
[Note:  Only the List View has the three icons at the right of each
record, even though the illustrations in this document also show them in
Table and Split Views.]

Two of the buttons on a List page bring up other views of the same
records.  The "Tabular Display" button brings up a table view that has
column headings and may often display more fields than a simple list.

<img src="/images/RampUserManual/TableView.png"  width="600px" />

A "Split View Display" shows the fields in two sections.  Fields that
have the same values for all the included records appear once in the top
section of the page, while fields that have different values across
different records appear in a table at the bottom.  The fields at the
top are usually the ones you would expect given the original search
(course module offerings with code "MATH 112" in our ongoing example),
but may also include less obvious fields that happen to have the
same value across all the records being displayed.

<img src="/images/RampUserManual/SplitView.png"  width="600px" />

One interesting feature of the split view format is that displays
from the same table can take on a very different meanings when
showing the results of a different search.  For example, a Student
Enrollment table represents classes taken by students.  The split view
results of a search for a given student will show the student's
enrollment history, while the split view results from a search for
a given class displays a class list.  The split view format makes the
difference between the two searches much more obvious than the same sets
of records displayed as a simple table.

<div class="row">
<div class="span6">
<img src="/images/RampUserManual/StudentClassHistory.png" />
</div>

<div class="span6">
<img src="/images/RampUserManual/ClassList.png"  />
</div>
</div>

<h3 id="records">
Viewing and Editing Records
</h3>

From a list display, you can "zoom in" on a
particular record by clicking on the magnifying glass
(<i class='icon-search'></i>) at the end of the record row.  The record
view format is similar to the format of a search page, except that it
shows the fields for a particular record rather than a series of empty
text boxes, and does not have the search page's comparison operators
(__HAS__, __=__, _etc._).

<img src="/images/RampUserManual/RecordView.png" width="600px" />

                ...

<img src="/images/RampUserManual/ExternalRefs.png" width="600px" />

One useful feature that is unique to record view pages is the
optional inclusion of links to related tables at the bottom of the
page.  In the case of a course module (class) offering, these links
might include a link to a page showing the full class description,
a link to the `MATH 112` course module record (of which this is
just one offering), a link to the Assignments record that shows who
is teaching this class, and a link to a class list of students
enrolled in the class.  If you click on one of these links, the system
will apply the appropriate data from the current record to a search of
the related table and then go straight to the single record or list that
matches the search.  The icons next to the links, like the icons at the
end of a row in a list, table, or split-view, indicate whether the
related record is "complete" with respect to recommended fields
(<i class='icon-ok'></i>), is only partially complete
(<i class='icon-adjust'></i>), or does not yet have any recommended
fields provided (<i class='icon-minus'></i>), possibly indicating
a completely empty record.

<h4>Editing Records</h4>

When viewing a record, you may choose to edit it.  The Edit View is
similar to a record view, but with more color.  By default, fields are
the same color as the background.  Bright yellow fields are
required by the database, while pale yellow fields are recommended
(though not required) by the application.  A pale green (the field
background color used in the read-only Record View) indicates a field
that is read-only in the Edit View as well.  Finally, an edit page may
include one or more grey fields; you are generally discouraged from
filling in or editing these fields, as they are usually fields whose
values are provided by the system, either through an auto-increment
process, by using defaults or taking values from another table, or
through some other
means.  Like the Search page, an Edit page may provide drop-down menus
rather than text boxes for fields that have a pre-defined set of valid
values.

<img src="/images/RampUserManual/EditView.png" width="600px" />

The buttons on the right side of the screen allow you to save your
changes, reset the values in all the fields to the values they had when
you came to the page, cancel your editing and go back to the Record View, or
delete this record entirely.

Some tables support a special "Edit Records in a Block" button if the
[table setting](#settings) for the table supports block editing.  A block
editing page is similar to a split-view entry, except that some of the
fields in the lower half of the screen will be text fields or drop-down
menus for editing multiple records at a time.  An example
where this would be useful would be entering the grades for all the
students in a given class.

<img src="/images/RampUserManual/BlockEdit.png" width="600px" />

<h4 id="add">
Adding New Records
</h4>

There are a number of ways to create new records in Ramp.  From the
Record View page there is an "Add New Entry" button that
will take you to a blank Add page.  The Add page looks just like the
Edit page, with the same color-coding for required, recommended,
optional, and discouraged fields, but with no data.  The example below
shows two required fields, both of which are keys to other tables (the
Student table and Module Offerings table) and several read-only fields
which actually come from those tables.  If you do not know the value of
a field that comes from another table, you can use the "look-up" icon
(<i class='icon-search'></i>) to open a new tab or window
in which you can search for the related record and then copy and paste
the needed value into the current Add page.

<img src="/images/RampUserManual/AddView.png" width="600px" />

From a Record View, it is also possible to "clone" a new record.  This
brings up an Add page with a number of fields already filled in.  The
"clone" cannot have all the same data values as the original,
since some values must be distinct to make each record unique, but it
will include all the values that may safely be copied.  You may keep
these values or replace them with more appropriate values before you
save your new record.

You can also create a new record from a Record View of another, related
record by clicking on a related table link at the bottom of the Record
View page.   For example, the full record for a staff member might
include a Person record (with the person's name and other common
information), a Staff record (with additional information specific
to staff members), a Staff Contract record, and other, related
records.  Creating a full record for a new staff member starts with
creating the Person record. If the Record View page for Person
records includes a link to the Staff record (initially an empty
related record (<i class='icon-minus'></i>)), you can click on that
link to start a search for the non-existent record and then choose
Add New Entry. The person's ID, which was the basis for the failed
search, will be filled in automatically.

The "Add New Entry" button on a List or Table Display will also bring
you to a partially-filled out Add page.  In this case, the filled-in
fields come from the search that resulted in the list or table display.
(If you were displaying all the records in the table rather than the
results of a search, then the "Add New Entry" will bring up an empty
Add page, just as it does from a Record View page.)

Similarly, if you do a search that results in no matches, an extra "Add
New Entry" button will appear on the Search page.  If you click on this,
it will bring you to an Add page with the search fields filled in.

Finally, from the Split View page, there may be a special "Add ... in a
Block" button (_e.g._, "Add Students in a Block"), if the [table
setting](#settings) for the table supports block record entry.  A block
record entry page is similar to a split-view entry, except that it has a
group of blank text fields that allow you to enter multiple instances of
some key field in order to create multiple records simultaneously.  The
example below shows a split view of a class list, as seen above, but
with empty text boxes in which you can enter up to 10 new student IDs at
a time.  Doing so and saving your changes will create a group of new
records with the same shared values shown in the top section of the
split view, but with different student IDs.

<img src="/images/RampUserManual/BlockRecordEntry.png" width="600px" />


<h4>Deleting Records</h4>

Just as there are a number of ways to create new records in Ramp, there
are also a number of ways to delete records.  From all
record or table "viewing" pages, and from the Edit page as well,
there is a "Delete Entry" button.  Clicking this button will bring you
to Confirmation page that shows the record you are deleting (in
essentially Record View format) and asks you to Confirm or Cancel the
deletion.

<h3 id="settings">
Activity Files and Table Settings
</h3>

Ramp is a program for navigating among activity pages and viewing and
updating tables in a database ("Record and Activity Management Program").
It knows nothing about the content of either the activity pages nor the
tables.  How, then, is the generic Ramp software transformed into a
system for managing academic records, a system for keeping track of
volunteers and donors for a non-profit organization, or an application
to track a company's suppliers and inventory?  The first and most
fundamental difference between different Ramp applications is the set of
database tables designed for each.  What creates a Ramp interface to
those tables, though, is a set of activity files and table settings that
define what activities should be grouped together and what data should
appear on what screen.  Table settings, in particular, determine
which fields get included in any particular view, the column
headings and hovering "tooltip" notes, and the inclusion (or not) of
links to related tables.  Table settings also allow for the viewing and
modification of "virtual tables," combinations of data from several
different database tables, such as a Staff Contract table that is
actually made up of name and address information from a Person
table, department and office information from a Staff table, and
title and start/end dates from a Staff Contract table.

Activity files and table settings are plain text files that describe how
activity and table pages should be constructed.  For example, an
activity file containing the lines below, defining subheadings,
table setting activities, and a separator, would generate a subset
of the initial Smart activity page shown [above](#activities).

        activityListHeading = "Choose a file or activity:"

        activity.coreHeading.type = "comment"
        activity.coreHeading.comment = "### Core Tables ###"

        activity.termsTable.type = "setting"
        activity.termsTable.source = "Smart/Term/Terms"
        activity.termsTable.title = "Terms"
        activity.termsTable.description = "List of Terms"

        activity.personTable.type = "setting"
        activity.personTable.source = "Smart/Person/Person"
        activity.personTable.title = "Person File"
        activity.personTable.description = "Person records"

        activity.horizRule.type = "separator"

        activity.curriculumHeading.type = "comment"
        activity.curriculumHeading.type = "Curriculum Heading"

Table setting files provide more options, but a simplified version of
the StaffContract example might look like the following:

        tableName = "StaffContract"
        tableConnection.Person = "StaffContract.staffID = Person.id"

        tableTitle = "Staff Contract Info"
        tableDescription = "Staff Contract History"

        field.staffID.label = "Staff ID"
        field.staffID.readOnly = true
        field.staffID.selectUsing = "Smart/Person/PersonSelection"

        field.firstname.label = "First Name"
        field.firstname.importedFrom = "Person"
        field.lastname.label = "Last Name"
        field.lastname.importedFrom = "Person"

        field.department.label = "Department"
        field.jobFunction.label = "Job Function"
        field.jobFunction.footnote = "If changes, consider making new entry"
        field.jobTitle.label = "Job Title"
        field.jobTitle.footnote = "If changes, consider making new entry"

        externalTableRef.Person.title = "Person Record"
        externalTableRef.Person.viewingSequence = Smart/Person/Person
        externalTableRef.Person.match1.localField = "staffID"
        externalTableRef.Person.match1.externalField = "id"

This example specifies the Ramp content
for a StaffContract table
that includes name information coming from a Person table, specifies the
labels or column headings for the fields, provides hover-style
footnotes for two of the fields, and provides a link at the bottom of
Record View pages to the full Person table entry for this staff member.

It is not necessary to understand activity files and table settings, or
to be able to create them, in order to use Ramp.  This brief
introduction may, however, be useful to Ramp users who wish to
better understand Ramp's cababilities and constraints.  As you can see,
creating activity files and table settings for a new Ramp application
requires basic knowledge of the structure of the application's
database tables, and a deeper understanding of the functional needs
of the application and its users.  It does not, however, necessarily
require any programming unless customized activities are going
to be provided.

For those who will be involved in creating activity files and table
settings, two documents describe their formats and features: [Creating
Activity Files][activities] and [Creating Table Settings][settings].

<!--   WRITE THIS SECTION AFTER I'VE DONE MORE WITH REPORTS
<h3 id="reports">
Reports
</h3>

Reports are similar to tabular views, except that it is possible to
create specialized stylesheets that govern their appearance.

Will this get documented here or in the Ramp Admin User Manual?
-->


<!-- Topic for the future:  Procedures and/or Workflows -->

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

[activities]: /document/index/document/ActivityLists.md
[settings]: /document/index/document/TableSettings.md

