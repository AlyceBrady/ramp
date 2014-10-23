# Creating Activity Files #

[ [Introduction](#intro) |
  [Activity Specification Lists](#lists) |
  [Activity Specifications](#specs) |
  [Examples](#examples) ]

<div id="intro"></div>

Ramp supports the creation of "activity pages" as a way to group
various tables or activities together,  providing a "home page" for
that set of related activities.

Activity pages are generated from activity files, each of which
may contain a single activity specification, a list
of activity specifications, or even multiple activity specification
lists.  A single list, corresponding to the contents of a single
activity page, is the most common form of activity
file.  Such a file may also contain an optional heading, such as

        activityListHeading = "Choose a table:"

When no heading is provided, the heading will default to `"Choose an
activity:"`.


<h3 id="lists"> Activity Specification Lists </h3>
An activity specification list defines a list of activities, in
the order in which they should appear, and specifications for those
activities.  The various types of supported activities are
listed below in the [Activity Specifications](#specs) section.

Activities may be added to an activity list by providing the full
specification "in place", or by merely providing a reference to a
section elsewhere in the file that has the full activity specification
(one activity specification per section).  In either case, each
activity is given a name that is unique within the activity list.

For example, a `comment` activity with the unique name `comment1`
could be added to the list using either of the two styles below:

> #### Style 1: ####
>       activity.comment1.type = "comment"
>       activity.comment1.comment = "This is a comment in the activity list"
>       activity.nextActivity.type = ...

> #### Style 2: ####
>       activity.comment1 = "comment1Spec"
>       activity.nextActivity = "nextActivitySpec"
>       ...

>       [ comment1Spec ]
>       type = "comment"
>       comment = "This is a comment in the activity list"

>       [ nextActivitySpec ]
>       type = ...

The ordering of the specification sections in Style 2 does not matter,
so activities can be re-ordered in the list merely by re-ordering the
initial list of section references.

Styles 1 and 2  may be mixed in the same file.  There is a variation
on Style 2 (which we could call Style 3) that does not require
providing a unique name for each activity:

> #### Style 3: ####
>       activity[] = "comment1spec"
>       activity[] = "nextActivitySpec"
>       ...

This style can only be used when deferring the activity specifications
to later sections of the file.  Warning: Do not mix named and unnamed
activities in the same file as doing so will result in a strangely
ordered list (all the unnamed activities will appear before the
named activities).  If you want to mix deferred and not-deferred
specifications in the same file, always use Styles 1 and 2.

See the [Examples](#examples) section below for examples of full, "in place"
activities (Style 1), deferred specifications (Style 2), and unnamed
specifications (Style 3).

Note: If an activity file contains multiple activity lists, one must
be at the top level (i.e., not in a section), or there must be a
section whose name matches the name of the activity file (including
the path from the application's activities directory, as specified in
the initial configuration file).

<h3 id="specs"> Activity Specifications </h3>
Activity specifications, whether they occur "in place" as the activities
are added to an activity list or in later sections, must always include
a type property.  The valid types are:

  * separator
  * comment
  * activitySpec ("activityList" is a synonym)
  * setting ("sequence" is a synonym)
  * report
  * document
  * url
  * controllerAction

In addition to the type property, the properties expected by each
type are:

> #### Separator type specifications require no other properties. ####
>>  [Visually, this type produces a horizontal line.]

> #### Comment type specifications include: ####

>   * comment: the comment

> #### Activity Spec (or Activity List) type specifications include: ####

>   * title: a short title (must fit on a button)
>   * description: a description (should not go beyond a line or two)
>   * source: the source (section name or file name)

> #### Setting/sequence or Report type specifications include: ####

>   * title: a short title (must fit on a button)
>   * description: a description (should not go beyond a line or two)
>   * source: the source (setting file name, without suffix)

>>  [A report is a table view with customized formatting.]

> #### Document type specifications include: ####
>   * title: a short title (must fit on a button)
>   * description: a description (should not go beyond a line or two)
>   * source: the source (a text, HTML, or Markdown document)

> #### URL type specifications include: ####
>   * title: a short title (must fit on a button)
>   * description: a description (should not go beyond a line or two)
>   * url:
     [The url must be complete, including any necessary parameters.]

> #### Controller/Action type specifications include: ####
>   * title: a short title (must fit on a button)
>   * description: a description (should not go beyond a line or two)
>   * controller:
>   * action:
>   * parameter:   [URL-style key1=param1&key2=param2 format]  

>>  [This type requires knowledge of the application code.]

Finally, activities may also have an optional `inactive` property.  For
most activity types, if the `inactive` property is provided and set to
`true`, the corresponding button is disabled and the style class of
its description set to "disabled".  The actual look
of the button, title, and description may change, depending on the
css styles set up for the application; for example, the button and
description may be greyed out.  The `inactive` property has no effect
on separator activity types; for comments, it merely changes the
style class of the comment from "comment" to
"disabledComment", again allowing for a different presentation.

<h3 id="examples"> Examples </h3>

##### Full Specification Example: #####
Adds activity `cityPopulations` to activity
list, providing the activity specification "in place".

        activity.cityPopulations.type = "setting"
        activity.cityPopulations.source = "cityPopTable"
        activity.cityPopulations.title = "City Populations"
        activity.cityPopulations.description = "A sequence/setting file for a city population table"
        activity.nextActivity.type = ...

##### Deferred Specification Example: #####
Adds activity `shortComment` to
activity list, deferring the specification to a section later in the
file called `shortComment`.  (The unique activity name and the
section name do not have to be the same.)

        activity.shortComment = "short comment"

        ...

        [short comment]

        type = "comment"
        comment = "Example of a short comment"

##### Unnamed Activity Example: #####
Adds a comment, a separator, another comment, two
additional settings, and a final separator to the activity list,
deferring the specifications to sections later in the file.  The order
of the activities in the list is determined by the assignments to the
array of unnamed activities, not by the order of the specification
sections.  The `horizRule` section name appears twice in the list, although
the section is defined only once.

        activity[] = "short comment"
        activity[] = "horizRule"
        activity[] = "long comment"
        activity[] = "viewTableA"
        activity[] = "viewTableB"
        activity[] = "horizRule"

        ...

        [short comment]

        type = "comment"
        comment = "Example of a short comment"

        [long comment]

        type = "comment"
        comment = "Example of a much longer comment than the short comment
        above; long enough, in fact, to span lines.  The open quotation mark, by
        the way, must be on the same line as the property and the equals sign."

        [viewTableA]

        type = "setting"
        source = "tableA"
        title = "View Table A"
        description = "This table includes lots of very interesting information"

        [viewTableB]

        type = "setting"
        source = "tableB"
        title = "View Table B"
        description = "This table includes even more very interesting information"

        [horizRule]

        type = "separator"

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

