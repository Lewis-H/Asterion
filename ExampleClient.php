<?php
/*
 * Name: Example Client
 * Description: This is an example client.
 * @author: Lewis-H
 */

// Create the socket
$resSock = socket_create(AF_INET, SOCK_STREAM, 0);
// Connect to our example server
socket_connect($resSock, 'localhost', 8000);
// Send "Thanks for using Asterion!" to the server
socket_write($resSock, "Thanks for using Asterion! :)\0");
// Wait for a reply
$strReturn = '';
while($strReturn == '') {
	$strReturn = socket_read($resSock, 1024);
}
// Echo the reply.
echo $strReturn, "\n";
?>
