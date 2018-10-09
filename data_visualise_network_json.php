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
//  26 April 2017
//
//
/////////////////////////////////////////////////////////// Clean post and get

	define('MyConstInclude', TRUE);
	$doCalc = "y";

/////////////////////////////////////////////////////////// Collect session data

	$MerdUser = session_id();
	if(empty($MerdUser)) { session_start(); }
	$SIDmerd = session_id(); 
	
/////////////////////////////////////////////////////////// Clean post and get	
	
	include("./ar.config.php");
	include("./ar.dbconnect.php");
	include("./index_functions.php");
	header("Cache-Control: no-cache");
	header("Pragma: no-cache");
	mb_internal_encoding("UTF-8");
	if (!mysqli_set_charset($mysqli_link, "utf8")) {
		echo "PROBLEM WITH CHARSET!";
		die;
	}

/////////////////////////////////////////////////////////// Generate JSON data
	
	$sample_data = array();
	$connections = array();
	$nodes = array();
	$m = 1;
	$sample_data[] = "{ \"name\": \"MENTIONS\", \"group\": 1 }";
	$myQueryCheck = "SELECT DISTINCT(annotations.value_string), ";
	$myQueryCheck .= "COUNT(annotations.value_string) AS TheCount ";
	$myQueryCheck .= "FROM annotations, items ";
	$myQueryCheck .= "WHERE annotations.dc_references = items.dc_identifier ";
	$myQueryCheck .= "AND annotations.value_string != \"\" ";
	$myQueryCheck .= "AND items.dct_accessRights = \"restricted\" ";
	$myQueryCheck .= "GROUP BY annotations.value_string ";
	$myQueryCheck .= "ORDER BY annotations.value_string ASC ";
	$mysqli_resultmqc = mysqli_query($mysqli_link, $myQueryCheck);
	while ($row = mysqli_fetch_row($mysqli_resultmqc)){
		$row[0] = trim($row[0]);
		$row[0] = preg_replace("/\"/i","'","$row[0]");
		if(($row[0] != "")) {
			$sample_data[] = "{ \"name\": \"$row[0]\", \"group\": $m }";
			$t = $row[0];
			$nodes["$m"] = "$t";
		}
		$m++;
	}
	$p = 1;
	foreach($nodes as $n) {
		if(($n != "")) {
			$connections[] = "{ \"source\": $p, \"target\": 0, \"weight\": 1 }";
		}
		$p++;
	}
	

/////////////////////////////////////////////////////////// Prepare Nodes and Connections	

	$sample_data = array_unique($sample_data);
	$connections = array_unique($connections);
	$cConn = count($connections);
	$cData = count($sample_data);

/////////////////////////////////////////////////////////// Do viz

?>

{
  "nodes":[
<?php
	$i = 0;	
	foreach($sample_data as $sd) {
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
	foreach($connections as $conn) {
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

?>