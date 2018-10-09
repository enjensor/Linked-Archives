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
//  9-12 January 2017
//  14-15 January 2017
//  9 February 2017
//  22-24 February 2017
//  27-28 February 2017
//  1-2 March 2017
//  14 March 2017
//  3 April 2017
//  18-21 April 2017
//  26 April 2017
//	2-4 May 2017
//	10-11 May 2017
//	23 May 2017
//	25 May 2017
//	30 May 2017
//	22 June 2017
//	30 June 2017
//	7 July 2017
//	10-13 August 2018
//
//
/////////////////////////////////////////////////////////// Set reload var	
	
	if(($_GET["reload"] != "")) { 
		$reload = $_GET["reload"]; 
	}
	if(($reload == "view")) {
		$dc_identifier = $view_metadata;
	} else {
		$dc_identifier = $_GET["dc_identifier"];
	}
	if(($reload == "") or ($reload == "yes")) {
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
	}
	
/////////////////////////////////////////////////////////// Clean post and get	
	
	$action = $_GET["action"];
	$data_dc_identifier = $_GET["data_dc_identifier"];
	$data_dc_creator = $_GET["data_dc_creator"];
	$data_gn_name = $_GET["data_gn_name"];
	$data_org_FormalOrganisation = $_GET["data_org_FormalOrganisation"];
	$data_dc_description = $_GET["data_dc_description"];
	$data_dc_created = $_GET["data_dc_created"];
	$data_marc_addressee = $_GET["data_marc_addressee"];
	$data_rdaa_groupMemberOf = $_GET["data_rdaa_groupMemberOf"];
	$data_mads_associatedLocale = $_GET["data_mads_associatedLocale"];
	$itemFields = array();
	$fields = array();
	$a = "0";
	$mentions_action = $_GET["mentions_action"];
	$mentions_uri = $_GET["mentions_uri"];
	$mentions_label = $_GET["mentions_label"];
	$mentions_value = $_GET["mentions_value"];
	$mentions_dc_identifier = $_GET["mentions_dc_identifier"];
	$mentions_prior_dc_identifier = $_GET["mentions_prior_dc_identifier"];
	$contributor = "contrib41T71U4BZZ";
	$_GET = array();
	$_POST = array();
	
///////////////////////////////////////////////////////////// OCR Status

	$do_OCR = "off";
	
