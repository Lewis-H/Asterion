<?php
/*
 * Name: Preloader
 * Description: Preloader, brings all files together.
 * @author: Lewis-H
 */
set_time_limit(0);
ob_implicit_flush();
error_reporting(E_ALL | E_STRICT);

function __autoload($strClass) {
    require_once($strClass . '.class.php');
}

?>
