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
//  22-23 February 2017
//	25 May 2017
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
		$do_not_process = "";
		$variation = $_GET["variation"];	
		if(($variation == "")) { $variation = "LOCATIONS"; }
		$found = "";
		$findCities = "datasource_cities";
		$findField = "combined";
	}	

/////////////////////////////////////////////////////////// Create JSON encoded array

	if(($do_not_process == "")) {
		if (isset($_GET['term'])){
			$_GET['term'] = preg_replace("/[^a-zA-Z0-9\s()']/", "", $_GET['term']);
			$seek = $_GET['term'];
			$seekB = preg_replace("/  /i", " ", "$seek");
			$return_arr = array();
			
/////////////////////////////////////////////////////////// Title search			
			
			if(($variation == "LOCATIONS")) {
				$query = "SELECT DISTINCT($findField) ";
				$query .= "FROM $findCities ";
				$query .= "WHERE ";
				if(($findCities == "annotations")) {
					$query .= "rdfs_label LIKE \"geo%\" ";
					$query .= "AND ";
				}
				$query .= "$findField LIKE \"$seekB%\" ";
				$query .= "GROUP BY $findField ";
				$query .= "ORDER BY $findField ASC ";
//				$query .= "LIMIT 8 ";				
				$mysqli_result = mysqli_query($mysqli_link, $query);
				while($row = mysqli_fetch_row($mysqli_result)) {	
					$array = "{\"value\":\"$row[0]\",\"label\":\"$row[0]\"}";
			        $return_arr[] = $array;
					$found = "y";
			    }
			}
			
/////////////////////////////////////////////////////////// No results?

			if(($found != "y")) {
				$array = "{\"value\":\"No Existing Matching Locations\",\"label\":\"No Existing Matching Locations\"}";
				$return_arr[] = $array;
			}

/////////////////////////////////////////////////////////// Return array		
			
			$c = count($return_arr);
			if(($c > 0)) {
				foreach($return_arr as $r) {
					$t++;
					if(($t == 1)) { echo "["; }
					echo $r;
					if(($t < $c)) { echo ","; }
					if(($t == $c)) { echo "]"; }
				}
			}	
		}
	}

///////////////////////////////////////////////////////////// Finish

	if(($reload == "")) {
		include("./ar.dbdisconnect.php");
	}

?>