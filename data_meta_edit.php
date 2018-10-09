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
//  9-10 February 2017
//  13 February 2017
//  24 February 2017
//  1-2 March 2017
//  14 March 2017
//	8 August 2018
//
//
/////////////////////////////////////////////////////////// Clean post and get	
	
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
	$action = $_GET["action"];
	$iana_UUID = $_GET["iana_UUID"];
	$items_UUID = $_GET["items_UUID"];
	$dc_identifier = $_GET["dc_identifier"];
	$items_dc_identifier = $_GET["items_dc_identifier"];
	$data_edit_tag_key = $_GET["data_edit_tag_key"];
	$data_edit_tag_value = $_GET["data_edit_tag_value"];
	$data_edit_tag_key_two = $_GET["data_edit_tag_key_two"];
	$data_edit_tag_value_two = $_GET["data_edit_tag_value_two"];
	$data_edit_tag_key_three = $_GET["data_edit_tag_key_three"];
	$data_edit_tag_value_three = $_GET["data_edit_tag_value_three"];
	$data_edit_tag_all = $_GET["data_edit_tag_all"];
	$_GET = array();
	$_POST = array();
	$contributor = "contrib41T71U4BZZ";
	
///////////////////////////////////////////////////////////// UPDATE Function

	if(($dc_identifier != "") && ($action == "EDIT_UPDATE") && ($iana_UUID != "") 
		&& ($items_dc_identifier != "") && ($items_UUID != "") && ($data_edit_tag_key != "") 
		&& ($data_edit_tag_value != "") && ($data_edit_tag_all != "")) {	
		
///////////////////////////////////// If ALL		
				
		if(($data_edit_tag_all == "ALL")) {
			
///////////////////////////////////// If 2nd and 3rd Additions			
			
			if(($data_edit_tag_key_two != "") && ($data_edit_tag_value_two != "")) {
				$additionsRefs = array();
				$queryD = "SELECT * FROM annotations WHERE iana_UUID = \"$iana_UUID\" AND dc_references = \"$dc_identifier\" ";
				$mysqli_resultD = mysqli_query($mysqli_link, $queryD);
				while($rowD = mysqli_fetch_row($mysqli_resultD)) { 
					$a_old_reg_uri = $rowD[5];
					$a_old_rdfs_label = $rowD[6];
					$a_old_value_string = $rowD[7];
					$a_foundOld = "y";
				}
				if(($a_foundOld == "y") && ($a_old_reg_uri != "") && ($a_old_rdfs_label != "") && ($a_old_value_string != "")) {
					$queryD = "SELECT * FROM annotations ";
					$queryD .= "WHERE reg_uri = \"$a_old_reg_uri\" ";
					$queryD .= "AND rdfs_label = \"$a_old_rdfs_label\" ";
					$queryD .= "AND value_string = \"$a_old_value_string\" ";	
					$queryD .= "ORDER BY ID ASC ";	
					$mysqli_resultD = mysqli_query($mysqli_link, $queryD);
					while($rowD = mysqli_fetch_row($mysqli_resultD)) {
						$additionsRefs[] = "$rowD[2]"."|"."$rowD[3]"."|"."$rowD[4]";
					}
					$cA = count($additionsRefs);
					if(($cA > 0)) {
						foreach($additionsRefs as $aR) {
						
///////////////////////////////////// Do Insert for Key 2							
							
							if(($data_edit_tag_key_two != "") && ($data_edit_tag_value_two != "")) {
								$new_iana_UUID = guidv4();
								$aRs = explode("|","$aR");
								$keys = explode(":","$data_edit_tag_key_two");
								$queryD = "INSERT INTO annotations VALUES (";
								$queryD .= "\"0\", ";
								$queryD .= "\"$new_iana_UUID\", ";
								$queryD .= "\"$aRs[0]\", ";
								$queryD .= "\"$aRs[1]\", ";
								$queryD .= "\"$aRs[2]\", ";
								$queryD .= "\"".$keys[0]."\", ";
								$queryD .= "\"".$keys[1]."\", ";
								$queryD .= "\"$data_edit_tag_value_two\", ";
								$queryD .= "\"\", ";
								$queryD .= "\"\", ";
								$queryD .= "\"$contributor\", ";
								$queryD .= "NOW() ";
								$queryD .= ");";
								$mysqli_resultD = mysqli_query($mysqli_link, $queryD);
							}
							
///////////////////////////////////// Do Insert for Key 3							
							
							if(($data_edit_tag_key_three != "") && ($data_edit_tag_value_three != "")) {
								$new_iana_UUID = guidv4();
								$aRs = explode("|","$aR");
								$keys = explode(":","$data_edit_tag_key_three");
								$queryD = "INSERT INTO annotations VALUES (";
								$queryD .= "\"0\", ";
								$queryD .= "\"$new_iana_UUID\", ";
								$queryD .= "\"$aRs[0]\", ";
								$queryD .= "\"$aRs[1]\", ";
								$queryD .= "\"$aRs[2]\", ";
								$queryD .= "\"".$keys[0]."\", ";
								$queryD .= "\"".$keys[1]."\", ";
								$queryD .= "\"$data_edit_tag_value_three\", ";
								$queryD .= "\"\", ";
								$queryD .= "\"\", ";
								$queryD .= "\"$contributor\", ";
								$queryD .= "NOW() ";
								$queryD .= ");";
								$mysqli_resultD = mysqli_query($mysqli_link, $queryD);
							}
						}
					}
				}
			}

///////////////////////////////////// Change All Other Instances
			
			$queryD = "SELECT * FROM annotations WHERE iana_UUID = \"$iana_UUID\" AND dc_references = \"$dc_identifier\" ";
			$mysqli_resultD = mysqli_query($mysqli_link, $queryD);
			while($rowD = mysqli_fetch_row($mysqli_resultD)) { 
				$old_reg_uri = $rowD[5];
				$old_rdfs_label = $rowD[6];
				$old_value_string = $rowD[7];
				$foundOld = "y";
			}
			if(($foundOld == "y") && ($old_reg_uri != "") && ($old_rdfs_label != "") && ($old_value_string != "")) {
				$keyZ = explode(":", "$data_edit_tag_key");
				if(($keyZ[0] != "") && ($keyZ[1] !="")) {
					$queryD = "UPDATE annotations ";
					$queryD .= "SET reg_uri = \"$keyZ[0]\", ";
					$queryD .= "rdfs_label = \"$keyZ[1]\", ";
					$queryD .= "value_string = \"$data_edit_tag_value\" ";
					$queryD .= "WHERE reg_uri = \"$old_reg_uri\" ";
					$queryD .= "AND rdfs_label = \"$old_rdfs_label\" ";
					$queryD .= "AND value_string = \"$old_value_string\" ";	
					$mysqli_resultD = mysqli_query($mysqli_link, $queryD);
					$msg = "* All matching records updated *";		
				}
			}
			
///////////////////////////////////// Else Individual			
			
		} else {
			if(($data_edit_tag_key_two != "") && ($data_edit_tag_value_two != "")) {
				$additionsRefs = array();
				$queryD = "SELECT * FROM annotations WHERE iana_UUID = \"$iana_UUID\" AND dc_references = \"$dc_identifier\" ";
				$mysqli_resultD = mysqli_query($mysqli_link, $queryD);
				while($rowD = mysqli_fetch_row($mysqli_resultD)) { 
					$additionsRefs[] = "$rowD[2]"."|"."$rowD[3]"."|"."$rowD[4]";
				}
				$cA = count($additionsRefs);
				if(($cA > 0)) {
					foreach($additionsRefs as $aR) {
						
///////////////////////////////////// Do Insert for Key 2					
						
						$new_iana_UUID = guidv4();
						$aRs = explode("|","$aR");
						$keys = explode(":","$data_edit_tag_key_two");
						$queryD = "INSERT INTO annotations VALUES (";
						$queryD .= "\"0\", ";
						$queryD .= "\"$new_iana_UUID\", ";
						$queryD .= "\"$aRs[0]\", ";
						$queryD .= "\"$aRs[1]\", ";
						$queryD .= "\"$aRs[2]\", ";
						$queryD .= "\"".$keys[0]."\", ";
						$queryD .= "\"".$keys[1]."\", ";
						$queryD .= "\"$data_edit_tag_value_two\", ";
						$queryD .= "\"\", ";
						$queryD .= "\"\", ";
						$queryD .= "\"$contributor\", ";
						$queryD .= "NOW() ";
						$queryD .= ");";
						$mysqli_resultD = mysqli_query($mysqli_link, $queryD);
						
///////////////////////////////////// Do Insert for Key 3					
						
						if(($data_edit_tag_key_three != "") && ($data_edit_tag_value_three != "")) {
							$new_iana_UUID = guidv4();
							$aRs = explode("|","$aR");
							$keys = explode(":","$data_edit_tag_key_three");
							$queryD = "INSERT INTO annotations VALUES (";
							$queryD .= "\"0\", ";
							$queryD .= "\"$new_iana_UUID\", ";
							$queryD .= "\"$aRs[0]\", ";
							$queryD .= "\"$aRs[1]\", ";
							$queryD .= "\"$aRs[2]\", ";
							$queryD .= "\"".$keys[0]."\", ";
							$queryD .= "\"".$keys[1]."\", ";
							$queryD .= "\"$data_edit_tag_value_three\", ";
							$queryD .= "\"\", ";
							$queryD .= "\"\", ";
							$queryD .= "\"$contributor\", ";
							$queryD .= "NOW() ";
							$queryD .= ");";
							$mysqli_resultD = mysqli_query($mysqli_link, $queryD);
						}
					}
				}
			}
			
///////////////////////////////////// Do Single Update			
			
			$keyZ = explode(":", "$data_edit_tag_key");
			if(($keyZ[0] != "") && ($keyZ[1] !="")) {
				$queryD = "UPDATE annotations ";
				$queryD .= "SET reg_uri = \"$keyZ[0]\", ";
				$queryD .= "rdfs_label = \"$keyZ[1]\", ";
				$queryD .= "value_string = \"$data_edit_tag_value\" ";
				$queryD .= "WHERE iana_UUID = \"$iana_UUID\" ";
				$mysqli_resultD = mysqli_query($mysqli_link, $queryD);
				$msg = "* Individual record updated *";
			}
		}
	}
	
