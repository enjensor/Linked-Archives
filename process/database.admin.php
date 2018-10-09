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
//  5 January 2017
//
//
/////////////////////////////////////////////////////////// Vars

	session_start();
	header("Content-type: text/html;charset=UTF-8");
	mb_internal_encoding("UTF-8");
	include("../ar.config.php");
	include("../ar.dbconnect.php");
	include("../index_functions.php");
	$create_fCollection_UUID = "n";
	$create_fData_UUID = "n";
	$fix_fCollection_dcIdentifier = "n";
	$fix_fData_dcTitle = "n";
	$join_fData = "n";
	$fix_fData_resource = "n";
	$fix_fData_dcCreated = "n";
	$fix_items_dcReferences = "n";
	$fix_annotations_dcReferences = "n";
	$create_annotations_UUID = "n";
	$get_UUID = "y";
	
/////////////////////////////////////////////////////////// Get items UUID for annotations

	if(($get_UUID == "y")) {
		$update = "0";
		$queryD = "SELECT * FROM annotations ORDER BY ID ASC";
		$mysqli_resultD = mysqli_query($mysqli_link, $queryD);
		while($rowD = mysqli_fetch_row($mysqli_resultD)) { 
			$ID = $rowD[0];
			$items_dc_identifier = $rowD[3];
			$UUID = "";
			$queryZ = "SELECT UUID FROM items WHERE dc_identifier = \"$items_dc_identifier\" ";
			$mysqli_resultZ = mysqli_query($mysqli_link, $queryZ);
			while($rowZ = mysqli_fetch_row($mysqli_resultZ)) {
				$UUID = $rowZ[0];
			}
			if(($UUID != "")) {
				$queryX = "UPDATE annotations SET items_UUID = \"$UUID\" WHERE ID = \"$ID\" ";
				$mysqli_resultX = mysqli_query($mysqli_link, $queryX);
			}	
			$update++;
		}
		echo "Done: $update items UUIDs added into annotations table";
	}
	
/////////////////////////////////////////////////////////// Create annotations UUID

	if(($create_annotations_UUID == "y")) {
		$update = "0";
		$queryD = "SELECT * FROM annotations ORDER BY ID ASC";
		$mysqli_resultD = mysqli_query($mysqli_link, $queryD);
		while($rowD = mysqli_fetch_row($mysqli_resultD)) { 
			$ID = $rowD[0];
			$UUID_original = $rowD[1];
			$UUID = guidv4();
			if(($UUID != "") && ($UUID_original == "") && ($ID != "")) {
				$queryX = "UPDATE annotations SET UUID = \"$UUID\" WHERE ID = \"$ID\" ";
				$mysqli_resultX = mysqli_query($mysqli_link, $queryX);
				$update++;
			}
		}
		echo "Done: $update annotations UUIDs created";
	}	

/////////////////////////////////////////////////////////// Fix Annotations dcReferences
	
	if(($fix_annotations_dcReferences == "y")) {
		$update = "0";
		$queryD = "SELECT * FROM annotations ORDER BY ID ASC";
		$mysqli_resultD = mysqli_query($mysqli_link, $queryD);
		while($rowD = mysqli_fetch_row($mysqli_resultD)) { 
			$ID = $rowD[0];
			$dcReferences_original = $rowD[3];
			$dcReferences = $dcReferences_original;
			if(($dcReferences != "") && ($ID != "")) {
				$queryX = "UPDATE annotations SET dc_references = \"$dcReferences\" WHERE ID = \"$ID\" ";
				$mysqli_resultX = mysqli_query($mysqli_link, $queryX);
				$update++;
			}
		}
		echo "Done: $update annotations dcReferences created";
	}	
	
