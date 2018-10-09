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
//  14 March 2017
//	22 June 2017
//
//
/////////////////////////////////////////////////////////// Clean post and get	
	
	if(($reload == "")) {
		define('MyConstInclude', TRUE);
		$MerdUser = session_id();
		if(empty($MerdUser)) { session_start(); }
		$SIDmerd = session_id();
		header("Content-type: text/html;charset=UTF-8");
		mb_internal_encoding("UTF-8");
		include("./ar.config.php");
		include("./ar.dbconnect.php");
		include("./index_functions.php");
		if (!mysqli_set_charset($mysqli_link, "utf8")) {
			echo "PROBLEM WITH CHARSET!";
			die;
		}
		$_GET = array();
		$_POST = array();
	}
	
/////////////////////////////////////////////////////////// Locked Items

	$queryD = "SELECT COUNT(*) FROM items ";
	$mysqli_resultD = mysqli_query($mysqli_link, $queryD);
	while($rowD = mysqli_fetch_row($mysqli_resultD)) {
		$fullTotal = $rowD[0];
	}
	$queryD = "SELECT COUNT(dct_accessRights) ";
	$queryD .= "FROM rights ";
	$queryD .= "WHERE dct_accessRights = \"restricted\" ";
	$mysqli_resultD = mysqli_query($mysqli_link, $queryD);
	while($rowD = mysqli_fetch_row($mysqli_resultD)) {
		$keywordTotal = $rowD[0];
	}
	$progress = round(($keywordTotal / $fullTotal) * 100);
	echo "<div class=\"pad-ver\">";
	echo "<p class=\"mar-no\"><span class=\"pull-right text-bold\">".number_format($keywordTotal,0,".",",")."</span>Locked Items</p>";
	echo "<p class=\"mar-no\"><span class=\"pull-right text-bold\">".number_format($fullTotal,0,".",",")."</span>Total Items</p>";
	echo "</div>";
	echo "<div class=\"progress progress-xl\">";
	echo "<div style=\"width: ".$progress."%;\" class=\"progress-bar progress-bar-dark\">".$progress."%</div>";
	echo "</div>";

/////////////////////////////////////////////////////////// Mentions
	
	$queryD = "SELECT COUNT(*) FROM annotations ";
	$mysqli_resultD = mysqli_query($mysqli_link, $queryD);
	while($rowD = mysqli_fetch_row($mysqli_resultD)) {
		$fullTotal = $rowD[0];
	}
	$queryD = "SELECT COUNT(DISTINCT(value_string)) FROM annotations";
	$mysqli_resultD = mysqli_query($mysqli_link, $queryD);
	while($rowD = mysqli_fetch_row($mysqli_resultD)) {
		$keywordTotal = $rowD[0];
	}
	$queryD = "SELECT COUNT(DISTINCT(annotations.value_string)) ";
	$queryD .= "FROM annotations, rights ";
	$queryD .= "WHERE annotations.dc_references = rights.dc_references ";
	$queryD .= "AND rights.dct_accessRights = \"restricted\"";
	$mysqli_resultD = mysqli_query($mysqli_link, $queryD);
	while($rowD = mysqli_fetch_row($mysqli_resultD)) {
		$otherTotal = $rowD[0];
	}
	$progress = round(($otherTotal / $keywordTotal) * 100);
	echo "<div class=\"pad-ver\">";
	echo "<p class=\"mar-no\"><span class=\"pull-right text-bold\">".number_format($fullTotal,0,".",",")."</span>Mentions</p>";
	echo "<p class=\"mar-no\"><span class=\"pull-right text-bold\">".number_format($keywordTotal,0,".",",")."</span>Unique Mentions</p>";
	echo "<p class=\"mar-no\"><span class=\"pull-right text-bold\">".number_format($otherTotal,0,".",",")."</span>Locked Unique Mentions</p>";
	echo "</div>";
	echo "<div class=\"progress progress-xl\">";
	echo "<div style=\"width: ".$progress."%;\" class=\"progress-bar progress-bar-dark\">".$progress."%</div>";
	echo "</div>";
	
/////////////////////////////////////////////////////////// Finish

	if(($reload == "")) {
		include("./ar.dbdisconnect.php");
	}

?>