///////////////////////////////////////////////////////////// EDIT Function

	if(($dc_identifier != "") && ($action == "EDIT" OR $action == "EDIT_UPDATE") && ($iana_UUID != "") && ($items_dc_identifier != "") && ($items_UUID != "")) {	

///////////////////////////////////////////////////////////// Get Details
	
		$queryD = "SELECT * FROM annotations WHERE iana_UUID = \"$iana_UUID\" AND dc_references = \"$dc_identifier\" ";
		$mysqli_resultD = mysqli_query($mysqli_link, $queryD);
		while($rowD = mysqli_fetch_row($mysqli_resultD)) { 
			$old_reg_uri = $rowD[5];
			$old_rdfs_label = $rowD[6];
			$old_value_string = $rowD[7];
			$foundAnn = "y";
		}		
		
		$queryD = "SELECT * FROM items WHERE dc_identifier = \"$items_dc_identifier\" AND iana_UUID = \"$items_UUID\" ";
		$mysqli_resultD = mysqli_query($mysqli_link, $queryD);
		while($rowD = mysqli_fetch_row($mysqli_resultD)) { 
			$iTitle = preg_replace("/\:/i","_","$rowD[6]");
		}
		
		$queryD = "SELECT COUNT(*) FROM annotations WHERE reg_uri = \"$old_reg_uri\" AND rdfs_label =\"$old_rdfs_label\" AND value_string = \"$old_value_string\"";
		$mysqli_resultD = mysqli_query($mysqli_link, $queryD);
		while($rowD = mysqli_fetch_row($mysqli_resultD)) { 
			$aCount = $rowD[0];
		}
		
		if(($action != "EDIT_UPDATE")) {

///////////////////////////////////////////////////////////// Start iFrame Page
	
?>
<!DOCTYPE html>
<html lang="en">
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]> 	   <html class="no-js"> <![endif]-->
    <head>   
    	<title>Edit Mentions</title>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta http-equiv='cache-control' content='no-cache'>
		<meta http-equiv='expires' content='0'>
		<meta http-equiv='pragma' content='no-cache'>
        <meta name="description" content="Manuscripts - Categorisation, Western Sydney University. Development: Dr Jason Ensor" />
        <meta name="robots" content="noindex,nofollow" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">    
        <meta name="theme-color" content="#ffffff" />  
		<meta name="msapplication-TileColor" content="#ffffff" />
		<meta name="msapplication-TileImage" content="./icons/ms-icon-144x144.png" />
		<link rel="shortcut icon" type="image/x-icon" href="./icons/favicon.ico" />     
        <link rel="manifest" href="./manifest.json" /> 
        <link rel="apple-touch-icon" href="./icons/apple-icon.png" />
		<link rel="apple-touch-icon" sizes="57x57" href="./icons/apple-icon-57x57.png" />
		<link rel="apple-touch-icon" sizes="60x60" href="./icons/apple-icon-60x60.png" />
		<link rel="apple-touch-icon" sizes="72x72" href="./icons/apple-icon-72x72.png" />
		<link rel="apple-touch-icon" sizes="76x76" href="./icons/apple-icon-76x76.png" />
		<link rel="apple-touch-icon" sizes="114x114" href="./icons/apple-icon-114x114.png" />
		<link rel="apple-touch-icon" sizes="120x120" href="./icons/apple-icon-120x120.png" />
		<link rel="apple-touch-icon" sizes="144x144" href="./icons/apple-icon-144x144.png" />
		<link rel="apple-touch-icon" sizes="152x152" href="./icons/apple-icon-152x152.png" />
		<link rel="apple-touch-icon" sizes="180x180" href="./icons/apple-icon-180x180.png" />
		<link rel="icon" type="image/png" sizes="192x192" href="./icons/android-icon-192x192.png" />
		<link rel="icon" type="image/png" sizes="32x32" href="./icons/favicon-32x32.png" />
		<link rel="icon" type="image/png" sizes="96x96" href="./icons/favicon-96x96.png" />
		<link rel="icon" type="image/png" sizes="16x16" href="./icons/favicon-16x16.png" />
        <link rel="icon" type="image/x-icon" href="./icons/favicon.ico" />  
        <link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700&amp;subset=latin">
        <link rel="stylesheet" type="text/css" href="./js/jquery-ui/themes/base/jquery.ui.all.css">
		<link rel="stylesheet" type="text/css" href="./css/bootstrap.min.css">
		<link rel="stylesheet" type="text/css" href="./plugins/themify-icons/themify-icons.min.css">
        <link rel="stylesheet" type="text/css" href="./plugins/ionicons/css/ionicons.min.css">
		<link rel="stylesheet" type="text/css" href="./css/pace.min.css">
        <link rel="stylesheet" type="text/css" href="./plugins/ionicons/css/ionicons.min.css" >
        <link rel="stylesheet" type="text/css" href="./plugins/chosen/chosen.min.css">
        <link rel="stylesheet" type="text/css" href="./plugins/bootstrap-select/bootstrap-select.min.css">
		<script language="javascript" type="text/javascript" src="./js/pace.min.js"></script>
        <script language="javascript" type="text/javascript" src="./js/jquery-2.2.4.min.js"></script>
		<script language="javascript" type="text/javascript" src="./js/bootstrap.min.js"></script>
		<script language="javascript" type="text/javascript" src="./js/nifty.min.js"></script>
        <script language="javascript" type="text/javascript" src="./plugins/datatables/media/js/jquery.dataTables.js"></script>
        <script language="javascript" type="text/javascript" src="./plugins/chosen/chosen.jquery.min.js"></script>
        <script language="javascript" type="text/javascript" src="./plugins/bootstrap-select/bootstrap-select.min.js"></script>
<?php

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// Manual CSS Interventions

?>         
        <style type="text/css" rel="stylesheet">
			
			pre {
    			white-space: pre-wrap; 
    			white-space: -moz-pre-wrap;
    			white-space: -pre-wrap;
    			white-space: -o-pre-wrap;
    			word-wrap: break-word;
				tab-size: 0;
				-moz-tab-size: 0;
    			-o-tab-size: 0;
				padding: 20px;
				font-size: 0.8em;
			}
			
			body, html {
				background-color: #f9f9f9;
				width: 100%;	
				padding: 0px;
				margin: 0px;
			}
			
			.btn-default {
				margin-bottom: 2px;
				margin-right: 2px;
				min-width: 55px;	
			}
			
			.input-sm {
				max-width: 100px;	
			}
			
			::-webkit-scrollbar {
    			-webkit-appearance: none;
    			width: 12px;
			}
	
			::-webkit-scrollbar-thumb {
    			border-radius: 4px;
    			background-color: rgba(0,0,0,0.20);
    			-webkit-box-shadow: 0 0 1px rgba(255,255,255,.5);
			}
			
		</style>
    </head>   
	<body>
    	&nbsp;<br />
        <div class="container" id="EditMetaPanel" style="padding-left: 15px; padding-right: 0px; margin-left: 0px; margin-right: 0px;">
 <?php               
 
		}
		
