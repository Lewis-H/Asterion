<?php
/*
 * Name: Logger Class
 * Description: This is the logger class which outputs server information and, optionally, saves it to a file.
 * @author: Lewis-H
 */
class Logger {
    private $strLogLevel = 'ALL';
    private $blnDisplayTime;
    private $resHandle;
    private $arrLogLevels = array(
        'ALL', 'ERROR', 'INFO', 'NONE', 'WARN',
        'ERROR' => array('ERROR'),
        'INFO' => array('INFO', 'ERROR'),
        'WARN' => array('ERROR', 'WARN')
    );

    function __construct($strFile, $blnDisplayTime = true) {
        if($strFile != '') {
            $this->resHandle = fopen($strFile, 'w');
        }
        $this->blnDisplayTime = $blnDisplayTime;
        $this->setLogLevel('NONE');
        $this->writeOutput('Initiating logger.', 'INFO');
    }

    public function writeOutput($strOutput, $strType) {
        $strTime = ($this->blnDisplayTime) ? '[' . date('H\:i\:s') . ']' : '';
        $strLog = $strTime . '[' . $strType . '] >> ' . $strOutput . "\n";
        echo $strLog;
        if($this->shouldLog($strType)) {
            fwrite($this->resHandle, $strLog);
        }
    }

    public function setLogLevel($strLevel) {
        $strLevel = strtoupper($strLevel);
        if(in_array($strLevel, $this->arrLogLevels)) {
            $this->strLogLevel = $strLevel;
        }else{
            return false;
        }
    }

    private function shouldLog($strType) {
        if($this->strLogLevel == 'ALL') {
            return true;
        }else if($this->strLogLevel == 'NONE') {
            return false;
        }else if(in_array($strType, $this->arrLogLevels[$this->strLogLevel])) {
            return true;
        }
        return false;
    }
}

?>
