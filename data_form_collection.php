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
//  11-19 November 2018
//
//
/////////////////////////////////////////////////////////// Hash Function

    function incrementalHash($len){
      $charset = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
      $base = strlen($charset);
      $result = '';
      $now = explode(' ', microtime())[1];
      while ($now >= $base){
        $i = $now % $base;
        $result = $charset[$i] . $result;
        $now /= $base;
      }
      return substr($result, -$len);
    }

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
    $msg = "You do not have sufficient privileges to add a collection ";
    $msg .= "or your administrator session has expired. ";
    $msg .= "Please login and try again.";
    $outcome = "n";
    if(($_SESSION["administrator"] == "yes")) {
        $action = $_GET["action"];
        $col_ID = "0";
        $col_iana_UUID = guidv4();
        $col_dc_identifier = time().rand(0,99999);
        $col_bf_heldBy = $_GET["col_bf_heldBy"];
        $col_bf_subLocation = $_GET["col_bf_subLocation"];
        $col_bf_physicalLocation = $_GET["col_bf_physicalLocation"];
        $col_skos_collection = $_GET["col_skos_collection"];
        $col_skos_orderedCollection = $_GET["col_skos_orderedCollection"];
        $col_bibo_volume = $_GET["col_bibo_volume"];
        $col_disco_startDate = $_GET["col_disco_startDate"];
        $col_disco_endDate = $_GET["col_disco_endDate"];
        if(($col_disco_endDate == "")) {
            $col_disco_endDate = $col_disco_startDate;   
        }
        $col_dc_relation = $_GET["col_dc_relation"];
        if(($col_dc_relation == "")) {
            $acronym = "";
            $words = explode(" ", "$col_skos_collection"); 
            if(is_array($words)) {
                foreach ($words as $w) {
                    $acronym .= $w[0];
                }
                $p = (6 - count($words));
                $acronym .= incrementalHash($p); 
            }
            $col_dc_relation = $acronym;
        }
        $del_iana_UUID = $_GET["del_iana_UUID"];
        $del_dc_identifier = $_GET["del_dc_identifier"];
        $del_ID = $_GET["del_ID"];
        $contributor = "contrib41T71U4BZZ";
    }
    $_GET = array();
    $_POST = array();

/////////////////////////////////////////////////////////// Add Collection

    if(($_SESSION["administrator"] == "yes")) {
        if(($action == "ADD")) {
            if(($col_bf_heldBy != "") && ($col_bf_subLocation != "") 
               && ($col_bf_physicalLocation != "") && ($col_skos_orderedCollection != "") && ($col_disco_startDate != "") && ($col_disco_endDate != "")) {
                $scotty = "";
                $dbQuery = "INSERT INTO collections VALUES ";
                $dbQuery .= "(";
                $dbQuery .= "$col_ID, ";
                $dbQuery .= "\"$col_iana_UUID\", ";
                $dbQuery .= "\"$col_dc_identifier\", ";
                $dbQuery .= "\"$col_bf_heldBy\", ";
                $dbQuery .= "\"$col_bf_subLocation\", ";
                $dbQuery .= "\"$col_bf_physicalLocation\", ";
                $dbQuery .= "\"$col_skos_collection\", ";
                $dbQuery .= "\"$col_skos_orderedCollection\", ";
                $dbQuery .= "\"$col_bibo_volume\", ";
                $dbQuery .= "\"$col_disco_startDate\", ";
                $dbQuery .= "\"$col_disco_endDate\", ";
                $dbQuery .= "\"$col_dc_relation\" ";
                $dbQuery .= "); ";
                $mysqli_result = mysqli_query($mysqli_link, $dbQuery);
                $scotty = mysqli_error($mysqli_link);
                if(($scotty)) { 
                    $msg = "The collection could NOT be added to the database. ";
                    $msg .= "Please review your form details.";
                    $outcome = "n";
                } else {
                    $msg = "The collection was successfully added to the Linked Archives database. ";
                    $msg .= "Please close this window and click on the 'Upload Images' button";
                    $outcome = "y";
                }
            } else {
                $msg = "Insufficient information was provided. ";
                $msg .= "Please review your form details and try again.";
                $outcome = "n";
            }
        }
    }

