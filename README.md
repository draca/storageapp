Angular-todo app
============

Description
----------------------------------
App for keeping track of your storage area


Nodes
----------------------------------
runs on an apache server with mysql. Wamp fits the purpose, for now.


How to run
----------------------------------
- Clone this repository to a folder in your desktop
- Set up wamp ( http://www.wampserver.com/en/#download-wrapper )
- Make sure Skype and similar applications stay away from port 80
- Once the server can start and run and be put online and all is green, edit the httpd.conf under Apache:
- - Adjust the path to DocumentRoot to point at the root of the application ( angular-todo/app )
- - Adjust the Directory below it to reflect the same path
- - Make sure that the line LoadModule rewrite_module modules/mod_rewrite.so is not commented out
- Go to localhost/phpmyadmin and create a new database named "todoapp"
- Inside that database, go "Import", browse to the root of this repository on your computer and select the .sql file
- Voila! Good to go! =)
