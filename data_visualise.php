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
//
//
/////////////////////////////////////////////////////////// Clean post and get

	define('MyConstInclude', TRUE);
	$doCalc = "y";
	
////////////////////////////////// Limit number of connections to a threshol	
	
	$doThres = "n";
	$threshold = $_GET["threshold"];
	if(($threshold == "")) { $threshold = 3000; }
	
////////////////////////////////// Limit number of results returned	
	
	$doLimit = "n";
	$limit = $_GET["limit"];
	if(($limit == "")) { $limit = 500; }
	
////////////////////////////////// Limit query to specific rdfs label	
	
	$doRDFS = "n";
	$rdfs_label = $_GET["rdfs_label"];
	if(($rdfs_label == "")) { $rdfs_label = "organisation"; }

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

		$myQueryCheck = "SELECT DISTINCT(annotations.value_string), ";
		$myQueryCheck .= "COUNT(annotations.value_string) AS thegoal ";
		$myQueryCheck .= "FROM annotations ";
		if(($doRDFS == "y")) {
			$myQueryCheck .= "WHERE rdfs_label LIKE \"%"."$rdfs_label"."%\" ";
		}
		$myQueryCheck .= "GROUP BY annotations.value_string ";
		if(($doThres == "y")) {
			$myQueryCheck .= "HAVING thegoal > $threshold ";
		}
		$myQueryCheck .= "ORDER BY annotations.value_string ASC ";
		if(($doLimit == "y")) {
			$myQueryCheck .= "LIMIT 0,$limit ";
		}
		$mysqli_resultmqc = mysqli_query($mysqli_link, $myQueryCheck);
		while ($row = mysqli_fetch_row($mysqli_resultmqc)){
			$row[0] = trim($row[0]);
			$row[0] = preg_replace("/\"/i","'","$row[0]");
			if(($row[0] != "")) {
				$sample_data[] = "{ \"name\": \"$row[0]\", \"size\": $row[1] }";
				$t = $row[0];
				$nodes["$t"] = "$row[0]";
			}
		}
		
/////////////////////////////////////////////////////////// Get All Mention to Mention Edges

		$nodes = array_unique($nodes);
		$nodes = array_filter($nodes);
		foreach($nodes as $n) {
			if(($n != "")) {
				$h++;
				$i = 0;
				$findmeAgain = "";
				$searchTerm = $n;
				$IDs = array();
				$having = "";
				$queryA = "SELECT DISTINCT(annotations.dc_references) ";
				$queryA .= "FROM annotations ";
				$queryA .= "WHERE annotations.value_string = \"$searchTerm\" ";
				$queryA .= "GROUP BY annotations.dc_references ";
				$queryA .= "ORDER BY annotations.dc_references ASC";
				$mysqli_resultA = mysqli_query($mysqli_link, $queryA);
				while($rowA = mysqli_fetch_row($mysqli_resultA)) {
					$IDs[] = $rowA[0];
				}
				$having = (count($IDs));
				foreach($IDs as $w) {
					$i++;
					if(($i == $having)) {
						$findmeAgain .= "annotations.dc_references = \"$w\"";
					} else {
						$findmeAgain .= "annotations.dc_references = \"$w\" OR ";
					}
				}
				$queryB = "SELECT DISTINCT(annotations.value_string), ";
				$queryB .= "COUNT(annotations.value_string) AS TheCount ";
				$queryB .= "FROM annotations ";
				$queryB .= "WHERE ($findmeAgain) ";
				if(($doRDFS == "y")) {
					$queryB .= "AND rdfs_label LIKE \"%"."$rdfs_label"."%\" ";
				}
				$queryB .= "GROUP BY annotations.value_string ";
				$queryB .= "ORDER BY TheCount DESC, ";
				$queryB .= "annotations.value_string ASC ";
				if(($doLimit == "y")) {
					$queryB .= "LIMIT 0,$limit ";
				}
				$mysqli_resultB = mysqli_query($mysqli_link, $queryB);
				while($rowB = mysqli_fetch_row($mysqli_resultB)) {
					$rowB[0] = trim($rowB[0]);
					$rowB[0] = preg_replace("/\"/i","'","$rowB[0]");
					if(($rowB[0] != $searchTerm) && ($rowB[0] != "") && ($searchTerm != "")) {
						$connections[] = "{ \"source\": \"$rowB[0]\", \"target\": \"$searchTerm\" }";
						if(($doThres == "y")) {
							$t = $rowB[0];
							if(($nodes["$t"] == "")) {
								$sample_data[] = "{ \"name\": \"$rowB[0]\", \"size\": $rowB[1] }";
								$nodes["$t"] = $rowB[0];
							}
						}
					}
				}
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