///////////////////////////////////// Table for EDIT and EDIT_UPDATE Start		
 
 ?>         
        	<div class="row">
            	<div class="col-lg-6 col-md-6 col-sm-6">
                    <div class="editTable" style="padding-left: 0px; padding-right: 0px;">             
                        <table width="100%" border="0" style="width: 100%;">
                            <tbody>
                            <?php
    
///////////////////////////////////// Table Header						
                            
                                echo "<tr>";
                                echo "<td style=\"font-size: 1.0em; text-align:left; color: #ffffff; background-color: #1b4f74; "; 
                                echo "vertical-align: middle; padding-right: 6px; padding: 12px; -webkit-border-radius: 8px; -moz-border-radius: 8px; border-radius: 8px;\" colspan=\"2\">";	
                                echo "&nbsp;<strong>Edit Mention : Item $iTitle</strong>";
                                echo "</td>";
                                echo "</tr>";
     
///////////////////////////////////// Key Dropdown
                                    
                                $itemLabels = array();
                                $queryD = "SELECT * FROM labels ORDER BY skos_definition ASC ";
                                $mysqli_resultD = mysqli_query($mysqli_link, $queryD);
                                while($rowD = mysqli_fetch_row($mysqli_resultD)) { 
                                    $itemLabels[] = $rowD[4]."|".$rowD[2]."|".$rowD[3];
                                }
                                sort($itemLabels);
                                echo "<tr>";
                                echo "<td style=\"padding-top:25px; font-size: 1.0em; text-align:right; border-top: 0px solid #768697; padding-top: 10px;\" nowrap>";
                                echo "<strong>Key&nbsp;/&nbsp;Label&nbsp;&nbsp;</strong>";
                                echo "</td>";
                                echo "<td style=\"padding-top:25px; font-size: 1.0em; border-top: 0px solid #768697; width: 100%; padding-left: 9px; padding-right: 1px; padding-bottom: 0px;\">";
                                echo "<select ";
                                echo "style=\"font-size: 1.0em; font-weight; 900; width: 100%;\" ";
                                echo "class=\"show-tick\" ";
                                echo "data-width=\"100%\" ";
                                echo "data-live-search=\"true\" ";
                                echo "id=\"edit-tag-key\" ";
								if(($msg != "")) {
									echo "disabled ";	
								}
                                echo "name=\"edit-tag-key\" ";
                                echo ">";
                                echo "<option style=\"font-size: 1.0em; white-space: normal;\" data-divider=\"true\"></option>";
                                