/////////////////////////////////////////////////////////// Fix Items dcReferences

	if(($fix_items_dcReferences == "y")) {
		$update = "0";
		$queryD = "SELECT * FROM items ORDER BY ID ASC";
		$mysqli_resultD = mysqli_query($mysqli_link, $queryD);
		while($rowD = mysqli_fetch_row($mysqli_resultD)) { 
			$ID = $rowD[0];
			$dcReferences_original = $rowD[4];
			$dcReferences = $dcReferences_original;
			if(($dcReferences != "") && ($ID != "")) {
				$queryX = "UPDATE items SET dc_references = \"$dcReferences\" WHERE ID = \"$ID\" ";
				$mysqli_resultX = mysqli_query($mysqli_link, $queryX);
				$update++;
			}
		}
		echo "Done: $update items dcReferences created";
	}	
	
/////////////////////////////////////////////////////////// Create fCollection UUID

	if(($create_fCollection_UUID == "y")) {
		$update = "0";
		$queryD = "SELECT * FROM fCollection ORDER BY ID ASC";
		$mysqli_resultD = mysqli_query($mysqli_link, $queryD);
		while($rowD = mysqli_fetch_row($mysqli_resultD)) { 
			$ID = $rowD[0];
			$UUID_original = $rowD[1];
			$UUID = guidv4();
			if(($UUID != "") && ($UUID_original == "") && ($ID != "")) {
				$queryX = "UPDATE fCollection SET UUID = \"$UUID\" WHERE ID = \"$ID\" ";
				$mysqli_resultX = mysqli_query($mysqli_link, $queryX);
				$update++;
			}
		}
		echo "Done: $update fCollection UUIDs created";
	}
	
/////////////////////////////////////////////////////////// Create fData UUID

	if(($create_fData_UUID == "y")) {
		$update = "0";
		$queryD = "SELECT * FROM fData ORDER BY ID ASC";
		$mysqli_resultD = mysqli_query($mysqli_link, $queryD);
		while($rowD = mysqli_fetch_row($mysqli_resultD)) { 
			$ID = $rowD[0];
			$UUID_original = $rowD[1];
			$UUID = guidv4();
			if(($UUID != "") && ($UUID_original == "") && ($ID != "")) {
				$queryX = "UPDATE fData SET UUID = \"$UUID\" WHERE ID = \"$ID\" ";
				$mysqli_resultX = mysqli_query($mysqli_link, $queryX);
				$update++;
			}
		}
		echo "Done: $update fData UUIDs created";
	}	
	
/////////////////////////////////////////////////////////// Fix fCollection DC Identifier

	if(($fix_fCollection_dcIdentifier == "y")) {
		$update = "0";
		$queryD = "SELECT * FROM fCollection ORDER BY ID ASC";
		$mysqli_resultD = mysqli_query($mysqli_link, $queryD);
		while($rowD = mysqli_fetch_row($mysqli_resultD)) { 
			$ID = $rowD[0];
			$ID_original = $rowD[2];
			$IDs = explode("|","$ID_original");
			$dcID = $IDs[1];
			if(($dcID != "") && ($ID != "")) {
				$queryX = "UPDATE fCollection SET dc_identifier = \"$dcID\" WHERE ID = \"$ID\" ";
				$mysqli_resultX = mysqli_query($mysqli_link, $queryX);
				$update++;
			}
		}
		echo "Done: $update fCollection identifiers fixed";
	}	
	
/////////////////////////////////////////////////////////// Fix fData DC Title

	if(($fix_fData_dcTitle == "y")) {
		$update = "0";
		$queryD = "SELECT * FROM fData ORDER BY ID ASC";
		$mysqli_resultD = mysqli_query($mysqli_link, $queryD);
		while($rowD = mysqli_fetch_row($mysqli_resultD)) {
			$ID = $rowD[0];
			$dcTitle_original = $rowD[5];
			$dcTitle = $dcTitle_original;
			$dcTitle = preg_replace("/ \(/i",":","$dcTitle");
			$dcTitle = preg_replace("/\)/i","","$dcTitle");
			if(($dcTitle != "") && ($ID != "")) {
				$queryX = "UPDATE fData SET dc_title = \"$dcTitle\" WHERE ID = \"$ID\" ";
				$mysqli_resultX = mysqli_query($mysqli_link, $queryX);
				$members = explode(":","$dcTitle");
				if(($members[1] != "")) {
					$queryX = "UPDATE fData SET skos_member = \"$members[1]\", rdf_type=\"image/jpeg\" WHERE ID = \"$ID\" ";
					$mysqli_resultX = mysqli_query($mysqli_link, $queryX);
				}
				$update++;
			}
		}
		echo "Done: $update fData titles fixed";
	}	
	
