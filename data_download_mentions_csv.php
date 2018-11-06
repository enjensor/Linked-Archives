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
//	30 May 2017
//	18 October 2018
//  24 october 2018
//
//
/////////////////////////////////////////////////////////// Clean post and get	
	
	if(($IMGreload == "")) {
		define('MyConstInclude', TRUE);
		$MerdUser = session_id();
		if(empty($MerdUser)) { session_start(); }
		$SIDmerd = session_id();
		mb_internal_encoding("UTF-8");
		include("./ar.config.php");
		include("./ar.dbconnect.php");
		include("./index_functions.php");
		include("./classes/download.class.php");
		if (!mysqli_set_charset($mysqli_link, "utf8")) {
			echo "PROBLEM WITH CHARSET!";
			die;
		}
        $doComplexSearch = "n";
		$type = $_GET["type"];
		$format = $_GET["format"];
        $doDebug = $_GET["doDebug"];
		$value_string = $_GET["value_string"];
        $value_phrase = $_GET["value_phrase"];
        $value_phrase = rtrim("$value_phrase","|");
        if(($value_string != $value_phrase)) {
            if(!preg_match("/$value_string/i","$value_phrase")) {
                $value_phrase .= "|".$value_string;
            }
        }     
        $searches = explode("|","$value_phrase");
        $searches = array_filter($searches);
        $having = count($searches);
        if(($having > 1)) {
           $doComplexSearch = "y"; 
        }
		$_GET = array();
		$_POST = array();
	}
	
///////////////////////////////////////////////////////////// RDF Export Type	
	
	$doFile = "y";
	$doPrefix = "n";
	if(($format == "rdfa")) {
		$doPrefix = "y";
	}	

///////////////////////////////////////////////////////////// If Debug

    if(($doDebug == "y")) {
        echo "doComplexSearch = $doComplexSearch<br />";
        echo "value_string = $value_string<br />";
        echo "value_phrase = $value_phrase<br />";
        echo "having = $having<br />";
    }

///////////////////////////////////////////////////////////// Get IDs for Selected Annotation(s)	
	
    if(($doComplexSearch == "n")) {
        if(($value_string != "")) {
            $IDs = array();
            $queryD = "SELECT DISTINCT(dc_references) FROM annotations WHERE value_string = \"$value_string\" ";
            $mysqli_resultD = mysqli_query($mysqli_link, $queryD);
            while($rowD = mysqli_fetch_row($mysqli_resultD)) { 
                $IDs[] = "$rowD[0]";
                $rdf_found = "y";
            }
            if(($doDebug == "y")) {
                echo "queryD = $queryD<br />";
                echo "ID count = ".count($IDs)."<br />";
            }
        }
    } else {
        if(($value_phrase != "")) {
            $findmeAgain = "";
            foreach($searches as $w) {
                $i++;
				if(($i == $having)) {
                    $findmeAgain .= "annotations.value_string = \"$w\"";
                } else {
                    $findmeAgain .= "annotations.value_string = \"$w\" OR ";   
                }
            }
            $IDs = array();
            $queryA = "SELECT ";
			$queryA .= "annotations.dc_references, ";
			$queryA .= "items.dc_identifier, ";
//			$queryA .= "COUNT(annotations.dc_references) AS goal ";
            $queryA .= "COUNT(DISTINCT annotations.value_string) AS goal ";
			$queryA .= "FROM annotations ";
			$queryA .= "LEFT JOIN items ";
			$queryA .= "ON annotations.dc_references = items.dc_identifier ";
			$queryA .= "WHERE ($findmeAgain) ";
			$queryA .= "GROUP BY annotations.dc_references ";
			$queryA .= "HAVING goal = $having ";
			$queryA .= "ORDER BY items.dc_title ASC ";
            $mysqli_resultA = mysqli_query($mysqli_link, $queryA);
			while($rowA = mysqli_fetch_row($mysqli_resultA)) {
				$IDs[] = $rowA[0];
                $rdf_found = "y";
			}
            if(($doDebug == "y")) {
                echo "queryA = $queryA<br />";
                echo "ID count = ".count($IDs)."<br />";
            }
        }
    }

