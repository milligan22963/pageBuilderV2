# pageBuilderV2
My new take on a content management system.

To get started, synch up the project and browse to: .../install/install.php
On this page, enter your postgres user name/pwd and what database you would like to use.
This will access a postgres database and create it as needed.  All base tables will be added.
Unlike my older version of pageBuilder, this one has an xml file definition of tables which auto loads and creates the database tables as defined, there is also a dependency option so if a table requires another table it will wait until said table is created before creating that one.  Extensions can use the same database creation mechanism which is demonstarted in the blog extensions (work in progress).
Right now I have a number of things I plan on adding however it is very much a one step at a time procedure.  If you are interested, feel free to pull and update as you like.
I do have some inconsistencies between the PHP side and the JS side, look at HtmlElements.php compared to elements.js.  In the end it would be nice to have a one to one correlation between the two.  Work continues in this area.
The final goal is a programatical system that a user can utilize the same methods regardless of PHP or JS to create the pages they require to display the content they want.
