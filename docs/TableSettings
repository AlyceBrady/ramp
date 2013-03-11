Here are some notes I shared with students about table settings that
should be expanded into an introduction or tutorial, with some samples
as "illustrations".

===========

At the code level, Ramp treats every table the same.  To allow
it to display tables (or different views of tables) uniquely, Ramp
allows users to define "table settings" that specify a title for the
table, which columns to include, the column order, and column headings.
Getting a little fancier, the table setting can include a "footnote" for
the table (although the current views display the footnote below the
title, not below the table) and "footnotes" for columns (displayed as
mouse-over notes).  An example of a fairly straight-forward table
setting is in settings/demo/PlacesComplete.ini.

[Note that some table setting information is for the table as a whole
(title, table footnote, external links), but most describes specific
fields (label, field footnote, whether the field should be hidden,
recommended, discouraged, etc).]

[Note the general syntax for field properties:
    field.fieldNameInDb.property = value
For example,
    field.first_name.label = "First Name"
specifies the label (column heading) to use for the column representing
the field called "first_name" in the database.
]

Field Visibility:

By default, a table view includes all columns (and only those columns)
that have a label (column heading) specified in the specific table
setting.  It is possible, though, to override both defaults.  One can
provide information about a column, including a label, but still choose
to not include it in the table view by setting the 'hide' property to
true.  One can choose to have all columns shown by default by specifying
showColsByDefault, in which case it is still possible to hide specific
columns.

Required, Recommended, and Discouraged Fields:

A table setting can also specify that a particular field or
column is recommended by setting the 'recommended' property to true.
(One specifies that a column is required, rather than merely
recommended, in the database itself as part of the column type and
properties.)  [Talk about fields with defaults or auto-incrementing
specified in the database, and why providing values for them might be
discouraged.  Last modified dates are another example of this: see
sample.ini for more information.]

Providing info from multiple tables in a single table view:

A table view can include data that actually comes from other
tables.  For example, what appears to be a table of student ids, student
names, and student addresses could actually, in the database, be the
result of a database join between a table of ids and names and a table
of ids and addresses.  The table setting would specify one database
table as the source and import the columns from the other table using an
"importedFrom" property.  For example, in a typical Smart application,
the Module Assignments table view includes information such as the
instructor's name from the Person table.

In order for a table to "import" fields from another table, the table
setting must establish the connection between the two tables.  For
example, the Student table setting can document a connection to the Person
table through its studentID field, which refers to the id field in the
Person table, with the following setting specification:
    tableConnection.Person = "Student.studentID = Person.id"
The Student table can then import a student's first and last names from
the Person table as follows:
    field.firstname.label = "First Name"
    field.firstname.importedFrom = "Person"
    field.lastname.label = "Last Name"
    field.lastname.importedFrom = "Person"

The simplest syntax for the tableConnection specification is:
    tableConnection.OtherTable = "ThisTable.col = OtherTable.its_col"
If a connection is based on more than one column, the multiple
connections can be linked with "and":
    tableConnection.Other="Table.col1 = Other.col1 AND Table.col2 = Other.col2"

In some cases, an external table may be used to provide information
for two (or more) different purposes, in which case defining one
or more aliases for the table and multiple table connections allows
the external table to be treated as multiple tables, one for each
purpose.  For example, an Advising table showing students and their
advisors might import both the student's name and the advisor's
name from a Person table.  In this case, we could define an alias
for the Person table for the advisor relationship and then establish
two table connections, one for the student relationship and one for
the advisor relationship.  The tableConnection and importFrom
statements dealing with the advisor relationship use the alias name
rather than the actual table name.
    tableConnection.Person = "Student.studentID = Person.id"
    tableConnection.Advisor.aliasFor = "Person"
    tableConnection.Advisor.connection = "Advising.advisorID = Advisor.id"
    ...
    field.lastname.importedFrom = "Person"
    field.studentLastname.importedField = "lastname"
    field.advisorLastname.importedFrom = "Advisor"
    field.advisorLastname.importedField = "lastname"
When a tableConnection includes an "aliasFor" property, the connection
must be defined with an explicit "connection" property, i.e.,
    tableConnection.AliasName.connection = "Tbl.col = AliasName.its_col"

The table connection depends on the right data being provided when the
dependent table entry is created.  For example,
When adding a new Student record, the user must provide the correct
Person id for the studentID field.  [In theory, the selectUsing field
setting allows the view to provide a link to the external table so that
the user can do a search and get the right id, but this is not currently
working!]

Initializing a field from a table of legal values:

[New feature: selectFrom field setting that allows the program to use a
specified field from an external table as a source of possible values,
e.g., for a drop-down menu or for validation or field auto-completion.]
    field.term.label = "Term"
    field.term.selectFrom = "Terms.term"  [dbTableName.dbColName]

Duplicating information for historical reasons or efficiency:

A more unusual specification indicates that, on creation of a new
record, a field should be initialized from a record in another table.
This is unusual because generally normalized tables should not have
duplicated information.  In Smart, however, module offerings store
copies of information such as the module title or capacity, since these
values may change over time but the offering information should be
historically accurate, capturing the correct value at the time of the
offering.  In this case, the title and capacity in a new module offering
would be initialized from its module record.

To do this, the table setting developer must establish the connection
between the current table and the source table (similar to a
tableConnection specification [why not just use tableConnection? why
does it need a setting?]).  This is done with an initTableRef
specification such as the following:
    initTableRef.Modules.viewingSequence = Smart/Curriculum/Modules
    initTableRef.Modules.match.localField = "moduleID"
    initTableRef.Modules.match.externalField = "moduleID"

[Syntax seems to be initTableRef.TableName.viewingSequence = Setting.]

The matchings do not specifically need to use the "match" keyword,
so long as the same keyword is used to link the localField with the
externalField.  If the connection depends on multiple fields (e.g.,
module number, section, and term), then one could use match1.localField,
match1.externalField, match2.localField, match2.externalField, etc.

Having established the connection, the setting can use the initFrom
property to specify that a given field is being initialized from the
other table.
    field.modCode.label = "Code"
    field.modCode.initFrom = "Modules"
    field.sTitle.label = "Short Title"
    field.sTitle.initFrom = "Modules"
    field.sTitle.initFromField = "shortTitle"

Difference between importFrom and initFrom:  importFrom is relevant when
viewing and is manifested in a join expression, whereas initFrom is
relevant only when adding, and involves getting and copying the values
from the original source table into the current table (so the values now
exist separately in the two tables and can diverge).  "Imported"
fields never become part of the dependent table; instead, the
importFrom provides a "pointer" to the value in the original source
table (where it stays).

External Links:

A table view can also provide links to related tables that are specified
in the table setting; for example, a module view could include a link to
a list of related module offerings, while each module offering view
could include a link back to its base module if the table settings for
modules and module offerings include appropriate external table
reference information.  An external table reference specifies a title
for the link, a table setting or <a href="#viewing_sequences">viewing
sequence</a> for the external
table, and the fields that the current and external tables have in
common, such as a Person ID, or the module ID, section, and term fields
for module offerings and assignments.

<a name="viewing_sequences"></a>
Viewing Sequences:

It is possible to provide a set of table settings for a single table:
one to be used for inserting new records, one for viewing records, one
for searches, one for listing key information for a set of records, and
one for viewing a full table or set of search results in tabular format.
A set of table settings like is this is called a "viewing sequence."
[Provide an example in place or a pointer to an example.]

The initial action (initAction) property can be set to search or
displayAll (list view).
