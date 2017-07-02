# pageBuilderV2
My new take on a content management system.  Main page is: .../index.php

Currently requires a web server (tested w/ Apache2), PHP, and Postgres.  I decided to start this one with Postgres with a plan to add MySQL afterwards.

To get started, synch up the project and browse to: .../install/install.php
On this page, enter your postgres user name/pwd and what database you would like to use.
This will access a postgres database and create it as needed.  While I intend to support MySQL, at the moment, just postgres is supported.

All base tables will be added and the user name / password will be placed in a hidden file .htignore which should by default not be served up by Apache.  Other web servers may handle things differently.  Given this, the apache user will need write access to the configuration folder in order to create this file.

Unlike my older version of pageBuilder, this one has an xml file definition of tables which auto loads and creates the database tables as defined, there is also a dependency option so if a table requires another table it will wait until said table is created before creating that one.

Themes and extensions can use the same database creation mechanism which is demonstrated in the blog extension (work in progress).

The extension activation is done via the admin site reachable via .../admin/index.php

Right now I have a number of things I plan on adding however it is very much a one step at a time procedure.  If you are interested, feel free to pull and update as you like.

I do have some inconsistencies between the PHP side and the JS side, look at HtmlElements.php compared to elements.js.  In the end it would be nice to have a one to one correlation between the two.  Work continues in this area.

The final goal is a programatical system that a user can utilize the same methods regardless of PHP or JS to create the pages they require to display the content they want.

Please note - this is work in progress and can change as time goes.  I am currently focusing on the programming side and plan on cleaning up the look/feel once I get some more of the basics in place.  I also plan to add more themes however until I get the system itself solid, this will be a future task.

-Dan
