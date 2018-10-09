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
//	17 May 2017
//	22-23 May 2017
//	30 May 2017
//	22 June 2017
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
		$dc_identifier = $_GET["dc_identifier"];
		$type = $_GET["type"];
		$format = $_GET["format"];
		$_GET = array();
		$_POST = array();
	} else {
		$dc_identifier = $randIMG;
		$type = "jpg";
	}
	
///////////////////////////////////////////////////////////// RDF Export Type	
	
	$doFile = "y";
	$doPrefix = "n";
	if(($format == "rdfa")) {
		$doPrefix = "y";
	}
	
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// Get JPG Details

	if(($type == "jpg")) {
		$queryD = "SELECT * FROM items WHERE dc_identifier = \"$dc_identifier\" ";
		$mysqli_resultD = mysqli_query($mysqli_link, $queryD);
		while($rowD = mysqli_fetch_row($mysqli_resultD)) { 
			$this_dc_references = $rowD[5];
			$this_dc_title = $rowD[6];
			$this_page = $rowD[7];
			$this_dc_creator = $rowD[13];
			$this_org_formalOrganisation = $rowD[14];
			$this_dc_created = $rowD[16];
			$this_restricted = $rowD[18];
			$this_marc_addressee = $rowD[19];
			$this_rdaa_groupMemberOf = $rowD[20];
			$rdf_resource = $rowD[11];
			$item_found = "y";
		}
	}
	
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// Get CSV Details

	if(($type == "other")) {
		$p = 0;
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
			$phpBase = "https://".$_SERVER["HTTP_HOST"].$phpSelf."item/".$this_dc_identifier."";
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
					$item_level .= "\"iana:UUID\"";
					$item_level .= "\n";
					$p++;
				}

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
						$queryH = "SELECT value_string FROM annotations WHERE dc_references = \"$this_dc_identifier\" AND rdfs_label = \"$C\" ORDER BY value_string ASC";
						$mysqli_resultH = mysqli_query($mysqli_link, $queryH);
						while($rowH = mysqli_fetch_row($mysqli_resultH)) {
							$temp .= "$rowH[0]; ";
						}
						$temp = preg_replace("/\"/","'","$temp");
						$item_level .= "\"$temp\",";
					}
				}
				$item_level .= "\"$this_dc_UUID\"";
				$item_level .= "\n";
			}		
		}
		$xml = $item_level;
	}
	
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// Get RDF Details	
	
	if(($type == "rdf")) {
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
			$rdf_found = "y";
			
///////////////////////////////////////////////////////////// Generate RDF+XML and RDFa Item Data			
			
			if((($format == "rdf") or ($format == "rdfa"))) {
				$item_level = "\t\t<dc:title>$this_dc_title</dc:title>\n";
				$item_level .= "\t\t<dc:identifier>$this_dc_identifier</dc:identifier>\n";
				if(($this_dc_created != "")) { $item_level .= "\t\t<dc:created>$this_dc_created</dc:created>\n"; }
				if(($this_dc_format != "")) { $item_level .= "\t\t<dc:format>$this_dc_format</dc:format>\n"; }
				if(($this_prism_byteCount  != "")) { $item_level .= "\t\t<prism:byteCount>$this_prism_byteCount</prism:byteCount>\n"; }
				if(($this_dc_creator != "")) { $item_level .= "\t\t<dc:creator>$this_dc_creator</dc:creator>\n"; }
				if(($this_org_formalOrganisation != "")) { $item_level .= "\t\t<org:formalOrganisation>$this_org_formalOrganisation</org:formalOrganisation>\n"; }
				if(($this_gn_name != "")) { $item_level .= "\t\t<gn:name>$this_gn_name</gn:name>\n"; }
				if(($this_marc_addressee != "")) { $item_level .= "\t\t<marc:addressee>$this_marc_addressee</marc:addressee>\n"; }
				if(($this_rdaa_groupMemberOf != "")) { $item_level .= "\t\t<rdaa:groupMemberOf>$this_rdaa_groupMemberOf</rdaa:groupMemberOf>\n"; }
				if(($this_mads_associatedLocale != "")) { $item_level .= "\t\t<mads:associatedLocale>$this_mads_associatedLocale</mads:associatedLocale>\n"; }
			}
			
///////////////////////////////////////////////////////////// Generate Turtle Item Data		

			if((($format == "turtle"))) {
				$item_level = " dc:title \"$this_dc_title\" ;\n";
				$item_level .= " dc:identifier \"$this_dc_identifier\" ;\n";
				if(($this_dc_created != "")) { $item_level .= " dc:created \"$this_dc_created\" ;\n"; }
				if(($this_dc_format != "")) { $item_level .= " dc:format \"$this_dc_format\" ;\n"; }
				if(($this_prism_byteCount  != "")) { $item_level .= " prism:byteCount \"$this_prism_byteCount\" ;\n"; }
				if(($this_dc_creator != "")) { $item_level .= " dc:creator \"$this_dc_creator\" ;\n"; }
				if(($this_org_formalOrganisation != "")) { $item_level .= " org:formalOrganisation \"$this_org_formalOrganisation\" ;\n"; }
				if(($this_gn_name != "")) { $item_level .= " gn:name \"$this_gn_name\" ;\n"; }
				if(($this_marc_addressee != "")) { $item_level .= " marc:addressee \"$this_marc_addressee\" ;\n"; }
				if(($this_rdaa_groupMemberOf != "")) { $item_level .= " rdaa:groupMemberOf \"$this_rdaa_groupMemberOf\" ;\n"; }
				if(($this_mads_associatedLocale != "")) { $item_level .= " mads:associatedLocale \"$this_mads_associatedLocale\" ;\n"; }
			}	
			
///////////////////////////////////////////////////////////// Generate N-Triples Data

			if((($format == "ntriples"))) {
				$rdfArray = array();
				$rdfArray[] = "dc|title|$this_dc_title";
				$rdfArray[] = "dc|identifier|$this_dc_identifier";
				$phpSelf = preg_replace("/data_download.php/i","",$_SERVER["PHP_SELF"]);
				$phpBase = "<https://".$_SERVER["HTTP_HOST"].$phpSelf."item/".$dc_identifier."> ";
				if(($this_dc_created != "")) { $rdfArray[] = "dc|created|$this_dc_created"; }
				if(($this_dc_format != "")) { $rdfArray[] = "dc|format|$this_dc_format"; }
				if(($this_prism_byteCount  != "")) { $rdfArray[] = "prism|byteCount|$this_prism_byteCount"; }
				if(($this_dc_creator != "")) { $rdfArray[] = "dc|creator|$this_dc_creator"; }
				if(($this_org_formalOrganisation != "")) { $rdfArray[] = "org|formalOrganisation|$this_org_formalOrganisation"; }
				if(($this_gn_name != "")) { $rdfArray[] = "gn|name|$this_gn_name"; }
				if(($this_marc_addressee != "")) { $rdfArray[] = "marc|addressee|$this_marc_addressee"; }
				if(($this_rdaa_groupMemberOf != "")) { $rdfArray[] = "rdaa|groupMemberOf|$this_rdaa_groupMemberOf"; }
				if(($this_mads_associatedLocale != "")) { $rdfArray[] = "mads|associatedLocale|$this_mads_associatedLocale"; }
			}
			
///////////////////////////////////////////////////////////// Close While Loop			
				
		}
		
