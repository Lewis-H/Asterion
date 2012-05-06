<?php
/*
 * Name: Asterion Server Class
 * Description: This is the parent class of your server, which must be extended.
 * @author: Lewis-H
 */
class AsterionBase {
    private $resResult;
    private $resSock;
    private $resLastSock;
    private $strClientClass;
    private $strVersion = "0.1b";
    private $strEndChar = "\0";
    public $objLogger;
    public $intUsers = 0;
    public $arrClients = array();

    public function writeOutput($strOutput, $strType) {
        echo '[', date('H\:i\:s'), ']', '[', $strType, '] >> ', $strOutput, "\n";
    }

    function __construct($strServ) {
        echo "       ==========================================\n";
        echo "       |              =/ASTERION\\=              |\n";
        echo "       |                                        |\n";
        echo "       |========================================|\n";
        echo "       |     A multi-client TCP server base.    |\n";
        echo "       |                                        |\n";
        echo '       |              Version ', $this->strVersion ,"              |\n";
        echo "       |                                        |\n";
        echo "       |========================================|\n";
        echo '       |           (c) ', date('Y'), " - Static            |\n";
        echo "       |                                        |\n";
        echo "       ==========================================\n";
        $arrConf = parse_ini_file('ini/serv.conf.ini', true);
        $this->initServer($arrConf[$strServ]['IP'], $arrConf[$strServ]['PORT'], $strServ, $arrConf[$strServ]['LOG_LEVEL']);
        $this->strClientClass = $arrConf[$strServ]['CLIENT_CLASS'];
        define('READ_LEN', $arrConf[$strServ]['READ_LEN']);
        unset($arrIniConf);
        $this->onStartup();
    }

    private function initServer($strIp, $intPort, $strServ, $strLogLevel) {
        if(file_exists('logs/' . $strServ . '.log')) {
            $intSuffix = 1;
            while(file_exists('logs/' . $strServ . '.log.' . $intSuffix)) {
                $intSuffix++;
            }
            $strLogFile = 'logs/' . $strServ . '.log.' . $intSuffix;
        }else{
            $strLogFile = 'logs/' . $strServ . '.log';
        }
        $this->objLogger = new Logger($strLogFile);
        $this->objLogger->setLogLevel($strLogLevel);
        $this->objLogger->writeOutput('The server ' . $strServ . ' will be started on ' . $strIp . ':' . $intPort . ' now.', 'INFO');
        $this->resSock = socket_create(AF_INET, SOCK_STREAM, 0)  or $this->fatalError('Server could not create the socket!');
        socket_set_option($this->resSock, SOL_SOCKET, SO_REUSEADDR, 1);
        socket_set_nonblock($this->resSock);
        $this->objLogger->writeOutput('The server\'s socket has been created.', 'INFO');
        $this->resResult = socket_bind($this->resSock, $strIp, $intPort) or $this->fatalError('Server socket could not bind to port: '. $intPort . '.');
        $this->objLogger->writeOutput('The server\'s socket has binded to ' . $strIp . ':' . $intPort, 'INFO');
        $this->resResult = socket_listen($this->resSock, 3);
        $this->objLogger->writeOutput('The server\'s socket is now listening for incoming connections.', 'INFO');
        unset($strIp);
        unset($intPort);
    }

    public function fatalError($strError) {
        echo '[', date('H\:i\:s'), ']','[WARN] >> ', $strError, "\n";
        die();
    }

    // User Defined Functions
    public function onRecieve($strPacket, $objUser) {
        // Over-ridden in child
    }

    public function onStartup() {
        // Over-ridden in child
    }

    public function onRemove($objUser) {
        // Over-ridden in child
    }

    public function onShutdown() {
        // Over-ridden in child
    }

    public function setEndChar($strChar) {
        $this->strEndChar = $strChar;
    }

    public function runServer() {
        while(true){
            $this->runFunction();
        }
    }

    private function runFunction() {
        $arrSocks = $this->getSockets();
        $arrWrite = NULL;
        $arrExcept = NULL;
        $intWSec = 120;
        socket_select($arrSocks, $arrWrite, $arrExcept, $intWSec);
        foreach($arrSocks as $resSock) {
            if($resSock == $this->resSock) {
                $this->addClient($resSock);
                break;
            }
            @socket_recv($resSock, $strRead, READ_LEN, 0);
            if($strRead == NULL) {
                $this->removeClientByAttribute('resSock', $resSock);
                break;
            }
            if($strRead != '') {
                $arrRead = explode($this->strEndChar, $strRead);
                if($arrRead === false) break;
                array_pop($arrRead);
                foreach($arrRead as $strPack) {
                    $this->handlePacket($resSock, $strPack);
                }
            }
        }
        unset($arrSocks);
    }

    private function getSockets() {
        foreach($this->arrClients as $intIndex=>$objUser) {
            if(is_resource($objUser->resSock)) {
                $arrSocks[] = $objUser->resSock;
            }else{
                unset($this->arrClients[$intIndex]);
                $this->objLogger->writeOutput('Removed client ' . $intIndex . ' [Socket not resource].', 'INFO');
            }
        }
        $arrSocks[] = $this->resSock;
        return $arrSocks;
    }

    public function removeClientByAttribute($strAttribute, $mixValue) {
        foreach($this->arrClients as $intIndex=>&$objUser) {
            if($objUser->$strAttribute == $mixValue) {
                $this->objLogger->writeOutput('Removing client: ' . $intIndex . '.', 'INFO');
                $this->onRemove($this->arrClients[$intIndex]);
                unset($this->arrClients[$intIndex]);
            }
        }
    }

    private function addClient($resSock) {
        if($this->resLastSock != $resSock) {
            $resSock = socket_accept($resSock);
            socket_set_nonblock($resSock);
            $this->arrClients[] = new $this->strClientClass($resSock, $this->objLogger);
            $this->intUsers++;
            $this->resLastSock = $resSock;
        }
    }

    public function clientNumBySock($resSock) {
        foreach($this->arrClients as $intIndex=>&$objUser) {
            if($objUser->resSock == $resSock) {
                return $intIndex;
            }
        }
    }
    
    private function handlePacket($resSock, $strData) {
        $objUser = $this->getClientByAttribute('resSock', $resSock);
        $this->onRecieve($strData, $objUser);
    }

    public function getClientByAttribute($strAttribute, $mixValue) {
        foreach($this->arrClients as $objUser) {
            if($objUser->$strAttribute == $mixValue) {
                return $objUser;
            }
        }
        return false;
    }

    function __destruct() {
        socket_close($this->resSock);
        $this->objLogger->writeOutput('Server shutting down.', 'WARN');
        $this->onShutdown();
    }
}
?>
