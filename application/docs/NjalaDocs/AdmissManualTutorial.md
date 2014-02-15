<h1> SMART: Software for Managing Academic Records and Transcripts </h1>
<h2> Admissions User Manual </h2>

[ [Introduction](#intro) | [Staff Tables](#structure) | [Adding New
Applicants](#newRecords) | [Editing Records](#edit) | [Forms, Reports,
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

 * __Student records__ (e.g., applicant information, courses of study,
                        test scores, enrollment history)

This document focuses on using Smart to manage student applicant records.
See the [Smart User Manual][suserman] for instructions on general Smart
activities, such as setting and changing your password,
navigating and using activity files, accessing data records in general, and
other basic functionality.

<h3 id="structure">
The Structure of Applicant records
</h3>

The information in an "applicant record" is actually composed of smaller
records from several different tables.  The Person ID number generated
by Smart is what ties all of the pieces
together.  The tables associated with applicants include: the Person
table (name, sex, citizenship, marital status, etc), the Applicant table
(chosen program, interview location, extracurricular activities,
approval/rejection indicators, etc.), the Institutions Attended
table (history of schools & qualifications earned), and the Address
and Phone Number tables.  Each table has its own search/view/edit
screens, which you can find under the Admissions menu or in the
Admissions activity page.

There are also several Admissions menu/activity page items at the top of
the menu or activity page that correspond to the Undergraduate
Application form and the spreadsheet you have used in the past.  There
may be additional forms that ICT or I can develop for you (e.g., the
interview summary sheet you have used at interviews).  I also plan to
develop a "Convert Applicant to Student procedure" and there may be
other procedures that would be useful as well.  (Procedures can either
be fully automated, as I hope the Convert to Student procedure will be,
or be a special activity page that guides you through a common scenario.
Examples that have been developed for HR cover the steps that need to be
taken when someone has a job change or a promotion.)

<h3 id="newRecords">
Adding New Applicants to Smart
</h3>

Smart provides a two-screen sequence based on the undergraduate application
form to facilitate entering new applicants into the system.  Each of the
the two screens allows you to fill in the items that correspond to a
particular Smart table (the Person table and the Applicant table).
Not all of the relevant data can be entered from
this set of screens, though; addresses, phone numbers, and
institutions attended are special cases because an applicant may
be associated with more than one of them.  As a result, they have
to be added to their own tables separately.

> Hint: Some people may find it easier to have multiple browser tabs open
> -- one for the application form, one for entering addresses, one for
> entering phone numbers, and
> one for entering information about qualifications or institutions attended.

From the Admissions
menu or the main Admissions activity page, choose UG Application Form.

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
> for example, or use both `Freetown` and `F/T` for interview locations,
> then you will not be able to do single searches to generate reports of
> all applicants who are Sierra Leonean citizens or who want to be
> interviewed in Freetown.

  - Second page:  After you have saved the first page you will be taken
    to a list screen showing just that one entry.  Select the entry by clicking
    on the magnifying glass on the far right, then scroll to the bottom
    of the page.  At this point you could go to entering Addresses
    (described below), or you could go to the second page of the
    UG Application Form screen (`Next Screen -- Applicant Info`).  If you
    click on that, you will come to a
    search page that says "No matching results were found", because you
    have not yet created an Applicant record for your new person.
    Click on the "Add New Entry" button, which will bring up a
    screen with the applicant's Person ID already filled in.
    The rest of the screen will look like the first page, except that a
    different set of fields will be available for you to enter -- the
    ones that correspond to the Applicant table.  Once you have entered the
    data on this screen, click on "Save Changes" (or hit Enter).

  - At the bottom of either page of the UG Application Form screen, click
    on Addresses.  For a new applicant,
    no addresses will be found.  Click on the "Add New Entry"
    button, which will create a new address record with the new
    applicant's Person ID number already filled in.  Set the address type to
    Correspondance or Permanent and then enter the appropriate address
    information.  I recommend filling in the 1st Line (e.g., Drury Lane,
    off Wilkinson), the city, state/province, and country, as these are
    particularly useful for future searches.  When you are done click
    on "Save Changes" (or hit Enter).

> If you decide to edit addresses, phone numbers, __etc.__ in separate tabs,
> then you will not be using the feature that automatically fills in the
> new applicant's Person ID number.  You can find the ID number at the top
> of the UG Application Form screen once you have saved your first page.

  - The easiest way to enter a second address for the same person is to
    click on "Clone this Entry".  Change the address type and any parts
    of the address that are different from the previous address.

>   If the person has the same Permanent and Correspondance Address,
>   you should still create two records.  In this case, the only
>   difference between them will be the Address Type.  Having both
>   records in the system will help with future searches and will
>   provide useful information if the applicant is accepted and becomes a
>   student.

  - You will find links for adding phone numbers at the bottom of either
    the Application Form screen or the individual Address screen.

  - To add information about an applicant's previous institutions and
    qualifications, go to the Institutions/Qual. item on the
    Admissions menu or activity page.  There are also links to this
    screen from the bottom of an individual's Application Form,
    Application Summary, and Applicant record screens.

When you enter or edit data, various fields may appear with different
colors:  bright yellow = required; light yellow = recommended; white =
optional; grey = discouraged.  See
the [Smart User Manual][suserman] for additional general information on 
adding and editing data.

If you do not have all the information you need for a given applicant,
you can either choose to enter that person later or enter the partial
information and then edit the record later.  If the missing information
is important, you will want to develop a good way of keeping track of
what still needs to be done (for example, you may choose to keep a pile
of forms that need to be entered or revised, with the missing
fields circled or highlighted with a big arrow).

Please note that the Smart Admissions tables include fields, such as
primary language, that the application form may not ask for.  You may wish
to revise the application to include some of them, if they seem
useful to your university, either for evaluating applications or for
analyzing applicant or student backgrounds and demographics for a school
or program.

<h3 id="edit">
Editing Applicant records
</h3>

You can edit Applicant records using the Undergraduate Application form, going
from screen to screen as you do when entering new applicants, or you
can edit the individual tables directly (Person, Applicant, Addresses,
etc.).  Screens for the individual tables appear in the Admissions menu or
activity page after the forms and procedures.  You can also enter or edit
limited applicant information such as the interview location using the
Application Summary screen, although that is meant to be used primarily
for reports.

<h3 id="searches">
Forms, Reports, and Searches
</h3>

Two sets of screens have been set up to match an existing form and
spreadsheet -- the Application Form and Application Summary (with Exam
Scores) screens.  The Application Form can be used for entering or
viewing information about applicants; the Application Summary, on the
other hand, is useful mostly for viewing basic information and exam
scores for individuals or groups of people, such as all applicants to a
particular academic program.
You
can also use the Application Form and any of the various
table-specific screens to see summary information for various groups and
to generate reports.

> More forms and reports may be added in the future.  Let your Smart
> administrator (ICT) know of particular requests so that they may
> investigate the feasibility and difficulty of meeting them.

To see information about a particular group, the first step is to
identify the right Search criteria.  Here are several examples:
  - If you want to see the applicants who have applied to the
    bachelor's program in the School of Forestry.  Go to the Search
    screen for the Applicant table or the Application or Summary
    forms, choose the appropriate program from the `Program Abbrev
    Title` drop-down menu, choose `Pending` for the `Application Status`
    field (near the bottom of the Application form), and click on the
    "Search on All Fields" button.
  - If you want to know how many of those applicants are men and how
    many are women, choose the program and status as above and set the
    sex to either `M` or
    `F`.  This will provide a list of the matching applicants, with a
    small message above it saying how many matches were found.
  - To see applicants whose status is `Waitlist` and who did __not__
    provide a secondary program, choose the appropriate status value and
    set the secondary program ID to `IS NULL`.
  - If you have already evaluated all applicants who applied before 15
    June and not want to evaluate applicants whose forms were received
    since then, set the `Appl Received` operation and field to `<` and
    the appropriate value for 15 June of the current year.
    (Alternatively, you might just choose to search for applicants whose
    status is still `Pending`.)
  - If you want to compare the number of applications received for a
    given program this year to the number received last year, do two
    searches, one for each year.  For the current year, set the program
    and status as before.  For the previous year, set the status
    operation and field to `!=` (not equal to) and `Pending` and set the
    `Appl Received` operation and field to `>` and a date earlier than
    the first applications were received, e.g., 15 February of that
    year.  (This particular solution assumes that all applications for
    the current year still have the `Pending` status.)

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
