<?php

//////////////////////////////////////////// CREDITS
//
//
//	Archiver Data Import
//	University Library
//  University of Western Sydney
//
//	Procedural Scripting: PHP | MySQL
//
//	FOR ALL ENQUIRIES ABOUT CODE
//
//	Who:	Dr Jason Ensor
//	Email: 	j.ensor@westernsydney.edu.au | jasondensor@gmail.com
//	Mobile: 0419 674 770
//
//  VERSION 0.1
//  28 February 2018
//	02 March 2018
//
//	Starting Items ID 43291
//	Starting Collections ID 247
//	MySQL Export Items over row 19624 and Collections over row 154 
//
//
//////////////////////////////////////////// START

	error_reporting(E_ALL & ~E_NOTICE & ~E_STRICT & ~E_DEPRECATED);
	define('MyConstInclude', TRUE);	
	include("ar.config.php");
	include("ar.dbconnect.php");
	include("index_functions.php");
	mb_internal_encoding("UTF-8");
	if (!mysqli_set_charset($mysqli_link, "utf8")) {
		echo "PROBLEM WITH CHARSET!";
		exit;
	}
	$dbQuery = "DELETE FROM items WHERE ID > 43290";
	$mysqli_result = mysqli_query($mysqli_link, $dbQuery);
	$dbQuery = "DELETE FROM collections WHERE ID > 246";
	$mysqli_result = mysqli_query($mysqli_link, $dbQuery);
	echo "\n\n\n\n";

//////////////////////////////////////////// ADDED FUNCTION(S)

	function gen_md5_password($len = 12) {
		return substr(md5(rand().rand()), 0, $len);
	}

