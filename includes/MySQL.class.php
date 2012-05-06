<?php
/*
 * Name: MySQL Class
 * Description: This is the MySQL class, designed to make MySQL operations easier.
 * @author: Lewis-H
 */
class MySQL {
    private $resConnector;

    public function connect($strHost, $strUsername, $strPassword) {
        $strUsingPassword = (isset($strPassword)) ? 'YES' : 'NO';
        if($this->resConnector = mysql_connect($strHost, $strUsername, $strPassword)) {
            $this->writeOutput('Logged in as ' . $strUsername . '@' . $strHost . ', using password: ' . $strUsingPassword);
             return true;
        }else{
            $this->writeOutput('Failed to log in as ' . $strUsername . '@' . $strHost . ', using password: ' . $strUsingPassword);
            return false;
        }
    }

    public function doQuery($strQuery, $blnReturn = false) {
        if($blnReturn) {
            $resQuery = mysql_query($strQuery, $this->resConnector);
            $strResult = mysql_result($strQuery, 0);
            mysql_free_result($resQuery);
            return $strResult;
        }else{
            mysql_query($strQuery, $this->resConnector);
        }
    }

    public function fetchArray($strQuery) {
        $resQuery = mysql_query($strQuery, $this->resConnector);
        $arrResult = mysql_fetch_array($resQuery);
        mysql_free_result($resQuery);
        return $arrResult;
    }

    public function selectDatabase($strDatabase) {
        $blnSelect = mysql_select_db($strDatabase, $this->resConnector);
        if($blnSelect) {
            $this->writeOutput('MySQL database ' . $strDatabase . ' selected.');
        }else{
            $this->writeOutput('Failed to select database ' . $strDatabase . '!');
        }
    }

    public function lastError() {
        return mysql_error($this->resConnector);
    }

    public function escapeString($strString) {
        return mysql_real_escape_string($strString);
    }

    public function disconnect() {
        $this->writeOutput('Disconnecting from MySQL.');
        mysql_close($this->resConnector);
    }

	public function writeOutput($strOutput) {
		echo '[', date('H\:i\:s'), ']', '[MYSQL] : ', $strOutput, "\n";
	}
}
?>