/////////////////////////////////////////////////////////// Delete Empty Collection

   if(($_SESSION["administrator"] == "yes")) {
       if(($action == "DELETE") && ($del_iana_UUID != "") && ($del_ID != "")) {
           $scotty = "";
           $dbQuery = "DELETE FROM collections WHERE ";
           $dbQuery .= "iana_UUID = \"$del_iana_UUID\" AND ";
           $dbQuery .= "dc_identifier = \"$del_dc_identifier\" AND ";
           $dbQuery .= "ID = \"$del_ID\"; ";
           $mysqli_result = mysqli_query($mysqli_link, $dbQuery);
           $scotty = mysqli_error($mysqli_link);
           if(($scotty)) { 
                $msg = "The collection could NOT be deleted from the database.";
                $outcome = "n";
           } else {
                $msg = "The collection was successfully deleted from the Linked Archives database.";
                $outcome = "y";
           }
       }
   }

    if(($action != "ADD") && ($action != "DELETE")) {

/////////////////////////////////////////////////////////// Start iFrame Page
	
?>
<!DOCTYPE html>
<html lang="en">
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]> 	   <html class="no-js"> <![endif]-->
    <head>   
    	<title>Create Collections</title>
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
            
            small {
                width: 100%!important;
                text-align: right!important;   
            }
			
			body, html {
				background-color: #f9f9f9;
				width: 100%;
                height: 100%; 
				padding: 0px;
				margin: 0px;
                font-size: 0.90em!important;
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
        <div class="container" id="EditColPanel" style="padding: 15px; margin-left: 0px; margin-right: 0px;">
            <div class="row">
            	<div class="col-lg-6 col-md-6 col-sm-6">
                    <div style="padding: 6px;" id="ColAddPanel"> 
<?php
        
/////////////////////////////////////////////////////////// Reloadable In-page Div Start        
        
    }

?>                        
                        <p><strong>CREATE COLLECTION</strong></p>
                        <p style="text-align: justify;">&nbsp;<br />To add an archive record to Linked Archives, please fill in all the details in the right hand form. For this, you need textual details of the collection you intend to upload such as institutional holder, collection title, manuscript name, and box / volume, folder numbers. (If you make a mistake, you can delete the archive record from the list below but please note that you can only delete archive records before you have uploaded any items.) Once you have finished here, just close this window and click on the button labelled 'Upload Images', following the instructions in the window that pops up.<br />&nbsp;</p>
                        <p><strong>RECENT ARCHIVE RECORDS</strong><br />&nbsp;</p>
                        <table width="100%" border="0" style="width: 100%; border: 1px solid #c9d1d7;">
                            <tbody>
