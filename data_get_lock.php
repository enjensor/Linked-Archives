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
//  16 January 2017
//	25 May 2017
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

	if(($_SESSION["administrator"] == "yes")) {
		if(($action != "") && ($items_dc_identifier != "")) {
			$queryD = "SELECT * FROM contributors WHERE credential_loginName = \"".$_SESSION["credential_loginName"]."\" ";
			$mysqli_resultD = mysqli_query($mysqli_link, $queryD);
			while($rowD = mysqli_fetch_row($mysqli_resultD)) { 
				$contributors_iana_UUID = $rowD[1];
				$contributors_dc_identifier = $rowD[2];
			}
			$queryD = "SELECT * FROM items WHERE dc_identifier = \"$items_dc_identifier\" ";
			$mysqli_resultD = mysqli_query($mysqli_link, $queryD);
			while($rowD = mysqli_fetch_row($mysqli_resultD)) { 
				$items_iana_UUID = $rowD[1];
			}
			if(($action == "lock")) {
				$iana_UUID = guidv4();
				$theTime = date("Y-m-d H:i:s",time());
				$characters = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
				$iana = "";
				for ($i = 0; $i < 12; $i++) {
					$iana .= $characters[mt_rand(0, 36)];
				}
				$queryD = "UPDATE items SET dct_accessRights = \"restricted\" WHERE dc_identifier = \"$items_dc_identifier\" ";
				$mysqli_resultD = mysqli_query($mysqli_link, $queryD);
				$queryD = "INSERT INTO rights VALUES ";
				$queryD .= "( ";
				$queryD .= "0, ";
				$queryD .= "\"$iana_UUID\", ";
				$queryD .= "\"".time()."_".$iana."\", ";
				$queryD .= "\"$items_iana_UUID\", ";
				$queryD .= "\"$items_dc_identifier\", ";
				$queryD .= "\"$items_dc_identifier\", ";
				$queryD .= "\"$contributors_iana_UUID\", ";
				$queryD .= "\"$contributors_dc_identifier\", ";
				$queryD .= "\"$contributors_dc_identifier\", ";
				$queryD .= "\"restricted\", ";
				$queryD .= "\"".$theTime."\" ";
				$queryD .= ");";
				$mysqli_resultD = mysqli_query($mysqli_link, $queryD);
			}
			if(($action == "unlock")) {
				$iana_UUID = guidv4();
				$theTime = date("Y-m-d H:i:s",time());
				$characters = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
				$iana = "";
				for ($i = 0; $i < 12; $i++) {
					$iana .= $characters[mt_rand(0, 36)];
				}
				$queryD = "UPDATE items SET dct_accessRights = \"available\" WHERE dc_identifier = \"$items_dc_identifier\" ";
				$mysqli_resultD = mysqli_query($mysqli_link, $queryD);
				$queryD = "INSERT INTO rights VALUES ";
				$queryD .= "( ";
				$queryD .= "0, ";
				$queryD .= "\"$iana_UUID\", ";
				$queryD .= "\"".time()."_".$iana."\", ";
				$queryD .= "\"$items_iana_UUID\", ";
				$queryD .= "\"$items_dc_identifier\", ";
				$queryD .= "\"$items_dc_identifier\", ";
				$queryD .= "\"$contributors_iana_UUID\", ";
				$queryD .= "\"$contributors_dc_identifier\", ";
				$queryD .= "\"$contributors_dc_identifier\", ";
				$queryD .= "\"available\", ";
				$queryD .= "\"".$theTime."\" ";
				$queryD .= ");";
				$mysqli_resultD = mysqli_query($mysqli_link, $queryD);
			}
		}
	}

/////////////////////////////////////////////////////////// Finish

	include("./ar.dbconnect.php");

?>