///////////////////////////////////////////////////////////// Generate N-Triples Mentions and Prefixes Data	
		
		if(($rdf_found == "y") && (($format == "ntriples"))) {
			$xml = "";
			$queryD = "SELECT reg_uri, rdfs_label, value_string FROM annotations WHERE dc_references = \"$dc_identifier\" ";
			$mysqli_resultD = mysqli_query($mysqli_link, $queryD);
			while($rowD = mysqli_fetch_row($mysqli_resultD)) {
				$rdfArray[] = "$rowD[0]|$rowD[1]|$rowD[2]";
			}
			$rdfArray = array_unique($rdfArray, SORT_REGULAR);
			foreach($rdfArray as $rA) {
				$ras = explode("|","$rA");
				$queryD = "SELECT * FROM namespaces WHERE reg_uri = \"$ras[0]\" ";
				$mysqli_resultD = mysqli_query($mysqli_link, $queryD);
				while($rowD = mysqli_fetch_row($mysqli_resultD)) {
					if(!preg_match("/\#/",$rowD)) {
						$rowD[2] = rtrim($rowD[2], '/') . '/';
					}
					$xml .= $phpBase." <".$rowD[2].$ras[1]."> \"$ras[2]\" .\n";
				}
			}
		}
	
///////////////////////////////////////////////////////////// Generate RDF+XML or RDFa Mentions and Prefixes Data	
	
		if(($rdf_found == "y") && (($format == "rdf") or ($format == "rdfa"))) {
			$xml = "";
			$rdfArray = array();
			$xml = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
			$xml .= "<rdf:RDF";
			if(($doPrefix == "y")) {
				$xml .= "\n";
			}
			$queryD = "SELECT reg_uri, rdfs_label, value_string FROM annotations WHERE dc_references = \"$dc_identifier\" ";
			$mysqli_resultD = mysqli_query($mysqli_link, $queryD);
			while($rowD = mysqli_fetch_row($mysqli_resultD)) {
				$rdfArray[] = "$rowD[0]|$rowD[1]|$rowD[2]";
			}
			$rdfArray[] = "bibo||";
			$rdfArray[] = "gn||";
			$rdfArray[] = "mads||";
			$rdfArray[] = "marc||";
			$rdfArray[] = "org||";
			$rdfArray[] = "prism||";
			$rdfArray[] = "rdaa||";
			$rdfArray[] = "rdf||";
			sort($rdfArray);
			$xmls = array();
			$valueStrings = array();
			foreach($rdfArray as $rA) {
				$ras = explode("|","$rA");
				if(($ras[1] != "") && ($ras[2] != "")) {
					$valueStrings[] = "\t\t<".$ras[0].":".$ras[1].">".$ras[2]."</".$ras[0].":".$ras[1].">\n";
				}
				$queryD = "SELECT * FROM namespaces WHERE reg_uri = \"$ras[0]\" ";
				$mysqli_resultD = mysqli_query($mysqli_link, $queryD);
				while($rowD = mysqli_fetch_row($mysqli_resultD)) {
					if(($doPrefix == "y")) {
						$xmls[] = "\t@prefix ".$rowD[1]."=\"".$rowD[2]."\"\n";
					} else {
						$xmls[] = " xmlns:".$rowD[1]."=\"".$rowD[2]."\"";
					}
				}
			}
			$xmls = array_unique($xmls);
			sort($xmls);
			foreach($xmls as $xs) {
				$xml .= $xs;	
			}
			$xml .= ">\n";
			$phpSelf = preg_replace("/data_download.php/i","",$_SERVER["PHP_SELF"]);
			$xml .= "\t<rdf:Description rdf:about=\"https://".$_SERVER["HTTP_HOST"].$phpSelf."item/".$dc_identifier."\">\n";
			$xml .= $item_level;
			foreach($valueStrings as $vs) {
				$xml .= $vs;	
			}
			$xml .= "\t</rdf:Description>\n";
			$xml .= "</rdf:RDF>";
		}
		
