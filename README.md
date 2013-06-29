project_shoujo
==============

Project Shoujo aims to be a web-based customizable dating simulator. It has an easy-to-use editor where players can create their characters and add content, while other players can play with them. I stopped developing this because I couldn't find artists to make a first beta, and I have been busy with other things.

Installation
------------
Change the config file located in config/constants.php, you have to add your database information and your hostname. Also use the database_dump.sql for the tables.

Issues
------
THIS IS NOT EVEN IN ALPHA STATE. Everything works but some things have to be added to be fully playable. The most important one is adding some session-related methods to save progress in the databse, everyhing is done using AJAX and the file that parses AJAX-related information is in ajax_server/state.php, check that out.

If you have problems or need something contact me by email, I answer them quickly.

Demo
----

![alt tag](https://raw.github.com/pacha64/project_shoujo/master/stallman-example.jpg)

![alt tag](https://raw.github.com/pacha64/project_shoujo/master/demo-example.jpg)