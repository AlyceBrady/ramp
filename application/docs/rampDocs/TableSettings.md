# Creating Table Settings and Sequences #

[ [Introduction](#intro) |
  [Table Properties](#tableProps) |
  [Field Properties](#fieldProps) |
  [Viewing Sequences](#viewing_sequences) |
  [Importing Data](#import) |
  [Initializing From Other Tables](#initFrom) |
  [External References](#external) ]

<h4 id="intro"> Generic Code, Table-Specific "Table Settings" </h4>

At the code level, Ramp treats every table the same.  To support
customized table displays (or even different customized displays
for the same table), Ramp allows users to define "table settings"
that specify a title for the table, which columns to include, the
column order, and column headings.  Getting a little fancier, the
table setting can include a "footnote" for the table (which is
displayed below the title, like a subtitle, not below the table)
and "footnotes" for columns (displayed as a temporary hovering note
when the cursor hovers over a field name).  An example of a fairly
straight-forward table setting is below.  (This setting can also
be found in `demoSettings/rampDemo/AlbumsExample.ini`.)

> <h4 id="simpleExample"> Simple Table Setting Example: </h4>

        ; This example shows a table with several fields, one of
        ; which is hidden.  It also specifies a title and subtitle
        ; (footnote) for the table, and labels for the fields.

        tableName = "albums"

        tableTitle = "Albums"
        tableFootnote = "A table of albums and artists"

        field.id.label = "id"
        field.id.hide = true

        field.artist.label = "Artist"
        field.artist.footnote = "A field footnote could go here"

        field.title.label = "Album Title"

Note that although some properties in a table setting are for the table as
a whole (such as the table title and footnote), most describe individual
fields (field label, field footnote, whether the field should be hidden,
_etc._).  It is also possible to specify different settings for
different actions, such as viewing, editing, or searching through a
table.  This is done by defining a [viewing
sequence](#viewing_sequences), described below.

<h3 id="tableProps"> Table Properties </h3>

The valid table properties are:

  * `tableName`  This property is required!  (Or did I change this?)
  * `tableTitle`
  * `tableFootnote`
  * `tableShowColsByDefault`
  * `tableConnection`
     ([Importing data](#import) is an advanced feature described below.)
  * `initTableRef`  (an advanced feature described in
                        [separate section](#initFrom) below)
  * `externalTableRef`  (an advanced feature described in
                        [separate section](#external) below)

The general syntax for table properties, as seen in the [simple table setting
example above](#simpleExample), is:

        tableProperty = value

The `tableName` property specifies the name of the table in the database
with which the setting is associated.  The `tableTitle` defines the
name (or title) to be used when displaying the table or its individual
records in Ramp.  The `tableFootnote` property can be used to provide a
subtitle or set of instructions to be displayed immediately below the
table title.  The string provided for the footnote may extend across
multiple lines, so long as the beginning of the string is on the same
line as the beginning of the property (`tableFootnote=`).
The `tableTitle` and `tableFootnote` properties may include
[Markdown][md] formatting to make some words italicized or bold, for
example.

The `tableShowColsByDefault` property, when set to `true` (or `1`),
specifies that all the fields in the table should be visible unless
explicitly hidden.  When this property is set, even a field that is not
referenced in the table setting will be displayed, whereas the usual
behavior is to hide unreferenced fields.

<h3 id="fieldProps"> Field Properties </h3>

The valid field properties are:

  * `label`  (All fields mentioned in a table setting should have a label.)
  * `footnote`
  * `hide`
  * `readOnly`
  * `recommended`
  * `discouraged`
  * `selectFrom`
  * `importedFrom` and `importedField`
     ([Importing data](#import) is an advanced feature described below.)
  * `selectUsing`  (This property is also related to
     [importing data](#import).)
  * `initFrom` and `initFromField`  (an advanced feature described in
                        a [separate section](#initFrom) below)

The general syntax for field properties, as seen in the [simple
example above](#simpleExample), is:

        field.fieldNameInDb.property = value

The behavior of field properties varies somewhat among three different
types of Ramp screens:  screens displaying a single record (_e.g._, for
search or for viewing, adding, or modifying a record), screens
displaying multiple records in "list view" (a list of records with no
column headings), or screens displaying multiple records in "table" (or
"tabular") view, with column headings.

#### Field Labels and Footnotes: ####

In the [table setting example above](#simpleExample), the `albums` table
in the database contains three fields, `id`, `artist`, and `title`
(referring to the album title).  Those are the names used in the
`fieldNameInDb` component of the property specification.  The `label`
associated with each field is the name that will appear next to the
field when viewing a single record, or at the top of the column in
table view form.  The optional `footnote` property can be used to provide
additional useful information (such as the correct format to provide for
a date); it appears as a hover note ("tooltip") when the cursor
hovers over the field name in single-record or table view mode.  In
list-view mode, the relevant field label and footnote appear together
as a hover note when the cursor hovers over a data item.

#### Field Visibility: ####

By default, a table view includes all columns (and only those columns)
that have a label (column heading) specified in the specific table
setting.  It is possible, though, to override these defaults.  One can
provide information about a column, including a label, but still choose
not to include it in the table view by setting the `hide` property to
`true`.  One can also choose to have all columns shown by default by
setting `showColsByDefault` to `true`, in which case it is still possible
to hide specific columns.

#### Read-Only, Required, Recommended, and Discouraged Fields: ####

[TODO: `readOnly`]

Fields are defined as "required" in the database itself, not in a table
setting.  In addition to that functionality, Ramp allows an application
to specify that a field is "recommended" (or, alternatively,
"discouraged") by setting the `recommended` or `discouraged` property to
`true`.  When editing or adding a new record, Ramp will display required
and recommended fields with different background colors, to draw
attention to them.  While the database will prevent the insertion of any
record required fields missing, it is possible to add records with
recommended fields missing (if, for example, the data is not known
when the record is created). Ramp will, however, indicate that such
a record is "incomplete" in a list or table view by displaying an
incomplete circle icon (<i class='icon-adjust'></i>) next to it.
It will also display the same icon next to an external reference
to an incomplete record (see [below](#external) for more on external
record references).

Fields may be marked as "discouraged" when, for example, the database
specifies that they are auto-incremented or they are provided with a
default (such as a current timestamp).  Such fields can be protected
by hiding them, but sometimes it is desirable to provide
the ability to set such a field manually in certain cases, even if it is
discouraged generally.

<h4 id="select">
Initializing a field from a table of legal values:
</h4>

The `selectFrom` property allows the program to use a
specified field from an external table as a source of possible values.
This provides behavior similar to an ENUM data type in the database, but
allows the set of valid values to change over time.  For example, a table
of university course module offerings might have a `term` field to
specify the term in which the course module is being offered.  Rather
than have this as an open-ended text field, where it would be easy to
type in an incorrect term, the `term` field could be initialized using
a drop-down menu of values from a table containing valid terms, as in
the example below.

        field.term.label = "Term"
        field.term.selectFrom = "Terms.term"

The syntax for the `selectFrom` property, as seen in this example, is:

        field.fieldNameInDb.selectFrom = "otherDbTableName.dbColName"

<h3 id="viewing_sequences">
Viewing Sequences
</h3>

It is possible to provide a set of table settings for a single table:
one to be used for inserting new records, one for viewing records, one
for editing records, one for instituting
searches, one for displaying search results (or all records in the
table) as a list, and one for viewing records in tabular format.
A set of table settings like is this is called a "viewing sequence."
The table settings specified in the viewing sequence may be defined in
the same file as the sequence or in different files.  The following
example illustrates both.

> <h4 id="sequenceExample"> Sequence Example: </h4>

        sequence.initAction = "search"
        sequence.setting = "Detailed View"
        sequence.addSetting = "Modifying View"
        sequence.editSetting = "Modifying View"
        sequence.searchResultsSetting = anotherDirectory/Selection

        [ Detailed View ]
        ; The "Detailed View" table setting goes in this section

        [ Modifying View ]
        ; The "Modifying View" table setting goes in this section

The valid sequence properties that can be defined are:

  * `initAction`  (can be `displayAll` or `search`)
  * `setting`:      the default setting
  * `editSetting`:  setting to use to edit a single record
  * `addSetting`:  setting to use to add a new record
  * `searchSpecSetting`:  setting to use for specifying search criteria
  * `searchResultsSetting`:  setting to use to display records in list view
  * `tabularSetting`:  setting to use to display records in table view
  * `referenceSetting`:  setting to use to resolve external references
        (see [below](#external))

All sequence properties are optional.
If a table setting file does not specify a setting sequence but does
provide table setting properties for a single table setting
(as in the [initial example above](#simpleExample)), Ramp assumes
that the table setting should be used for all table and record actions.

The `initAction` property specifies whether the initial action
associated with viewing the table should be to search for specific items
or to display all the records in the table.  The results of either the
search or `displayAll` action are displayed as a list.  (Actually, if
the result is just a single record, the result is displayed in
single-record mode.)
If there is no viewing sequence information in a table setting file,
or there is but the `initAction` property is not provided, the default
initial action is to start a search.

The `setting` property specifies the setting to use to display single
records, but is also the default setting to use for other actions if the
more specialized settings are not provided.  If the `setting` property
is not defined but at least one of the specialized settings is,
the main setting is set from the edit 
setting, the add setting, the search specification setting,
the search results setting, or the tabular setting (in that order).
For any of those five that are not provided, Ramp uses the now-defined
main setting as the default.

A field may also include a reference setting, used when 
resolving external references.  If a reference setting is not 
provided, the add setting is used to resolve external references.

#### Reducing duplicated information: ####
NOTE:  It is possible to use inheritance among sections in an `ini` file
to avoid repeating similar information for different table settings in a
viewing sequence.  The only potential problem is that an additional
field defined in a table setting will appear after the inherited
fields, since the order in which fields appear is the order in which
they are introduced in the table setting.  For example, an `ini` file
could have the following properties:

        [ SharedProperties ]
        tableName = "theTable"
        field.a.label = "A"
        field.b.label = "B"
        field.d.label = "D"
        [ View ]
        field.b.recommended = true
        [ Modify ]
        field.c.label = "C"
        field.d.recommended = true

In this case, both the View and Modify table settings have the three
fields labeled A, B, and D.  In the View table setting, field B is
recommended, whereas in
Modify, field D is recommended.  Modify also introduces an additional field C.
The order of the fields in the Modify table setting will always be
A, B, D, C, regardless of the order of the two property specifications
in Modify, even if A, B, C, D might have been more desirable, because D
was initially introduced in the SharedProperties section.  The
easiest way to get around this ordering problem is to introduce all the
appropriate fields in the parent section and then hide fields you do not
want in some settings.  In this case, the corrected ini file would be:

        [ SharedProperties ]
        tableName = "theTable"
        field.a.label = "A"
        field.b.label = "B"
        field.c.label = "C"
        field.d.label = "D"
        [ View ]
        field.b.recommended = true
        field.c.hide = true
        [ Modify ]
        field.d.recommended = true

A more realistic example is in `demoSettings/Smart/Student/Enrollment.ini`.

<h3 id="advanced">
Advanced Table and Field Features
</h3>

<h4 id="import">
Importing data from other tables:
</h4>

A table view can include data that actually comes from other
tables.  For example, what appears to be a table of student ids, student
names, and student addresses could actually, in the database, be the
result of a database join between a table of ids and names and a table
of ids and addresses.  The table setting would specify one database
table as the source and import the columns from the other table using an
`importedFrom` property.  For example, in a typical Smart application,
the Course Module Assignments table view includes information such as the
instructor's name from the Person table.

In order for a table to "import" fields from another table, the table
setting must establish the connection between the two tables.  For
example, using the following setting specification, the `Student`
table setting can document a connection to the
`Person` table through its `studentID` field, which refers to the
`id` field in the `Person` table.

        tableConnection.Person = "Student.studentID = Person.id"

The `Student` table can then import a student's first and last names from
the `Person` table (using the field names from that table) as follows:

        field.firstname.label = "First Name"
        field.firstname.importedFrom = "Person"
        field.lastname.label = "Last Name"
        field.lastname.importedFrom = "Person"

The simplest syntax for the `tableConnection` specification, as modeled
above, is:

        tableConnection.OtherTable = "ThisTable.col = OtherTable.its_col"

If a connection is based on more than one column, the multiple
connections can be linked with `and`:

        tableConnection.Other="Table.col1 = Other.col1 AND Table.col2 = Other.col2"

In some cases, an external table may be used to provide information
for two (or more) different purposes, in which case defining one
or more aliases for the table allows it to be treated as multiple
tables.  For example, an Advising table showing students and their
advisors might import both the student's name and the advisor's
name from a Person table.  In this case, we could define an alias
for the Person table for retrieving the advisor's Person information
and then establish two table connections, one for getting the student
information and one for the advisor information.  The `tableConnection`
and `importedFrom` statements dealing with the advisor information
use the alias name rather than the actual table name.

        tableConnection.Person = "Student.studentID = Person.id"
        tableConnection.Advisor.aliasFor = "Person"
        tableConnection.Advisor.connection = "Advising.advisorID = Advisor.id"
        ...
        field.lastname.importedFrom = "Person"
        field.studentLastname.importedField = "lastname"
        field.advisorLastname.importedFrom = "Advisor"
        field.advisorLastname.importedField = "lastname"

When a `tableConnection` includes an `aliasFor` property, the
statement establishing the connection through the relevant fields
must use an explicit `connection` property, as seen above.  The
syntax is:

        tableConnection.AliasName.aliasFor = "OtherTable"
        tableConnection.AliasName.connection = "Tbl.col = AliasName.its_col"

The table connection depends on the right data being provided when the
dependent table entry is created.  For example,
when adding a new Student record, the user must provide the correct
Person id for the studentID field.
 > In theory, the `selectUsing` field
 > setting allows the view to provide a link to the external table so that
 > the user can do a search and get the right id, but this is not currently
 > working!

<h4 id="initFrom">
Duplicating information for historical reasons or efficiency:
</h4>

A more unusual specification indicates that, on creation of a new
record, a field should be duplicated from a record in another table.
This is unusual because generally normalized tables should not have
duplicated information.  Sometimes, however, such duplication is
useful, either to provide a historical snapshot of a particular value
at a particular time, or for efficiency (accessing the value in the
current table is more efficient than importing it from another table
every time it is read).

For example, in an academic records system it is not uncommon to
have course module records, some of whose values (such as the title)
may change over time.  Once a student enrolls in a particular
offering of a course module, though, the title in the student's
record should reflect what the title was when the student took it,
not the current value when the student record is being viewed.  In
this case, a copy of the title should be made and stored with the
student's enrollment record when the student enrolls.

To achieve this, the table setting uses an `initTableRef` property
to specify a reference to a sequence or table setting for the source
table, which will be used to search for the source record with the
desired field(s).  The `initTableRef` property also specifies the
field or fields to use for the search, as in the following example.

        initTableRef.Modules.viewingSequence = Smart/Curriculum/Modules
        initTableRef.Modules.match.localField = "moduleID"
        initTableRef.Modules.match.externalField = "moduleID"

The matching does not specifically need to use the `match` keyword,
so long as a single keyword is used to link the `localField` with the
`externalField`.  If the connection depends on multiple fields (e.g.,
module number, section, and term), then a different keyword is used for
each local/external field pairing.

The general syntax for the `initTableRef` property is:

        initTableRef.TableName.viewingSequence = OtherTableSetting
        initTableRef.TableName.keyword1.localField = localFieldName1
        initTableRef.TableName.keyword1.externalField = otherFieldName1
        initTableRef.TableName.keyword2.localField = localFieldName2
        initTableRef.TableName.keyword2.externalField = otherFieldName2

Note that the `viewingSequence` connection is to a table _setting_
(or sequence of table settings) rather than directly to the table
in the database, as in the `tableConnection` property.

Having established the connection to the external viewing sequence with
the fields necessary to find the appropriate source record, the
table setting uses the `initFrom` property to specify that a given
field is being initialized from the other table.  [TODO: Check the
next 2 statements!] If the local field name is the same as the
associated field name in the extrnal table, or if the field is one
of those specified in the `initTableRef` specification for searching
purposes, then the `initFrom` property is sufficient.  If the field
is a new one, then the additional `initFromField` property specifies
the name of the field in the external table from which to initialize
this field.  The example below illustrates the initialization of
fields both with and without an `initFromField` property.

        field.modCode.label = "Code"
        field.modCode.initFrom = "Modules"
        field.sTitle.label = "Short Title"
        field.sTitle.initFrom = "Modules"
        field.sTitle.initFromField = "shortTitle"

##### Comparing `importFrom` and `initFrom`: #####
The `importFrom` and `initFrom` properties look deceptively similar, but
represent very different behavior.  The `importFrom` property, which is
more common, represents reading information from another table just
for the purpose of display; the "imported" fields never become part
of the dependent table.  The `initFrom` property, on the other hand,
is used when creating a new record, and involves getting and copying
the values from the original source table into the new record in
the current table, so the values now exist separately in the two
tables and may, over time, diverge.

<h4 id="external">
External Table References:
</h4>

A table setting can provide references, or links, to other settings
for viewing related records.  For example, when displaying a single
record in a student/advisor relationship table, the setting could
provide links that lead to the associated Student and Staff table
records.

Like the `initFrom` property, the `externalTableRef` property
establishes a relationship between a local field and an external
field using an external table setting to retrieve the data.  The
syntax, however, is different.

The example below creates a link (external reference) titled `Staff`
that will appear
at the bottom of record-viewing pages that use this setting.  Following
the link initiates a
search, using the sequence or table setting specified with the
`viewingSequence` property, for a Staff record whose "staffID" matches the
"advisorID" in the current record.

        externalTableRef.Staff.title = "Staff"
        externalTableRef.Staff.viewingSequence = "Smart/Staff/Staff"
        externalTableRef.Staff.localField = "advisorID"
        externalTableRef.Staff.externalField = "staffID"

When displaying the external reference link at the bottom of the
record-viewing screen, Ramp includes an icon indicating whether all
recommended fields in the external record have been provided (<i
class='icon-ok'></i>), whether some recommended fields are missing
(<i class='icon-adjust'></i>),
or whether the related record is blank (<i class='icon-minus'></i>).

[TODO: What if you need more than one field for the search?  Does
`externalTableRef` then use the same keyword syntax as `initTableRef`?
And, can `initTableRef` define a search field without the keywords shown
in the example above if only one field is necessary for the search?  The
wording in the `initTableRef` section implies that a keyword (e.g.,
"match") is always necessary.]



[md]:  http://daringfireball.net/projects/markdown/