///////////////////////////////////// Loop Labels							
                                
                                foreach($itemLabels as $iL) {
                                    $bits = explode("|","$iL");
                                    echo "<option style=\"font-size: 1.0em; white-space: normal;\" ";
                                    $bitsK = ucwords($bits[1]);
                                    $bitsT = ucwords($bits[2]);
                                    $old_rdfs_labelT = ucwords($old_rdfs_label);
                                    $old_reg_uriT = ucwords($old_reg_uri);
                                    if(($bitsK == "$old_reg_uriT") && ($bitsT == "$old_rdfs_labelT")) { 
                                        echo "selected "; 
                                    }
                                    echo "value=\"".$bits[1].":".$bits[2]."\">".$bits[0]."</option>";
                                }
                                
///////////////////////////////////// Loop Finish							
                                
                                echo "</select>";
                                echo "</td>";
                                echo "</tr>";
                                        
///////////////////////////////////// Value Input
    
                                echo "<tr>";
                                echo "<td style=\"font-size: 1.0em; text-align:right; border-top: 0px solid #768697; vertical-align:top; padding-top: 10px;\" nowrap>";
                                echo "<strong>Value&nbsp;&nbsp;</strong>";
                                echo "</td>";
                                echo "<td style=\"font-size: 1.0em; border-top: 0px solid #768697; width: 100%; padding: 5px;\">";
                                echo "<textarea rows=\"5\" ";
                                echo "style=\"font-size: 1.0em; border: 1px solid #27a59b; resize: none; width: 100%;\" ";
                                echo "class=\"form-control\" ";
                                echo "id=\"edit-tag-value\" ";
								if(($msg != "")) {
									echo "disabled ";	
								}
                                echo "name=\"edit-tag-value\" >";
                                echo $old_value_string;
                                echo "</textarea>";
                                echo "</td>";
                                echo "</tr>";	
                                
