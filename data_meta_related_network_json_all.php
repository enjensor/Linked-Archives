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
//  20 August 2018
//
//
/////////////////////////////////////////////////////////// Clean post and get	
	
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

/////////////////////////////////////////////////////////// Generate JSON data
	
	$m = 2;
	$nodes = array();
	$links = array();

/////////////////////////////////////////////////////////// Get mention's children and parents

	$myQueryCheck = "SELECT ";
	$myQueryCheck .= "* ";
	$myQueryCheck .= "FROM relatedconcepts ";
	$myQueryCheck .= "ORDER BY value_string ASC ";
	$mysqli_resultmqc = mysqli_query($mysqli_link, $myQueryCheck);
	while ($row = mysqli_fetch_row($mysqli_resultmqc)){
		$row[0] = trim($row[0]);
		$row[0] = preg_replace("/\"/i","'","$row[0]");
		if(($row[0] != "")) {		
			$nodes[] = "{ \"id\": \"$row[4]\", \"group\": 1, \"regUri\": \"$row[2]\", \"rdfsLabel\": \"$row[3]\" }";
			$nodes[] = "{ \"id\": \"$row[7]\", \"group\": 1, \"regUri\": \"$row[5]\", \"rdfsLabel\": \"$row[6]\" }";
			$links[] = "{ \"source\": \"$row[4]\", \"target\": \"$row[7]\", \"value\": 1 }";
		}
	}



/////////////////////////////////////////////////////////// Prepare nodes and links	

	$nodes = array_unique($nodes);
	$links = array_unique($links);
	$cData = count($nodes);
	$cConn = count($links);
	
/////////////////////////////////////////////////////////// Do viz

?>

{
  "nodes":[
<?php
	$i = 0;	
	foreach($nodes as $sd) {
		$i++;
		echo "\t".$sd;
		if(($i != $cData)) {
			echo ", \n";
		} else {
			echo " \n";	
		}
	}
?>
  ],
  "links":[
<?php
	$i = 0;	
	foreach($links as $conn) {
		$i++;
		echo "\t".$conn;
		if(($i != $cConn)) {
			echo ", \n";
		} else {
			echo " \n";	
		}
	}
?>
  ]
}
<?php

/////////////////////////////////////////////////////////// Finish	
	
	include("./ar.dbdisconnect.php");

/////////////////////////////////////////////////////////// Close	

?>