///////////////////////////////////////////////////////////// Generate Turtle Prefixes and Mentions Data		
		
		if(($rdf_found == "y") && (($format == "turtle"))) {
			$xml = "";
			$rdfArray = array();
			$queryD = "SELECT reg_uri, rdfs_label, value_string FROM annotations WHERE dc_references = \"$dc_identifier\" ";
			$mysqli_resultD = mysqli_query($mysqli_link, $queryD);
			while($rowD = mysqli_fetch_row($mysqli_resultD)) {
				$rdfArray[] = "$rowD[0]|$rowD[1]|$rowD[2]";
			}
			$rdfArray[] = "bibo||";
			$rdfArray[] = "gn||";
			$rdfArray[] = "mads||";
			$rdfArray[] = "marc||";
			$rdfArray[] = "org||";
			$rdfArray[] = "prism||";
			$rdfArray[] = "rdaa||";
			$rdfArray[] = "rdf||";
			sort($rdfArray);
			$xmls = array();
			$valueStrings = array();
			foreach($rdfArray as $rA) {
				$ras = explode("|","$rA");
				if(($ras[1] != "") && ($ras[2] != "")) {
					$valueStrings[] = " ".$ras[0].":".$ras[1]." \"".$ras[2]."\" ;\n";
				}
				$queryD = "SELECT * FROM namespaces WHERE reg_uri = \"$ras[0]\" ";
				$mysqli_resultD = mysqli_query($mysqli_link, $queryD);
				while($rowD = mysqli_fetch_row($mysqli_resultD)) {
					$xmls[] = "@prefix ".$rowD[1].": <".$rowD[2]."> .\n";
				}
			}
			$xmls = array_unique($xmls);
			sort($xmls);
			foreach($xmls as $xs) {
				$xml .= $xs;	
			}
			$phpSelf = preg_replace("/data_download.php/i","",$_SERVER["PHP_SELF"]);
			$xml .= "\n<https://".$_SERVER["HTTP_HOST"].$phpSelf."item/".$dc_identifier.">\n";
			$xml .= $item_level;
			foreach($valueStrings as $vs) {
				$xml .= $vs;	
			}
		}
		
///////////////////////////////////////////////////////////// Close Get RDF Details Loop		
		
	}
	
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// Download File	
	
	if(($item_found == "y") && ($type == "jpg")) {
		$file_parts = explode("/","$rdf_resource");
		$file_parts = array_reverse($file_parts);
		$file = "./data/items/".$file_parts[0];
		if(file_exists($file)){
			$downloadFile = new Downloader();
			$downloadFile->__construct($file_parts[0]);
			$downloadFile->download_file($file);
		} else {
			die;	
		}
	} elseif(($rdf_found == "y") && ($type == "rdf") && ($xml != "")) {
		$mtime = time();
		$file_parts = explode("/","$rdf_resource");
		$file_parts = array_reverse($file_parts);
		$rdf_parts = explode(".","$file_parts[0]");
		if(($format == "rdf")) {
			$rdf_file = "item_".$dc_identifier.".xml";
		}
		if(($format == "rdfa")) {
			$rdf_file = "item_".$dc_identifier.".rdf";
		}
		if(($format == "turtle")) {
			$rdf_file = "item_".$dc_identifier.".ttl";
		}
		if(($format == "ntriples")) {
			$rdf_file = "item_".$dc_identifier.".nt";
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
	} elseif(($rdf_found == "y") && ($type == "other") && ($xml != "")) {	
		$mtime = time();
		$file_parts = explode("/","$rdf_resource");
		$file_parts = array_reverse($file_parts);
		$rdf_parts = explode(".","$file_parts[0]");
		if(($format == "csv")) {
			$rdf_file = "item_".$dc_identifier.".csv";
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