<?php
                                echo "<tr>";
                                echo "<td style=\"";
                                echo "text-align: right; ";
                                echo "color: #FFFFFF; ";
                                echo "background-color: #c9d1d7; "; 
                                echo "vertical-align: top; ";
                                echo "padding: 8px; ";
                                echo "\" ";
                                echo ">";	
                                echo "<strong>#</strong>";
                                echo "</td>";
                                echo "<td style=\"";
                                echo "text-align: left; ";
                                echo "color: #FFFFFF; ";
                                echo "background-color: #c9d1d7; "; 
                                echo "vertical-align: top; ";
                                echo "padding: 8px; ";
                                echo "\" ";
                                echo ">";	
                                echo "<strong>Collection</strong>";
                                echo "</td>";
                                echo "<td style=\"";
                                echo "text-align: ;left; ";
                                echo "color: #FFFFFF; ";
                                echo "background-color: #c9d1d7; "; 
                                echo "vertical-align: top; ";
                                echo "padding: 8px; ";
                                echo "\" ";
                                echo ">";	
                                echo "<strong>Items</strong>";
                                echo "</td>";
                                echo "</tr>";
        
                                $queryD = "SELECT * FROM collections ORDER BY ID DESC LIMIT 4 ";
                                $mysqli_resultD = mysqli_query($mysqli_link, $queryD);
                                while($rowD = mysqli_fetch_row($mysqli_resultD)) { 
                                    $empty = "y";
                                    echo "<tr>";
                                    echo "<td style=\"";
                                    echo "text-align: right; ";
                                    echo "color: #1b4f74; ";
                                    echo "background-color: #FFFFFF; "; 
                                    echo "vertical-align: top; ";
                                    echo "padding: 8px; ";
                                    echo "border-bottom: 1px solid #c9d1d7; ";
                                    echo "\" ";
                                    echo ">";	
                                    echo "<strong>$rowD[0]</strong>";
                                    echo "</td>";
                                    echo "<td style=\"";
                                    echo "text-align: left; ";
                                    echo "color: #1b4f74; ";
                                    echo "background-color: #FFFFFF; "; 
                                    echo "vertical-align: top; ";
                                    echo "padding: 8px; ";
                                    echo "border-bottom: 1px solid #c9d1d7; ";
                                    echo "\" ";
                                    echo ">";		
                                    echo "$rowD[6], ";
                                    if(($rowD[8] != "")) {
                                        echo "$rowD[7]"."/"."$rowD[8], ";
                                    } else {
                                        echo "$rowD[7], ";
                                    }
                                    if(($rowD[9] != $rowD[10])) {
                                        echo "$rowD[9]"."-"."$rowD[10]. ";
                                    } else {
                                        echo "$rowD[9]. ";
                                    }
                                    echo "[$rowD[11]]";
                                    echo "</td>";
                                    echo "<td style=\"";
                                    echo "text-align: right; ";
                                    echo "color: #1b4f74; ";
                                    echo "background-color: #FFFFFF; "; 
                                    echo "vertical-align: top; ";
                                    echo "padding: 8px; ";
                                    echo "border-bottom: 1px solid #c9d1d7; ";
                                    echo "\" ";
                                    echo ">";
                                    $total = 0;
                                    $queryE = "SELECT COUNT(*) FROM items WHERE collections_dc_identifier = \"$rowD[2]\"; ";
                                    $mysqli_resultE = mysqli_query($mysqli_link, $queryE);
                                    while($rowE = mysqli_fetch_row($mysqli_resultE)) { 
                                        $total = $rowE[0];
                                        $empty = "n";
                                    }
                                    if(($total < 1)) {
                                        $empty = "y";
                                    }
                                    if(($empty == "y")) {
                                        echo "<a href=\"";
                                        echo "javascript: ";
                                        echo "var dataAll = 'action=DELETE&del_iana_UUID=".$rowD[1]."&del_dc_identifier=".$rowD[2]."&del_ID=".$rowD[0]."'; ";
                                        echo "var doAddMe = $('#ColAddPanel').fadeOut('fast', function(){ ";
                                        echo "var doAddMeB = $('#ColAddPanel').load('./data_form_collection.php',dataAll, function(){ ";
                                        echo "var doAddMeC = $('#ColAddPanel').fadeIn('fast'); ";
                                        echo "}); ";
                                        echo "}); ";
                                        echo "\" ";
                                        echo "style=\"color: #800000;\">";
                                        echo "<strong>DELETE</strong>";
                                        echo "</a>";
                                    } else {
                                        echo "$total";
                                    }
                                    echo "</td>";
                                    echo "</tr>";
                                }                            
?>
                            </tbody>
                        </table>
                        <br />&nbsp;<br />
<?php

/////////////////////////////////////////////////////////// Data Routine Results                        
                        
    if(($action == "ADD") or ($action == "DELETE")) { 
        if(($outcome == "n")) {
            echo "<div class=\"alert alert-danger\" role=\"alert\">";
            echo "<strong>Routine Failed</strong><br />$msg";
            echo "</div>";
        } else {
            echo "<div class=\"alert alert-success\" role=\"alert\">";
            echo "<strong>Routine Completed</strong><br />$msg";
            echo "</div>";
        }
    }