///////////////////////////////////// Save Changes							
                                
                                if(($foundAnn == "y")) {
                                
///////////////////////////////////// Update All Instances Text
    
                                    echo "<tr>";
                                    echo "<td style=\"font-size: 1.0em; text-align:right; border-top: 0px solid #768697; vertical-align: middle;\" nowrap>&nbsp;</td>";
                                    echo "<td style=\"font-size: 0.9em; border-top: 0px solid #768697; ";
                                    echo "width: 100%; ";
                                    echo "padding-left: 5px; ";
                                    echo "padding-top: 20px; ";
                                    echo "padding-bottom: 17px; ";
                                    echo "text-align: justify; ";
                                    echo "padding-right: 0px; \">";
                                    echo "To edit this mention, please select a different Key and / or edit the Value above and then click the 'Save Changes' button to make changes only to this individual mention. The Key / Value pair of <strong><span style=\"color: #AA0000;\">$old_reg_uri"." : "."$old_rdfs_label</span></strong> ";
                                    echo "and <strong><span style=\"color: #AA0000;\">$old_value_string</span></strong> ";
                                    echo "occurs in <strong><span style=\"color: #AA0000;\">".number_format($aCount, 0)."</span></strong> places throughout the database."; 
                                    echo "</td>";
                                    echo "</tr>";	
                                
///////////////////////////////////// Update All Instances Tick Box
    
                                    echo "<tr>";
                                    echo "<td style=\"font-size: 1.0em; text-align:right; border-top: 0px solid #768697; vertical-align: middle;\" nowrap>&nbsp;</td>";
                                    echo "<td style=\"text-align: right; padding-bottom: 15px; padding-top: 3px; padding-left: 0px; padding-right: 0px;\">";
                                    echo "<span style=\"display: block; background-color: #DFDFDF; font-size: 0.9em; border: 1px solid #444444; padding: 15px; text-align: center; width: 100%;\">";
                                    echo "<input type=\"checkbox\" id=\"edit-tag-all\" name=\"edit-tag-all\" value=\"EDITALL\" ";
									if(($msg != "")) {
										echo "disabled ";	
									}
                                    echo "style=\"-ms-transform: scale(1.5); -moz-transform: scale(1.5); -webkit-transform: scale(1.5); -o-transform: scale(1.5); \">";
                                    echo "&nbsp;&nbsp;";
                                    echo "<strong>Change All Instances in Database</strong>";
                                    echo "</span>";
                                    echo "</td>";
                                    echo "</tr>";

///////////////////////////////////// Save Changes Button
									
									echo "<tr>";
                                    echo "<td style=\"font-size: 1.0em; text-align:right; border-top: 0px solid #768697; vertical-align: middle;\" nowrap>&nbsp;</td>";
                                    echo "<td style=\"font-size: 1.0em; border-top: 0px solid #768697; width: 100%; padding: 0px; padding-top: 20px; padding-bottom: 20px;\">";
                                    echo "<button class=\"btn btn-success col-lg-12\" id=\"edit-submit-item\" style=\"width:100%; padding: 14px;\" ";
									if(($msg != "")) {
										echo "disabled ";	
									}
									echo "><strong>Save Changes</strong></button>"; 
                                    echo "</td>";
                                    echo "</tr>";
                                }
    
