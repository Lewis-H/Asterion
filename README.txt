             _______         __               __              
            |   _   |.-----.|  |_.-----.----.|__|.-----.-----.
            |       ||__ --||   _|  -__|   _||  ||  _  |     |
            |___|___||_____||____|_____|__|  |__||_____|__|__|
            __________________________________________________
           |__________________________________________________|

Asterion Server is a PHP socket server base (or "skeleton") written by Lewis-H.

To use this, you must extend the class "AsterionServer" and the class "ClientBase".

For examples on basic usage, please look in includes/Example.inc.php, Example.php and ExampleClient.php.

Asterion is free to use, but must not be redistributed as your own, read gpl3.0.txt. :)

Version Log:
0.1a
----
 - Server Base working.

0.1b
----
 - Added "getClientByAttribute" and "removeClientByAttribute" methods to AsterionServer.
 - Removed "getClientBySock" and "removeClientBySock" methods from AsterionServer.
 - Added preloader.php to include or require all needed files.
 - Wrote Logger class.
 - Added "logs" folder to hold logs files.
 - Added log levels to define what to be logged and what not to be logged.
