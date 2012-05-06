<?php
/*
 * Name: Asterion Server Class
 * Description: This is an example of a server running.
 * @author: Lewis-H
 */
// Require the Extended Classes.
require_once('includes/Example.inc.php');
// Create the server object, "EXAMPLE" states the server name, look in ini/serv.conf.ini to configure servers.
$objServer = new ExampleServer('EXAMPLE');
// Run the server.
$objServer->runServer();
?>
