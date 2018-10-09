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
//  4-7 April 2017
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
	
/////////////////////////////////////////////////////////// Write document header
	
?>
<!DOCTYPE html>
<html lang="en">
<!--[if lt IE 7]>      	<html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         	<html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         	<html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]>		<html class="no-js"><![endif]-->
	<head>
		<title>Visualise Mentions</title>
		<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<meta name="description" content="Visualise Mentions">
		<meta name="robots" content="noindex,nofollow">
		<meta http-equiv='cache-control' content='no-cache'>
		<meta http-equiv='expires' content='0'>
		<meta http-equiv='pragma' content='no-cache'>
        <script language="javascript" type="text/javascript" src="./js/d3.js"></script>
    	<script language="javascript" type="text/javascript" src="./js/d3plus.js"></script>
        <style type="text/css">
		
			body { 
  				font: 11px helvetica neue, helvetica, arial, sans-serif;
			}
	
		</style>
	</head>
	<body style="overflow-x: hidden;">
	<!--[if lt IE 7]>
    	<p class="browsehappy">You are using an <strong>outdated</strong> browser. 
    	Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
	<![endif]-->
    <div id="viz"></div>
<?php

	if(($doCalc == "y")) {
	
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// Database Routines

		$sample_data = array();
		$connections = array();
		$nodes = array();

/////////////////////////////////////////////////////////// Get All Mention Nodes	

		$m = 0;
		$myQueryCheck = "SELECT DISTINCT(annotations.value_string), ";
		$myQueryCheck .= "COUNT(annotations.value_string) AS TheCount ";
		$myQueryCheck .= "FROM annotations, items ";
		$myQueryCheck .= "WHERE annotations.dc_references = items.dc_identifier ";
		$myQueryCheck .= "AND items.dct_accessRights = \"restricted\" ";
		$myQueryCheck .= "GROUP BY annotations.value_string ";
		$myQueryCheck .= "ORDER BY annotations.value_string ASC ";
		$mysqli_resultmqc = mysqli_query($mysqli_link, $myQueryCheck);
		while ($row = mysqli_fetch_row($mysqli_resultmqc)){
			$m++;
			$row[0] = trim($row[0]);
			$row[0] = preg_replace("/\"/i","'","$row[0]");
			if(($row[0] != "")) {
				$sample_data[] = "{ \"name\": \"$row[0]\", \"size\": $row[1] }";
				$t = $row[0];
				$nodes["$t"] = "$row[0]";
			}
		}
		$sample_data[] = "{ \"name\": \"MENTIONS\", \"size\": $m }";
		
/////////////////////////////////////////////////////////// Get All Mention to Mention Edges

		$nodes = array_unique($nodes);
		$nodes = array_filter($nodes);
		foreach($nodes as $n) {
			if(($n != "")) {
				$connections[] = "{ \"source\": \"$n\", \"target\": \"MENTIONS\" }";
			}
		}

/////////////////////////////////////////////////////////// Prepare Nodes and Connections	

		$sample_data = array_unique($sample_data);
		$connections = array_unique($connections);
		sort($sample_data);
		sort($connections);
		$cConn = count($connections);
		$cData = count($sample_data);

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// Do Viz

	}

	if(($doCalc == "y")) {

?>
<script language="javascript" type="text/javascript" >

  var sample_data = [
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
  ]

  var connections = [
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

  var visualization = d3plus.viz()
    .container("#viz") 
    .type("network")  
    .data(sample_data) 
    .edges(connections) 
    .size("size")  
    .id("name") 
	.focus({
      "tooltip" : true
    })
    .draw();

</script>
<?php

	} else {
		echo "\n\t<script language=\"javascript\" ";
        echo "type=\"text/javascript\" ";
        echo "src=\"./data_visualise_script.js\">";
        echo "</script>";
	}
	echo "\n</body>";
	echo "\n</html>";

/////////////////////////////////////////////////////////// Finish

	include("./ar.dbdisconnect.php");

?>