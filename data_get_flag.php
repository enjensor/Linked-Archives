<?php

/////////////////////////////////////////////////////////// Credits
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
//  6 January 2017
//  8-13 January 2017	
//
//
/////////////////////////////////////////////////////////// Collect session data

	$MerdUser = session_id();
	if(empty($MerdUser)) { session_start(); }
	$SIDmerd = session_id(); 
	
/////////////////////////////////////////////////////////// Clean post and get	
	
	define('MyConstInclude', TRUE);
	header("Content-type: text/html;charset=UTF-8");
	mb_internal_encoding("UTF-8");
	include("./ar.config.php");
	include("./ar.dbconnect.php");
	include("./index_functions.php");
	if (!mysqli_set_charset($mysqli_link, "utf8")) {
		echo "PROBLEM WITH CHARSET!";
		die;
	}
	$action = $_GET["action"];
	$items_dc_identifier = $_GET["items_dc_identifier"];
	$_GET = array();
	$_POST = array();

/////////////////////////////////////////////////////////// Data routine

	if(($action != "") && ($items_dc_identifier != "")) {
		if(($action == "add")) {
			$theTime = date("Y-m-d H:i:s",time());
			$queryD = "INSERT INTO flags VALUES(\"0\", \"$items_dc_identifier\", \"$theTime\", \"INVALID\", \"".$_SESSION["credential_loginName"]."\"); ";
			$mysqli_resultD = mysqli_query($mysqli_link, $queryD);
		}
		if(($action == "delete")) {
			$queryD = "DELETE FROM flags WHERE dc_references = \"$items_dc_identifier\" ";
			$mysqli_resultD = mysqli_query($mysqli_link, $queryD);
		}
	}

/////////////////////////////////////////////////////////// Finish

	include("./ar.dbconnect.php");

?>