///////////////////////////////////////////////////////////// Start AJAX Load	
	
	if(($reload == "") or ($reload == "view")) {
		
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// Add Prior Individual Mention

		if(($mentions_action == "SAVE_MENTION") && ($mentions_dc_identifier != "") && ($_SESSION["administrator"] == "yes")) {		
			$new_iana_UUID = guidv4();
			$characters = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
			$new_iana = "";
			for ($i = 0; $i < 12; $i++) {
				$new_iana .= $characters[mt_rand(0, 36)];
			}	
			$foundExisting = "";
			$queryB = "SELECT * FROM annotations WHERE ";
			$queryB .= "dc_references = \"$mentions_dc_identifier\" AND ";
			$queryB .= "reg_uri = \"$mentions_uri\" AND ";
			$queryB .= "rdfs_label = \"$mentions_label\" AND ";
			$queryB .= "value_string = \"$mentions_value\" ";
			$mysqli_resultB = mysqli_query($mysqli_link, $queryB);
			while($rowB = mysqli_fetch_row($mysqli_resultB)) {
				$foundExisting = "y";
			}
			if(($foundExisting != "y")) {
				$queryD = "INSERT INTO annotations VALUES (";
				$queryD .= "\"0\", ";
				$queryD .= "\"$new_iana_UUID\", ";
				$queryD .= "\"".time()."_".$new_iana."\", ";
				$queryD .= "\"$mentions_dc_identifier\", ";
				$queryD .= "\"$mentions_dc_identifier\", ";
				$queryD .= "\"$mentions_uri\", ";
				$queryD .= "\"$mentions_label\", ";
				$queryD .= "\"$mentions_value\", ";
				$queryD .= "\"\", ";
				$queryD .= "\"\", ";
				$queryD .= "\"$contributor\", ";
				$queryD .= "NOW() ";
				$queryD .= ");";
				$mysqli_resultD = mysqli_query($mysqli_link, $queryD);
				$dc_identifier = $mentions_dc_identifier;
			}
		}
		
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// Add Prior Multiple Mentions
		
		if(($mentions_action == "SAVE_MULTIPLE_MENTIONS") && ($mentions_prior_dc_identifier != "") && ($mentions_dc_identifier != "") && ($_SESSION["administrator"] == "yes")) {	
			$queryA = "SELECT reg_uri, rdfs_label, value_string FROM annotations WHERE dc_references = \"$mentions_prior_dc_identifier\" ORDER BY reg_uri ASC";
			$mysqli_resultA = mysqli_query($mysqli_link, $queryA);
			while($rowA = mysqli_fetch_row($mysqli_resultA)) {
				$reg_uri = $rowA[0];
				$rdfs_label = $rowA[1];
				$value_string = $rowA[2];
				$foundExisting = "";
				$queryB = "SELECT * FROM annotations WHERE ";
				$queryB .= "dc_references = \"$mentions_dc_identifier\" AND ";
				$queryB .= "reg_uri = \"$reg_uri\" AND ";
				$queryB .= "rdfs_label = \"$rdfs_label\" AND ";
				$queryB .= "value_string = \"$value_string\" ";
				$mysqli_resultB = mysqli_query($mysqli_link, $queryB);
				while($rowB = mysqli_fetch_row($mysqli_resultB)) {
					$foundExisting = "y";
				}
				if(($foundExisting != "y")) {
					$new_iana_UUID = guidv4();
					$characters = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
					$new_iana = "";
					for ($i = 0; $i < 12; $i++) {
						$new_iana .= $characters[mt_rand(0, 36)];
					}
					$queryD = "INSERT INTO annotations VALUES (";
					$queryD .= "\"0\", ";
					$queryD .= "\"$new_iana_UUID\", ";
					$queryD .= "\"".time()."_".$new_iana."\", ";
					$queryD .= "\"$mentions_dc_identifier\", ";
					$queryD .= "\"$mentions_dc_identifier\", ";
					$queryD .= "\"$reg_uri\", ";
					$queryD .= "\"$rdfs_label\", ";
					$queryD .= "\"$value_string\", ";
					$queryD .= "\"\", ";
					$queryD .= "\"\", ";
					$queryD .= "\"$contributor\", ";
					$queryD .= "NOW() ";
					$queryD .= ");";
					$mysqli_resultD = mysqli_query($mysqli_link, $queryD);
				}
			}
			$dc_identifier = $mentions_dc_identifier;
		}
		
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// If Update

		if(($action == "UPDATE") && ($data_dc_identifier != "") && ($_SESSION["administrator"] == "yes")) {
			
			if(($data_dc_creator == "Nothing Specified")) { $data_dc_creator = ""; }
			if(($data_gn_name == "Nothing Specified")) { $data_gn_name = ""; }
			if(($data_org_FormalOrganisation == "Nothing Specified")) { $data_org_FormalOrganisation = ""; }
			if(($data_dc_description == "Nothing Specified")) { $data_dc_description = ""; }
			if(($data_marc_addressee == "Nothing Specified")) { $data_marc_addressee = ""; }
			if(($data_rdaa_groupMemberOf == "Nothing Specified")) { $data_rdaa_groupMemberOf = ""; }
			if(($data_mads_associatedLocale == "Nothing Specified")) { $data_mads_associatedLocale = ""; }
			
			$queryD = "UPDATE items SET ";
			$queryD .= "dc_creator = \"$data_dc_creator\", ";
			$queryD .= "gn_name = \"$data_gn_name\", ";
			$queryD .= "org_FormalOrganisation = \"$data_org_FormalOrganisation\", ";
			$queryD .= "dc_description = \"$data_dc_description\", ";
			$queryD .= "marc_addressee = \"$data_marc_addressee\", ";
			$queryD .= "rdaa_groupMemberOf = \"$data_rdaa_groupMemberOf\", ";
			$queryD .= "mads_associatedLocale = \"$data_mads_associatedLocale\", ";	
			$queryD .= "dc_created = \"$data_dc_created\" ";
			$queryD .= "WHERE dc_identifier = \"$data_dc_identifier\"; ";
			$mysqli_resultD = mysqli_query($mysqli_link, $queryD);

///////////////////////////////////////// Create Mention for dc_creator
			
			if(($data_dc_creator != "")) {
				$foundExisting = "";
				$queryB = "SELECT * FROM annotations WHERE ";
				$queryB .= "dc_references = \"$data_dc_identifier\" AND ";
				$queryB .= "reg_uri = \"foaf\" AND ";
				$queryB .= "rdfs_label = \"person\" AND ";
				$queryB .= "value_string = \"$data_dc_creator\" ";
				$mysqli_resultB = mysqli_query($mysqli_link, $queryB);
				while($rowB = mysqli_fetch_row($mysqli_resultB)) {
					$foundExisting = "y";
				}
				if(($foundExisting != "y")) {
					$new_iana_UUID = guidv4();
					$characters = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
					$new_iana = "";
					for ($i = 0; $i < 12; $i++) {
						$new_iana .= $characters[mt_rand(0, 36)];
					}
					$queryD = "INSERT INTO annotations VALUES (";
					$queryD .= "\"0\", ";
					$queryD .= "\"$new_iana_UUID\", ";
					$queryD .= "\"".time()."_".$new_iana."\", ";
					$queryD .= "\"$data_dc_identifier\", ";
					$queryD .= "\"$data_dc_identifier\", ";
					$queryD .= "\"foaf\", ";
					$queryD .= "\"person\", ";
					$queryD .= "\"$data_dc_creator\", ";
					$queryD .= "\"\", ";
					$queryD .= "\"\", ";
					$queryD .= "\"$contributor\", ";
					$queryD .= "NOW() ";
					$queryD .= ");";
					$mysqli_resultD = mysqli_query($mysqli_link, $queryD);
				}
			}
			
///////////////////////////////////////// Create Mention for gn_name

			if(($data_gn_name != "")) {
				$foundExisting = "";
				$queryB = "SELECT * FROM annotations WHERE ";
				$queryB .= "dc_references = \"$data_dc_identifier\" AND ";
				$queryB .= "reg_uri = \"cerl\" AND ";
				$queryB .= "rdfs_label = \"geographicNote\" AND ";
				$queryB .= "value_string = \"$data_gn_name\" ";
				$mysqli_resultB = mysqli_query($mysqli_link, $queryB);
				while($rowB = mysqli_fetch_row($mysqli_resultB)) {
					$foundExisting = "y";
				}
				if(($foundExisting != "y")) {
					$new_iana_UUID = guidv4();
					$characters = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
					$new_iana = "";
					for ($i = 0; $i < 12; $i++) {
						$new_iana .= $characters[mt_rand(0, 36)];
					}
					$queryD = "INSERT INTO annotations VALUES (";
					$queryD .= "\"0\", ";
					$queryD .= "\"$new_iana_UUID\", ";
					$queryD .= "\"".time()."_".$new_iana."\", ";
					$queryD .= "\"$data_dc_identifier\", ";
					$queryD .= "\"$data_dc_identifier\", ";
					$queryD .= "\"cerl\", ";
					$queryD .= "\"geographicNote\", ";
					$queryD .= "\"$data_gn_name\", ";
					$queryD .= "\"\", ";
					$queryD .= "\"\", ";
					$queryD .= "\"$contributor\", ";
					$queryD .= "NOW() ";
					$queryD .= ");";
					$mysqli_resultD = mysqli_query($mysqli_link, $queryD);
				}
			}

///////////////////////////////////////// Create Mention for org_FormalOrganisation		

			if(($data_org_FormalOrganisation != "")) {
				$foundExisting = "";
				$queryB = "SELECT * FROM annotations WHERE ";
				$queryB .= "dc_references = \"$data_dc_identifier\" AND ";
				$queryB .= "reg_uri = \"org\" AND ";
				$queryB .= "rdfs_label = \"formalOrganisation\" AND ";
				$queryB .= "value_string = \"$data_org_FormalOrganisation\" ";
				$mysqli_resultB = mysqli_query($mysqli_link, $queryB);
				while($rowB = mysqli_fetch_row($mysqli_resultB)) {
					$foundExisting = "y";
				}
				if(($foundExisting != "y")) {
					$new_iana_UUID = guidv4();
					$characters = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
					$new_iana = "";
					for ($i = 0; $i < 12; $i++) {
						$new_iana .= $characters[mt_rand(0, 36)];
					}
					$queryD = "INSERT INTO annotations VALUES (";
					$queryD .= "\"0\", ";
					$queryD .= "\"$new_iana_UUID\", ";
					$queryD .= "\"".time()."_".$new_iana."\", ";
					$queryD .= "\"$data_dc_identifier\", ";
					$queryD .= "\"$data_dc_identifier\", ";
					$queryD .= "\"org\", ";
					$queryD .= "\"formalOrganisation\", ";
					$queryD .= "\"$data_org_FormalOrganisation\", ";
					$queryD .= "\"\", ";
					$queryD .= "\"\", ";
					$queryD .= "\"$contributor\", ";
					$queryD .= "NOW() ";
					$queryD .= ");";
					$mysqli_resultD = mysqli_query($mysqli_link, $queryD);
				}
			}

///////////////////////////////////////// Create Mention for marc_addressee

			if(($data_marc_addressee != "")) {
				$foundExisting = "";
				$queryB = "SELECT * FROM annotations WHERE ";
				$queryB .= "dc_references = \"$data_dc_identifier\" AND ";
				$queryB .= "reg_uri = \"foaf\" AND ";
				$queryB .= "rdfs_label = \"person\" AND ";
				$queryB .= "value_string = \"$data_marc_addressee\" ";
				$mysqli_resultB = mysqli_query($mysqli_link, $queryB);
				while($rowB = mysqli_fetch_row($mysqli_resultB)) {
					$foundExisting = "y";
				}
				if(($foundExisting != "y")) {
					$new_iana_UUID = guidv4();
					$characters = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
					$new_iana = "";
					for ($i = 0; $i < 12; $i++) {
						$new_iana .= $characters[mt_rand(0, 36)];
					}
					$queryD = "INSERT INTO annotations VALUES (";
					$queryD .= "\"0\", ";
					$queryD .= "\"$new_iana_UUID\", ";
					$queryD .= "\"".time()."_".$new_iana."\", ";
					$queryD .= "\"$data_dc_identifier\", ";
					$queryD .= "\"$data_dc_identifier\", ";
					$queryD .= "\"foaf\", ";
					$queryD .= "\"person\", ";
					$queryD .= "\"$data_marc_addressee\", ";
					$queryD .= "\"\", ";
					$queryD .= "\"\", ";
					$queryD .= "\"$contributor\", ";
					$queryD .= "NOW() ";
					$queryD .= ");";
					$mysqli_resultD = mysqli_query($mysqli_link, $queryD);
				}
			}

///////////////////////////////////////// Create Mention for rdaa_groupMemberOf

			if(($data_rdaa_groupMemberOf != "")) {
				$foundExisting = "";
				$queryB = "SELECT * FROM annotations WHERE ";
				$queryB .= "dc_references = \"$data_dc_identifier\" AND ";
				$queryB .= "reg_uri = \"org\" AND ";
				$queryB .= "rdfs_label = \"formalOrganisation\" AND ";
				$queryB .= "value_string = \"$data_rdaa_groupMemberOf\" ";
				$mysqli_resultB = mysqli_query($mysqli_link, $queryB);
				while($rowB = mysqli_fetch_row($mysqli_resultB)) {
					$foundExisting = "y";
				}
				if(($foundExisting != "y")) {
					$new_iana_UUID = guidv4();
					$characters = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
					$new_iana = "";
					for ($i = 0; $i < 12; $i++) {
						$new_iana .= $characters[mt_rand(0, 36)];
					}
					$queryD = "INSERT INTO annotations VALUES (";
					$queryD .= "\"0\", ";
					$queryD .= "\"$new_iana_UUID\", ";
					$queryD .= "\"".time()."_".$new_iana."\", ";
					$queryD .= "\"$data_dc_identifier\", ";
					$queryD .= "\"$data_dc_identifier\", ";
					$queryD .= "\"org\", ";
					$queryD .= "\"formalOrganisation\", ";
					$queryD .= "\"$data_rdaa_groupMemberOf\", ";
					$queryD .= "\"\", ";
					$queryD .= "\"\", ";
					$queryD .= "\"$contributor\", ";
					$queryD .= "NOW() ";
					$queryD .= ");";
					$mysqli_resultD = mysqli_query($mysqli_link, $queryD);
				}
			}

///////////////////////////////////////// Create Mention for mads_associatedLocale
			
			if(($data_mads_associatedLocale != "")) {
				$foundExisting = "";
				$queryB = "SELECT * FROM annotations WHERE ";
				$queryB .= "dc_references = \"$data_dc_identifier\" AND ";
				$queryB .= "reg_uri = \"cerl\" AND ";
				$queryB .= "rdfs_label = \"geographicNote\" AND ";
				$queryB .= "value_string = \"$data_mads_associatedLocale\" ";
				$mysqli_resultB = mysqli_query($mysqli_link, $queryB);
				while($rowB = mysqli_fetch_row($mysqli_resultB)) {
					$foundExisting = "y";
				}
				if(($foundExisting != "y")) {
					$new_iana_UUID = guidv4();
					$characters = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
					$new_iana = "";
					for ($i = 0; $i < 12; $i++) {
						$new_iana .= $characters[mt_rand(0, 36)];
					}
					$queryD = "INSERT INTO annotations VALUES (";
					$queryD .= "\"0\", ";
					$queryD .= "\"$new_iana_UUID\", ";
					$queryD .= "\"".time()."_".$new_iana."\", ";
					$queryD .= "\"$data_dc_identifier\", ";
					$queryD .= "\"$data_dc_identifier\", ";
					$queryD .= "\"cerl\", ";
					$queryD .= "\"geographicNote\", ";
					$queryD .= "\"$data_mads_associatedLocale\", ";
					$queryD .= "\"\", ";
					$queryD .= "\"\", ";
					$queryD .= "\"$contributor\", ";
					$queryD .= "NOW() ";
					$queryD .= ");";
					$mysqli_resultD = mysqli_query($mysqli_link, $queryD);
				}
			}		
		}
	
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// Get Metadata

		$queryD = "SHOW COLUMNS FROM items";
		$mysqli_resultD = mysqli_query($mysqli_link, $queryD);
		while($rowD = mysqli_fetch_row($mysqli_resultD)) {
			$fields[] = $rowD[0];
		}

		$queryD = "SELECT * FROM items WHERE dc_identifier = \"$dc_identifier\" ";
		$mysqli_resultD = mysqli_query($mysqli_link, $queryD);
		while($rowD = mysqli_fetch_row($mysqli_resultD)) { 
			foreach($fields as $f) {
				$itemFields["$f"] = $rowD[$a];
				$a++;
			}
			$item_found = "y";
		}

///////////////////////////////////////////////////////////// Display Metadata
	
		if(($_SESSION["administrator"] != "yes")) {
			$itemFields["dct_accessRights"] = "restricted";
		}
		if(($item_found == "y")) {

///////////////////////////////////////////////////////////// Collection Metadata

?>  
            <div class="panel" style="background-color: #FFFFFF; border: 1px solid #27A59B;">
                <div class="panel-heading">
					<div class="panel-control">
					<?php

						if(($_SESSION["administrator"] == "yes")) {
							$status = $itemFields["dct_accessRights"];
							if(($status != "restricted")) {
								echo "<a href=\"#\">";
								echo "<i id=\"mLOCK_".$dc_identifier."\" class=\"ion-unlocked mlock-toggle\" ";
								echo "style=\"color:#D93427; font-size: 1.4em;\" ";
								echo "data-status=\"UNLOCKED\" data-id=\"".$dc_identifier."\"></i>";
								echo "</a>";
							} else {
								echo "<a href=\"#\">";
								echo "<i id=\"mLOCK_".$dc_identifier."\" class=\"ion-locked mlock-toggle\" ";
								echo "style=\"color:#68C970; font-size: 1.4em;\" ";
								echo "data-status=\"LOCKED\" data-id=\"".$dc_identifier."\"></i>";
								echo "</a>";
							}
						} else {
							echo "<i id=\"mLOCK_".$dc_identifier."\" class=\"ion-locked\" ";
							echo "style=\"color:#cccccc; font-size: 1.4em;\" ";
							echo "data-status=\"LOCKED\" data-id=\"".$dc_identifier."\"></i>";
						}
						echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
						
					?>	
					</div>
					<h4 class="panel-title"><a name="scrollItems">Item</a></h4>
				</div>
  				<div id="collection-panel-collapse" class="collapse in">
    				<div class="panel-body" style="text-align: justify; font-size: 0.9em;">
                    <?php
					
						echo "<em>";
						$thisPage = $itemFields["bibo_pages"];
					
///////////////////////////////////////////////////////////// Get Collection Details

						$queryD = "SELECT * FROM collections WHERE dc_identifier = \"".$itemFields["collections_dc_identifier"]."\" ";
						$mysqli_resultD = mysqli_query($mysqli_link, $queryD);
						while($rowD = mysqli_fetch_row($mysqli_resultD)) { 
							$col_UUID = $rowD[2];
							$col_bf_heldBy	= $rowD[3];
							$col_bf_subLocation = $rowD[4];
							$col_bf_physicalLocation = $rowD[5];
							$col_skos_Collection = $rowD[6];	
							$col_skos_OrderedCollection = $rowD[7];	
							$col_skos_member = $rowD[8];	
							$col_disco_startDate = $rowD[9];	
							$col_disco_endDate = $rowD[10];
							$col_found = "y";
						}

///////////////////////////////////// Record Viewing of Item			
			
						if(($_SESSION["contributor"] != "")){
							$v_iana_UUID = guidv4();
							$queryV = "INSERT INTO ";
							$queryV .= "audit_viewed ";
							$queryV .= "VALUES (";
							$queryV .= "\"0\", ";
							$queryV .= "\"".$v_iana_UUID."\", ";
							$queryV .= "\"".$itemFields["dc_identifier"]."\", ";
							$queryV .= "\"".$itemFields["collections_iana_UUID"]."\", ";
							$queryV .= "\"".$itemFields["collections_dc_identifier"]."\", ";
							$queryV .= "\"".$itemFields["dc_references"]."\", ";
							$queryV .= "\"".$itemFields["dc_title"]."\", ";
							$queryV .= "\"".$_SESSION["contributor"]."\", ";
							$queryV .= "NOW() ";
							$queryV .= ")";
							$mysqliV = mysqli_query($mysqli_link, $queryV);
						}
							
						if(($col_found == "y")) {
							echo "<strong>$col_skos_Collection<br />";
							echo "$col_skos_OrderedCollection / ";
							echo "$col_skos_member / ";
							echo "$thisPage, ";
							echo "$col_disco_startDate-$col_disco_endDate</strong><br /><br />";
							echo "$col_bf_heldBy, ";
							echo "$col_bf_subLocation, ";
							echo "$col_bf_physicalLocation";
							echo "<br /><br />";
						}			
						echo "</em>";
						
///////////////////////////////////////////////////////////// Get Page Details

						$new_items_dc_identifier = "";
						$thisPage = floor($thisPage);
						if(($thisPage > 99)) {
							$noPages = 7;
						} else {
							$noPages = 8;
						}
						echo "<div class=\"btn-group btn-group-justified\" role=\"group\" aria-label=\"Justified button group\">";	
						$thisCount = 0;
						$queryD = "SELECT COUNT(*) FROM items WHERE dc_references = \"".$itemFields["collections_dc_identifier"]."\" ";
						$mysqli_resultD = mysqli_query($mysqli_link, $queryD);
						while($rowD = mysqli_fetch_row($mysqli_resultD)) {
							$thisCount = $rowD[0];
						}						
						$btn = ($thisPage + $noPages);
						if(($thisPage - 5) > 0) { 
							$newPage = ($thisPage - 5); 
							$queryX = "SELECT * FROM items WHERE dc_references = \"".$itemFields["collections_dc_identifier"]."\" AND bibo_pages = \"".$newPage."\"";
							$mysqli_resultX = mysqli_query($mysqli_link, $queryX);
							while($rowX = mysqli_fetch_row($mysqli_resultX)) { 
								$new_items_dc_identifier = $rowX[2];
							}
							if(($newPage < 10)) { 
								$newPage = "0".$newPage; 
							} 
							echo "<div class=\"btn-group\" role=\"group\" style=\"padding-right:1px;\">";
							echo "<a href=\"javascript: ";
		
///////////////////////////////////// Load Image		
		
							echo "var dataE = 'dc_identifier=".$new_items_dc_identifier."';	";		
							echo "var doDivK = $('#doc_detail').fadeOut('fast', function(){ ";
							echo "var doDivP = $('#doc_detail').load('./data_doc.php',dataE, function(){ ";
							echo "var doDivW = $('#doc_detail').fadeIn('slow'); ";
							echo "}); ";
							echo "}); ";
		
///////////////////////////////////// Load Metadata	Editor Panel
		
							echo "var dataF = 'dc_identifier=".$new_items_dc_identifier."&reload=';	";
							echo "var doDivM = $('#titleTags').fadeOut('fast', function(){ ";
							echo "var doDivN = $('#titleTags').load('./data_meta.php',dataF, function(){ ";
							echo "var doDivO = $('#titleTags').fadeIn('slow'); ";
							echo "}); ";
							echo "}); ";
							echo "\">";
							echo "<button class=\"btn btn-warning\">";
							echo "<span style='font-size:0.89em;'>$newPage</span>";
							echo "</button>"; 
							echo "</a>";
							echo "</div>";
							$btn--; 
						}
						if(($thisPage - 4) > 0) { 
							$newPage = ($thisPage - 4); 
							$queryX = "SELECT * FROM items WHERE dc_references = \"".$itemFields["collections_dc_identifier"]."\" AND bibo_pages = \"".$newPage."\"";
							$mysqli_resultX = mysqli_query($mysqli_link, $queryX);
							while($rowX = mysqli_fetch_row($mysqli_resultX)) { 
								$new_items_dc_identifier = $rowX[2];
							}
							if(($newPage < 10)) { 
								$newPage = "0".$newPage; 
							} 
							echo "<div class=\"btn-group\" role=\"group\" style=\"padding-right:1px;\">";
							echo "<a href=\"javascript: ";
		
///////////////////////////////////// Load Image		
		
							echo "var dataE = 'dc_identifier=".$new_items_dc_identifier."';	";		
							echo "var doDivK = $('#doc_detail').fadeOut('fast', function(){ ";
							echo "var doDivP = $('#doc_detail').load('./data_doc.php',dataE, function(){ ";
							echo "var doDivW = $('#doc_detail').fadeIn('slow'); ";
							echo "}); ";
							echo "}); ";
		
///////////////////////////////////// Load Metadata	Editor Panel
		
							echo "var dataF = 'dc_identifier=".$new_items_dc_identifier."&reload=';	";
							echo "var doDivM = $('#titleTags').fadeOut('fast', function(){ ";
							echo "var doDivN = $('#titleTags').load('./data_meta.php',dataF, function(){ ";
							echo "var doDivO = $('#titleTags').fadeIn('slow'); ";
							echo "}); ";
							echo "}); ";
							echo "\">";
							echo "<button class=\"btn btn-warning\">";
							echo "<span style='font-size:0.89em;'>$newPage</span>";
							echo "</button>"; 
							echo "</a>";
							echo "</div>";
							$btn--; 
						}	
						if(($thisPage - 3) > 0) { 
							$newPage = ($thisPage - 3); 
							$queryX = "SELECT * FROM items WHERE dc_references = \"".$itemFields["collections_dc_identifier"]."\" AND bibo_pages = \"".$newPage."\"";
							$mysqli_resultX = mysqli_query($mysqli_link, $queryX);
							while($rowX = mysqli_fetch_row($mysqli_resultX)) { 
								$new_items_dc_identifier = $rowX[2];
							}
							if(($newPage < 10)) { 
								$newPage = "0".$newPage; 
							} 
							echo "<div class=\"btn-group\" role=\"group\" style=\"padding-right:1px;\">";
							echo "<a href=\"javascript: ";
		
///////////////////////////////////// Load Image		
		
							echo "var dataE = 'dc_identifier=".$new_items_dc_identifier."';	";		
							echo "var doDivK = $('#doc_detail').fadeOut('fast', function(){ ";
							echo "var doDivP = $('#doc_detail').load('./data_doc.php',dataE, function(){ ";
							echo "var doDivW = $('#doc_detail').fadeIn('slow'); ";
							echo "}); ";
							echo "}); ";
		
///////////////////////////////////// Load Metadata	Editor Panel
		
							echo "var dataF = 'dc_identifier=".$new_items_dc_identifier."&reload=';	";
							echo "var doDivM = $('#titleTags').fadeOut('fast', function(){ ";
							echo "var doDivN = $('#titleTags').load('./data_meta.php',dataF, function(){ ";
							echo "var doDivO = $('#titleTags').fadeIn('slow'); ";
							echo "}); ";
							echo "}); ";
							echo "\">";
							echo "<button class=\"btn btn-warning\">";
							echo "<span style='font-size:0.89em;'>$newPage</span>";
							echo "</button>"; 
							echo "</a>";
							echo "</div>";
							$btn--; 
						}
						if(($thisPage - 2) > 0) { 
							$newPage = ($thisPage - 2); 
							$queryX = "SELECT * FROM items WHERE dc_references = \"".$itemFields["collections_dc_identifier"]."\" AND bibo_pages = \"".$newPage."\"";
							$mysqli_resultX = mysqli_query($mysqli_link, $queryX);
							while($rowX = mysqli_fetch_row($mysqli_resultX)) { 
								$new_items_dc_identifier = $rowX[2];
							}
							if(($newPage < 10)) { 
								$newPage = "0".$newPage; 
							} 
							echo "<div class=\"btn-group\" role=\"group\" style=\"padding-right:1px;\">";
							echo "<a href=\"javascript: ";
		
///////////////////////////////////// Load Image		
		
							echo "var dataE = 'dc_identifier=".$new_items_dc_identifier."';	";		
							echo "var doDivK = $('#doc_detail').fadeOut('fast', function(){ ";
							echo "var doDivP = $('#doc_detail').load('./data_doc.php',dataE, function(){ ";
							echo "var doDivW = $('#doc_detail').fadeIn('slow'); ";
							echo "}); ";
							echo "}); ";
		
///////////////////////////////////// Load Metadata	Editor Panel
		
							echo "var dataF = 'dc_identifier=".$new_items_dc_identifier."&reload=';	";
							echo "var doDivM = $('#titleTags').fadeOut('fast', function(){ ";
							echo "var doDivN = $('#titleTags').load('./data_meta.php',dataF, function(){ ";
							echo "var doDivO = $('#titleTags').fadeIn('slow'); ";
							echo "}); ";
							echo "}); ";
							echo "\">";
							echo "<button class=\"btn btn-warning\">";
							echo "<span style='font-size:0.89em;'>$newPage</span>";
							echo "</button>"; 
							echo "</a>";
							echo "</div>";
							$btn--; 
						}
						if(($thisPage - 1) > 0) { 
							$newPage = ($thisPage - 1); 
							$queryX = "SELECT * FROM items WHERE dc_references = \"".$itemFields["collections_dc_identifier"]."\" AND bibo_pages = \"".$newPage."\"";
							$mysqli_resultX = mysqli_query($mysqli_link, $queryX);
							while($rowX = mysqli_fetch_row($mysqli_resultX)) { 
								$new_items_dc_identifier = $rowX[2];
							}
							if(($newPage < 10)) { 
								$newPage = "0".$newPage; 
							} 
							echo "<div class=\"btn-group\" role=\"group\" style=\"padding-right:1px;\">";
							echo "<a href=\"javascript: ";
		
///////////////////////////////////// Load Image		
		
							echo "var dataE = 'dc_identifier=".$new_items_dc_identifier."';	";		
							echo "var doDivK = $('#doc_detail').fadeOut('fast', function(){ ";
							echo "var doDivP = $('#doc_detail').load('./data_doc.php',dataE, function(){ ";
							echo "var doDivW = $('#doc_detail').fadeIn('slow'); ";
							echo "}); ";
							echo "}); ";
		
///////////////////////////////////// Load Metadata	Editor Panel
		
							echo "var dataF = 'dc_identifier=".$new_items_dc_identifier."&reload=';	";
							echo "var doDivM = $('#titleTags').fadeOut('fast', function(){ ";
							echo "var doDivN = $('#titleTags').load('./data_meta.php',dataF, function(){ ";
							echo "var doDivO = $('#titleTags').fadeIn('slow'); ";
							echo "}); ";
							echo "}); ";
							echo "\">";
							echo "<button class=\"btn btn-warning\">";
							echo "<span style='font-size:0.89em;'>$newPage</span>";
							echo "</button>"; 
							echo "</a>";
							echo "</div>";
							$btn--; 
						}					
						if(($thisPage < 10)) { 
							$queryX = "SELECT * FROM items WHERE dc_references = \"".$itemFields["collections_dc_identifier"]."\" AND bibo_pages = \"".$thisPage."\"";
							$mysqli_resultX = mysqli_query($mysqli_link, $queryX);
							while($rowX = mysqli_fetch_row($mysqli_resultX)) { 
								$new_items_dc_identifier = $rowX[2];
							}
							$newthisPage = "0".$thisPage; 
							echo "<div class=\"btn-group\" role=\"group\" style=\"padding-right:1px;\">";
							echo "<a href=\"javascript: ";
		
///////////////////////////////////// Load Image		
		
							echo "var dataE = 'dc_identifier=".$new_items_dc_identifier."';	";		
							echo "var doDivK = $('#doc_detail').fadeOut('fast', function(){ ";
							echo "var doDivP = $('#doc_detail').load('./data_doc.php',dataE, function(){ ";
							echo "var doDivW = $('#doc_detail').fadeIn('slow'); ";
							echo "}); ";
							echo "}); ";
		
///////////////////////////////////// Load Metadata	Editor Panel
		
							echo "var dataF = 'dc_identifier=".$new_items_dc_identifier."&reload=';	";
							echo "var doDivM = $('#titleTags').fadeOut('fast', function(){ ";
							echo "var doDivN = $('#titleTags').load('./data_meta.php',dataF, function(){ ";
							echo "var doDivO = $('#titleTags').fadeIn('slow'); ";
							echo "}); ";
							echo "}); ";
							echo "\">";
							echo "<button class=\"btn btn-primary\">";
							echo "<span style='font-size:0.89em;'>$newthisPage</span>";
							echo "</button>";
							echo "</a>";
							echo "</div>";
						} else { 
							$queryX = "SELECT * FROM items WHERE dc_references = \"".$itemFields["collections_dc_identifier"]."\" AND bibo_pages = \"".$thisPage."\"";
							$mysqli_resultX = mysqli_query($mysqli_link, $queryX);
							while($rowX = mysqli_fetch_row($mysqli_resultX)) { 
								$new_items_dc_identifier = $rowX[2];
							}
							$newthisPage = $thisPage; 
							echo "<div class=\"btn-group\" role=\"group\" style=\"padding-right:1px;\">";
							echo "<a href=\"javascript: ";
		
///////////////////////////////////// Load Image		
		
							echo "var dataE = 'dc_identifier=".$new_items_dc_identifier."';	";		
							echo "var doDivK = $('#doc_detail').fadeOut('fast', function(){ ";
							echo "var doDivP = $('#doc_detail').load('./data_doc.php',dataE, function(){ ";
							echo "var doDivW = $('#doc_detail').fadeIn('slow'); ";
							echo "}); ";
							echo "}); ";
		
///////////////////////////////////// Load Metadata	Editor Panel
		
							echo "var dataF = 'dc_identifier=".$new_items_dc_identifier."&reload=';	";
							echo "var doDivM = $('#titleTags').fadeOut('fast', function(){ ";
							echo "var doDivN = $('#titleTags').load('./data_meta.php',dataF, function(){ ";
							echo "var doDivO = $('#titleTags').fadeIn('slow'); ";
							echo "}); ";
							echo "}); ";
							echo "\">";
							echo "<button class=\"btn btn-primary\">";
							echo "<span style='font-size:0.89em;'>$newthisPage</span>";
							echo "</button>";
							echo "</a>";
							echo "</div>";
						}
						if((($thisPage + 1) < $thisCount) && (($thisPage + 1) < $btn)) { 
							$newPage = ($thisPage + 1); 
							$queryX = "SELECT * FROM items WHERE dc_references = \"".$itemFields["collections_dc_identifier"]."\" AND bibo_pages = \"".$newPage."\"";
							$mysqli_resultX = mysqli_query($mysqli_link, $queryX);
							while($rowX = mysqli_fetch_row($mysqli_resultX)) { 
								$new_items_dc_identifier = $rowX[2];
							}
							if(($newPage < 10)) { 
								$newPage = "0".$newPage; 
							} 
							echo "<div class=\"btn-group\" role=\"group\" style=\"padding-right:1px;\">";
							echo "<a href=\"javascript: ";
		
///////////////////////////////////// Load Image		
		
							echo "var dataE = 'dc_identifier=".$new_items_dc_identifier."';	";		
							echo "var doDivK = $('#doc_detail').fadeOut('fast', function(){ ";
							echo "var doDivP = $('#doc_detail').load('./data_doc.php',dataE, function(){ ";
							echo "var doDivW = $('#doc_detail').fadeIn('slow'); ";
							echo "}); ";
							echo "}); ";
		
///////////////////////////////////// Load Metadata	Editor Panel
		
							echo "var dataF = 'dc_identifier=".$new_items_dc_identifier."&reload=';	";
							echo "var doDivM = $('#titleTags').fadeOut('fast', function(){ ";
							echo "var doDivN = $('#titleTags').load('./data_meta.php',dataF, function(){ ";
							echo "var doDivO = $('#titleTags').fadeIn('slow'); ";
							echo "}); ";
							echo "}); ";
							echo "\">";
							echo "<button class=\"btn btn-warning\">";
							echo "<span style='font-size:0.89em;'>$newPage</span>";
							echo "</button>"; 
							echo "</a>";
							echo "</div>";
						}
						if((($thisPage + 2) < $thisCount) && (($thisPage + 2) < $btn)) { 
							$newPage = ($thisPage + 2); 
							$queryX = "SELECT * FROM items WHERE dc_references = \"".$itemFields["collections_dc_identifier"]."\" AND bibo_pages = \"".$newPage."\"";
							$mysqli_resultX = mysqli_query($mysqli_link, $queryX);
							while($rowX = mysqli_fetch_row($mysqli_resultX)) { 
								$new_items_dc_identifier = $rowX[2];
							}
							if(($newPage < 10)) { 
								$newPage = "0".$newPage; 
							} 
							echo "<div class=\"btn-group\" role=\"group\" style=\"padding-right:1px;\">";
							echo "<a href=\"javascript: ";
		
///////////////////////////////////// Load Image		
		
							echo "var dataE = 'dc_identifier=".$new_items_dc_identifier."';	";		
							echo "var doDivK = $('#doc_detail').fadeOut('fast', function(){ ";
							echo "var doDivP = $('#doc_detail').load('./data_doc.php',dataE, function(){ ";
							echo "var doDivW = $('#doc_detail').fadeIn('slow'); ";
							echo "}); ";
							echo "}); ";
		
///////////////////////////////////// Load Metadata	Editor Panel
		
							echo "var dataF = 'dc_identifier=".$new_items_dc_identifier."&reload=';	";
							echo "var doDivM = $('#titleTags').fadeOut('fast', function(){ ";
							echo "var doDivN = $('#titleTags').load('./data_meta.php',dataF, function(){ ";
							echo "var doDivO = $('#titleTags').fadeIn('slow'); ";
							echo "}); ";
							echo "}); ";
							echo "\">";
							echo "<button class=\"btn btn-warning\">";
							echo "<span style='font-size:0.89em;'>$newPage</span>";
							echo "</button>"; 
							echo "</a>";
							echo "</div>";
						}
						if((($thisPage + 3) < $thisCount) && (($thisPage + 3) < $btn)) { 
							$newPage = ($thisPage + 3); 
							$queryX = "SELECT * FROM items WHERE dc_references = \"".$itemFields["collections_dc_identifier"]."\" AND bibo_pages = \"".$newPage."\"";
							$mysqli_resultX = mysqli_query($mysqli_link, $queryX);
							while($rowX = mysqli_fetch_row($mysqli_resultX)) { 
								$new_items_dc_identifier = $rowX[2];
							}
							if(($newPage < 10)) { 
								$newPage = "0".$newPage; 
							} 
							echo "<div class=\"btn-group\" role=\"group\" style=\"padding-right:1px;\">";
							echo "<a href=\"javascript: ";
		
///////////////////////////////////// Load Image		
		
							echo "var dataE = 'dc_identifier=".$new_items_dc_identifier."';	";		
							echo "var doDivK = $('#doc_detail').fadeOut('fast', function(){ ";
							echo "var doDivP = $('#doc_detail').load('./data_doc.php',dataE, function(){ ";
							echo "var doDivW = $('#doc_detail').fadeIn('slow'); ";
							echo "}); ";
							echo "}); ";
		
///////////////////////////////////// Load Metadata	Editor Panel
		
							echo "var dataF = 'dc_identifier=".$new_items_dc_identifier."&reload=';	";
							echo "var doDivM = $('#titleTags').fadeOut('fast', function(){ ";
							echo "var doDivN = $('#titleTags').load('./data_meta.php',dataF, function(){ ";
							echo "var doDivO = $('#titleTags').fadeIn('slow'); ";
							echo "}); ";
							echo "}); ";
							echo "\">";
							echo "<button class=\"btn btn-warning\">";
							echo "<span style='font-size:0.89em;'>$newPage</span>";
							echo "</button>"; 
							echo "</a>";
							echo "</div>";
						}
						if((($thisPage + 4) < $thisCount) && (($thisPage + 4) < $btn)) { 
							$newPage = ($thisPage + 4); 
							$queryX = "SELECT * FROM items WHERE dc_references = \"".$itemFields["collections_dc_identifier"]."\" AND bibo_pages = \"".$newPage."\"";
							$mysqli_resultX = mysqli_query($mysqli_link, $queryX);
							while($rowX = mysqli_fetch_row($mysqli_resultX)) { 
								$new_items_dc_identifier = $rowX[2];
							}
							if(($newPage < 10)) { 
								$newPage = "0".$newPage; 
							} 
							echo "<div class=\"btn-group\" role=\"group\" style=\"padding-right:1px;\">";
							echo "<a href=\"javascript: ";
		
///////////////////////////////////// Load Image		
		
							echo "var dataE = 'dc_identifier=".$new_items_dc_identifier."';	";		
							echo "var doDivK = $('#doc_detail').fadeOut('fast', function(){ ";
							echo "var doDivP = $('#doc_detail').load('./data_doc.php',dataE, function(){ ";
							echo "var doDivW = $('#doc_detail').fadeIn('slow'); ";
							echo "}); ";
							echo "}); ";
		
///////////////////////////////////// Load Metadata	Editor Panel
		
							echo "var dataF = 'dc_identifier=".$new_items_dc_identifier."&reload=';	";
							echo "var doDivM = $('#titleTags').fadeOut('fast', function(){ ";
							echo "var doDivN = $('#titleTags').load('./data_meta.php',dataF, function(){ ";
							echo "var doDivO = $('#titleTags').fadeIn('slow'); ";
							echo "}); ";
							echo "}); ";
							echo "\">";
							echo "<button class=\"btn btn-warning\">";
							echo "<span style='font-size:0.89em;'>$newPage</span>";
							echo "</button>"; 
							echo "</a>";
							echo "</div>";
						}
						if((($thisPage + 5) < $thisCount) && (($thisPage + 5) < $btn)) { 
							$newPage = ($thisPage + 5); 
							$queryX = "SELECT * FROM items WHERE dc_references = \"".$itemFields["collections_dc_identifier"]."\" AND bibo_pages = \"".$newPage."\"";
							$mysqli_resultX = mysqli_query($mysqli_link, $queryX);
							while($rowX = mysqli_fetch_row($mysqli_resultX)) { 
								$new_items_dc_identifier = $rowX[2];
							}
							if(($newPage < 10)) { 
								$newPage = "0".$newPage; 
							} 
							echo "<div class=\"btn-group\" role=\"group\" style=\"padding-right:1px;\">";
							echo "<a href=\"javascript: ";
		
///////////////////////////////////// Load Image		
		
							echo "var dataE = 'dc_identifier=".$new_items_dc_identifier."';	";		
							echo "var doDivK = $('#doc_detail').fadeOut('fast', function(){ ";
							echo "var doDivP = $('#doc_detail').load('./data_doc.php',dataE, function(){ ";
							echo "var doDivW = $('#doc_detail').fadeIn('slow'); ";
							echo "}); ";
							echo "}); ";
		
///////////////////////////////////// Load Metadata	Editor Panel
		
							echo "var dataF = 'dc_identifier=".$new_items_dc_identifier."&reload=';	";
							echo "var doDivM = $('#titleTags').fadeOut('fast', function(){ ";
							echo "var doDivN = $('#titleTags').load('./data_meta.php',dataF, function(){ ";
							echo "var doDivO = $('#titleTags').fadeIn('slow'); ";
							echo "}); ";
							echo "}); ";
							echo "\">";
							echo "<button class=\"btn btn-warning\">";
							echo "<span style='font-size:0.89em;'>$newPage</span>";
							echo "</button>"; 
							echo "</a>";
							echo "</div>";
						}
						if((($thisPage + 6) < $thisCount) && (($thisPage + 6) < $btn)) { 
							$newPage = ($thisPage + 6); 
							$queryX = "SELECT * FROM items WHERE dc_references = \"".$itemFields["collections_dc_identifier"]."\" AND bibo_pages = \"".$newPage."\"";
							$mysqli_resultX = mysqli_query($mysqli_link, $queryX);
							while($rowX = mysqli_fetch_row($mysqli_resultX)) { 
								$new_items_dc_identifier = $rowX[2];
							}
							if(($newPage < 10)) { 
								$newPage = "0".$newPage; 
							} 
							echo "<div class=\"btn-group\" role=\"group\" style=\"padding-right:1px;\">";
							echo "<a href=\"javascript: ";
		
///////////////////////////////////// Load Image		
		
							echo "var dataE = 'dc_identifier=".$new_items_dc_identifier."';	";		
							echo "var doDivK = $('#doc_detail').fadeOut('fast', function(){ ";
							echo "var doDivP = $('#doc_detail').load('./data_doc.php',dataE, function(){ ";
							echo "var doDivW = $('#doc_detail').fadeIn('slow'); ";
							echo "}); ";
							echo "}); ";
		
///////////////////////////////////// Load Metadata	Editor Panel
		
							echo "var dataF = 'dc_identifier=".$new_items_dc_identifier."&reload=';	";
							echo "var doDivM = $('#titleTags').fadeOut('fast', function(){ ";
							echo "var doDivN = $('#titleTags').load('./data_meta.php',dataF, function(){ ";
							echo "var doDivO = $('#titleTags').fadeIn('slow'); ";
							echo "}); ";
							echo "}); ";
							echo "\">";
							echo "<button class=\"btn btn-warning\">";
							echo "<span style='font-size:0.89em;'>$newPage</span>";
							echo "</button>"; 
							echo "</a>";
							echo "</div>";
						}
						if((($thisPage + 7) < $thisCount) && (($thisPage + 7) < $btn)) { 
							$newPage = ($thisPage + 7); 
							$queryX = "SELECT * FROM items WHERE dc_references = \"".$itemFields["collections_dc_identifier"]."\" AND bibo_pages = \"".$newPage."\"";
							$mysqli_resultX = mysqli_query($mysqli_link, $queryX);
							while($rowX = mysqli_fetch_row($mysqli_resultX)) { 
								$new_items_dc_identifier = $rowX[2];
							}
							if(($newPage < 10)) { 
								$newPage = "0".$newPage; 
							} 
							echo "<div class=\"btn-group\" role=\"group\" style=\"padding-right:1px;\">";
							echo "<a href=\"javascript: ";
		
///////////////////////////////////// Load Image		
		
							echo "var dataE = 'dc_identifier=".$new_items_dc_identifier."';	";		
							echo "var doDivK = $('#doc_detail').fadeOut('fast', function(){ ";
							echo "var doDivP = $('#doc_detail').load('./data_doc.php',dataE, function(){ ";
							echo "var doDivW = $('#doc_detail').fadeIn('slow'); ";
							echo "}); ";
							echo "}); ";
		
///////////////////////////////////// Load Metadata	Editor Panel
		
							echo "var dataF = 'dc_identifier=".$new_items_dc_identifier."&reload=';	";
							echo "var doDivM = $('#titleTags').fadeOut('fast', function(){ ";
							echo "var doDivN = $('#titleTags').load('./data_meta.php',dataF, function(){ ";
							echo "var doDivO = $('#titleTags').fadeIn('slow'); ";
							echo "}); ";
							echo "}); ";
							echo "\">";
							echo "<button class=\"btn btn-warning\">";
							echo "<span style='font-size:0.89em;'>$newPage</span>";
							echo "</button>"; 
							echo "</a>";
							echo "</div>";
						}
						if((($thisPage + 8) < $thisCount) && (($thisPage + 8) < $btn)) { 
							$newPage = ($thisPage + 8); 
							$queryX = "SELECT * FROM items WHERE dc_references = \"".$itemFields["collections_dc_identifier"]."\" AND bibo_pages = \"".$newPage."\"";
							$mysqli_resultX = mysqli_query($mysqli_link, $queryX);
							while($rowX = mysqli_fetch_row($mysqli_resultX)) { 
								$new_items_dc_identifier = $rowX[2];
							}
							if(($newPage < 10)) { 
								$newPage = "0".$newPage; 
							} 
							echo "<div class=\"btn-group\" role=\"group\" style=\"padding-right:1px;\">";
							echo "<a href=\"javascript: ";
		
///////////////////////////////////// Load Image		
		
							echo "var dataE = 'dc_identifier=".$new_items_dc_identifier."';	";		
							echo "var doDivK = $('#doc_detail').fadeOut('fast', function(){ ";
							echo "var doDivP = $('#doc_detail').load('./data_doc.php',dataE, function(){ ";
							echo "var doDivW = $('#doc_detail').fadeIn('slow'); ";
							echo "}); ";
							echo "}); ";
		
///////////////////////////////////// Load Metadata	Editor Panel
		
							echo "var dataF = 'dc_identifier=".$new_items_dc_identifier."&reload=';	";
							echo "var doDivM = $('#titleTags').fadeOut('fast', function(){ ";
							echo "var doDivN = $('#titleTags').load('./data_meta.php',dataF, function(){ ";
							echo "var doDivO = $('#titleTags').fadeIn('slow'); ";
							echo "}); ";
							echo "}); ";
							echo "\">";
							echo "<button class=\"btn btn-warning\">";
							echo "<span style='font-size:0.89em;'>$newPage</span>";
							echo "</button>"; 
							echo "</a>";
							echo "</div>";
						}
						if((($thisPage + 9) < $thisCount) && (($thisPage + 9) < $btn)) { 
							$newPage = ($thisPage + 9); 
							$queryX = "SELECT * FROM items WHERE dc_references = \"".$itemFields["collections_dc_identifier"]."\" AND bibo_pages = \"".$newPage."\"";
							$mysqli_resultX = mysqli_query($mysqli_link, $queryX);
							while($rowX = mysqli_fetch_row($mysqli_resultX)) { 
								$new_items_dc_identifier = $rowX[2];
							}
							if(($newPage < 10)) { 
								$newPage = "0".$newPage; 
							} 
							echo "<div class=\"btn-group\" role=\"group\" style=\"padding-right:1px;\">";
							echo "<a href=\"javascript: ";
		
///////////////////////////////////// Load Image		
		
							echo "var dataE = 'dc_identifier=".$new_items_dc_identifier."';	";		
							echo "var doDivK = $('#doc_detail').fadeOut('fast', function(){ ";
							echo "var doDivP = $('#doc_detail').load('./data_doc.php',dataE, function(){ ";
							echo "var doDivW = $('#doc_detail').fadeIn('slow'); ";
							echo "}); ";
							echo "}); ";
		
///////////////////////////////////// Load Metadata	Editor Panel
		
							echo "var dataF = 'dc_identifier=".$new_items_dc_identifier."&reload=';	";
							echo "var doDivM = $('#titleTags').fadeOut('fast', function(){ ";
							echo "var doDivN = $('#titleTags').load('./data_meta.php',dataF, function(){ ";
							echo "var doDivO = $('#titleTags').fadeIn('slow'); ";
							echo "}); ";
							echo "}); ";
							echo "\">";
							echo "<button class=\"btn btn-warning\">";
							echo "<span style='font-size:0.89em;'>$newPage</span>";
							echo "</button>"; 
							echo "</a>";
							echo "</div>";
						}
						echo "</div>";
					?>
      				</div>
  				</div>
			</div>
            <!-- <br /> //-->
 <?php

//////////////////////////////////////////////////////////////////////////////////////// OCR

			$tPixels = ((substr_count($itemFields["dc_description"], "\n" ) + 8) * 15);

?> 
            <div class="panel" style="background-color: #FFFFFF; border: 1px solid #196961;">
                <div class="panel-heading">
					<div class="panel-control">
                    	<button class="btn btn-default" id="btn-refresh-ocr"><i class="ion-refresh" style="color: #cacaca; font-size: 1.4em;"></i></button>
                        <?php
						
							if(($itemFields["dct_accessRights"] != "restricted")) {
							
						?>
						<button class="btn btn-default" data-target="#ocr-panel-collapse" data-toggle="collapse">
                        	<i class="ion-chevron-up" style="color: #cacaca; font-size: 1.4em;"></i></button>
                        <?php
						
							}
							
						?>
					</div>
					<h4 class="panel-title"><a name="scrollOCR">Text Recognition</a></h4>
				</div> 
                <div id="ocr-panel-collapse" class="collapse in" style="min-height: <?php echo $tPixels; ?>px;">
    				<div class="panel-body" id="ocrContainer">
						<?php
                        
                            $reload = "No";
                            include("./data_meta_ocr.php");
                        
                        ?>
                	</div>
         		</div>
      		</div>           
            <!-- <br /> //-->
<?php				

//////////////////////////////////////////////////////////////////////////////////////// Item Metadata

?>
            <div class="panel" style="background-color: #FFFFFF; border: 1px solid #1B4F74;">
                <div class="panel-heading">
					<div class="panel-control">
                    	<button class="btn btn-default" id="btn-refresh-items"><i class="ion-refresh" style="color: #cacaca; font-size: 1.4em;"></i></button>
						<?php
						
///////////////////////////////////// Flag Report		
		
							if(($_SESSION["administrator"] == "yes")) {
								$valid = "yes";
								$queryFU = "SELECT * FROM flags WHERE dc_references = \"$dc_identifier\" ";
								$mysqli_resultFU = mysqli_query($mysqli_link, $queryFU);
								while($rowFU = mysqli_fetch_row($mysqli_resultFU)) {
									$valid = "no";
								}
								if(($valid == "yes")) {
									echo "<a href=\"#\">";
									echo "<i id=\"m".$dc_identifier."\" class=\"ion-flag mbtn-toggle\" ";
									echo "style=\"color:#CCCCCC; font-size: 1.4em;\" ";
									echo "data-status=\"YES\" data-id=\"".$dc_identifier."\"></i>";
									echo "</a>";
								} else {
									echo "<a href=\"#\">";
									echo "<i id=\"m".$dc_identifier."\" class=\"ion-flag mbtn-toggle\" ";
									echo "style=\"color:#8B0D82; font-size: 1.4em;\" ";
									echo "data-status=\"NO\" data-id=\"".$dc_identifier."\"></i>";
									echo "</a>";
								}	
								echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";	
							}

///////////////////////////////////// Metadata
						
						?>
					</div>
					<h4 class="panel-title"><a name="scrollMain">Metadata</a></h4>
				</div>
  				<div id="item-panel-collapse" class="collapse in">
    				<div class="panel-body">
                        <div id="refreshItemMeta">
                            <div class="table">
                                <table class="table <?php
                                
									if(($itemFields["dct_accessRights"] == "restricted")) {
										echo "table-striped";
									} else {
										echo "table-condensed table-hover";
									}
									
									?>" width="99%" border="0">
                                    <tbody>
                                    <?php
									
										$itemTags = array();
										$queryD = "SELECT * FROM annotations WHERE dc_references = \"$dc_identifier\" ";
										$mysqli_resultD = mysqli_query($mysqli_link, $queryD);
										while($rowD = mysqli_fetch_row($mysqli_resultD)) { 
											$itemTags[] = $rowD[7]."|".$rowD[5]."|".$rowD[6]."|".$rowD[1]."|".$rowD[0];
										}
										sort($itemTags);
										$itemFields["dc_title"] = preg_replace("/\:/i","_",$itemFields["dc_title"]);

///////////////////////////////////// Title                           
                                        
                                        echo "<tr>";
                                        echo "<td style=\"color: #FFFFFF; background-color: #1B4F74; padding-top: 10px; padding-bottom: 10px; ";
                                        echo "font-size: 0.9em; font-weight: 900;text-align:right;\">#</td>";
                                        echo "<td style=\"color: #FFFFFF; background-color: #1B4F74; padding-top: 10px; padding-bottom: 10px; ";
                                        echo "font-size: 0.9em; font-weight: 900;\">".$itemFields["dc_title"]."</td>";
                                        echo "</tr>";
										$runningTitle = $itemFields["dc_title"];
																												
///////////////////////////////////// DC Creator Dropdown										
										
										$runningID = $itemFields["dc_identifier"];
										if(($itemFields["dct_accessRights"] == "restricted")) {
											echo "<tr>";
											echo "<td style=\"font-size: 0.9em; text-align:right; border-top: 0px solid #768697; padding-top: 10px;\">&nbsp;</td>";
											echo "<td style=\"font-size: 0.9em; text-align:right; border-top: 0px solid #768697; padding-top: 10px;\">&nbsp;</td>";
											echo "</tr>";
										}
										if((($itemFields["dct_accessRights"] == "restricted") && ($itemFields["dc_creator"] != "")) or ($itemFields["dct_accessRights"] != "restricted")) {
											echo "<tr>";
											echo "<td style=\"font-size: 0.9em; text-align:right; border-top: 0px solid #768697; padding-top: 10px;\">Writer</td>";
											echo "<td style=\"font-size: 0.9em; border-top: 0px solid #768697; padding-left: 10px; padding-right: 0px; ";
											if(($itemFields["dct_accessRights"] == "restricted")) { echo "padding-top: 10px; "; }
											echo "\">";	
											echo "<span id=\"input-dc_creator_frame\">";	
											if(($itemFields["dct_accessRights"] != "restricted")) {
												echo "<select ";
												echo "style=\"";
												echo "font-size: 1.0em; ";
												echo "white-space: normal!important; ";
												echo "word-wrap: break-word!important; ";
												echo "white-space: -moz-pre-wrap!important; ";
												echo "white-space: pre-wrap!important; ";
												echo "\" ";
												echo "class=\"show-tick\" ";
												echo "data-size=\"9\" ";
												echo "data-width=\"100%\" ";
												echo "data-live-search=\"true\" ";
												echo "id=\"input-dc_creator\" ";
												echo "name=\"input-dc_creator\" ";
												echo ">";
												echo "<option style=\"font-size: 0.9em; ";
												echo "white-space: normal!important; ";
												echo "word-wrap: break-word!important; ";
												echo "white-space: -moz-pre-wrap!important; ";
												echo "white-space: pre-wrap!important; ";
												echo "\" value=\"".$itemFields["dc_creator"]."\">";
												echo $itemFields["dc_creator"]."</option>";
												echo "<option style=\"font-size: 0.9em; ";
												echo "white-space: normal!important; ";
												echo "word-wrap: break-word!important; ";
												echo "white-space: -moz-pre-wrap!important; ";
												echo "white-space: pre-wrap!important; ";
												echo "\" data-divider=\"true\"></option>";
												echo "<option style=\"font-size: 0.9em; ";
												echo "white-space: normal!important; ";
												echo "word-wrap: break-word!important; ";
												echo "white-space: -moz-pre-wrap!important; ";
												echo "white-space: pre-wrap!important; ";
												echo "\" value=\"Nothing Specified\">Nothing Specified</option>";
												foreach($itemTags as $iT) {
													$bits = explode("|","$iT");
													echo "<option style=\"font-size: 0.9em; ";
													echo "white-space: normal!important; ";
													echo "word-wrap: break-word!important; ";
													echo "white-space: -moz-pre-wrap!important; ";
													echo "white-space: pre-wrap!important; ";
													echo "\" value=\"".$bits[0]."\">".$bits[0]."</option>";
												}
												echo "<option style=\"font-size: 0.9em; ";
												echo "white-space: normal!important; ";
												echo "word-wrap: break-word!important; ";
												echo "white-space: -moz-pre-wrap!important; ";
												echo "white-space: pre-wrap!important; ";
												echo "\" data-divider=\"true\"></option>";
												echo "<option style=\"color: #800000; font-size: 0.9em; ";
												echo "white-space: normal!important; ";
												echo "word-wrap: break-word!important; ";
												echo "white-space: -moz-pre-wrap!important; ";
												echo "white-space: pre-wrap!important; ";
												echo "\" disabled>If you do not see any appropriate option in the list above, ";
												echo "please add a key / value pair of metadata in the Tags panel below and it will be ";
												echo "added to the above list for selection when you refresh this panel.</option>";
												echo "</select>";
											} else {
												echo $itemFields["dc_creator"];
											}
											echo "</span>";
											echo "</td>";
											echo "</tr>";
										}
										
///////////////////////////////////// Org FormalOrganisation Dropdown										
										
										if((($itemFields["dct_accessRights"] == "restricted") && ($itemFields["org_FormalOrganisation"] != "")) or ($itemFields["dct_accessRights"] != "restricted")) {
											echo "<tr>";
											echo "<td style=\"font-size: 0.9em; text-align:right; border-top: 0px solid #768697; padding-top: 10px;\">Writer's<br />Affiliation</td>";
											echo "<td style=\"font-size: 0.9em; border-top: 0px solid #768697; padding-left: 10px; padding-right: 0px; ";
											if(($itemFields["dct_accessRights"] == "restricted")) { echo "padding-top: 10px; "; }
											echo "\">";
											echo "<span id=\"input-org_FormalOrganisation_frame\">";
											if(($itemFields["dct_accessRights"] != "restricted")) {
												echo "<select ";
												echo "style=\"font-size: 1.0em; ";
												echo "white-space: normal!important; ";
												echo "word-wrap: break-word!important; ";
												echo "white-space: -moz-pre-wrap!important; ";
												echo "white-space: pre-wrap!important; ";
												echo "\" ";
												echo "class=\"show-tick\" ";
												echo "data-size=\"9\" ";
												echo "data-width=\"100%\" ";
												echo "data-live-search=\"true\" ";
												echo "id=\"input-org_FormalOrganisation\" ";
												echo "name=\"input-org_FormalOrganisation\" ";
												echo ">";
												echo "<option style=\"font-size: 0.9em; ";
												echo "white-space: normal!important; ";
												echo "word-wrap: break-word!important; ";
												echo "white-space: -moz-pre-wrap!important; ";
												echo "white-space: pre-wrap!important; ";
												echo "\" value=\"".$itemFields["org_FormalOrganisation"]."\">";
												echo $itemFields["org_FormalOrganisation"]."</option>";
												echo "<option style=\"font-size: 0.9em; ";
												echo "white-space: normal!important; ";
												echo "word-wrap: break-word!important; ";
												echo "white-space: -moz-pre-wrap!important; ";
												echo "white-space: pre-wrap!important; ";
												echo "\" data-divider=\"true\"></option>";
												echo "<option style=\"font-size: 0.9em; ";
												echo "white-space: normal!important; ";
												echo "word-wrap: break-word!important; ";
												echo "white-space: -moz-pre-wrap!important; ";
												echo "white-space: pre-wrap!important; ";
												echo "\" value=\"Nothing Specified\">Nothing Specified</option>";
												foreach($itemTags as $iT) {
													$bits = explode("|","$iT");
													echo "<option style=\"font-size: 0.9em; ";
													echo "white-space: normal!important; ";
													echo "word-wrap: break-word!important; ";
													echo "white-space: -moz-pre-wrap!important; ";
													echo "white-space: pre-wrap!important; ";
													echo "\" value=\"".$bits[0]."\">".$bits[0]."</option>";
												}
												echo "<option style=\"font-size: 0.9em; ";
												echo "white-space: normal!important; ";
												echo "word-wrap: break-word!important; ";
												echo "white-space: -moz-pre-wrap!important; ";
												echo "white-space: pre-wrap!important; ";
												echo "\" data-divider=\"true\"></option>";
												echo "<option style=\"color: #800000; font-size: 0.9em; ";
												echo "white-space: normal!important; ";
												echo "word-wrap: break-word!important; ";
												echo "white-space: -moz-pre-wrap!important; ";
												echo "white-space: pre-wrap!important; ";
												echo "\" disabled>If you do not see any appropriate option in the list above, ";
												echo "please add a key / value pair of metadata in the Tags panel below and it will be ";
												echo "added to the above list for selection when you refresh this panel.</option>";
												echo "</select>";	
											} else {
												echo $itemFields["org_FormalOrganisation"];
											}
											echo "</span>";
											echo "</td>";
											echo "</tr>";
										}

///////////////////////////////////// GN Name Input										
										
										if((($itemFields["dct_accessRights"] == "restricted") && ($itemFields["gn_name"] != "")) or ($itemFields["dct_accessRights"] != "restricted")) {
											echo "<tr>";
											echo "<td style=\"font-size: 0.9em; text-align:right; border-top: 0px solid #768697; padding-top: 10px;\">Origin</td>";
											echo "<td style=\"font-size: 0.9em; border-top: 0px solid #768697; ";
											if(($itemFields["dct_accessRights"] == "restricted")) { echo "padding-left: 10px; padding-top: 10px; "; }
											echo "\">";
											echo "<span id=\"input-gn_name_frame\">";
											if(($itemFields["dct_accessRights"] != "restricted")) {
												echo "<input ";
												echo "type=\"text\" ";
												echo "style=\"font-size: 1.0em; border: 1px solid #27a59b; \" ";
												echo "class=\"form-control\" ";
												echo "id=\"input-gn_name\" ";
												echo "name=\"input-gn_name\" ";
												echo "value=\"".$itemFields["gn_name"]."\" ";
												echo "/>";
											} else {
												echo $itemFields["gn_name"];
											}
											echo "</span>";
											echo "</td>";
											echo "</tr>";
										}

///////////////////////////////////// MARC Addressee Dropdown										
										
										if((($itemFields["dct_accessRights"] == "restricted") && ($itemFields["marc_addressee"] != "")) or ($itemFields["dct_accessRights"] != "restricted")) {
											echo "<tr>";
											echo "<td style=\"font-size: 0.9em; text-align:right; border-top: 0px solid #768697; padding-top: 10px;\">Recipient</td>";
											echo "<td style=\"font-size: 0.9em; border-top: 0px solid #768697; padding-left: 10px; padding-right: 0px; ";
											if(($itemFields["dct_accessRights"] == "restricted")) { echo "padding-top: 10px; "; }
											echo "\">";	
											echo "<span id=\"input-marc_addressee_frame\">";
											if(($itemFields["dct_accessRights"] != "restricted")) {
												echo "<select ";
												echo "style=\"font-size: 1.0em;\" ";
												echo "class=\"show-tick\" ";
												echo "data-size=\"9\" ";
												echo "data-width=\"100%\" ";
												echo "data-live-search=\"true\" ";
												echo "id=\"input-marc_addressee\" ";
												echo "name=\"input-marc_addressee\" ";
												echo ">";
												echo "<option style=\"font-size: 0.9em; white-space: normal!important;\" value=\"".$itemFields["marc_addressee"]."\">";
												echo $itemFields["marc_addressee"]."</option>";
												echo "<option style=\"font-size: 0.9em; white-space: normal!important;\" data-divider=\"true\"></option>";
												echo "<option style=\"font-size: 0.9em; ";
												echo "white-space: normal!important; ";
												echo "word-wrap: break-word!important; ";
												echo "white-space: -moz-pre-wrap!important; ";
												echo "white-space: pre-wrap!important; ";
												echo "\" value=\"Nothing Specified\">Nothing Specified</option>";
												foreach($itemTags as $iT) {
													$bits = explode("|","$iT");
													echo "<option style=\"font-size: 0.9em; white-space: normal!important;\" value=\"".$bits[0]."\">".$bits[0]."</option>";
												}
												echo "<option style=\"font-size: 0.9em; white-space: normal!important;\" data-divider=\"true\"></option>";
												echo "<option style=\"color: #800000; font-size: 0.9em; white-space: normal;\" disabled>If you do not see any appropriate option in the list above, ";
												echo "please add a key / value pair of metadata in the Tags panel below and it will be ";
												echo "added to the above list for selection when you refresh this panel.</option>";
												echo "</select>";	
											} else {
												echo $itemFields["marc_addressee"];
											}
											echo "</span>";
											echo "</td>";
											echo "</tr>";
										}
										
///////////////////////////////////// RDAA GroupMemberOf Dropdown										
										
										if((($itemFields["dct_accessRights"] == "restricted") && ($itemFields["rdaa_groupMemberOf"] != "")) or ($itemFields["dct_accessRights"] != "restricted")) {
											echo "<tr>";
											echo "<td style=\"font-size: 0.9em; text-align:right; border-top: 0px solid #768697; padding-top: 10px;\">Recipient's<br />Affiliation</td>";
											echo "<td style=\"font-size: 0.9em; border-top: 0px solid #768697; padding-left: 10px; padding-right: 0px; ";
											if(($itemFields["dct_accessRights"] == "restricted")) { echo "padding-top: 10px; "; }
											echo "\">";	
											echo "<span id=\"input-rdaa_groupMemberOf_frame\">";
											if(($itemFields["dct_accessRights"] != "restricted")) {
												echo "<select ";
												echo "style=\"font-size: 1.0em;\" ";
												echo "class=\"show-tick\" ";
												echo "data-size=\"9\" ";
												echo "data-width=\"100%\" ";
												echo "data-live-search=\"true\" ";
												echo "id=\"input-rdaa_groupMemberOf\" ";
												echo "name=\"input-rdaa_groupMemberOf\" ";
												echo ">";
												echo "<option style=\"font-size: 0.9em; white-space: normal!important;\" value=\"".$itemFields["rdaa_groupMemberOf"]."\">";
												echo $itemFields["rdaa_groupMemberOf"]."</option>";
												echo "<option style=\"font-size: 0.9em; white-space: normal!important;\" data-divider=\"true\"></option>";
												echo "<option style=\"font-size: 0.9em; ";
												echo "white-space: normal!important; ";
												echo "word-wrap: break-word!important; ";
												echo "white-space: -moz-pre-wrap!important; ";
												echo "white-space: pre-wrap!important; ";
												echo "\" value=\"Nothing Specified\">Nothing Specified</option>";
												foreach($itemTags as $iT) {
													$bits = explode("|","$iT");
													echo "<option style=\"font-size: 0.9em; white-space: normal!important;\" value=\"".$bits[0]."\">".$bits[0]."</option>";
												}
												echo "<option style=\"font-size: 0.9em; white-space: normal!important;\" data-divider=\"true\"></option>";
												echo "<option style=\"color: #800000; font-size: 0.9em; white-space: normal!important;\" disabled>";
												echo "If you do not see any appropriate option in the list above, ";
												echo "please add a key / value pair of metadata in the Tags panel below and it will be ";
												echo "added to the above list for selection when you refresh this panel.</option>";
												echo "</select>";	
											} else {
												echo $itemFields["rdaa_groupMemberOf"];
											}
											echo "</span>";
											echo "</td>";
											echo "</tr>";	
										}
										
///////////////////////////////////// MADS AssociatedLocale Input										
										
										if((($itemFields["dct_accessRights"] == "restricted") && ($itemFields["mads_associatedLocale"] != "")) or ($itemFields["dct_accessRights"] != "restricted")) {
											echo "<tr>";
											echo "<td style=\"font-size: 0.9em; text-align:right; border-top: 0px solid #768697; padding-top: 10px;\">Destination</td>";
											echo "<td style=\"font-size: 0.9em; border-top: 0px solid #768697; ";
											if(($itemFields["dct_accessRights"] == "restricted")) { echo "padding-left: 10px; padding-top: 10px; "; }
											echo "\">";	
											echo "<span id=\"input-mads_associatedLocale_frame\">";
											if(($itemFields["dct_accessRights"] != "restricted")) {
												echo "<input ";
												echo "type=\"text\" ";
												echo "style=\"font-size: 1.0em; border: 1px solid #27a59b; \" ";
												echo "class=\"form-control\" ";
												echo "id=\"input-mads_associatedLocale\" ";
												echo "name=\"input-mads_associatedLocale\" ";
												echo "value=\"".$itemFields["mads_associatedLocale"]."\" ";
												echo "/>";
											} else {
												echo $itemFields["mads_associatedLocale"];
											}
											echo "</span>";
											echo "</td>";
											echo "</tr>";
										}

///////////////////////////////////// DC Created Input										
										
										if((($itemFields["dct_accessRights"] == "restricted") && ($itemFields["dc_created"] != "")) or ($itemFields["dct_accessRights"] != "restricted")) {
											echo "<tr>";
											echo "<td style=\"font-size: 0.9em; text-align:right; border-top: 0px solid #768697; padding-top: 10px;\">Created</td>";
											echo "<td style=\"font-size: 0.9em; border-top: 0px solid #768697; ";
											if(($itemFields["dct_accessRights"] == "restricted")) { echo "padding-left: 10px; padding-top: 10px; "; }
											echo "\">";
											if(($itemFields["dct_accessRights"] != "restricted")) {
												echo "<input ";
												echo "type=\"text\" ";
												echo "style=\"font-size: 1.0em; border: 1px solid #27a59b; \" ";
												echo "class=\"form-control\" ";
												echo "id=\"input-dc_created\" ";
												echo "name=\"input-dc_created\" ";
												echo "value=\"".$itemFields["dc_created"]."\" ";
												echo "/>";			
											} else {
												echo $itemFields["dc_created"];
											}
											echo "</td>";
											echo "</tr>";	
										}
										
///////////////////////////////////// DC Description Input										
										
										if(($do_OCR != "off")) {
											echo "<tr>";
											echo "<td style=\"font-size: 0.9em; text-align:right; border-top: 0px solid #768697; padding-top: 10px;\" width=\"23%\" nowrap>OCR Text</td>";
											echo "<td style=\"font-size: 0.9em; border-top: 0px solid #768697; width: 100%; \">";
											echo "<textarea rows=\"2\" ";
											echo "style=\"font-size: 1.0em; border: 1px solid #27a59b; resize: none; \" ";
											echo "class=\"form-control\" ";
											echo "id=\"input-dc_description\" ";
											echo "name=\"input-dc_description\" >";
											echo $itemFields["dc_description"];
											echo "</textarea>";
											echo "</td>";
											echo "</tr>";
										} else {
											echo "<input ";
											echo "type=\"hidden\" ";
											echo "id=\"input-dc_description\" ";
											echo "name=\"input-dc_description\" ";
											echo ">";
										}
										
///////////////////////////////////// Form Submit	
                
                                    ?>
                                    </tbody>
                                </table>
                                <?php 
								
									if(($itemFields["dct_accessRights"] != "restricted")) {
										echo "<button class=\"btn btn-success col-lg-12\" id=\"input_submit_item\"><strong>Save Changes</strong></button>";
									}
									
///////////////////////////////////// Most Used Metadata

									if(($itemFields["bibo_pages"] > 1) && ($itemFields["dc_references"] != "") && ($itemFields["dct_accessRights"] != "restricted")) {
										echo "<br />&nbsp;<br /><br /><br />"; 
										echo "<p style=\"text-align: center; font-weight: 900;\">Frequently Used Locations<br />(Click to add value into form)</p>";
										echo "<table class=\"table table-condensed\" width=\"99%\" border=\"0\">";
										echo "<tbody>";	
										echo "<tr>";
										echo "<td style=\"border-right: 1px solid #ffffff; color: #FFFFFF; background-color: #888888; padding: 11px; ";
										echo "font-size: 0.9em; font-weight: 900;text-align:right;\" width=\"23%\" nowrap>Key</td>";
										echo "<td style=\"color: #FFFFFF; background-color: #888888; padding: 9px; ";
										echo "font-size: 0.9em; font-weight: 900;\" width=\"100%\">Value</td>";
										echo "<td style=\"color: #FFFFFF; background-color: #888888; padding: 9px; ";
										echo "font-size: 0.9em; font-weight: 900;\" width=\"100%\">Freq.</td>";
										echo "</tr>";
									
/////////////////////// Origins									
									
										$t = 0;
										$queryZ = "SELECT DISTINCT(gn_name), ";
										$queryZ .= "COUNT(gn_name) AS TheCount ";
										$queryZ .= "FROM items ";
										$queryZ .= "WHERE gn_name != \"\" ";
										$queryZ .= "GROUP BY gn_name ";
										$queryZ .= "ORDER BY TheCount DESC LIMIT 5";
										$mysqli_resultZ = mysqli_query($mysqli_link, $queryZ);
										while($rowZ = mysqli_fetch_row($mysqli_resultZ)) { 
											$t++;
											echo "<tr>";
											echo "<td style=\"border-bottom: 1px solid #ffffff; border-right: 1px solid #ffffff; background-color: #f2f4f6; ";
											echo "font-size: 0.9em; text-align:right; border-top: 0px solid #768697; padding: 9px;\" width=\"23%\">Origin</td>";
											echo "<td style=\"border-bottom: 1px solid #ffffff; background-color: #f2f4f6; font-size: 0.9em; ";
											echo "border-top: 0px solid #768697; width: 100%; padding: 9px;\">";
											echo "<a href=\"";
											echo "javascript: var startPrior = $('#input-gn_name').val('$rowZ[0]'); ";
											echo "\" style=\"color: #800000;\" id=\"doTop_gn_name[$t]\">";
											echo "$rowZ[0]";
											echo "</a>";
											echo "</td>";
											echo "<td style=\"border-bottom: 1px solid #ffffff; background-color: #f2f4f6; font-size: 0.9em; ";
											echo "border-top: 0px solid #768697; width: 100%; padding: 9px; text-align: right;\">";
											echo "$rowZ[1]";
											echo "</td>";
											echo "</tr>";
										}
									
/////////////////////// Destinations

										$u = 0;
										$queryZ = "SELECT DISTINCT(mads_associatedLocale), ";
										$queryZ .= "COUNT(mads_associatedLocale) AS TheCount ";
										$queryZ .= "FROM items ";
										$queryZ .= "WHERE mads_associatedLocale != \"\" ";
										$queryZ .= "GROUP BY mads_associatedLocale ";
										$queryZ .= "ORDER BY TheCount DESC LIMIT 5";
										$mysqli_resultZ = mysqli_query($mysqli_link, $queryZ);
										while($rowZ = mysqli_fetch_row($mysqli_resultZ)) { 
											$u++;
											echo "<tr>";
											echo "<td style=\"border-bottom: 1px solid #ffffff; border-right: 1px solid #ffffff; background-color: #f2f4f6; ";
											echo "font-size: 0.9em; text-align:right; border-top: 0px solid #768697; padding: 9px;\" width=\"23%\">Dest.</td>";
											echo "<td style=\"border-bottom: 1px solid #ffffff; background-color: #f2f4f6; font-size: 0.9em; ";
											echo "border-top: 0px solid #768697; width: 100%; padding: 9px;\">";
											echo "<a href=\"";
											echo "javascript: var startPrior = $('#input-mads_associatedLocale').val('$rowZ[0]'); ";
											echo "\" style=\"color: #800000;\" id=\"doTop_mads_associatedLocale[$u]\">";
											echo "$rowZ[0]";
											echo "</a>";
											echo "</td>";
											echo "<td style=\"border-bottom: 1px solid #ffffff; background-color: #f2f4f6; font-size: 0.9em; ";
											echo "border-top: 0px solid #768697; width: 100%; padding: 9px; text-align: right;\">";
											echo "$rowZ[1]";
											echo "</td>";
											echo "</tr>";
										}

/////////////////////// Finish Table
									
										echo "</tbody>";
										echo "</table>";
									}

///////////////////////////////////// Previous Item-Level MetaData If Else
									
									if(($itemFields["bibo_pages"] > 1) && ($itemFields["dc_references"] != "") && ($itemFields["dct_accessRights"] != "restricted")) {
										
										echo "<br />"; 
										echo "<p style=\"text-align: center; font-weight: 900;\">Previous Item's Metadata<br />(Click to add value into form)</p>";
										echo "<table class=\"table table-condensed\" width=\"99%\" border=\"0\">";
										echo "<tbody>";  
										
///////////////////////////////////// Start Previous Item-Level MetaData Table                       
                                        
                                        echo "<tr>";
                                        echo "<td style=\"border-right: 1px solid #ffffff; color: #FFFFFF; background-color: #888888; padding: 11px; ";
                                        echo "font-size: 0.9em; font-weight: 900;text-align:right;\" width=\"23%\" nowrap>Key</td>";
                                        echo "<td style=\"color: #FFFFFF; background-color: #888888; padding: 9px; ";
                                        echo "font-size: 0.9em; font-weight: 900;\" width=\"100%\">Value</td>";
                                        echo "</tr>";
										
										$foundPrior = "";
										$new_bibo_pages = ($itemFields["bibo_pages"] - 1);
										$queryPrior = "SELECT * FROM items WHERE bibo_pages = \"$new_bibo_pages\" AND dc_references = \"".$itemFields["dc_references"]."\" ";
										$mysqli_resultPrior = mysqli_query($mysqli_link, $queryPrior);
										while($rowP = mysqli_fetch_row($mysqli_resultPrior)) { 

/////////////////// Previous Writer
										
											if(($rowP[13] != "")) {
												echo "<tr>";
												echo "<td style=\"border-bottom: 1px solid #ffffff; border-right: 1px solid #ffffff; background-color: #f2f4f6; ";
												echo "font-size: 0.9em; text-align:right; border-top: 0px solid #768697; padding: 9px;\" width=\"23%\">Writer</td>";
												echo "<td style=\"border-bottom: 1px solid #ffffff; background-color: #f2f4f6; font-size: 0.9em; ";
												echo "border-top: 0px solid #768697; width: 100%; padding: 9px;\">";
												echo "<a href=\"#\" style=\"color: #800000;\" id=\"doPrior_dc_creator\">";
												$doPrior_dc_creator = $rowP[13];
												echo "$rowP[13]";
												echo "</a>";
												echo "</td>";
												echo "</tr>";
												$foundPrior = "y";
											}

/////////////////// Previous Affiliation for Writer
											
											if(($rowP[14] != "")) {
												echo "<tr>";
												echo "<td style=\"border-bottom: 1px solid #ffffff; border-right: 1px solid #ffffff; background-color: #f2f4f6; ";
												echo "font-size: 0.9em; text-align:right; border-top: 0px solid #768697; padding: 9px;\" width=\"23%\">Writer's Affiliation</td>";
												echo "<td style=\"border-bottom: 1px solid #ffffff; background-color: #f2f4f6; font-size: 0.9em; ";
												echo "border-top: 0px solid #768697; width: 100%; padding: 9px;\">";
												echo "<a href=\"#\" style=\"color: #800000;\" id=\"doPrior_org_FormalOrganisation\">";
												$doPrior_org_FormalOrganisation = $rowP[14];
												echo "$rowP[14]";
												echo "</a>";
												echo "</td>";
												echo "</tr>";
												$foundPrior = "y";
											}

/////////////////// Previous Letter's Origin
											
											if(($rowP[15] != "")) {
												echo "<tr>";
												echo "<td style=\"border-bottom: 1px solid #ffffff; border-right: 1px solid #ffffff; background-color: #f2f4f6; ";
												echo "font-size: 0.9em; text-align:right; border-top: 0px solid #768697; padding: 9px;\" width=\"23%\">Origin</td>";
												echo "<td style=\"border-bottom: 1px solid #ffffff; background-color: #f2f4f6; font-size: 0.9em; ";
												echo "border-top: 0px solid #768697; width: 100%; padding: 9px;\">";
												echo "<a href=\"#\" style=\"color: #800000;\" id=\"doPrior_gn_name\">";
												$doPrior_gn_name = $rowP[15];
												echo "$rowP[15]";
												echo "</a>";
												echo "</td>";
												echo "</tr>";
												$foundPrior = "y";
											}

/////////////////// Previous Recipient

											if(($rowP[19] != "")) {
												echo "<tr>";
												echo "<td style=\"border-bottom: 1px solid #ffffff; border-right: 1px solid #ffffff; background-color: #f2f4f6; ";
												echo "font-size: 0.9em; text-align:right; border-top: 0px solid #768697; padding: 9px;\" width=\"23%\">Recipient</td>";
												echo "<td style=\"border-bottom: 1px solid #ffffff; background-color: #f2f4f6; font-size: 0.9em; ";
												echo "border-top: 0px solid #768697; width: 100%; padding: 9px;\">";
												echo "<a href=\"#\" style=\"color: #800000;\" id=\"doPrior_marc_addressee\">";
												$doPrior_marc_addressee = $rowP[19];
												echo "$rowP[19]";
												echo "</a>";
												echo "</td>";
												echo "</tr>";
												$foundPrior = "y";
											}

/////////////////// Previous Affiliation for Recipient

											if(($rowP[20] != "")) {
												echo "<tr>";
												echo "<td style=\"border-bottom: 1px solid #ffffff; border-right: 1px solid #ffffff; background-color: #f2f4f6; ";
												echo "font-size: 0.9em; text-align:right; border-top: 0px solid #768697; padding: 9px;\" width=\"23%\">Recipient's Affiliation</td>";
												echo "<td style=\"border-bottom: 1px solid #ffffff; background-color: #f2f4f6; font-size: 0.9em; ";
												echo "border-top: 0px solid #768697; width: 100%; padding: 9px;\">";
												echo "<a href=\"#\" style=\"color: #800000;\" id=\"doPrior_rdaa_groupMemberOf\">";
												$doPrior_rdaa_groupMemberOf = $rowP[20];
												echo "$rowP[20]";
												echo "</a>";
												echo "</td>";
												echo "</tr>";
												$foundPrior = "y";
											}

/////////////////// Previous Letter's Destination

											if(($rowP[21] != "")) {
												echo "<tr>";
												echo "<td style=\"border-bottom: 1px solid #ffffff; border-right: 1px solid #ffffff; background-color: #f2f4f6; ";
												echo "font-size: 0.9em; text-align:right; border-top: 0px solid #768697; padding: 9px;\" width=\"23%\">Destination</td>";
												echo "<td style=\"border-bottom: 1px solid #ffffff; background-color: #f2f4f6; font-size: 0.9em; ";
												echo "border-top: 0px solid #768697; width: 100%; padding: 9px;\">";
												echo "<a href=\"#\" style=\"color: #800000;\" id=\"doPrior_mads_associatedLocale\">";
												$doPrior_mads_associatedLocale = $rowP[21];
												echo "$rowP[21]";
												echo "</a>";
												echo "</td>";
												echo "</tr>";
												$foundPrior = "y";
											}										
										}

/////////////////// No Previous MetaData
										
										if(($foundPrior == "")) {
											echo "<tr>";
											echo "<td style=\"border-bottom: 1px solid #ffffff; border-right: 1px solid #ffffff; background-color: #f2f4f6; ";
											echo "font-size: 0.9em; text-align:right; border-top: 0px solid #768697; padding: 9px;\" colspan=\"2\">No previous information.</td>";
											echo "</tr>";
										}

///////////////////////////////////// Close Previous Item-Level MetaData Table 
										
										echo "</tbody>";
                                		echo "</table>"; 
										                
									}

///////////////////////////////////// Copy and Save ALL Previous MetaData

									if(($itemFields["dct_accessRights"] != "restricted") && ($foundPrior == "y")) {
										echo "<button class=\"btn btn-info col-lg-12\" id=\"input_submit_prior\"><strong>Click to Copy and Save<br />All Previous Item's Metadata</strong></button>";
									}
									
								?>
                            </div>
                        </div>
      				</div>
  				</div>
			</div>
            <!-- <br /> //-->
 <?php

///////////////////////////////////////////////////////////// Tag Metadata

?> 
            <div class="panel" style="background-color: #FFFFFF; border: 1px solid #A52731;">
                <div class="panel-heading">
					<div class="panel-control">
                    	<button class="btn btn-default" id="btn-refresh-tags"><i class="ion-refresh" style="color: #cacaca; font-size: 1.4em;"></i></button>
                        <?php
						
							if(($itemFields["dct_accessRights"] != "restricted")) {
							
						?>
						<button class="btn btn-default" data-target="#tag-panel-collapse" data-toggle="collapse">
                        	<i class="ion-chevron-up" style="color: #cacaca; font-size: 1.4em;"></i></button>
                        <?php
						
							}
							
						?>
					</div>
					<h4 class="panel-title"><a name="scrollMentions">Mentions</a></h4>
				</div>
  				<div id="tag-panel-collapse" class="collapse in">
    				<div class="panel-body">
                    	<?php
						
							if(($itemFields["dct_accessRights"] != "restricted")) {
							
						?>
                    	<div id="addCustomMeta">
                        	<table class="table table-condensed table-hover" width="99%">
                            	<tbody>
								<?php
								
///////////////////////////////////// Title                           
                                        
                        			echo "<tr>";
                         			echo "<td style=\"color: #FFFFFF; background-color: #1B4F74; padding-top: 10px; padding-bottom: 10px; ";
                            		echo "font-size: 0.9em; font-weight: 900;text-align:right;\">#</td>";
                           			echo "<td style=\"color: #FFFFFF; background-color: #1B4F74; padding-top: 10px; padding-bottom: 10px; ";
                           			echo "font-size: 0.9em; font-weight: 900;\">$runningTitle</td>";
                               		echo "</tr>";
 
///////////////////////////////////// Identifer										
//										
//									echo "<tr>";
//                    				echo "<td style=\"padding-top: 10px; padding-bottom: 10px; font-size: 0.9em; text-align:right; border-top: 0px solid #768697;\">ID</td>";
//	                   				echo "<td style=\"padding-top: 10px; padding-bottom: 10px; font-size: 0.9em; border-top: 0px solid #768697;\">$runningID</td>";
//    	               				echo "</tr>";
// 
///////////////////////////////////// Key Dropdown
                                
                                    $itemLabels = array();
                                    $queryD = "SELECT * FROM labels ORDER BY skos_definition ASC ";
                                    $mysqli_resultD = mysqli_query($mysqli_link, $queryD);
                                    while($rowD = mysqli_fetch_row($mysqli_resultD)) { 
                                        $itemLabels[] = $rowD[4]."|".$rowD[2]."|".$rowD[3];
                                    }
                                    sort($itemLabels);
                                	echo "<tr>";
                                   	echo "<td style=\"font-size: 0.9em; text-align:right; border-top: 0px solid #768697; padding-top: 10px;\" width=\"23%\" nowrap>Key&nbsp;or&nbsp;Label</td>";
                                    echo "<td style=\"font-size: 0.9em; border-top: 0px solid #768697; padding-left: 10px; padding-right: 0px;\">";
                                    echo "<select ";
                                    echo "style=\"font-size: 1.0em; font-weight; 900;\" ";
                                    echo "class=\"show-tick\" ";
                                    echo "data-size=\"12\" ";
                                    echo "data-width=\"100%\" ";
                                	echo "data-live-search=\"true\" ";
                                    echo "id=\"input-tag-key\" ";
                                    echo "name=\"input-tag-key\" ";
                                    echo "onchange=\"var clearThis = $('#input-tag-value').val('');\" ";
                                    echo ">";
                                    echo "<option style=\"font-size: 0.9em; white-space: normal;\" data-divider=\"true\"></option>";
                                    foreach($itemLabels as $iL) {
                                        $bits = explode("|","$iL");
                                        echo "<option style=\"font-size: 0.9em; white-space: normal;\" ";
										if(($bits[0] == "Subject")) { 
											echo "selected "; 
										}
										echo "value=\"".$bits[1].":".$bits[2]."\">".$bits[0]."</option>";
                                    }
                                    echo "<option style=\"font-size: 0.9em; white-space: normal;\" data-divider=\"true\"></option>";
									echo "<option style=\"color: #800000; font-size: 0.9em; white-space: normal;\" disabled>If do you ";
									echo "not see an appropriate KEY, please contact the research manager to add a new KEY as identified from a ";
									echo "RDF-compatible ontology or namespace.</option>";
									echo "<option style=\"font-size: 0.9em; white-space: normal;\" data-divider=\"true\"></option>";
                                    echo "</select>";
                                	echo "</td>";
                                    echo "</tr>";
									
///////////////////////////////////// Value Input

									echo "<tr>";
                                    echo "<td style=\"font-size: 0.9em; text-align:right; border-top: 0px solid #768697; padding-top: 10px;\" width=\"23%\" nowrap>Value</td>";
                                    echo "<td style=\"font-size: 0.9em; border-top: 0px solid #768697; width: 100%; \">";
									echo "<textarea rows=\"2\" ";
                                    echo "style=\"font-size: 1.0em; border: 1px solid #27a59b; resize: none; \" ";
                                    echo "class=\"form-control\" ";
                                    echo "id=\"input-tag-value\" ";
                                    echo "name=\"input-tag-value\" >";
									echo "</textarea>";
                                    echo "</td>";
                                    echo "</tr>";									
  
///////////////////////////////////// Apply All Items Checkbox
  
									echo "<tr>";
									echo "<td style=\"font-size: 0.9em; text-align:right; border-top: 0px solid #768697; ";
									echo "padding-top: 10px; padding-right: 5px; padding-bottom: 10px;\" width=\"23%\" nowrap>";
									echo "<input type=\"checkbox\" id=\"apply-tag-all\" name=\"apply-tag-all\" value=\"ALL\" ";
									echo "style=\"-ms-transform: scale(1.5); -moz-transform: scale(1.5); -webkit-transform: scale(1.5); -o-transform: scale(1.5); \">";
									echo "</td>";
									echo "<td style=\"font-size: 0.9em; border-top: 0px solid #768697; width: 100%; padding-top: 10px; ";
									echo "padding-left: 10px; text-align: left; padding-bottom: 10px;\">";
									echo "<strong><span style=\"color: #800000;\">Apply to ALL items in this COLLECTION?</span></strong>";
									echo "</td>";
									echo "</tr>";
                                
                                ?>
                        		</tbody>
                        	</table>
                            <?php 
							
///////////////////////////////////// Submit New Metadata
							
								echo "<button class=\"btn btn-success col-lg-12\" id=\"input-submit_tag\"><strong>Add Mention</strong></button>"; 
								
							?>
                            <br />&nbsp;<br />&nbsp;
                        </div>
                        <?php
						
							}
						
						?>
                    	<div id="refreshCustomMeta" style="display: block; height: auto; padding-bottom: 0px;">
                        	<div id="refreshCustomMetaMain">
							<?php
                            
                                $reload = "No";
                                include("./data_meta_tag.php");
                            
                            ?>
                        	</div>
                    	</div>
      				</div>
  				</div>
			</div>
            <br />
<?php

///////////////////////////////////////////////////////////// Close Accordian
		
		}

///////////////////////////////////////////////////////////// Finish AJAX Load
		
	} else {
	
///////////////////////////////////////////////////////////// Default Listing	
		
////////////////////////////////////// Database Structure		
		
		echo "<p style=\"text-align:justify; background-color: #FFFFFF; padding: 10px; border: 1px solid #777778; border-radius: 5px; \">";
		echo "<a class=\"extLink\" id=\"extLink\" href=\"./img/db_draft.png\">";
		echo "<img src=\"./img/db_draft_sm.png\" width=\"100%\" border=\"0\" style=\"border: 0px solid #777777;\">";
		echo "</a>";
		echo "</p>";	
		echo "<p>&nbsp;</p>";		
		
////////////////////////////////////// Schemas		
		
		echo "<p><strong>SCHEMAS</strong></p>";
		echo "<p style=\"text-align:justify;\">This panel will load the form for assigning, adding, deleting or modifying existing <em>metadata</em> that are associated ";
		echo "with the item selected in the right-hand panel. You can also assign <em>mentions</em>, with keywords drawn from the following schemas:</p>";
		echo "<p>&nbsp;</p>";
		$j = 1;
		echo "<div class=\"btn-group btn-group-justified\" role=\"group\" aria-label=\"Justified button group\">";
		$queryD = "SELECT * FROM namespaces ORDER BY reg_uri ASC";
		$mysqli_resultD = mysqli_query($mysqli_link, $queryD);
		while($rowD = mysqli_fetch_row($mysqli_resultD)) { 
			echo "<div class=\"btn-group\" role=\"group\" style=\"padding-right:1px;\">";
			echo "<a href=\"$rowD[2]\" target=\"_RDF\" style=\"color: #FFFFFF;\">";
			echo "<button class=\"btn btn-primary\" style=\"margin-right: 1px; margin-bottom: 1px; font-size: 0.8em;\">";
			echo "<strong>";
			echo strtoupper($rowD[1]);
			echo "</strong>";
			echo "</button>";
			echo "</a>";
			echo "</div>";
			if(($j == 3)) {
				echo "</div>";
				echo "<div class=\"btn-group btn-group-justified\" role=\"group\" aria-label=\"Justified button group\">";
				$j = 1;
			} else {
				$j++;
			}
		}
		echo "</div>";
		echo "<p>&nbsp;</p>";
		
////////////////////////////////////// Project Team

		echo "<p><strong>PROJECT TEAM</strong></p>";
		echo "<p>&nbsp;</p>";
		echo "<p style=\"text-align:justify; background-color: #FFFFFF; padding: 25px; border: 1px solid #777778; border-radius: 5px;\">";
		echo "<strong>Dr Jason Ensor</strong><br />2016 - Current<br />Manager, Library Digital Infrastructure, ";
		echo "University Library, Western Sydney University.";
		echo "</p>";
		echo "<p style=\"text-align:justify; background-color: #FFFFFF; padding: 25px; border: 1px solid #777778; border-radius: 5px;\">";
		echo "<strong>Dr Helen Bones</strong><br />2016 - Current<br />Research Officer in Digital Humanities, ";
		echo "University Library, Western Sydney University.";
		echo "</p>";
		echo "<p style=\"text-align:justify; background-color: #FFFFFF; padding: 25px; border: 1px solid #777778; border-radius: 5px;\">";
		echo "<strong>Michael Gonzalez</strong><br />2016 - 2017<br />University Librarian, University of Technology, Sydney.";
		echo "</p>";
		echo "<p style=\"text-align:justify; background-color: #FFFFFF; padding: 25px; border: 1px solid #777778; border-radius: 5px;\">";
		echo "<strong>Stephen Hannan</strong><br />2016<br />Executive Director, REDI (Research, Development &amp; Engagement), Western Sydney University.";
		echo "</p>";		
		echo "<p style=\"text-align:justify; background-color: #FFFFFF; padding: 25px; border: 1px solid #777778; border-radius: 5px;\">";
		echo "<strong>Professor Simon Burrows</strong><br />2016<br />Professor in Digital Humanities, ";
		echo "Digital Humanities Research Group, Western Sydney University.";
		echo "</p>";
		echo "<p>&nbsp;</p>";
		
////////////////////////////////////// Logos	
		
		echo "<p><strong>PARTNERS</strong></p>";
		echo "<p>&nbsp;</p>";
		echo "<p style=\"text-align:center; background-color: #FFFFFF; padding: 25px; border: 1px solid #777778; border-radius: 5px;\">";
		echo "<img src=\"./logo_SLNSW.jpg\" width=\"50%\" border=\"0\" style=\"border: 0px solid #777777;\">";
		echo "</p>";
		echo "<p style=\"text-align:center; background-color: #FFFFFF; padding: 25px; border: 1px solid #777778; border-radius: 5px;\">";
		echo "<img src=\"./logo_ANDS.jpg\" width=\"70%\" border=\"0\" style=\"border: 0px solid #777777;\">";
		echo "</p>";
		echo "<p style=\"text-align:center; background-color: #FFFFFF; padding: 25px; border: 1px solid #777778; border-radius: 5px;\">";
		echo "<img src=\"./logo_WSU.jpg\" width=\"80%\" border=\"0\" style=\"border: 0px solid #777777;\">";
		echo "</p>";
		echo "<p>&nbsp;</p>";		
		
///////////////////////////////////////////////////////////// Default Listing End	
			
	}

