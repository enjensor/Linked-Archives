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
//  6 April 2017
//
//
/////////////////////////////////////////////////////////// Clean post and get

	define('MyConstInclude', TRUE);
	$alphabet = array("A" => "1", "B" => "2", "C" => "3", "D" => "4", "E" => "5", "F" => "6", 
		"G" => "7", "H" => "8", "I" => "9", "J" => "10", "K" => "11", "L" => "12", "M" => "13", 
		"N" => "14", "O" => "15", "P" => "16", "Q" => "17", "R" => "18", "S" => "19", "T" => "20", 
		"U" => "21", "V" => "22", "W" => "23", "X" => "24", "Y" => "25", "Z" => "26"); 

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

	if(($doCalc != "n")) {
	
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// Database Routines

		$sample_data = array();
		$connections = array();
		$nodes = array();

/////////////////////////////////////////////////////////// Get All Mention Nodes	

		$myQueryCheck = "SELECT DISTINCT(annotations.value_string), ";
		$myQueryCheck .= "COUNT(annotations.value_string) AS thegoal ";
		$myQueryCheck .= "FROM annotations ";
		$myQueryCheck .= "GROUP BY annotations.value_string ";
		$myQueryCheck .= "ORDER BY annotations.value_string ASC ";
		$mysqli_resultmqc = mysqli_query($mysqli_link, $myQueryCheck);
		while ($row = mysqli_fetch_row($mysqli_resultmqc)){
			$row[0] = trim($row[0]);
			$fLetter = ucwords(substr($row[0],0,1));
			$nLetter = $alphabet["$fLetter"];
			if(($nLetter == "")) { $nLetter = "0"; }
			$sample_data[] = "{ \"name\": \"$row[0]\", \"size\": $row[1] }";
			$nodes[] = "$row[0]";
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
				$queryB .= "GROUP BY annotations.value_string ";
				$queryB .= "ORDER BY TheCount DESC, ";
				$queryB .= "annotations.value_string ASC";
				$mysqli_resultB = mysqli_query($mysqli_link, $queryB);
				while($rowB = mysqli_fetch_row($mysqli_resultB)) {
					$rowB[0] = trim($rowB[0]);
					$connections[] = "{ \"source\": \"$rowB[0]\", \"target\": \"$searchTerm\" }";
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

?>
var sample_data = [
<?php

	if(($doCalc != "n")) {	
		$i = 0;	
		foreach($sample_data as $sd) {
			$i++;
			echo $sd;
			if(($i != $cData)) {
				echo ", \n";
			} else {
				echo " \n";	
			}
		}
	} else {
?>

<?php
	}
?>
]

var connections = [
<?php
	if(($doCalc != "n")) {
		$i = 0;	
		foreach($connections as $conn) {
			$i++;
			echo $conn;
			if(($i != $cConn)) {
				echo ", \n";
			} else {
				echo " \n";	
			}
		}
	} else {
?>

<?php
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
<?php

/////////////////////////////////////////////////////////// Finish

	include("./ar.dbdisconnect.php");

?>