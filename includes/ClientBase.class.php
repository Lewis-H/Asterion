<?php
/*
 * Title: ClientBase
 * Description: This is the base of your client class. This class must be extended.
 * @author: Lewis-H
 */
class ClientBase {
    public $strRndK;
    public $resSock;
    public $objLogger;

    public function __construct($resSock, $objLogger) {
        $this->resSock = $resSock;
        $this->strRndK = $this->makeRndK();
        $this->onJoin($resSock);
        $this->objLogger = $objLogger;
    }

    private function makeRndK() {
        $intLength = mt_rand(25, 50);
        $strRand = '';
        for($intLoops = 0; $intLoops <= $intLength; $intLoops++) {
            $strRand .= chr(mt_rand(32, 126));
        }
        return $strRand;
    }

    public function writeData($strData) {
        socket_write($this->resSock, $strData);
    }

    public function onJoin($resSock) {
        // Over-ridden in child
    }
}
?>