///////////////////////////////////// Edit Update Message
                                
                                if(($msg != "")) {
                                    echo "<tr>";
                                    echo "<td colspan = \"2\" style=\"";
                                    echo "text-align: center; ";
                                    echo "-webkit-border-radius: 8px; ";
                                    echo "-moz-border-radius: 8px; ";
                                    echo "border-radius: 8px; ";
                                    echo "padding: 20px; ";
                                    echo "background-color: #ff8c67; ";
                                    echo "color: #3e0402; ";
                                    echo "vertical-align: middle;\">";
                                    echo "<strong>$msg</strong>";
                                    echo "</td>";
                                    echo "</tr>";
                                }
                                
///////////////////////////////////// Debug							
//                                
//                                echo "<tr>";
//                                echo "<td style=\"font-size: 1.0em; text-align:right; border-top: 0px solid #768697; "; 
//                                echo "vertical-align: middle; padding-left: 6px; padding-top: 10px;\">";
//                                echo "&nbsp;";
//                                echo "</td>";
//                                echo "<td style=\"font-size: 1.0em; text-align:left; border-top: 0px solid #768697; "; 
//                                echo "vertical-align: middle; padding-left: 6px; padding-top: 10px;\">";	
//                                echo "<p style=\"font-size:0.6em; color: #aaaaaa;\">";
//                                echo "<em>"; 
//                                echo "<strong>IDENTIFIERS</strong>";
//                                echo "<br />A.DCI - $dc_identifier";
//                                echo "<br />I.DCI - $items_dc_identifier [Should match A.DCI]"; 
//                                echo "<br />A.UUID - $iana_UUID"; 
//                                echo "<br />I.UUID - $items_UUID"; 
//                                echo "</em>"; 
//                                echo "</p>";
//                                echo "</td>";
//                                echo "</tr>";							
//    
///////////////////////////////////// Table Close
                                    
                            ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="col-lg-6 col-md-6 col-sm-6">
                	<div class="editTable2" style="padding-left: 0px; padding-right: 0px;">             
                    	<table width="100%" border="0" style="width: 100%;">
                       		<tbody>
<?php                            
                            
///////////////////////////////////// Table Header						
                            
                                echo "<tr>";
                                echo "<td style=\"font-size: 1.0em; text-align:left; color: #ffffff; background-color: #000000; "; 
                                echo "vertical-align: middle; padding-right: 6px; padding: 12px; -webkit-border-radius: 8px; -moz-border-radius: 8px; border-radius: 8px;\" colspan=\"2\">";	
                                echo "&nbsp;<strong>Split Mention : Item $iTitle (Optional)</strong>";
                                echo "</td>";
                                echo "</tr>";                           