//////////////////////////////////////////// CREATE ARRAY

	$contributor = "contrib41T71U4BZZ";
	$archival = dir("C:\\Temp\\Whitcomb\\MS 99_95 (Whitcoulls Archive 1)\\");
	$Qfiles = array();
	while($QfolderEntry=$archival->read()){
		if(($QfolderEntry != ".") && ($QfolderEntry != "..")) {
			$Qfiles[] = $QfolderEntry;
		}
	}
	$archival->close();
	sort($Qfiles);
	foreach($Qfiles as $QQ) {
		
//////////////////////////////////////////// BeEGIN TIER ONE		
		
		echo $QQ."\n";	

//////////////////////////////////////////// FOLDER VARS

		$start = time();
		$x = 0;
		$dirx = "C:\\Temp\\Whitcomb\\MS 99_95 (Whitcoulls Archive 1)\\".$QQ."\\";
		$dirz = "C:\\Temp\\Whitcomb\\MS 99_95 (Whitcoulls Archive 1)\\".$QQ;
		$folder = dir("$dirx"); 
		$folderMain = "$dirz"; 
		
//////////////////////////////////////////// EXAMPLE A Ledgers, A3 Journals Vol 75, 1921-1927		
		
		$Trow = explode(", ","$QQ");
		$Dz = explode("-","$Trow[2]");
		if(preg_match("/Vol/i","$Trow[1]")){
			$Scz = explode(" Vol ","$Trow[1]");
		}
		if(preg_match("/Folder/i","$Trow[1]")){
			$Scz = explode(" Folder ","$Trow[1]");
		}
		
//////////////////////////////////////////// COLLECTION VARS		
		
		$col_ID = "0";
		$col_iana_UUID = guidv4();
		$col_dc_identifier = time().rand(0,99999);
		$col_fb_heldBy = "Auckland War Memorial Museum, New Zealand";
		$col_bf_subLocation = "Auckland War Memorial Museum";
		$col_bf_physicalLocation = "Whitcoulls Archive 1";
		$col_skos_collection = "$Trow[0], $Scz[0]";
		$col_skos_orderedCollection = "MS 99/95";
		$col_bibo_volume = "$Scz[1]";
		$col_disco_startDate = "$Dz[0]";
		$col_disco_endDate = "$Dz[1]";	
		
//////////////////////////////////////////// CREATE COLLECTION RECORD
		
		$scotty = "";
		$dbQuery = "INSERT INTO collections VALUES ";
		$dbQuery .= "(";
		$dbQuery .= "$col_ID, ";
		$dbQuery .= "\"$col_iana_UUID\", ";
		$dbQuery .= "\"$col_dc_identifier\", ";
		$dbQuery .= "\"$col_fb_heldBy\", ";
		$dbQuery .= "\"$col_bf_subLocation\", ";
		$dbQuery .= "\"$col_bf_physicalLocation\", ";
		$dbQuery .= "\"$col_skos_collection\", ";
		$dbQuery .= "\"$col_skos_orderedCollection\", ";
		$dbQuery .= "\"$col_bibo_volume\", ";
		$dbQuery .= "\"$col_disco_startDate\", ";
		$dbQuery .= "\"$col_disco_endDate\"";
		$dbQuery .= "); ";

//////////////////////////////////////////// DO DB		
		
		$mysqli_result = mysqli_query($mysqli_link, $dbQuery);
		$scotty = mysqli_error($mysqli_link);
		if(($scotty)) { 
			echo "Could not add collection \"$Trow[0] $Trow[1] $Trow[2]\" to the database\n\n";
			echo "$scotty\n\n";
			echo "$dbQuery\n\n"; 
			exit;
		} else {
//			echo "Collection Record Created\n\n";
		}

//////////////////////////////////////////// READ FOLDER

		$files = array();
		while($folderEntry=$folder->read()){
			if(preg_match("/.jpg/i","$folderEntry")) {
				$files[] = $folderEntry;
			}
		}
		$folder->close();
		natcasesort($files);

//////////////////////////////////////////// OPEN EACH FILE

		$bibo = 0;
		foreach($files as $f) {
			$scotty = "";
			$file = "$f";
			$folderFile = $folderMain."\\".$file;
			$fileLocation = str_replace("\\","\\\\","$folderFile");
			$file = str_replace(".jpg","","$file");
			$file = str_replace(".JPG","","$file");

//////////////////////////////////////////// MANAGE VARS

			$x++; 
			$bibo = ($bibo + 1);
			$fileAttrib = explode(".","$f");
			$fileName = $fileAttrib[0];
			$fileType = $fileAttrib[1];
			$fileSize =  filesize($folderFile);
			$fileINT = $bibo;
			$fileName = preg_replace("/ \(/i",":","$fileName");
			$fileName = preg_replace("/\)/i","","$fileName");
			$fileName = strtoupper($fileName);
			
			$item_ID = "0";
			$item_iana_UUID = guidv4();
			$item_dc_identifier = time()."_".gen_md5_password();
			$item_col_iana_UUID = $col_iana_UUID;
			$item_col_dc_identifier = $col_dc_identifier;
			$item_dc_references = $col_dc_identifier;
			$item_dc_title = "$fileName";
			$item_bibo_pages = "$fileINT";
			$item_dc_type = "image";
			$item_dc_format = "image/jpeg";
			$item_prism_byteCount = "$fileSize";
			$item_rdf_resource = "$col_skos_orderedCollection"."/"."$QQ"."/"."$f";
			$item_rights_dc_identifer = "";
			$item_dc_creator = "";
			$item_org_FormalOrganisation = "";
			$item_gn_name = "";
			$item_dc_created = "";
			$item_dc_description = "";
			$item_dct_accessRights = "";
			$item_marc_addressee = "";
			$item_rdaa_groupMemberOf = "";
			$item_mads_associatedLocale = "";

//////////////////////////////////////////// INSERT ITEM			
			
			$dbQuery = "INSERT INTO items VALUES ";
			$dbQuery .= "(";
			$dbQuery .= "$item_ID, ";
			$dbQuery .= "\"$item_iana_UUID\", ";
			$dbQuery .= "\"$item_dc_identifier\", ";
			$dbQuery .= "\"$item_col_iana_UUID\", ";
			$dbQuery .= "\"$item_col_dc_identifier\", ";
			$dbQuery .= "\"$item_dc_references\", ";
			$dbQuery .= "\"$item_dc_title\", ";
			$dbQuery .= "\"$item_bibo_pages\", ";
			$dbQuery .= "\"$item_dc_type\", ";
			$dbQuery .= "\"$item_dc_format\", ";
			$dbQuery .= "\"$item_prism_byteCount\", ";
			$dbQuery .= "\"$item_rdf_resource\", ";
			$dbQuery .= "\"$item_rights_dc_identifer\", ";
			$dbQuery .= "\"$item_dc_creator\", ";
			$dbQuery .= "\"$item_org_FormalOrganisation\", ";
			$dbQuery .= "\"$item_gn_name\", ";
			$dbQuery .= "\"$item_dc_created\", ";
			$dbQuery .= "\"$item_dc_description\", ";
			$dbQuery .= "\"$item_dct_accessRights\", ";
			$dbQuery .= "\"$item_marc_addressee\", ";
			$dbQuery .= "\"$item_rdaa_groupMemberOf\", ";
			$dbQuery .= "\"$item_mads_associatedLocale\"";
			$dbQuery .= "); ";

//////////////////////////////////////////// DO DB			
			
			$mysqli_result = mysqli_query($mysqli_link, $dbQuery);
			$scotty = mysqli_error($mysqli_link);
			if(($scotty)) { 
				echo "Could not add item \"$item_dc_title\" to the database\n\n";
				echo "$scotty\n\n";
				echo "$dbQuery\n\n"; 
				exit;
			}

		}
		
//////////////////////////////////////////// CLEANUP		
		
		$finish = time();
		$finish = ($finish - $start);
		echo "$x Document Records Created : $finish seconds\n\n";

//////////////////////////////////////////// FINISH

	}

	include("ar.dbdisconnect.php");
	echo "\n\n\n\n";

?>