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
//  15 August 2018
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
	$annotations_value_string = $_GET["annotations_value_string"];
	$annotations_reg_uri = $_GET["annotations_reg_uri"];
	$annotations_rdfs_label = $_GET["annotations_rdfs_label"];
	$_GET = array();
	$_POST = array();

/////////////////////////////////////////////////////////// Generate JSON data
	
	$m = 2;
	$nodes = array();
	$links = array();
	$othernodes = array();
	$nodes[] = "{ \"id\": \"$annotations_value_string\", \"group\": 1, \"regUri\": \"$annotations_reg_uri\", \"rdfsLabel\": \"$annotations_rdfs_label\" }";

/////////////////////////////////////////////////////////// Get mention's children and parents

	$myQueryCheck = "SELECT ";
	$myQueryCheck .= "value_string, ";
	$myQueryCheck .= "reg_uri, ";
	$myQueryCheck .= "rdfs_label ";
	$myQueryCheck .= "FROM relatedconcepts ";
	$myQueryCheck .= "WHERE annotations_value_string = \"$annotations_value_string\" ";
	$myQueryCheck .= "ORDER BY value_string ASC ";
	$mysqli_resultmqc = mysqli_query($mysqli_link, $myQueryCheck);
	while ($row = mysqli_fetch_row($mysqli_resultmqc)){
		$row[0] = trim($row[0]);
		$row[0] = preg_replace("/\"/i","'","$row[0]");
		if(($row[0] != "")) {		
			$links[] = "{ \"source\": \"$annotations_value_string\", \"target\": \"$row[0]\", \"value\": 2 }";
			$nodes[] = "{ \"id\": \"$row[0]\", \"group\": 2, \"regUri\": \"$row[1]\", \"rdfsLabel\": \"$row[2]\" }";
			$othernodes[] = "$row[0]";
		}
	}

	$myQueryCheck = "SELECT ";
	$myQueryCheck .= "annotations_value_string, ";
	$myQueryCheck .= "annotations_reg_uri, ";
	$myQueryCheck .= "annotations_rdfs_label ";
	$myQueryCheck .= "FROM relatedconcepts ";
	$myQueryCheck .= "WHERE value_string = \"$annotations_value_string\" ";
	$myQueryCheck .= "ORDER BY annotations_value_string ASC ";
	$mysqli_resultmqc = mysqli_query($mysqli_link, $myQueryCheck);
	while ($row = mysqli_fetch_row($mysqli_resultmqc)){
		$row[0] = trim($row[0]);
		$row[0] = preg_replace("/\"/i","'","$row[0]");
		if(($row[0] != "")) {		
			$links[] = "{ \"source\": \"$row[0]\", \"target\": \"$annotations_value_string\", \"value\": 2 }";
			$nodes[] = "{ \"id\": \"$row[0]\", \"group\": 2, \"regUri\": \"$row[1]\", \"rdfsLabel\": \"$row[2]\" }";
			$othernodes[] = "$row[0]";
		}
	}

/////////////////////////////////////////////////////////// Find other links children and parents

	$cOther = count($othernodes);
	if(($cOther > 0)) {
		foreach($othernodes as $annotations) {
			$myQueryCheck = "SELECT ";
			$myQueryCheck .= "value_string, ";
			$myQueryCheck .= "reg_uri, ";
			$myQueryCheck .= "rdfs_label ";
			$myQueryCheck .= "FROM relatedconcepts ";
			$myQueryCheck .= "WHERE annotations_value_string = \"$annotations\" ";
			$myQueryCheck .= "AND annotations_value_string != \"$annotations_value_string\" ";
			$myQueryCheck .= "AND value_string != \"$annotations_value_string\" ";
			$myQueryCheck .= "ORDER BY value_string ASC ";
			$mysqli_resultmqc = mysqli_query($mysqli_link, $myQueryCheck);
			while ($row = mysqli_fetch_row($mysqli_resultmqc)){
				$row[0] = trim($row[0]);
				$row[0] = preg_replace("/\"/i","'","$row[0]");
				if(($row[0] != "")) {		
					$links[] = "{ \"source\": \"$annotations\", \"target\": \"$row[0]\", \"value\": 2 }";
					$nodes[] = "{ \"id\": \"$row[0]\", \"group\": 3, \"regUri\": \"$row[1]\", \"rdfsLabel\": \"$row[2]\" }";
					$othernodes[] = "$row[0]";
				}
			}
			
			$myQueryCheck = "SELECT ";
			$myQueryCheck .= "annotations_value_string, ";
			$myQueryCheck .= "annotations_reg_uri, ";
			$myQueryCheck .= "annotations_rdfs_label ";
			$myQueryCheck .= "FROM relatedconcepts ";
			$myQueryCheck .= "WHERE value_string = \"$annotations\" ";
			$myQueryCheck .= "AND annotations_value_string != \"$annotations_value_string\" ";
			$myQueryCheck .= "AND value_string != \"$annotations_value_string\" ";
			$myQueryCheck .= "ORDER BY annotations_value_string ASC ";
			$mysqli_resultmqc = mysqli_query($mysqli_link, $myQueryCheck);
			while ($row = mysqli_fetch_row($mysqli_resultmqc)){
				$row[0] = trim($row[0]);
				$row[0] = preg_replace("/\"/i","'","$row[0]");
				if(($row[0] != "")) {		
					$links[] = "{ \"source\": \"$row[0]\", \"target\": \"$annotations\", \"value\": 2 }";
					$nodes[] = "{ \"id\": \"$row[0]\", \"group\": 3, \"regUri\": \"$row[1]\", \"rdfsLabel\": \"$row[2]\" }";
					$othernodes[] = "$row[0]";
				}
			}
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