///////////////////////////////////// Key 2 Dropdown
                                    
                                $itemLabels = array();
                                $queryD = "SELECT * FROM labels ORDER BY skos_definition ASC ";
                                $mysqli_resultD = mysqli_query($mysqli_link, $queryD);
                                while($rowD = mysqli_fetch_row($mysqli_resultD)) { 
                                    $itemLabels[] = $rowD[4]."|".$rowD[2]."|".$rowD[3];
                                }
                                sort($itemLabels);
                                echo "<tr>";
                                echo "<td style=\"padding-top:25px; font-size: 1.0em; text-align:right; border-top: 0px solid #768697; padding-top: 10px;\" nowrap>";
                                echo "<strong>2nd&nbsp;Key&nbsp;/&nbsp;Label&nbsp;&nbsp;</strong>";
                                echo "</td>";
                                echo "<td style=\"padding-top:25px; font-size: 1.0em; border-top: 0px solid #768697; width: 100%; padding-left: 9px; padding-right: 1px; padding-bottom: 0px;\">";
                                echo "<select ";
                                echo "style=\"font-size: 1.0em; font-weight; 900; width: 100%;\" ";
                                echo "class=\"show-tick\" ";
                                echo "data-width=\"100%\" ";
								if(($msg != "")) {
									echo "disabled ";	
								}
                                echo "data-live-search=\"true\" ";
                                echo "id=\"edit-tag-key_two\" ";
                                echo "name=\"edit-tag-key_two\" ";
                                echo ">";
                                echo "<option style=\"font-size: 1.0em; white-space: normal;\" data-divider=\"true\"></option>";
                                
///////////////////////////////////// Loop Labels							
                                
                                foreach($itemLabels as $iL) {
                                    $bits = explode("|","$iL");
                                    echo "<option style=\"font-size: 1.0em; white-space: normal;\" value=\"".$bits[1].":".$bits[2]."\"";
									$tempBits = $bits[1].":".$bits[2];
									if(($data_edit_tag_key_two == "$tempBits") && ($data_edit_tag_key_two != "")) {
										echo " selected ";	
									}
                                	echo ">".$bits[0]."</option>";
                                }
                                
///////////////////////////////////// Loop Finish							
                                
                                echo "</select>";
                                echo "</td>";
                                echo "</tr>";

///////////////////////////////////// Value Input
    
                                echo "<tr>";
                                echo "<td style=\"font-size: 1.0em; text-align:right; border-top: 0px solid #768697; vertical-align:top; padding-top: 10px;\" nowrap>";
                                echo "<strong>2nd&nbsp;Value&nbsp;&nbsp;</strong>";
                                echo "</td>";
                                echo "<td style=\"font-size: 1.0em; border-top: 0px solid #768697; width: 100%; padding: 5px;\">";
                                echo "<textarea rows=\"2\" ";
                                echo "style=\"font-size: 1.0em; border: 1px solid #27a59b; resize: none; width: 100%;\" ";
                                echo "class=\"form-control\" ";
                                echo "id=\"edit-tag-value_two\" ";
								if(($msg != "")) {
									echo "disabled ";	
								}
                                echo "name=\"edit-tag-value_two\" >";
								echo $data_edit_tag_value_two;
                                echo "</textarea>";
                                echo "</td>";
                                echo "</tr>";

///////////////////////////////////// Key 3 Dropdown
                                    
                                $itemLabels = array();
                                $queryD = "SELECT * FROM labels ORDER BY skos_definition ASC ";
                                $mysqli_resultD = mysqli_query($mysqli_link, $queryD);
                                while($rowD = mysqli_fetch_row($mysqli_resultD)) { 
                                    $itemLabels[] = $rowD[4]."|".$rowD[2]."|".$rowD[3];
                                }
                                sort($itemLabels);
                                echo "<tr>";
                                echo "<td style=\"padding-top:25px; font-size: 1.0em; text-align:right; border-top: 0px solid #768697; padding-top: 10px;\" nowrap>";
                                echo "<strong>3rd&nbsp;Key&nbsp;/&nbsp;Label&nbsp;&nbsp;</strong>";
                                echo "</td>";
                                echo "<td style=\"padding-top:25px; font-size: 1.0em; border-top: 0px solid #768697; width: 100%; padding-left: 9px; padding-right: 1px; padding-bottom: 0px;\">";
                                echo "<select ";
                                echo "style=\"font-size: 1.0em; font-weight; 900; width: 100%;\" ";
                                echo "class=\"show-tick\" ";
                                echo "data-width=\"100%\" ";
								if(($msg != "")) {
									echo "disabled ";	
								}
                                echo "data-live-search=\"true\" ";
                                echo "id=\"edit-tag-key_three\" ";
                                echo "name=\"edit-tag-key_three\" ";
                                echo ">";
                                echo "<option style=\"font-size: 1.0em; white-space: normal;\" data-divider=\"true\"></option>";
                                
///////////////////////////////////// Loop Labels							
                                
                                foreach($itemLabels as $iL) {
                                    $bits = explode("|","$iL");
                                    echo "<option style=\"font-size: 1.0em; white-space: normal;\" value=\"".$bits[1].":".$bits[2]."\"";
									$tempBits = $bits[1].":".$bits[2];
									if(($data_edit_tag_key_three == "$tempBits") && ($data_edit_tag_key_three != "")) {
										echo " selected ";	
									}
                                	echo ">".$bits[0]."</option>";
                                }
                                
