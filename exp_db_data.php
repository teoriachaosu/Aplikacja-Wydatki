<?php

define("DSN", "mysql:host=localhost;dbname=expenses");
define("USERNAME", "root");
define("PASSWORD", "");

$options = array(PDO::ATTR_PERSISTENT => true);

try{
    $con = new PDO(DSN, USERNAME, PASSWORD, $options);

    $con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    //echo "connection successful";
}catch (PDOException $ex){
    echo "A database error occurred ".$ex->getMessage();
}

$con->exec('SET NAMES utf8');

function keepData($val, $clr_form) { // prevent clearing field on form submit
	if($val && isset($_POST[$clr_form])) 
		echo htmlentities($val);
	}