/////////////////////////////////////////////////////////// Reloadable In-page Div End                        
                        
    if(($action != "ADD") && ($action != "DELETE")) {  
        echo "</div>";
        
/////////////////////////////////////////////////////////// The Form
        
?>
                </div>
                <div class="col-lg-6 col-md-6 col-sm-6">
                	<div class="collection_form" style="padding: 6px;">
                        <p>&nbsp;<br />&nbsp;</p>
                        <form>
                            <div class="form-group">
                                <label for="col_bf_heldBy">HELD BY</label>
                                <input type="text" class="form-control" id="col_bf_heldBy" aria-describedby="col_bf_heldByHelp" placeholder="Enter Institution">
                                <small id="col_bf_heldByHelp" class="form-text text-muted">Example: State Library of New South Wales, Mitchell Library</small>
                            </div>
                            <div class="form-group">
                                <label for="col_bf_subLocation">SUB LOCATION</label>
                                <input type="text" class="form-control" id="col_bf_subLocation" aria-describedby="col_bf_subLocationHelp" placeholder="Enter Sub Location">
                                <small id="col_bf_subLocationHelp" class="form-text text-muted">Example: Mitchell Library</small>
                            </div>
                            <div class="form-group">
                                <label for="col_bf_physicalLocation">PHYSICAL LOCATION</label>
                                <input type="text" class="form-control" id="col_bf_physicalLocation" aria-describedby="col_bf_physicalLocationHelp" placeholder="Enter Physical Location">
                                <small id="col_bf_physicalLocationHelp" class="form-text text-muted">Example: Manuscripts Collection</small>
                            </div>
                            <div class="form-group">
                                <label for="col_skos_collection">COLLECTION NAME</label>
                                <input type="text" class="form-control" id="col_skos_collection" aria-describedby="col_skos_collectionHelp" placeholder="Enter the Name of the Collection">
                                <small id="col_skos_collectionHelp" class="form-text text-muted">Example: Singapore Office</small>
                            </div>
                            <div class="form-group">
                                <label for="col_skos_orderedCollection">MANUSCRIPT</label>
                                <input type="text" class="form-control" id="col_skos_orderedCollection" aria-describedby="col_skos_orderedCollectionHelp" placeholder="Enter the Manuscript Ascension Number">
                                <small id="col_skos_orderedCollectionHelp" class="form-text text-muted">Example: ML MSS 3269</small>
                            </div>
                            <div class="form-group">
                                <label for="col_bibo_volume">VOLUME / FOLDER</label>
                                <input type="text" class="form-control" id="col_bibo_volume" aria-describedby="col_bibo_volumeHelp" placeholder="Enter the Volume and / or Folder Number">
                                <small id="col_bibo_volumeHelp" class="form-text text-muted">Example: 42 (must be a number)</small>
                            </div>
                            <div class="form-group">
                                <label for="col_disco_startDate">START DATE</label>
                                <input type="text" class="form-control" id="col_disco_startDate" aria-describedby="col_disco_startDateHelp" placeholder="Enter the Start Date of the Collection">
                                <small id="col_disco_startDateHelp" class="form-text text-muted">Example: 1966 (must be a year)</small>
                            </div>
                            <div class="form-group">
                                <label for="col_disco_endDate">END DATE</label>
                                <input type="text" class="form-control" id="col_disco_endDate" aria-describedby="col_disco_endDateHelp" placeholder="Enter the End Date of the Collection (if any))">
                                <small id="col_disco_endDateHelp" class="form-text text-muted">Example: 1970 (optional, must be a year)</small>
                            </div>
                             <button class="btn btn-danger col-sm-12 col-md-12 col-lg-12" id="adminAddThisArchive"><strong>Add Archive</strong></button>
                        </form>                        
                    </div>
                </div>
            </div>
        </div>
		<script language="javascript" type="text/javascript" >

///////////////////////////////////// Start Scripts            
            
			$(document).ready(function() {	
                $("#adminAddThisArchive").click(function(event) {
                    var col_bf_heldBy = $("#col_bf_heldBy").val();
                    var col_bf_subLocation = $("#col_bf_subLocation").val();
                    var col_bf_physicalLocation = $("#col_bf_physicalLocation").val();
                    var col_skos_collection = $("#col_skos_collection").val();
                    var col_skos_orderedCollection = $("#col_skos_orderedCollection").val();
                    var col_bibo_volume = $("#col_bibo_volume").val();
                    var col_disco_startDate = $("#col_disco_startDate").val();
                    var col_disco_endDate = $("#col_disco_endDate").val();
                    var dataAll = "action=ADD"
						+"&col_bf_heldBy="+col_bf_heldBy
						+"&col_bf_subLocation="+col_bf_subLocation
						+"&col_bf_physicalLocation="+col_bf_physicalLocation
						+"&col_skos_collection="+col_skos_collection
                        +"&col_skos_orderedCollection="+col_skos_orderedCollection
						+"&col_bibo_volume="+col_bibo_volume
						+"&col_disco_startDate="+col_disco_startDate
						+"&col_disco_endDate="+col_disco_endDate;
                    var doAddMe = $('#ColAddPanel').fadeOut('fast', function(){ 
						var doAddMeB = $('#ColAddPanel').load('./data_form_collection.php',dataAll, function(){ 
							var doAddMeC = $('#ColAddPanel').fadeIn('fast'); 		
						}); 
					});
                    return false;
                }); 					
            });

///////////////////////////////////// Finish Scripts
		
		</script> 
    </body>
</html>
<?php
        
    }
        
/////////////////////////////////////////////////////////// Finish        

    include("./ar.dbdisconnect.php");

/////////////////////////////////////////////////////////// Close

?>