/////////////////////////////////////////////////////////// Join fCollection Identifiers

	if(($join_fData == "y")) {
		$update = "0";
		$queryD = "SELECT * FROM ar_file_tags ORDER BY ID ASC";
		$mysqli_resultD = mysqli_query($mysqli_link, $queryD);
		while($rowD = mysqli_fetch_row($mysqli_resultD)) {
			$FileKey = $rowD[4];
			$FileSize = $rowD[3];
			$FileSource = $rowD[5];
			$FileSources = explode("|","$FileSource");
			if(($FileSize != "") && ($FileKey != "")) {
				$queryX = "UPDATE fData SET rdf_comment = \"$FileSize\" WHERE dc_identifier = \"$FileKey\" ";
				$mysqli_resultX = mysqli_query($mysqli_link, $queryX);	
			}
			if(($FileSources[1] != "") && ($FileKey != "")) {
				$UUID = "";
				$queryX = "UPDATE fData SET fCollection_dc_identifier = \"$FileSources[1]\" WHERE dc_identifier = \"$FileKey\" ";
				$mysqli_resultX = mysqli_query($mysqli_link, $queryX);	
				$queryZ = "SELECT UUID FROM fcollection WHERE dc_identifier = \"$FileSources[1]\" ";
				$mysqli_resultZ = mysqli_query($mysqli_link, $queryZ);
				while($rowZ = mysqli_fetch_row($mysqli_resultZ)) {
					$UUID = $rowZ[0];
				}
				if(($UUID != "")) {
					$queryX = "UPDATE fData SET fCollection_UUID = \"$UUID\" WHERE dc_identifier = \"$FileKey\" ";
					$mysqli_resultX = mysqli_query($mysqli_link, $queryX);
				}	
				$update++;
			}
		}
		echo "Done: $update fData joins created";
	}
	
/////////////////////////////////////////////////////////// Fix fData Resource

	if(($fix_fData_resource == "y")) {
		$update = "0";
		$queryD = "SELECT * FROM fData ORDER BY ID ASC";
		$mysqli_resultD = mysqli_query($mysqli_link, $queryD);
		while($rowD = mysqli_fetch_row($mysqli_resultD)) {	
			$ID = $rowD[0];
			$rdfResource_original = $rowD[9];
			$rdfResource = $rdfResource_original;
			$rdfResource = preg_replace("/C:\\\\My Research\\\\/","",$rdfResource);
			$rdfResource = preg_replace("/\\\\/i","/","$rdfResource");
			if(($rdfResource != "") && ($ID != "")) {
				$queryX = "UPDATE fData SET rdf_resource = \"$rdfResource\" WHERE ID = \"$ID\" ";
				$mysqli_resultX = mysqli_query($mysqli_link, $queryX);
				$update++;
			}
		}
		echo "Done: $update fData titles fixed";
	}
	
/////////////////////////////////////////////////////////// Fix fData dcCreated	
	
	if(($fix_fData_dcCreated == "y")) {
		$update = "0";
		$queryD = "SELECT * FROM ar_file_date ORDER BY ID ASC";
		$mysqli_resultD = mysqli_query($mysqli_link, $queryD);
		while($rowD = mysqli_fetch_row($mysqli_resultD)) {
			$FileKey = $rowD[1];
			$FileDate = $rowD[2];
			if(($FileDate != "") && ($FileKey != "")) {
				$queryX = "UPDATE fData SET dc_created = \"$FileDate\" WHERE dc_identifier = \"$FileKey\" ";
				$mysqli_resultX = mysqli_query($mysqli_link, $queryX);
				$update++;
			}
		}
		echo "Done: $update fData dcCreated elements";
	}
	
/////////////////////////////////////////////////////////// Finish

	include("../ar.dbdisconnect.php");

?>