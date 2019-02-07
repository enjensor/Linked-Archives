<?php

/////////////////////////////////////////////////////////// Source
//
//
//	DialogFlow Webhook, Archiver Project
//	Library, Western Sydney University
//
//	FOR ALL ENQUIRIES ABOUT CODE
//
//	Who:	Dr Jason Ensor
//	Email: 	j.ensor@westernsydney.edu.au | jasondensor@gmail.com
//	Mobile: 0419 674 770
//
//  VERSION 0.1
//  03 October 2018
//
//
/////////////////////////////////////////////////////////// Prevent Direct Access

	if(!defined('MyConstInclude')) {
   		die('Direct access not permitted');
	}	

/////////////////////////////////////////////////////////// Sanitise functions

	function cleanInput($input) {
  		$search = array(
			'@<script[^>]*?>.*?</script>@si',
			'@<[\/\!]*?[^<>]*?>@si',
			'@<style[^>]*?>.*?</style>@siU',
			'@<![\s\S]*?--[ \t\n\r]*>@'
  		);
    	$output = preg_replace($search, '', $input);
    	return $output;
  	}
	
	function sanitize($input) {
		if (is_array($input)) {
			foreach($input as $var=>$val) {
				$output[$var] = sanitize($val);
			}
		}
		else {
			if (get_magic_quotes_gpc()) {
				$input = stripslashes($input);
			}
			$input  = cleanInput($input);
			$output = mysql_real_escape_string($input);
		}
		return $output;
	}	
	
/////////////////////////////////////////////////////////// Main DB configuration

	$serverName = $_SERVER["SERVER_NAME"];

    $localhost = "localhost";
    $username = "***";
    $password = "***";
    $database = "ar_metadata";

/////////////////////////////////////////////////////////// Detaint all vars

	$dbc = @mysqli_connect($localhost, $username, $password);

	foreach($_POST as $key => $value) {
		$newVal = trim($value);
    	$newVal = mysqli_real_escape_string($dbc,$newVal);
		$_POST[$key] = $newVal;
	}

	foreach($_GET as $key => $value) {
		$newVal = trim($value);
    	$newVal = mysqli_real_escape_string($dbc,$newVal);
		$_GET[$key] = $newVal;
	}

/////////////////////////////////////////////////////////// Close
	
?>
