<?php
/*
 * Title: Asterion Example
 * Description: This is an example of how to make a basic server using the AsterionServer class.
 * @author: Lewis-H
 */

// Require the preloader
require_once('preload.inc.php');

// Extend the AsterionServer class.
class ExampleServer extends AsterionBase {
	// Set the user-defined startup function of the server.
	function onStartup() {
		// An example of the server's output.
		$this->objLogger->writeOutput('This is an example of a startup function!', 'EXAMPLE');
	}

	// Set what to do with packets.
	function onRecieve($strPacket, $objUser) {
        $this->objLogger->writeOutput('A user sent a packet which reads: "' . $strPacket . '", sending back "Hello!".', 'INFO');
		$objUser->sendHello();
	}
}
// Extend the ClientBase class
class Client extends ClientBase {
	// A function for the client
	public function sendHello() {
		// Write "Hello!" to the client.
		$this->writeData('Hello!');
	}
}
?>