///////////////////////////////////// Loop Finish							
                                
                                echo "</select>";
                                echo "</td>";
                                echo "</tr>";

///////////////////////////////////// Value Input
    
                                echo "<tr>";
                                echo "<td style=\"font-size: 1.0em; text-align:right; border-top: 0px solid #768697; vertical-align:top; padding-top: 10px;\" nowrap>";
                                echo "<strong>3rd&nbsp;Value&nbsp;&nbsp;</strong>";
                                echo "</td>";
                                echo "<td style=\"font-size: 1.0em; border-top: 0px solid #768697; width: 100%; padding: 5px;\">";
                                echo "<textarea rows=\"2\" ";
                                echo "style=\"font-size: 1.0em; border: 1px solid #27a59b; resize: none; width: 100%;\" ";
                                echo "class=\"form-control\" ";
                                echo "id=\"edit-tag-value_three\" ";
								if(($msg != "")) {
									echo "disabled ";	
								}
                                echo "name=\"edit-tag-value_three\" >";
								echo $data_edit_tag_value_three;
                                echo "</textarea>";
                                echo "</td>";
                                echo "</tr>";

///////////////////////////////////// Explanation Text

?>                            
                            </tbody>
                        </table>
                		<p style=" -webkit-border-radius: 8px; -moz-border-radius: 8px; border-radius: 8px; 
                        	background-color: #ffffff; border:1px solid #000000; text-align: justify; font-size: 0.9em; padding:25px; margin-top: 15px;">
                        	Sometimes, due to the complexity of the vocabulary originally employed, a pre-existing 'mention' may 
                    		need to be split into two or three 'mentions'. In these instances, please edit the first of the 'mentions' in the left-hand panel as usual, 
                    		with the additional second and / or third 'mentions' added in the input boxes above. Then proceed by ticking the 'Change All Instances 
                    		in the Database' checkbox if applicable and click the 'Save Changes' button. Once completed, remember to refresh your Metadata panel view after you close this popup.
              			</p>
                	</div>
                </div>
        	</div>
<?php	

///////////////////////////////////// Start Scripts
	
?>
		</div>
		<script language="javascript" type="text/javascript" >
		
			$(document).ready(function() {	
			
///////////////////////////////////////////////////////////// Apply Select Class			
				
				$('#edit-tag-key').selectpicker();
				$('#edit-tag-key_two').selectpicker();
				$('#edit-tag-key_three').selectpicker();

///////////////////////////////////////////////////////////// Submit Item Data
				
				$("#edit-submit-item").click(function(event) {
					var data_edit_tag_key = $("#edit-tag-key option:selected").val();
					var data_edit_tag_value = $("#edit-tag-value").val();
					var data_edit_tag_key_two = $("#edit-tag-key_two option:selected").val();
					var data_edit_tag_value_two = $("#edit-tag-value_two").val();
					var data_edit_tag_key_three = $("#edit-tag-key_three option:selected").val();
					var data_edit_tag_value_three = $("#edit-tag-value_three").val();
					if($("#edit-tag-all").prop("checked") == true) {
						var data_edit_tag_all = "ALL";	
					} else {
						var data_edit_tag_all = "0";	
					}
					var dataAll = "action=EDIT_UPDATE"
						+"&data_edit_tag_key="+data_edit_tag_key
						+"&data_edit_tag_value="+data_edit_tag_value
						+"&data_edit_tag_key_two="+data_edit_tag_key_two
						+"&data_edit_tag_value_two="+data_edit_tag_value_two
						+"&data_edit_tag_key_three="+data_edit_tag_key_three
						+"&data_edit_tag_value_three="+data_edit_tag_value_three
						+"&data_edit_tag_all="+data_edit_tag_all
						+"&items_dc_identifier=<?php echo $items_dc_identifier; ?>"
						+"&items_UUID=<?php echo $items_UUID; ?>"
						+"&dc_identifier=<?php echo $dc_identifier; ?>"
						+"&iana_UUID=<?php echo $iana_UUID; ?>";
					var doEditMe = $('#EditMetaPanel').fadeOut('fast', function(){ 
						var doEditMeB = $('#EditMetaPanel').load('./data_meta_edit.php',dataAll, function(){ 
							var doEditMeC = $('#EditMetaPanel').fadeIn('fast'); 		
						}); 
					}); 					
				});
			});
<?php

///////////////////////////////////// Finish Scripts

?>		
		</script> 
<?php

///////////////////////////////////// Table for EDIT and EDIT_UPDATE Finish

		if(($action != "EDIT_UPDATE")) {

?>               
    </body>
</html>
<?php

		}
	}

///////////////////////////////////////////////////////////// Finish

	include("./ar.dbdisconnect.php");
	
?>