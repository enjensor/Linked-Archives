<?php

/////////////////////////////////////////////////////////// Source
//
//
//	Angus & Robertson Metadata Editor
//	Digital Humanities Research Group
//  School of Humanities and Communication Arts
//  University of Western Sydney
//
//	Procedural Scripting: PHP | MySQL | JQuery
//
//	FOR ALL ENQUIRIES ABOUT CODE
//
//	Who:	Dr Jason Ensor
//	Email: 	j.ensor@westernsydney.edu.au | jasondensor@gmail.com
//	Mobile: 0419 674 770
//
//  WEB FRAMEWORK
//
//  Bootstrap Twitter | http://getbootstrap.com/
//  Font Awesome | http://fortawesome.github.io/Font-Awesome/
//  Google Fonts API | http://fonts.googleapis.com
//  Modernizr | http://modernizr.com/
//  JQuery | http://jquery.com/download/
//	JQuery UI | https://jqueryui.com/
//
//  VERSION 0.1
//  5 January 2017
//	3 April 2017
//	10 August 2018
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
	
/////////////////////////////////////////////////////////// Session Handler Coming Soon	
//	
//	$_SESSION["credential_loginName"] = "jensor";
//
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
//		$newVal = sanitize($newVal);
    	$newVal = mysqli_real_escape_string($dbc,$newVal);
		$_POST[$key] = $newVal;
	}

	foreach($_GET as $key => $value) {
		$newVal = trim($value);
//		$newVal = sanitize($newVal);
    	$newVal = mysqli_real_escape_string($dbc,$newVal);
		$_GET[$key] = $newVal;
	}

/////////////////////////////////////////////////////////// Close
	
?>
