<h1> SMART: Software for Managing Academic Records and Transcripts </h1>
<h2> HR User Manual </h2>

[ [Introduction](#intro) | [Staff Tables](#structure) | [Adding New
Staff](#newStaff) | [Editing Records](#edit) | [Forms, Reports,
Searches](#searches) ]

<div id="intro"></div>

Smart is a program for managing records needed to support an academic
institution.  It currently supports managing academic records in
three broad categories:

 * __Curriculum records__ dealing with courses of study (e.g.,
   undergraduate mathematics), courses or modules (e.g., Calculus
   I), and their individual offerings (e.g., the Spring 2012
   offering of Calculus I)

 * __Staff records__ (e.g., contract start/end dates, courses or
   modules taught)

 * __Student records__ (e.g., courses of study, test scores, enrollment
   history)

This document focuses on using Smart to manage staff records.
See the [Smart User Manual][suserman] for instructions on general Smart
activities, such as setting and changing your password,
navigating and using activity files, accessing data records in general, and
other basic functionality.

<h3 id="structure">
The Structure of Staff records
</h3>

The information in a "staff record" is actually composed of smaller
records from a number of different tables.  The Staff PF number (also
known inside Smart as the Person ID) is what ties all of the pieces
together.  The tables associated with staff members include: the Person
table (name, sex, citizenship, marital status, etc), the Staff table
(office location, original appointment date, last promotion date), the
Institutions Attended table (history of schools & qualifications
earned), the Job Function table (history of job functions), the
Staff Contract table (history of contract information), the Address
and Phone Number tables, and a Children table.  There are also
associated Accidents and Staff Disciplinary Action tables.  Each table
has its own search/view/edit screens, which you can find under the HR
menu or in the HR activity page.

There are also several HR menu/activity page items that correspond to
forms or reports
(in particular, the Personal Data Form and the Department Staff List),
and several that can guide you through some common scenarios, such as
what tables need to be updated when someone has a job change or a
promotion.

<h3 id="newStaff">
Adding New Staff records
</h3>

One way to start entering data about staff members is to use the
Personal Data Form circulated to staff members in late 2013.  Smart
provides a set of screens that are based on this form.  Each screen
allows you to fill in the items from the form that correspond to a
particular Smart table (the Person table, the Staff table, the Job
Function table, etc.).  Not all of the relevant data can be entered from
this set of screens, though; addresses, phone numbers, children, and
institutions attended are special cases because a staff member may
be associated with more than one of them.  As a result, they have
to be added to their own tables separately.

> Hint: Some people may find it easier to have multiple browser tabs open
> -- one for the Personal Data form, one for entering addresses, one for
> entering phone numbers, one for entering information about children, and
> one for entering information about qualifications or institutions attended.

From the Staff
menu or the main Staff activity page, choose Personal Data Entry.

  - Before entering a new person, search for that person first.
    There might already be a record in the system!  If the search
    turns up no matching entries, you can add a new entry.

  - First page:  On the first page, notice that some fields will allow
    you to enter data, while others will not.  The fields that are
    available are the ones associated with the Person file: personal
    information, such as name, sex, email address, NASSIT, date and
    place of birth, marital status, spouse information, and next of kin.
    Fill these in.
    (You will have to scroll down to find some of these fields.)
    Click on "Save Changes".  (You can also just hit Enter if your
    cursor is in a plain text field; this does not work, though, if the
    cursor is on a drop-down menu.)

> Be sure to use standard, consistent names, codes, or abbreviations for
> data such as country and city names to allow you to search on those
> fields later.  If you use both `Sierra Leone` and `SL` for citizenship,
> for example, or use both `Freetown` and `F/T` for addresses,
> then you will not be able to do single searches to generate reports of
> all staff who are (or are not) Sierra Leonean citizens or who live in
> Freetown.

  - Second page:  After you have saved the first page you will be taken
    to a list screen showing just that one entry.  Select the entry by clicking
    on the magnifying glass on the far right, then scroll to the bottom
    of the page.  At this point you could go to entering Addresses
    (described below), or you could go to the second page of the
    Personal Data Entry screen (`Next Screen -- Staff Info`).  If you
    click on that, you will come to a
    search page that says "No matching results were found", because you
    have not yet created a Staff record for your new person.
    Click on the "Add New Entry" button, which will bring up a
    screen with the staff member's PF number already filled in.
    The rest of the screen will look like the first page, except that a
    different set of fields will be available for you to enter -- the
    ones that correspond to the Staff table.  Once you have entered the
    data on this screen, click on "Save Changes" (or hit Enter).

  - Third page:  You get to the third page in the same way you got to
    the second page: select the record you just entered, scroll to the bottom,
    and click on the next page (`Next Screen -- Job Function`).  Again,
    no matching results will be found, so add a new entry.  Now you can
    enter the fields corresponding to the Job Function table.  Save your
    changes.  If you want to return to the first page of this form, to
    view it, edit it, or go on to adding Addresses, select the new record
    from the "list" (which just contains one entry anyway), and then
    scroll to the bottom where you will see `First Screen - Person Entry`.

  - At the bottom of the first page of the Person Data Entry screen, click
    on Addresses.  For a new staff
    member, no addresses will be found.  Click on the "Add New Entry"
    button, which will create a new address record with the new staff
    member's PF number already filled in.  Set the address type to
    Current or Permanent and then enter the appropriate address
    information.  I recommend filling in the 1st Line (e.g., Drury Lane,
    off Wilkinson), the city, state/province, and country, as these are
    particularly useful for future searches.  When you are done click
    on "Save Changes" (or hit Enter).

> If you decide to edit addresses, phone numbers, __etc.__ in separate tabs,
> then you will not be using the feature that automatically fills in the
> new staff member's PF number.  You can find the PF number at the top
> of the Personal Data Entry screen anytime after you have saved your first
> page.

  - The easiest way to enter a second address for the same person is to
    click on "Clone this Entry".  Change the address type and any parts
    of the address that are different from the previous address.

>   If the person has the same Permanent and Current Address,
>   you should still create two records.  In this case, the only
>   difference between them will be the Address Type.  Having both
>   records in the system will help with future searches.

  - You will find links for adding phone numbers at the bottom of either
    the Personal Data screen or the individual Address screen.

  - If the staff member has children, you will find the Children table
    listed under the HR menu or activity page.  (You might be tempted
    to back up in your
    browser until you return to the Personal Data Entry screen, but
    backing up through data entry screens can cause duplicate data
    entry, so it is not advised.  This is one reason you might find it
    convenient to edit the different tables in different browser tabs.)

  - To add information about a staff member's qualifications (degrees,
    certificates, etc.), go to the Institutions/Qual. item on the
    HR menu or activity page.  There are also links to this screen from
    the bottom of the
    Personal Data Entry, Department List, and Staff record screens.

When you enter or edit data, various fields may appear with different
colors:  bright yellow = required; light yellow = recommended; white =
optional; grey = discouraged.  See
the [Smart User Manual][suserman] for additional general information on 
adding and editing data.

If you do not have all the information you need for a given staff
member, you can either choose to enter that person later or enter the partial
information and then edit the record later.  If the missing information
is important, you will want to develop a good way of keeping track of
what still needs to be done (for example, you may choose to keep a pile
of forms that need to be entered or revised, with the missing
fields circled or highlighted with a big arrow).

Please note that the Smart HR tables include fields, such as
citizenship, that the Personal Data Form did not ask for.  You may wish
to revise the Personal Data Form to include some of them, if they seem
useful to your university, and use that in the future.  You could also
develop something like an Annual Personal Information Update Form that
would show the information that is currently in the system and allow
staff members to correct or update fields as necessary.

<h3 id="edit">
Editing Staff records
</h3>

You can edit Staff records using the Personal Data Entry form, going
from screen to screen as you do when entering new staff members, or you
can edit the individual tables directly (Person, Staff, Addresses,
etc.).  Screens for the individual tables appear in the HR menu or
activity page after the forms and procedures.  You cannot enter or edit
staff information using the Department Staff List screen, as all the
fields on that screen are read-only.

<h3 id="searches">
Forms, Reports, and Searches
</h3>

Two sets of screens have been set up to match existing forms -- the
Personal Data Entry screens and the Department Staff List screen.  The
Personal Data Entry screens is particularly useful for data entry and
for seeing details about a single individual (although there are some
fields it does not show, since they were not on the existing form).  The
Department Staff List screen cannot be used for data entry; it is most
useful for seeing information about groups of people, especially broken
down by departments, as its name imples.  You
can also use the Personal Data Entry information and any of the various
table-specific screens to see summary information for various groups and
to generate reports.

> More forms and reports may be added in the future.  Let your Smart
> administrator (ICT) know of particular requests so that they may
> investigate the feasibility and difficulty of meeting them.

To see information about a particular group, the first step is to
identify the right Search criteria.  Here are several examples:

 - If you want to see active staff within the Finance department.
   Go to the Search screen for the Staff table or the Personal Data
   Entry or Department Staff forms, fill in the School/Department
   name (`FIN`) and the Active field (`Active`), and click on the
   "Search on All Fields" button.
 - To look for all active staff whose last promotion was at
   least 5 years ago, set the Active field and set the operation and field
   for Last Promotion Date to `<` and the date five years ago (e.g.,
   `2009-02-14`).
 - To view staff members whose contracts are set to expire in the
   next two months, go to the Contracts table, set the Active field
   to `Active` and set the operation and field for the Expiration
   Date to `<` and the date for two months from now.
 - To see if there are contracts that are
   about to expire for whom you do not yet have renewal recommendations
   (whether to renew or not, and for how long), set the Active and
   Expiration Date as before and set the Renewal Recommendation operation
   to `IS NULL`.  Or, since a renewal recommendation might be out-of-date,
   set the Renewal Recommendation Date to one year ago to find expiring
   contracts for whom you do not have a renewal recommendation within
   the last year.

These are just a few examples of how various Search combinations
("queries") can provide useful information.

See the general [Smart User Manual][suserman]
for information about using the "Match Against
Any Field" button rather than the "Search on All Fields" button, or
for more information on
going from the abbreviated List results returned by a query to Tabular
or Split View results.

If you wish to print your query results, to share
with senior staff or department heads for example, you should be able
to print right from your browser window.  If you have table or split
view results that you want to work with in a spreadsheet, drag your
mouse over the relevant data, use the browser's Edit menu to copy the
data, and then paste them into a spreadsheet.
(Both printing and copy/pasting to a spreadsheet have been tested and
work on a Mac; I assume they will also work on a PC.)

[suserman]: /document/index/document/smartDocs%252FSmartRampUserManual.md