///////////////////////////////////////////////////////////// If Debug End

    if(($doDebug == "y")) {
        die;   
    }
	
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// Get CSV Details

	$xml = "";
	$p = 0;
	$go = 0;
	foreach($IDs as $dc_identifier) {
		$queryD = "SELECT * FROM items WHERE dc_identifier = \"$dc_identifier\" ";
		$mysqli_resultD = mysqli_query($mysqli_link, $queryD);
		while($rowD = mysqli_fetch_row($mysqli_resultD)) { 
			$this_dc_UUID = $rowD[1];
			$this_dc_identifier = $rowD[2];
			$this_dc_references = $rowD[5];
			$this_dc_title = $rowD[6];
			$this_page = $rowD[7];
			$this_dc_type = $rowD[8];
			$this_dc_format = $rowD[9];
			$this_prism_byteCount = $rowD[10];
			$rdf_resource = $rowD[11];
			$this_gn_name = $rowD[15];
			$this_dc_creator = $rowD[13];
			$this_org_formalOrganisation = $rowD[14];
			$this_dc_created = $rowD[16];
			$this_restricted = $rowD[18];
			$this_marc_addressee = $rowD[19];
			$this_rdaa_groupMemberOf = $rowD[20];
			$this_mads_associatedLocale = $rowD[21];	
			$phpSelf = preg_replace("/data_download.php/i","",$_SERVER["PHP_SELF"]);
			$phpBase = "https://".$_SERVER["HTTP_HOST"]."/".$phpSelf."item/".$this_dc_identifier."";
			$rdf_found = "y";
			if((($format == "csv"))) {

////////////////////////////////// Column Data
				
				if(($p < 1)) {
					$columns = array();
					$item_level = "\"rdf:about\",";
					$item_level .= "\"dc:title\",";
					$item_level .= "\"dc:identifier\",";
					$item_level .= "\"dc:created\",";
					$item_level .= "\"dc:format\",";
					$item_level .= "\"prism:byteCount\",";
					$item_level .= "\"dc:creator\",";
					$item_level .= "\"org:formalOrganisation\",";
					$item_level .= "\"gn:name\",";
					$item_level .= "\"marc:addressee\",";
					$item_level .= "\"rdaa:groupMemberOf\",";
					$item_level .= "\"mads:associatedLocale\",";
					$queryH = "SELECT DISTINCT(rdfs_label), reg_uri FROM annotations ORDER BY rdfs_label ASC";
					$mysqli_resultH = mysqli_query($mysqli_link, $queryH);
					while($rowH = mysqli_fetch_row($mysqli_resultH)) {
						$columns[] = $rowH[0];
						$item_level .= "\"$rowH[1]:$rowH[0]\",";
					}
					$item_level .= "\"gn:name-dc:coverage.y\",";
					$item_level .= "\"gn:name-dc:coverage.x\",";
					$item_level .= "\"mads:associatedLocale-dc:coverage.y\",";
					$item_level .= "\"mads:associatedLocale-dc:coverage.x\",";
					$item_level .= "\"iana:UUID\"";
					$item_level .= "\n";
					$p++;
				}

////////////////////////////////// GeoRefs
				
				$gn_dc_coverage_y = "";
				$gn_dc_coverage_x = "";
				$mads_dc_coverage_y = "";
				$mads_dc_coverage_x = "";
				
////////////////////////////////// Field Values
				
				$item_level .= "\"$phpBase\",";
				$item_level .= "\"$this_dc_title\",";
				$item_level .= "\"$this_dc_identifier\",";
				$item_level .= "\"$this_dc_created\",";
				$item_level .= "\"$this_dc_format\",";
				$item_level .= "\"$this_prism_byteCount\",";
				$item_level .= "\"$this_dc_creator\",";
				$item_level .= "\"$this_org_formalOrganisation\",";
				$item_level .= "\"$this_gn_name\",";
				$item_level .= "\"$this_marc_addressee\",";
				$item_level .= "\"$this_rdaa_groupMemberOf\",";
				$item_level .= "\"$this_mads_associatedLocale\",";
				
				if(count($columns > 0)) {
					foreach($columns as $C) {
						$temp = "";
						$queryH = "SELECT value_string FROM annotations ";
						$queryH .= "WHERE dc_references = \"$this_dc_identifier\" ";
						$queryH .= "AND rdfs_label = \"$C\" ORDER BY value_string ASC";
						$mysqli_resultH = mysqli_query($mysqli_link, $queryH);
						while($rowH = mysqli_fetch_row($mysqli_resultH)) {
							$temp .= "$rowH[0]; ";
						}
						$temp = preg_replace("/\"/","'","$temp");
						$item_level .= "\"$temp\",";
					}
				}
				
				if(($this_gn_name != "") && ($this_gn_name != " ")) {
					$queryH = "SELECT latitude, longitude FROM datasource_cities WHERE combined = \"$this_gn_name\" LIMIT 1";
					$mysqli_resultH = mysqli_query($mysqli_link, $queryH);
					while($rowH = mysqli_fetch_row($mysqli_resultH)) {
						$gn_dc_coverage_y = $rowH[0];
						$gn_dc_coverage_x = $rowH[1];
					}
				}
				
				if(($this_mads_associatedLocale != "") && ($this_mads_associatedLocale != " ")) {
					$queryH = "SELECT latitude, longitude FROM datasource_cities WHERE combined = \"$this_mads_associatedLocale\" LIMIT 1";
					$mysqli_resultH = mysqli_query($mysqli_link, $queryH);
					while($rowH = mysqli_fetch_row($mysqli_resultH)) {
						$mads_dc_coverage_y = $rowH[0];
						$mads_dc_coverage_x = $rowH[1];
					}
				}
				
				$item_level .= "\"$gn_dc_coverage_y\",";
				$item_level .= "\"$gn_dc_coverage_x\",";
				$item_level .= "\"$mads_dc_coverage_y\",";
				$item_level .= "\"$mads_dc_coverage_x\",";
				$item_level .= "\"$this_dc_UUID\"";
				$item_level .= "\n";
			}		
		}
	}
	$xml = $item_level;
	
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// Download File	
	
	if(($rdf_found == "y") && ($type == "other") && ($xml != "")) {	
		$mtime = time();
		$file_parts = explode("/","$rdf_resource");
		$file_parts = array_reverse($file_parts);
		$rdf_parts = explode(".","$file_parts[0]");
		if(($format == "csv")) {
			$rdf_file = "items_mentions_".$mtime.".csv";
		}
		$file = "./temp/".$rdf_file;
		if(($doFile != "y")) {
			echo $xml;
			die;
		} else {
			if(file_exists("$file")) unlink("$file");
			file_put_contents($file, $xml);
			if(file_exists($file)){
				$downloadFile = new Downloader();
				$downloadFile->__construct($rdf_file);
				$downloadFile->download_file($file);
			}
		}
	} else {
		die;	
	}
	
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// Finish

	if(($IMGreload == "")) {
		include("./ar.dbdisconnect.php");
	}

?>