////////////////////////////////////// Scripts

?>
    <script language="javascript" type="text/javascript" >
	
		$(document).ready(function() {	
		
/////////////////////////////////////////////////////////// Toggle Flag
				
				$(".mbtn-toggle").click(function(event) {
 					var currentState = $(this).attr('data-status');
					if(currentState == "YES") {
						var changeID = $(this).attr('data-id');
						var changeCss = $(this).css('color','#8B0D82');
						var changeStatus = $(this).attr('data-status','NO');
						var dataE = "action=add&items_dc_identifier=" + changeID;
						var searchValP = $('#theDarkCloset').load('./data_get_flag.php', dataE, function(){});				
					} else {
						var changeID = $(this).attr('data-id');
						var changeCss = $(this).css('color','#BBBBBB');
						var changeStatus = $(this).attr('data-status','YES');
						var dataE = "action=delete&items_dc_identifier=" + changeID;
						var searchValQ = $('#theDarkCloset').load('./data_get_flag.php', dataE, function(){});
					}	
				});			
		
/////////////////////////////////////////////////////////// Toggle Lock				

				$(".mlock-toggle").click(function(event) {
 					var currentState = $(this).attr('data-status');
					if(currentState == "UNLOCKED") {
						var changeID = $(this).attr('data-id');
						var changeCss = $(this).css('color','#68C970');
						var changeStatus = $(this).attr('data-status','LOCKED');
						var changeClass = $(this).removeClass('ion-unlocked').addClass('ion-locked');
						var otherchangeID = "#LOCK_" + changeID;
						var otherchangeCss = $(otherchangeID).css('color','#68C970');
						var otherchangeStatus = $(otherchangeID).attr('data-status','LOCKED');
						var otherchangeClass = $(otherchangeID).removeClass('ion-unlocked').addClass('ion-locked');
						var dataE = "action=lock&items_dc_identifier=" + changeID;
						var searchValP = $('#theDarkCloset').load('./data_get_lock.php', dataE, function(){
							var dataF = "dc_identifier=" + changeID + "&reload=";
							var doDivM = $("#titleTags").fadeOut('fast', function(){
        						var doDivN = $("#titleTags").load('./data_meta.php',dataF, function(){
        							var doDivO = $('#titleTags').fadeIn('slow');
        						});
        					});
						});				
					} else {
						var changeID = $(this).attr('data-id');
						var changeCss = $(this).css('color','#D93427');
						var changeStatus = $(this).attr('data-status','UNLOCKED');
						var changeClass = $(this).removeClass('ion-locked').addClass('ion-unlocked');
						var otherchangeID = "#LOCK_" + changeID;
						var otherchangeCss = $(otherchangeID).css('color','#D93427');
						var otherchangeStatus = $(otherchangeID).attr('data-status','UNLOCKED');
						var otherchangeClass = $(otherchangeID).removeClass('ion-locked').addClass('ion-unlocked');
						var dataE = "action=unlock&items_dc_identifier=" + changeID;
						var searchValQ = $('#theDarkCloset').load('./data_get_lock.php', dataE, function(){
							var dataF = "dc_identifier=" + changeID + "&reload=";
							var doDivM = $("#titleTags").fadeOut('fast', function(){
        						var doDivN = $("#titleTags").load('./data_meta.php',dataF, function(){
        							var doDivO = $('#titleTags').fadeIn('slow');
        						});
        					});
						});
					}								
				});			
		
/////////////////////////////////////////////////////////// Submit Item Data

			$("#input_submit_prior").click(function(event) {
				var data_dc_identifier = "<?php echo $dc_identifier; ?>";
				var data_dc_creator = "<?php echo $doPrior_dc_creator; ?>";
				var data_gn_name = "<?php echo $doPrior_gn_name; ?>";
				var data_org_FormalOrganisation = "<?php echo $doPrior_org_FormalOrganisation; ?>";
				var data_marc_addressee = "<?php echo $doPrior_marc_addressee; ?>";
				var data_rdaa_groupMemberOf = "<?php echo $doPrior_rdaa_groupMemberOf; ?>";
				var data_mads_associatedLocale = "<?php echo $doPrior_mads_associatedLocale; ?>";
				var data_dc_description = $("#input-dc_description").val();
				var data_dc_created = $("#input-dc_created").val();
				var dataAll = "action=UPDATE"
					+"&data_dc_identifier="+data_dc_identifier
					+"&data_dc_creator="+data_dc_creator
					+"&data_gn_name="+data_gn_name
					+"&data_org_FormalOrganisation="+data_org_FormalOrganisation
					+"&data_dc_description="+data_dc_description
					+"&data_dc_created="+data_dc_created
					+"&dc_identifier="+data_dc_identifier
					+"&data_marc_addressee="+data_marc_addressee
					+"&data_rdaa_groupMemberOf="+data_rdaa_groupMemberOf
					+"&data_mads_associatedLocale="+data_mads_associatedLocale;
				var doDivMa = $('#titleTags').fadeOut('fast', function(){
        			var doDivNa = $('#titleTags').load('./data_meta.php',dataAll, function(){
        				var doDivOa = $('#titleTags').fadeIn('slow');
        			});
        		});	
			});		
		
/////////////////////////////////////////////////////////// Replace Prior dc_creator
		
			$("#doPrior_dc_creator").click(function(event) {
				var tagValue = "<?php echo $doPrior_dc_creator; ?>";
				var startPrior = $("#input-dc_creator").remove();
				$("#input-dc_creator_frame").html('<?php
					echo "<select ";
					echo "style=\"font-size: 1.0em; ";
					echo "white-space: normal!important; ";
					echo "word-wrap: break-word!important; ";
					echo "white-space: -moz-pre-wrap!important; ";
					echo "white-space: pre-wrap!important; ";
					echo "\" ";
					echo "class=\"show-tick\" ";
					echo "data-size=\"9\" ";
					echo "data-width=\"100%\" ";
					echo "data-live-search=\"true\" ";
					echo "id=\"input-dc_creator\" ";
					echo "name=\"input-dc_creator\" ";
					echo "> ";
					echo "<option style=\"font-size: 0.9em; ";
					echo "white-space: normal!important; ";
					echo "word-wrap: break-word!important; ";
					echo "white-space: -moz-pre-wrap!important; ";
					echo "white-space: pre-wrap!important; ";
					echo "\" value=\"$doPrior_dc_creator\" >";
					echo "$doPrior_dc_creator";
					echo "</option>";
					echo "</select>";
				?>');							
				var finishPrior = $('#input-dc_creator').selectpicker();														
			});
		
/////////////////////////////////////////////////////////// Replace Prior org_FormalOrganisation			
			
			$("#doPrior_org_FormalOrganisation").click(function(event) {
				var tagValue = "<?php echo $doPrior_org_FormalOrganisation; ?>";
				var startPrior = $("#input-org_FormalOrganisation").remove();
				$("#input-org_FormalOrganisation_frame").html('<?php
					echo "<select ";
					echo "style=\"font-size: 1.0em; ";
					echo "white-space: normal!important; ";
					echo "word-wrap: break-word!important; ";
					echo "white-space: -moz-pre-wrap!important; ";
					echo "white-space: pre-wrap!important; ";
					echo "\" ";
					echo "class=\"show-tick\" ";
					echo "data-size=\"9\" ";
					echo "data-width=\"100%\" ";
					echo "data-live-search=\"true\" ";
					echo "id=\"input-org_FormalOrganisation\" ";
					echo "name=\"input-org_FormalOrganisation\" ";
					echo "> ";
					echo "<option style=\"font-size: 0.9em; ";
					echo "white-space: normal!important; ";
					echo "word-wrap: break-word!important; ";
					echo "white-space: -moz-pre-wrap!important; ";
					echo "white-space: pre-wrap!important; ";
					echo "\" value=\"$doPrior_org_FormalOrganisation\" >";
					echo "$doPrior_org_FormalOrganisation";
					echo "</option>";
					echo "</select>";
				?>');							
				var finishPrior = $('#input-org_FormalOrganisation').selectpicker();														
			});		
		
/////////////////////////////////////////////////////////// Replace Prior gn_name

			$("#doPrior_gn_name").click(function(event) {
				var startPrior = $("#input-gn_name").val("<?php echo $doPrior_gn_name; ?>");							
			});	

/////////////////////////////////////////////////////////// Replace Prior marc_addressee

			$("#doPrior_marc_addressee").click(function(event) {
				var tagValue = "<?php echo $doPrior_marc_addressee; ?>";
				var startPrior = $("#input-marc_addressee").remove();
				$("#input-marc_addressee_frame").html('<?php
					echo "<select ";
					echo "style=\"font-size: 1.0em; ";
					echo "white-space: normal!important; ";
					echo "word-wrap: break-word!important; ";
					echo "white-space: -moz-pre-wrap!important; ";
					echo "white-space: pre-wrap!important; ";
					echo "\" ";
					echo "class=\"show-tick\" ";
					echo "data-size=\"9\" ";
					echo "data-width=\"100%\" ";
					echo "data-live-search=\"true\" ";
					echo "id=\"input-marc_addressee\" ";
					echo "name=\"input-marc_addressee\" ";
					echo "> ";
					echo "<option style=\"font-size: 0.9em; ";
					echo "white-space: normal!important; ";
					echo "word-wrap: break-word!important; ";
					echo "white-space: -moz-pre-wrap!important; ";
					echo "white-space: pre-wrap!important; ";
					echo "\" value=\"$doPrior_marc_addressee\" >";
					echo "$doPrior_marc_addressee";
					echo "</option>";
					echo "</select>";
				?>');							
				var finishPrior = $('#input-marc_addressee').selectpicker();														
			});

/////////////////////////////////////////////////////////// Replace Prior rdaa_groupMemberOf

			$("#doPrior_rdaa_groupMemberOf").click(function(event) {
				var tagValue = "<?php echo $doPrior_rdaa_groupMemberOf; ?>";
				var startPrior = $("#input-rdaa_groupMemberOf").remove();
				$("#input-rdaa_groupMemberOf_frame").html('<?php
					echo "<select ";
					echo "style=\"font-size: 1.0em; ";
					echo "white-space: normal!important; ";
					echo "word-wrap: break-word!important; ";
					echo "white-space: -moz-pre-wrap!important; ";
					echo "white-space: pre-wrap!important; ";
					echo "\" ";
					echo "class=\"show-tick\" ";
					echo "data-size=\"9\" ";
					echo "data-width=\"100%\" ";
					echo "data-live-search=\"true\" ";
					echo "id=\"input-rdaa_groupMemberOf\" ";
					echo "name=\"input-rdaa_groupMemberOf\" ";
					echo "> ";
					echo "<option style=\"font-size: 0.9em; ";
					echo "white-space: normal!important; ";
					echo "word-wrap: break-word!important; ";
					echo "white-space: -moz-pre-wrap!important; ";
					echo "white-space: pre-wrap!important; ";
					echo "\" value=\"$doPrior_rdaa_groupMemberOf\" >";
					echo "$doPrior_rdaa_groupMemberOf";
					echo "</option>";
					echo "</select>";
				?>');							
				var finishPrior = $('#input-rdaa_groupMemberOf').selectpicker();														
			});

/////////////////////////////////////////////////////////// Replace Prior mads_associatedLocale		
		
			$("#doPrior_mads_associatedLocale").click(function(event) {
				var startPrior = $("#input-mads_associatedLocale").val("<?php echo $doPrior_mads_associatedLocale; ?>");							
			});	
		
/////////////////////////////////////////////////////////// Origin Autocomplete
				
			$("#input-gn_name").autocomplete({
				source: function(request, response){
					$.ajax({
						url: "./data_locations.php",
						dataType: "json",
						data: {
							term : request.term,
							variation : "LOCATIONS"
						},
						success: function (data) {
							response(data);
						}
					});
				},
				minLength: 4,
				delay: 500, 
				maxCacheLength: 4, 
				select: function(event, ui) {
					if(ui.item){
						var valink = ui.item.label;
						var lablink = ui.item.value;
						var cleanBarB = $('#input-gn_name').val(''+valink);						
						return false;
					}
				}
			});	
				
/////////////////////////////////////////////////////////// Destination Autocomplete				
				
			$("#input-mads_associatedLocale").autocomplete({
				source: function(request, response){
					$.ajax({
						url: "./data_locations.php",
						dataType: "json",
						data: {
							term : request.term,
							variation : "LOCATIONS"
						},
						success: function (data) {
							response(data);
						}
					});
				},
				minLength: 4,
				delay: 500, 
				maxCacheLength: 4, 
				select: function(event, ui) {
					if(ui.item){
						var valink = ui.item.label;
						var lablink = ui.item.value;
						var cleanBarB = $('#input-mads_associatedLocale').val(''+valink);						
						return false;
					}
				}
			});						

/////////////////////////////////////////////////////////// Annotations Autocomplete				

			$("#input-tag-value").autocomplete({
				source: function(request, response){
					var myKey = $("#input-tag-key").val();
					$.ajax({
						url: "./data_annotations.php",
						dataType: "json",
						data: {
							term : request.term,
							thiskey : myKey, 
							anidentifier : "<?php echo $dc_identifier; ?>", 
							variation : "ANNOTATIONS"
						},
						success: function (data) {
							response(data);
						}
					});
				},
				minLength: 4,
				delay: 500, 
				maxCacheLength: 4, 
				select: function(event, ui) {
					if(ui.item){
						var valink = ui.item.label;
						var lablink = ui.item.value;
						var cleanBarB = $('#input-tag-value').val(''+valink);						
						return false;
					}
				}
			});	
			
/////////////////////////////////////////////////////////// Submit Item Data

			$("#input_submit_item").click(function(event) {
				var data_dc_identifier = "<?php echo $dc_identifier; ?>";
				var data_dc_creator = $("#input-dc_creator option:selected").val();
				var data_gn_name = $("#input-gn_name").val();
				var data_org_FormalOrganisation = $("#input-org_FormalOrganisation option:selected").val();
				var data_marc_addressee = $("#input-marc_addressee option:selected").val();
				var data_rdaa_groupMemberOf = $("#input-rdaa_groupMemberOf option:selected").val();
				var data_mads_associatedLocale = $("#input-mads_associatedLocale").val();
				var data_dc_description = $("#input-dc_description").val();
				var data_dc_created = $("#input-dc_created").val();
				var dataAll = "action=UPDATE"
					+"&data_dc_identifier="+data_dc_identifier
					+"&data_dc_creator="+data_dc_creator
					+"&data_gn_name="+data_gn_name
					+"&data_org_FormalOrganisation="+data_org_FormalOrganisation
					+"&data_dc_description="+data_dc_description
					+"&data_dc_created="+data_dc_created
					+"&dc_identifier="+data_dc_identifier
					+"&data_marc_addressee="+data_marc_addressee
					+"&data_rdaa_groupMemberOf="+data_rdaa_groupMemberOf
					+"&data_mads_associatedLocale="+data_mads_associatedLocale;
				var doDivMa = $('#titleTags').fadeOut('fast', function(){
        			var doDivNa = $('#titleTags').load('./data_meta.php',dataAll, function(){
        				var doDivOa = $('#titleTags').fadeIn('slow');
        			});
        		});	
			});
		
/////////////////////////////////////////////////////////// DC Creator Dropdown

			$('#input-dc_creator').selectpicker();
			$('#input-org_FormalOrganisation').selectpicker();
			$('#input-marc_addressee').selectpicker();
			$('#input-rdaa_groupMemberOf').selectpicker();
			$('#input-tag-key').selectpicker();

/////////////////////////////////////////////////////////// DC Created Datepicker
		
			$('#input-dc_created').datepicker({
        		format: "yyyy-mm-dd",
				orientation: "bottom left",
        		autoclose: true
    		});

/////////////////////////////////////////////////////////// Refresh Tags Button
		
			$("#btn-refresh-tags").click(function(event) {
				var dataFa = "dc_identifier=<?php echo $dc_identifier; ?>&reload=";
				var doDivMa = $('#titleTags').fadeOut('fast', function(){
        			var doDivNa = $('#titleTags').load('./data_meta.php',dataFa, function(){
        				var doDivOa = $('#titleTags').fadeIn('slow');
        			});
        		});	
			});
			
/////////////////////////////////////////////////////////// Refresh Items Button
		
			$("#btn-refresh-items").click(function(event) {
				var dataFa = "dc_identifier=<?php echo $dc_identifier; ?>&reload=";
				var doDivMa = $('#titleTags').fadeOut('fast', function(){
        			var doDivNa = $('#titleTags').load('./data_meta.php',dataFa, function(){
        				var doDivOa = $('#titleTags').fadeIn('slow');
        			});
        		});	
			});	
			
/////////////////////////////////////////////////////////// Refresh OCR Button
			
			$("#btn-refresh-ocr").click(function(event) {
				var dataFa = "dc_identifier=<?php echo $dc_identifier; ?>&reload=";
				var doDivMa = $('#titleTags').fadeOut('fast', function(){
        			var doDivNa = $('#titleTags').load('./data_meta.php',dataFa, function(){
        				var doDivOa = $('#titleTags').fadeIn('slow');
        			});
        		});	
			});
			
/////////////////////////////////////////////////////////// Submit Key Value Pair

			$("#input-submit_tag").click(function(event) {
				var tagValue = $("#input-tag-value").val();
				if($("#apply-tag-all").prop("checked") == true) {
					var data_apply_tag_all = "ALL";	
				} else {
					var data_apply_tag_all = "0";	
				}
				if(tagValue != "") {
					var tagKey = $("#input-tag-key option:selected" ).val();
					var itemsDCIdentifier = "<?php echo $dc_identifier; ?>";
					var itemsUUID = "<?php echo $itemFields["iana_UUID"]; ?>";
					var divH = $('#refreshCustomMeta').height()+100;  
					var divHb = $('#refreshCustomMeta').height(divH); 
					var dataFa = "dc_identifier="+itemsDCIdentifier
						+"&items_dc_identifier="+itemsDCIdentifier
						+"&action=ADD"
						+"&input_tag_value="+tagValue
						+"&input_tag_key="+tagKey
						+"&data_apply_tag_all="+data_apply_tag_all
						+"&items_UUID="+itemsUUID; 
					var doDivMa = $('#refreshCustomMetaMain').fadeOut('fast', function(){ 
						var doDivNa = $('#refreshCustomMetaMain').load('./data_meta_tag.php',dataFa, function(){ 
							var doDivOa = $('#refreshCustomMetaMain').fadeIn('fast'); 		
							var divHb = $('#refreshCustomMeta').height(divH); 
						}); 
					}); 
				}
			});

/////////////////////////////////////////////////////////// Editable Data
			
			$.fn.editable.defaults.mode = 'popup';
			$.fn.editable.defaults.placement = 'right';
			$.fn.editable.defaults.ajaxOptions = { 
				type: "GET"
			};
			
			<?php 
			
				if(($do_Edits == "y")) {
					foreach($itemFields as $i) { 
			
			?>
			
			$('#key_<?php echo $i; ?>').editable({
				placement : 'left',
				type : 'textarea',
				params: function(params) {
					var data = {};
					data['dc_identifier'] = params.pk;
					data[params.name] = params.value;
					return data;
				}
			});
			
			<?php 
			
					}
				} 
				
			?>
			
		});
		
	</script>
<?php

///////////////////////////////////////////////////////////// Finish

	if(($reload == "")) {
		include("./ar.dbdisconnect.php");
	}
	
?>