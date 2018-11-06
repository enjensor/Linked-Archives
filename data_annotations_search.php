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
//  25 May 2017
//	30 May 2017
//  5-6 November 2018
//
//
/////////////////////////////////////////////////////////// Clean post and get	
	
	if(($searchReload == "")) {
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
		$userCols = $_GET["variation"];	
        if(($userCols == "")) {
            $userCollections = array();
            $userCollections[] = "NONE";
        } else {
            $userCollections = explode(",","$userCols");
        }
        $collectionList = "";
        foreach($userCollections as $key => $value) {
            if($value == "Collections") {
                unset($userCollections[$key]);
            }
            $collectionList .= "skos_orderedCollection = \"$value\" OR ";
        }
        $collectionList = substr($collectionList, 0, -4);
        $findCols = "";
        $query = "SELECT dc_identifier FROM collections WHERE (".$collectionList.") ORDER by dc_identifier ASC; ";
        $mysqli_result = mysqli_query($mysqli_link, $query);
        while($row = mysqli_fetch_row($mysqli_result)) {
            $findCols .= "items.dc_references = \"$row[0]\" OR ";
        }
        $findCols = substr($findCols, 0, -4);
		$variation = "ANNOTATIONS";
		$found = "";
	}	

/////////////////////////////////////////////////////////// Create JSON encoded array

	if(($do_not_process == "")) {
		if (isset($_GET['term'])){
			$_GET['term'] = preg_replace("/[^a-zA-Z0-9\s()']/", "", $_GET['term']);
			$seek = $_GET['term'];
			$seekB = preg_replace("/  /i", " ", "$seek");
			$return_arr = array();
			
/////////////////////////////////////////////////////////// Title search			
			
			if(($variation == "ANNOTATIONS")) {
				$query = "SELECT DISTINCT(annotations.value_string), COUNT(annotations.value_string) ";
				$query .= "FROM annotations ";
                $query .= "LEFT JOIN items ";
                $query .= "ON items.dc_identifier = annotations.items_dc_identifier ";
				$query .= "WHERE ";
				$query .= "annotations.value_string LIKE \"%$seekB%\" ";
                $query .= "AND (".$findCols.") ";
                
                if(($thiskey != "")) {
					$keys = explode(":", "$thiskey");
					$query .= "AND annotations.rdfs_label = \"".$keys[1]."\" ";
                }
                
				$query .= "GROUP BY annotations.value_string ";
				$query .= "ORDER BY annotations.value_string ASC";
				$mysqli_result = mysqli_query($mysqli_link, $query);
				while($row = mysqli_fetch_row($mysqli_result)) {	
					$array = "{\"value\":\"$row[0]\",\"label\":\"$row[0] ($row[1])\"}";
			        $return_arr[] = $array;
					$found = "y";
			    }
			}

/////////////////////////////////////////////////////////// Debug Query            
//           
//            $query = preg_replace("/\"/i","'","$query");
//            $array = "{\"value\":\"$query\",\"label\":\"$query\"}";
//            $return_arr[] = $array;
//            		
/////////////////////////////////////////////////////////// No results?

			if(($found != "y")) {
				$array = "{\"value\":\"No Existing Matching Tags\",\"label\":\"No Existing Matching Tags\"}";
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

	if(($searchReload == "")) {
		include("./ar.dbdisconnect.php");
	}

?>