# RAMP: Record and Activity Management Program #

Welcome to the RAMP Project.

#### RAMP ####

Ramp is a program that supports domain expert access to a relational
database.  Whereas a program like phpMyAdmin is aimed at database
administrators who need to update and maintain a database, Ramp is aimed
at the store owner, librarian, registrar, or other professional who
needs to populate, view, and update data in a structured set of tables.

Ramp's software treats the tables in a database generically, but
provides reasonably light-weight methods for customizing the user's
interaction with those tables to give the appearance of an
application-specific program.  The two primary mechanisms for this
customization are activity lists, which generate formatted pages
of grouped activities with descriptions, and table settings, which
provide customized views of tables.  Activity files and table
settings are created as `ini` configuration files, making it
relatively easy to create a Ramp-based application to interact with
any new set of tables.


### RELEASE INFORMATION ###

RAMP/SMART Release 0.9.2.
Released on October 12, 2014.

### SYSTEM REQUIREMENTS ###

Ramp is set up to work with tables defined in a MySQL database,
using the Zend Framework.

#### AMP and ZF1 ####
RAMP (Record and Activity Management Program) was developed using
[Apache HTTP Server 2] [apache], [MySQL Community Server 5] [mysql] (5.5),
[PHP 5] [php] (5.3) and [Zend Framework 1] [zf1] (1.11.11).  It has
also been tested with later versions up to MySQL 5.6.13, PHP 5.4.19,
and Zend Framework 1.12.  It does not work yet with [Zend 2] [zf2],
which is a completely redesigned version of the Zend Framework.

#### Markdown, Twitter Bootstrap, and twitter-bootstrap-zf1 ####
Ramp also uses Michel Fortin's [PHP Markdown Lib 1.3] [md], which
depends on PHP 5.3, for converting Markdown text to HTML, and
[`twitter-bootstrap-zf1`] [tbz], a library to work with [Twitter
Bootstrap 2] [tb] and Zend Framework 1.  Ramp uses several of the Glyph
icons provided in Twitter Bootstrap by [Glyphicons][glyphicons].

Ramp also includes some dependencies on HTML 5.  Thus the full list of
dependencies is:

>   Apache HTTP Server 2  
>   MySQL Community Server, version 5.3 or later  
>   PHP, version 5.3 or later  
>   Zend Framework 1, version 1.11.10 or later (but not Zend Framework 2)  
>   PHP Markdown Lib 1.3  
>   twitter-bootstrap-zf  
>   Twitter Bootstrap 2 (but not Twitter Bootstrap 3)  
>   Browsers that support HTML 5  

The current version of Ramp includes bundled versions of the
Zend Framework 1, the Markdown library, the `twitter-bootstrap-zf1`
library, and a subset of Twitter Bootstrap 2.  (See the [License section]
[license-section] for information about the licenses for those
components.)
See [INSTALL.md] [install] for more details about the software
infrastructure on which Ramp depends, including specific Apache modules.

### INSTALLATION ###

[TODO: Need to provide at least an overview of what will be found (and
what to look for) in INSTALL.md before directing people off to that
file.]
Please see [INSTALL.md] [install].  (Under construction...)

<h3 id="LICENSE"> LICENSE INFORMATION </h3>

The source files for Ramp are released under a BSD 2-Clause license.
You can find a copy of this license in [LICENSE.md] [license].

[TODO: Need to choose the correct CC license for documentation.  Do
activity files and table settings fit under software or documentation
for licensing purposes?]

The following software may be included with this project:

Zend Framework 1 (version 1.11.11):  The Zend Framework license is
included as [LICENSE-ZF1.txt] [zf-license].

PHP Markdown Lib 1.3:  The Markdown Library license is
included as [License-php-markdown-lib.md] [md-license].

`twitter-bootstrap-zf1` and Twitter Bootstrap:  The
Twitter-Bootstrap-ZF1 license is
included as [README-twitter-bootstrap-zf1.md] [tbz-license]; the Twitter
Bootstrap license as [LICENSE-bootstrap-2.3.2] [tb-license].

### ACKNOWLEDGEMENTS ###

The Ramp team would like to thank all the contributors to the
Ramp project and the institutional supporters who have provided
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

[license-section]: #LICENSE
[install]: ..%252F..%252FINSTALL.md
[license]:  ..%252F..%252FLICENSE.md
[apache]:  http://httpd.apache.org/
[mysql]:  http://dev.mysql.com/downloads/
[php]: http://php.net/
[zf1]: http://www.zend.com/community/downloads
[zf-license]: ..%252F..%252FLICENSE-ZF1.txt
[zf-license-online]: http://framework.zend.com/license/new-bsd
[zf2]: http://framework.zend.com/
[md]:  http://michelf.ca/projects/php-markdown/
[md-license]: ..%252F..%252FLicense-php-markdown-lib.md
[tb]: http://getbootstrap.com/2.3.2/index.html
[tb-license]: ..%252F..%252FLICENSE-bootstrap-2.3.2
[tbz]: https://github.com/andreaswarnaar/twitter-bootstrap-zf1
[tbz-license]: ..%252F..%252FREADME-twitter-bootstrap-zf1.md
[glyphicons]: http://glyphicons.com/

