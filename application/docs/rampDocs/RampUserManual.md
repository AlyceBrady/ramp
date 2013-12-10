<h1> User Manual </h1>
<h2> RAMP: Record and Activity Management Program </h2>

[ [Introduction](#intro) |
  [Passwords](#pw) |
  [Activity Files](#activities) |
  [Data Searches](#search) |
  [Viewing Lists](#lists) |
  [Viewing, Adding, Editing Records](#add) |
  [Generating Reports](#reports) ]

<div id="intro"></div>

[VERY EARLY, VERY DRAFTY NOTES]

May be able to get quite a bit from the Word documents created for
Njala, circa June 2012.

Describe the major activities or types of pages within Ramp:

Logging in and setting/changing your password.

Activity files provide a way of grouping or structuring related data and
activities into a sort of "home page" for that grouping.

Table screens provide a way of viewing data in a table by record or
groups, editing data, or adding or deleting data.  Specifically, the
actions are:
  - Search for a record or group of records
  - View a list of records (e.g., the results of a search, or all the
    records in a table if the table is not too big)
  - View the records in tabular format (a simple report with column
    headings)
  - View a single record
  - Edit a record
  - Delete a record
  - Add a new record
  - Add a new record by first cloning an existing, similar record
    (not including the key information that makes that record unique)

Reports are similar to tabular views, except that it is possible to
create specialized stylesheets that govern their appearance.

Documents ...

<h3 id="pw"> Setting and Changing Your Password </h3>

Notes:
  - New users (including DBAs when accounts first created) are given a
  default password.  This causes RAMP to redirect the user to a Set
  Password screen the first time they try to log in.

<h3 id="activities"> Understanding Menus and Activity Files </h3>

How much can be said about this?  Is most of it
application-dependent?

Say anything about procedures or workflows?

<h3 id="tables"> Accessing Data </h3>

Probably the most important activities to document are Search and Add.

<h4 id="search"> Searching </h4>

Explain how to do a search, including
the difference between Search-All and Search-Any.

<h4 id="lists"> Viewing Lists and Tables </h4>

How to choose a single record to view/edit/delete from a Search Results
page (list or tabular view). <i class='icon-remove'></i>, <i
class='icon-search'></i>

Explain the 
<i class='icon-ok'></i>, 
<i class='icon-adjust'></i>, and
<i class='icon-minus'></i> icons.

Would be nice if we had a sorting feature that we could document!

Explain the purpose and use of external references at the bottom of
single-record view pages.

<h4 id="add"> Viewing, Adding, and Editing Single Records </h4>

Will need lots of documentation on adding.

Some of that might be on ways to create related records with "foreign
keys" filled in or with ability to look up foreign keys.

  - __"Drill-down" functionality:__
Use External References to Staff and Student from Person to provide 
a "drill-down" option with search.  If we have created a new Person
entry and now need to make the person a Student, we click on Student
Record external reference which will come up with no matches found 
and then we can Add New Entry with the foreign key automagically 
filled in.

> From the Admin User Manual:
External references are also useful when creating related records.  For
example, the full record for a staff member might include a Person
record (with the person's name and other common information), a Staff
record (with additional information specific to staff members), a Staff
Contract record, and other, related records.  Creating a full record for
a new staff member starts with creating the Person record.  If the table
setting for viewing Person records includes an external reference to the
Staff record (initially an empty related record
(<i class='icon-minus'></i>)), the user can click on that
external reference to start a search for the non-existent record and
then choose to Add New Entry.  The person's ID, which was the basis for
the failed search, will be filled in automatically.


  - __"Add-another" functionality:__
When a list/table is a subset based on a search or filter, the new 
entry will have the search/filter values automagically filled in.  (Can
be changed if the new entry is not, in fact, related to the previous 
search or filter.)  Example: clicking on the Module Offerings external 
reference of a Module, or searching for that module's ID or code/number
explicitly, results in a list of existing Offerings for that Module (or
an empty list).  Choosing Add New Entry will create a new Module 
Offering record with the Module ID already filled in.

  - __"Look up key" functionality:__
Use the magnifying glass next to text input field to open a new tab
or window in which you can search for the key of a related record.
> For example, a Student record might require an advisor's ID.  A
> user might not know the ID, however, but might know the advisor's
> name.  If there ia a
little magnifying glass (<i class='icon-search'></i>)
next to the field, the user can click on that to open a new tab or
window that will contain a search page for the table that contains the
advisor's ID.  The user can do the search, copy the ID number, and
then paste it back into the student record.


Editing may not need much, but can piggy-back on some of the explanation
under adding of things like required, recommended, and discouraged
fields, external fields, etc.

<h4 id="reports"> Generating Reports </h4>

Will this get documented here or in the Ramp Admin User Manual?


