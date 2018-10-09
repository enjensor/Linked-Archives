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
//  13-15 August 2018
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
	$annotations_reg_uri = $_GET["annotations_reg_uri"];
	$annotations_rdfs_label = $_GET["annotations_rdfs_label"];
	$annotations_value_string = $_GET["annotations_value_string"];
	$data_value_string = $_GET["data_value_string"];
	$data_source = $_GET["data_source"];
	$data_delete = $_GET["data_delete"];
	$data_delete_id = $_GET["data_delete_id"];
	$data_parent = $_GET["data_parent"];
	$_GET = array();
	$_POST = array();
	$me = $_SESSION["contributor"];
	
///////////////////////////////////////////////////////////// Function Add

	if(($action == "EDIT_CONCEPT") 
	   	&& ($annotations_reg_uri != "") 
	   	&& ($annotations_rdfs_label != "") 
	   	&& ($annotations_value_string != "") 
	   	&& ($data_value_string != "") 
		&& ($me != "")) {
		$data_reg_uri = "";
		$data_rdfs_label = "";
		$queryD = "SELECT ";
		$queryD .= "reg_uri, ";
		$queryD .= "rdfs_label ";
		$queryD .= "FROM ";
		$queryD .= "annotations ";
		$queryD .= "WHERE ";
		$queryD .= "value_string = \"".$data_value_string."\" ";
		$queryD .= "ORDER BY reg_uri ASC ";
		$queryD .= "LIMIT 1";
		$mysqli_resultD = mysqli_query($mysqli_link, $queryD);
		while($rowD = mysqli_fetch_row($mysqli_resultD)) { 
			$data_reg_uri = $rowD[0];
			$data_rdfs_label = $rowD[1];
		}
		if(($data_parent == "")){
			if(($data_reg_uri != "") && ($data_rdfs_label != "")) {
				$new_iana_UUID = guidv4();
				$queryD = "INSERT INTO ";
				$queryD .= "relatedconcepts ";
				$queryD .= "VALUES (";
				$queryD .= "\"0\", ";
				$queryD .= "\"".$new_iana_UUID."\", ";
				$queryD .= "\"".$annotations_reg_uri."\", ";
				$queryD .= "\"".$annotations_rdfs_label."\", ";
				$queryD .= "\"".$annotations_value_string."\", ";
				$queryD .= "\"".$data_reg_uri."\", ";
				$queryD .= "\"".$data_rdfs_label."\", ";
				$queryD .= "\"".$data_value_string."\", ";
				$queryD .= "\"".$me."\", ";
				$queryD .= "NOW(), ";
				$queryD .= "\"".$data_source."\"";
				$queryD .= ");";			
				$mysqli_resultD = mysqli_query($mysqli_link, $queryD);
			}
		} else {
			if(($data_reg_uri != "") && ($data_rdfs_label != "")) {
				$new_iana_UUID = guidv4();
				$queryD = "INSERT INTO ";
				$queryD .= "relatedconcepts ";
				$queryD .= "VALUES (";
				$queryD .= "\"0\", ";
				$queryD .= "\"".$new_iana_UUID."\", ";
				$queryD .= "\"".$data_reg_uri."\", ";
				$queryD .= "\"".$data_rdfs_label."\", ";
				$queryD .= "\"".$data_value_string."\", ";
				$queryD .= "\"".$annotations_reg_uri."\", ";
				$queryD .= "\"".$annotations_rdfs_label."\", ";
				$queryD .= "\"".$annotations_value_string."\", ";
				$queryD .= "\"".$me."\", ";
				$queryD .= "NOW(), ";
				$queryD .= "\"".$data_source."\"";
				$queryD .= ");";			
				$mysqli_resultD = mysqli_query($mysqli_link, $queryD);
			}
		}
	}

///////////////////////////////////////////////////////////// Function Delete

	if(($action == "EDIT_CONCEPT") 
	   	&& ($annotations_reg_uri != "") 
	   	&& ($annotations_rdfs_label != "") 
	   	&& ($annotations_value_string != "") 
	   	&& ($data_delete_id != "") 
		&& ($me != "")) {
		$queryD = "DELETE ";
		$queryD .= "FROM ";
		$queryD .= "relatedconcepts ";
		$queryD .= "WHERE ";
		$queryD .= "iana_UUID = \"".$data_delete_id."\" ";
		$mysqli_resultD = mysqli_query($mysqli_link, $queryD);
	}

///////////////////////////////////////////////////////////// Display Panels

	if(($action == "EDIT" or $action == "EDIT_CONCEPT") 
	   	&& ($annotations_reg_uri != "") 
	   	&& ($annotations_rdfs_label != "") 
	   	&& ($annotations_value_string != "") 
		&& ($me != "")){		

///////////////////////////////////////////////////////////// Get Details
	 
		$old_reg_uri = $annotations_reg_uri;
		$old_rdfs_label = $annotations_rdfs_label;
		$old_value_string = $annotations_value_string;
		$foundAnn = "y";	
		$iTitle = "Edit Related Concepts";
		$queryD = "SELECT COUNT(*) FROM annotations WHERE ";
		$queryD .= "reg_uri = \"$old_reg_uri\" AND ";
		$queryD .= "rdfs_label =\"$old_rdfs_label\" AND ";
		$queryD .= "value_string = \"$old_value_string\"";
		$mysqli_resultD = mysqli_query($mysqli_link, $queryD);
		while($rowD = mysqli_fetch_row($mysqli_resultD)) { 
			$aCount = $rowD[0];
		}
		if(($action != "EDIT_CONCEPT")) {

///////////////////////////////////////////////////////////// Start iFrame Page
	
?>
<!DOCTYPE html>
<html lang="en">
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]> 	   <html class="no-js"> <![endif]-->
    <head>   
    	<title>Edit Related Concepts</title>
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
		<script language="javascript" type="text/javascript" src="https://d3js.org/d3.v4.min.js"></script>
        <script language="javascript" type="text/javascript" src="./plugins/datatables/media/js/jquery.dataTables.js"></script>
        <script language="javascript" type="text/javascript" src="./plugins/chosen/chosen.jquery.min.js"></script>
        <script language="javascript" type="text/javascript" src="./plugins/bootstrap-select/bootstrap-select.min.js"></script>
<?php

//////////////////////////////////////////////////////////////// Manual CSS Interventions

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
			
			.ui-autocomplete {
				max-height: 310px;
				width: 300px;
				padding: 2px;
				text-decoration: none;
				overflow-y: auto;
				overflow-x: hidden;
			}
			
			body .ui-autocomplete, .ui-menu-item, .ui-corner-all, .ui-corner-top, .ui-corner-left, 
			.ui-corner-tl, .ui-corner-right, .ui-corner-tr, .ui-corner-bottom, .ui-corner-bl, .ui-corner-br {
				line-height: 1.4em !important;
				font-size: 0.96em !important;
				white-space: pre-wrap;
				diplay: inline; 
				padding: 2px !important;
				text-decoration: none !important;
				border-radius: 0px !important;
    			-webkit-border-radius: 0px !important;
    			-moz-border-radius: 0px !important;
				background-color: #FFFFFF !important;
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
			
			.links line {
			  stroke: #999;
			  stroke-opacity: 0.9;
			}

			.nodes circle {
			  stroke: #fff;
			  stroke-width: 1.5px;
			}

			text {
			  font-size: 12px;
			}

		</style>
    </head>   
	<body>
    	&nbsp;<br />
        <div class="container" id="EditMetaPanel" style="padding: 0px; margin: 0px; width: 100%;">
 <?php               
 
		}
		
///////////////////////////////////// Table for EDIT and EDIT_UPDATE Start		
 
 ?>         
        	<div class="row" style="padding: 0px; margin: 0px; width: 100%">
            	<div class="col-lg-5 col-md-5 col-sm-5" style="overflow-y: scroll; overflow-x: hidden; height: 725px; width: 370px;">
                    <div class="editConcept" style="padding-left: 0px; padding-right: 0px; position: absolute;">
                        <table width="100px;" border="0" style="position: relative;">
                            <tbody>
                            <?php
    
///////////////////////////////////// Mention Value String			
		
								echo "<tr>";
								echo "<td style=\"";
                                echo "font-size: 1.0em; ";
                                echo "text-align: left; ";
                                echo "vertical-align: middle; ";
                                echo "padding-left: 10px; ";
								echo "background-color: #632A9D; ";
                                echo "\" ";
                                echo "colspan=\"2\">";
								echo "<span style=\"";
								echo "padding-top: 5px; ";
								echo "padding-bottom: 5px; ";
								echo "line-height: 1.7em; ";
								echo "font-size: 1.3em; ";
								echo "background-color: #632A9D; ";
								echo "color: #FFFFFF; ";
								echo "display: inline; ";
								echo "white-space: pre-wrap; ";
								echo "box-shadow: 10px 0 0 #632A9D, -10px 0 0 #632A9D; ";
								echo "\">$annotations_value_string</span>";
                                echo "</td>";
                                echo "</tr>";
		
///////////////////////////////////// Mention URI and Field
		
								echo "<tr>";
                                echo "<td style=\"";
                                echo "font-size: 1.0em; ";
                                echo "text-align: left; ";
                                echo "vertical-align: middle; ";
                                echo "padding-top: 10px; ";
								echo "color: #800000; ";
                                echo "\" ";
                                echo "colspan=\"2\">";	
                                echo "$annotations_reg_uri : $annotations_rdfs_label";
                                echo "<br />&nbsp;";
                                echo "</td>";
                                echo "</tr>";
		
///////////////////////////////////// Instructions		
		
								echo "<tr>";
                                echo "<td style=\"";
                                echo "font-size: 0.9em; ";
                                echo "text-align: justify; ";
                                echo "vertical-align: middle; ";
                                echo "padding-top: 10px; ";
								echo "color: #000000; ";
                                echo "\" ";
                                echo "colspan=\"2\">";	
                                echo "To add a related concept to the above highlighted phrase, begin a search by typing in the first few characters of another mention. If the mention you are looking for appears in the results pop-down list, simply click on it to add it as a related concept. You can add Child and Parent concepts.";
                                echo "<br />&nbsp;";
                                echo "</td>";
                                echo "</tr>";
		
///////////////////////////////////// Parent Concepts Header
		
								echo "<tr>";
                                echo "<td style=\"";
                                echo "font-size: 1.0em; ";
                                echo "text-align:left; ";
                                echo "vertical-align: middle; ";
								echo "padding-top: 10px; ";
								echo "padding-bottom: 0px; ";
                                echo "\" ";
                                echo "colspan=\"2\">";	
                                echo "<strong>Parent Concepts</strong>";
								echo "</td>";
                                echo "</tr>";
	
///////////////////////////////////// Search for Parent Concepts		
		
								echo "<tr>";
                                echo "<td style=\"";
                                echo "font-size: 1.0em; ";
                                echo "text-align: left; ";
                                echo "vertical-align: middle; ";
                                echo "padding-top: 10px; ";
								echo "padding-bottom: 10px; ";
								echo "color: #800000; ";
                                echo "\" ";
                                echo "colspan=\"2\" nowrap>";	
								echo "<div class=\"input-group\">";
								echo "<input id=\"parentSearch\" ";
								echo "type=\"text\" ";
								echo "class=\"form-control\" ";
								echo "name=\"parentSearch\" ";
								echo "value=\"\" ";
								echo "placeholder=\"Search for Parent Concepts\" ";
								echo "style=\"width: 300px; display: block; \" ";
								echo "onclick=\"var clearThis = $('#parentSearch').val('');\" ";
								echo ">";
								echo "<span class=\"input-group-addon\">";
								echo "<i class=\"glyphicon glyphicon-search\"></i>";
								echo "</span>";
								echo "</div>";
                                echo "</td>";
                                echo "</tr>";	
		
///////////////////////////////////// List Parent Concepts
		
								$rp = 0;
								$rpars = array();
								$foundRP = "";
								$queryA = "SELECT ";
								$queryA .= "* ";
								$queryA .= "FROM ";
								$queryA .= "relatedconcepts ";
								$queryA .= "WHERE ";
								$queryA .= "value_string = \"$annotations_value_string\" ";
								$queryA .= "ORDER BY value_string ASC";	
								$mysqli_resultA = mysqli_query($mysqli_link, $queryA);
								while($rowA = mysqli_fetch_row($mysqli_resultA)) {
									echo "<tr>";
									echo "<td style=\"";
									echo "font-size: 1.0em; ";
									echo "text-align:left; ";
									echo "vertical-align: top; ";
									echo "padding: 10px; ";
									echo "color: #000000; ";
									echo "background-color: #DFDFDF; ";
									echo "border-bottom: 2px solid #F9F9F9; ";
									echo "\" ";
									echo "width=\"100%\" >";	
									echo $rowA[4];	
									echo "<br />";
									echo "<span style=\"font-size: 0.8em;\">";
									echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
									echo "$rowA[2] : $rowA[3]";
									echo "</span>";
									echo "</td>";
									echo "<td style=\"";
									echo "font-size: 1.1em; ";
									echo "text-align: right; ";
									echo "vertical-align: bottom; ";
									echo "padding: 10px; ";
									echo "color: #800000; ";
									echo "background-color: #DFDFDF; ";
									echo "border-bottom: 2px solid #F9F9F9; ";
									echo "width: 35px; ";
									echo "\" ";
									echo "nowrap >";	
									echo "<a href=\"#\" id=\"deleteParent_".$rp."\">";
									echo "<i class=\"ti-trash\" style=\"font-weight: 900; color: #800000;\">";
									echo "</i>";
									echo "</a>";
									echo "</td>";
									echo "</tr>";
									$foundRP = "y";
									$rpars[$rp] = "$rowA[1]";
									$rp++;
								}	
								if(($foundRP != "y")){
									echo "<tr>";
									echo "<td style=\"";
									echo "font-size: 0.9em; ";
									echo "text-align: justify; ";
									echo "vertical-align: middle; ";
									echo "padding-top: 10px; ";
									echo "color: #800000; ";
									echo "\" ";
									echo "colspan=\"2\">";		
									echo "There are no parent concepts for the above phrase.";
									echo "</td>";
									echo "</tr>";
								}
		
///////////////////////////////////// Child Concepts Header	
                          
		                        echo "<tr>";
                                echo "<td style=\"";
                                echo "font-size: 1.0em; ";
                                echo "text-align:left; ";
                                echo "vertical-align: middle; ";
								echo "padding-top: 30px; ";
								echo "padding-bottom: 0px; ";
                                echo "\" ";
                                echo "colspan=\"2\">";	
                                echo "<strong>Child Concepts</strong>";
								echo "</td>";
                                echo "</tr>";
		
///////////////////////////////////// Search for Child Concepts
		
								echo "<tr>";
                                echo "<td style=\"";
                                echo "font-size: 1.0em; ";
                                echo "text-align: left; ";
                                echo "vertical-align: middle; ";
                                echo "padding-top: 10px; ";
								echo "padding-bottom: 10px; ";
								echo "color: #800000; ";
                                echo "\" ";
                                echo "colspan=\"2\" nowrap>";	
								echo "<div class=\"input-group\">";
								echo "<input id=\"mentionSearch\" ";
								echo "type=\"text\" ";
								echo "class=\"form-control\" ";
								echo "name=\"mentionSearch\" ";
								echo "value=\"\" ";
								echo "placeholder=\"Search for Child Concepts\" ";
								echo "style=\"width: 300px; display: block; \" ";
								echo "onclick=\"var clearThis = $('#mentionSearch').val('');\" ";
								echo ">";
								echo "<span class=\"input-group-addon\">";
								echo "<i class=\"glyphicon glyphicon-search\"></i>";
								echo "</span>";
								echo "</div>";
                                echo "</td>";
                                echo "</tr>";
		
///////////////////////////////////// List Child Concepts			
		
								$rc = 0;
								$rcons = array();
								$foundRC = "";
								$queryA = "SELECT ";
								$queryA .= "* ";
								$queryA .= "FROM ";
								$queryA .= "relatedconcepts ";
								$queryA .= "WHERE ";
								$queryA .= "annotations_reg_uri = \"$annotations_reg_uri\" AND ";
								$queryA .= "annotations_rdfs_label = \"$annotations_rdfs_label\" AND ";
								$queryA .= "annotations_value_string = \"$annotations_value_string\" ";
								$queryA .= "ORDER BY annotations_value_string ASC";	
								$mysqli_resultA = mysqli_query($mysqli_link, $queryA);
								while($rowA = mysqli_fetch_row($mysqli_resultA)) {
									echo "<tr>";
									echo "<td style=\"";
									echo "font-size: 1.0em; ";
									echo "text-align:left; ";
									echo "vertical-align: top; ";
									echo "padding: 10px; ";
									echo "color: #000000; ";
									echo "background-color: #DFDFDF; ";
									echo "border-bottom: 2px solid #F9F9F9; ";
									echo "\" ";
									echo "width=\"100%\" >";	
									echo $rowA[7];	
									echo "<br />";
									echo "<span style=\"font-size: 0.8em;\">";
									echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
									echo "$rowA[5] : $rowA[6]";
									echo "</span>";
									echo "</td>";
									echo "<td style=\"";
									echo "font-size: 1.1em; ";
									echo "text-align: right; ";
									echo "vertical-align: bottom; ";
									echo "padding: 10px; ";
									echo "color: #800000; ";
									echo "background-color: #DFDFDF; ";
									echo "border-bottom: 2px solid #F9F9F9; ";
									echo "width: 35px; ";
									echo "\" ";
									echo "nowrap >";	
									echo "<a href=\"#\" id=\"deleteMention_".$rc."\">";
									echo "<i class=\"ti-trash\" style=\"font-weight: 900; color: #800000;\">";
									echo "</i>";
									echo "</a>";
									echo "</td>";
									echo "</tr>";
									$foundRC = "y";
									$rcons[$rc] = "$rowA[1]";
									$rc++;
								}	
								if(($foundRC != "y")){
									echo "<tr>";
									echo "<td style=\"";
									echo "font-size: 0.9em; ";
									echo "text-align: justify; ";
									echo "vertical-align: middle; ";
									echo "padding-top: 10px; ";
									echo "color: #800000; ";
									echo "\" ";
									echo "colspan=\"2\">";		
									echo "There are no child concepts for the above phrase.";
									echo "</td>";
									echo "</tr>";
								}
		
///////////////////////////////////// Table Close
                                    
                            ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="col-lg-7 col-md-7 col-sm-7" style="margin-left: 10px; overflow-y: hidden; overflow-x: hidden; height: 725px; width: 470px; border: 2px solid #dddddd;">
                	<div class="editConcept2" style="padding-left: 0px; padding-right: 0px;">             
					<svg width="466" height="721"></svg>
                	</div>
                </div>
        	</div>
<?php	

///////////////////////////////////// Start Scripts
	
		
		$new = str_replace(' ', '%20', $your_string);
?>
		</div>
		<script language="javascript" type="text/javascript" 
			src="./js/jquery-ui/ui/jquery.ui.core.js"></script>
		<script language="javascript" type="text/javascript" 
			src="./js/jquery-ui/ui/jquery.ui.widget.js"></script>
		<script language="javascript" type="text/javascript" 
			src="./js/jquery-ui/ui/jquery.ui.position.js"></script>
		<script language="javascript" type="text/javascript" 
			src="./js/jquery-ui/ui/jquery.ui.menu.js"></script>
		<script language="javascript" type="text/javascript" 
			src="./js/jquery-ui/ui/jquery.ui.autocomplete.js"></script>
		<script language="javascript" type="text/javascript" 
			src="./plugins/bootstrap-tour/build/js/bootstrap-tour.min.js"></script>
		<script language="javascript" type="text/javascript" 
			src="./js/typeahead.bundle.js"></script>
		<script language="javascript" type="text/javascript" >
		
			$(document).ready(function() {
				
				<?php for($a=0;$a<$rc;$a++) { ?>
		
				$("#deleteMention_<?php echo $a; ?>").click(function(event) {
					var textA = "<?php echo $annotations_reg_uri; ?>";
					var textB = "<?php echo $annotations_rdfs_label; ?>";
					var textC = "<?php echo $annotations_value_string; ?>";
					var data_delete = "yes";
					var data_delete_id = "<?php echo $rcons[$a]; ?>";
					var dataE = 'action=EDIT_CONCEPT&data_delete=' 
										+ data_delete 
										+ '&data_delete_id=' 
										+ data_delete_id 
										+ '&annotations_reg_uri=' 
										+ encodeURI(textA) 
										+ '&annotations_rdfs_label=' 
										+ encodeURI(textB) 
										+ '&annotations_value_string=' 
										+ encodeURI(textC);	
					var doDiv = $('#EditMetaPanel').fadeOut('fast', function(){
						var searchVal = $('#EditMetaPanel').load('./data_meta_related.php',dataE, function(){
							var doDivAlso = $('#EditMetaPanel').fadeIn('slow');
						});
					});	
					return false;
				});	
				
				<?php } ?>
				
				<?php for($a=0;$a<$rp;$a++) { ?>
		
				$("#deleteParent_<?php echo $a; ?>").click(function(event) {
					var textA = "<?php echo $annotations_reg_uri; ?>";
					var textB = "<?php echo $annotations_rdfs_label; ?>";
					var textC = "<?php echo $annotations_value_string; ?>";
					var data_delete = "yes";
					var data_delete_id = "<?php echo $rpars[$a]; ?>";
					var dataE = 'action=EDIT_CONCEPT&data_delete=' 
										+ data_delete 
										+ '&data_delete_id=' 
										+ data_delete_id 
										+ '&annotations_reg_uri=' 
										+ encodeURI(textA) 
										+ '&annotations_rdfs_label=' 
										+ encodeURI(textB) 
										+ '&annotations_value_string=' 
										+ encodeURI(textC);	
					var doDiv = $('#EditMetaPanel').fadeOut('fast', function(){
						var searchVal = $('#EditMetaPanel').load('./data_meta_related.php',dataE, function(){
							var doDivAlso = $('#EditMetaPanel').fadeIn('slow');
						});
					});	
					return false;
				});	
				
				<?php } ?>
				
				$('#parentSearch').keypress(function(event) {
					if (event.keyCode == 13) {
						event.preventDefault();
					}
				});
				
				$('#parentSearch').bind('keyup input',function(event) {	
					var myLength = $("#parentSearch").val().length;
					if(myLength > 3) {	
						$("#parentSearch").autocomplete({
							source: function(request, response){
								$.ajax({
									url: "./data_relatedconcepts_search.php",
									dataType: "json",
									data: {
										term : request.term,
										variation : "ANNOTATIONS"
									},
									success: function (data) {
										response(data);
									}
								});
							},
							minLength: 3,
							delay: 5, 
							maxCacheLength: 3, 
							select: function(event, ui) {
								if(ui.item){
									var textA = "<?php echo $annotations_reg_uri; ?>";
									var textB = "<?php echo $annotations_rdfs_label; ?>";
									var textC = "<?php echo $annotations_value_string; ?>";
									var valink = ui.item.label;
									var lablink = ui.item.value;
									var cleanBarB = $('#parentSearch').val(''+valink);	
									var dataE = 'action=EDIT_CONCEPT&data_value_string=' 
										+ lablink 
										+ '&data_parent=<?php echo microtime(); ?>'
										+ '&annotations_reg_uri=' 
										+ encodeURI(textA) 
										+ '&annotations_rdfs_label=' 
										+ encodeURI(textB) 
										+ '&annotations_value_string=' 
										+ encodeURI(textC);		
									var doDiv = $('#EditMetaPanel').fadeOut('fast', function(){
										var searchVal = $('#EditMetaPanel').load('./data_meta_related.php',dataE, function(){
											var doDivAlso = $('#EditMetaPanel').fadeIn('slow');
										});
									});	
									return false;
								}
							}
						});							
					}
				});
				
				$('#mentionSearch').bind('keyup input',function(event) {	
					var myLength = $("#mentionSearch").val().length;
					if(myLength > 3) {	
						$("#mentionSearch").autocomplete({
							source: function(request, response){
								$.ajax({
									url: "./data_relatedconcepts_search.php",
									dataType: "json",
									data: {
										term : request.term,
										variation : "ANNOTATIONS"
									},
									success: function (data) {
										response(data);
									}
								});
							},
							minLength: 3,
							delay: 5, 
							maxCacheLength: 3, 
							select: function(event, ui) {
								if(ui.item){
									var textA = "<?php echo $annotations_reg_uri; ?>";
									var textB = "<?php echo $annotations_rdfs_label; ?>";
									var textC = "<?php echo $annotations_value_string; ?>";
									var valink = ui.item.label;
									var lablink = ui.item.value;
									var cleanBarB = $('#mentionSearch').val(''+valink);	
									var dataE = 'action=EDIT_CONCEPT&data_value_string=' 
										+ lablink 
										+ '&annotations_reg_uri=' 
										+ encodeURI(textA) 
										+ '&annotations_rdfs_label=' 
										+ encodeURI(textB) 
										+ '&annotations_value_string=' 
										+ encodeURI(textC);		
									var doDiv = $('#EditMetaPanel').fadeOut('fast', function(){
										var searchVal = $('#EditMetaPanel').load('./data_meta_related.php',dataE, function(){
											var doDivAlso = $('#EditMetaPanel').fadeIn('slow');
										});
									});	
									return false;
								}
							}
						});							
					}
				});
				
				$('#mentionSearch').keypress(function(event) {
					if (event.keyCode == 13) {
						event.preventDefault();
					}
				});

<?php

///////////////////////////////////// Do Visualisation

		if(($foundRC == "y") OR ($foundRP == "y")) {
		
?>
				var svg = d3.select("svg"),
					width = +svg.attr("width"),
					height = +svg.attr("height");

				var color = d3.scaleOrdinal(d3.schemeCategory20);

				var nodeRadius = 20;			

				var simulation = d3.forceSimulation()
					.velocityDecay(0.9)
					.force("link", d3.forceLink().id(function(d) { return d.id; }))
					.force("charge", d3.forceManyBody().strength(-2000))
					.force("collide", d3.forceCollide().radius(function(d) {
						return nodeRadius + 9.0; }).iterations(2))
					.force("center", d3.forceCenter(width / 3.3, height / 2));

				d3.json("data_meta_related_network_json.php?annotations_value_string=<?php echo $annotations_value_string; ?>&annotations_reg_uri=<?php echo $annotations_reg_uri; ?>&annotations_rdfs_label=<?php echo $annotations_rdfs_label; ?>", function(error, graph) {
				  if (error) throw error;

				  var link = svg.append("g")
					.attr("class", "links")
					.selectAll("line")
					.data(graph.links)
					.enter().append("line")
					.attr("stroke-width", "2");

				  var node = svg.append("g")
					.attr("class", "nodes")
					.selectAll("g")
					.data(graph.nodes)
					.enter().append("g")

				  var circles = node.append("circle")
					  .attr("r", 8)
					  .attr("fill", function(d) { return color(d.group); })
					  .call(d3.drag()
					  .on("start", dragstarted)
					  .on("drag", dragged)
					  .on("end", dragended));

				  var labels = node.append("text")
					  .text(function(d) {
						return d.id;
					  })
					  .on("mouseover",function(d,i){
						 $(this).css('cursor','pointer');
					  })
				  	  .on("click",function(d,i){
							var textA = d.regUri;
							var textB = d.rdfsLabel;
							var textC = d.id;
							var dataE = 'reload=&action=EDIT_CONCEPT' 							
								+ '&annotations_reg_uri=' 
								+ encodeURI(textA) 
								+ '&annotations_rdfs_label=' 
								+ encodeURI(textB) 
								+ '&annotations_value_string=' 
								+ encodeURI(textC);	
							var doDiv = $('#EditMetaPanel').fadeOut('fast', function(){
								var searchVal = $('#EditMetaPanel').load('./data_meta_related.php',dataE, function(){
									var doDivAlso = $('#EditMetaPanel').fadeIn('slow');
								});
							});	
							return false;						  
					  })
				  	  .style("font-size", 12)
					  .attr('x', 10)
					  .attr('y', 5);

//				  node.append("title")
//					  .text(function(d) { return d.id; });

				  simulation
					  .nodes(graph.nodes)
					  .on("tick", ticked);

				  simulation.force("link")
					  .links(graph.links);

				  function ticked() {
					link
						.attr("x1", function(d) { return d.source.x; })
						.attr("y1", function(d) { return d.source.y; })
						.attr("x2", function(d) { return d.target.x; })
						.attr("y2", function(d) { return d.target.y; });

					node
						.attr("transform", function(d) {
							return "translate(" + d.x + "," + d.y + ")";
						})
				  }
				});

				function dragstarted(d) {
				  if (!d3.event.active) simulation.alphaTarget(0.3).restart();
				  d.fx = d.x;
				  d.fy = d.y;
				}

				function dragged(d) {
				  d.fx = d3.event.x;
				  d.fy = d3.event.y;
				}

				function dragended(d) {
				  if (!d3.event.active) simulation.alphaTarget(0);
				  d.fx = null;
				  d.fy = null;
				}		
<?php

		}
			
///////////////////////////////////// Finish Visualisation

?>		
				
			});
			
<?php

///////////////////////////////////// Finish Scripts

?>		
		</script> 
<?php

///////////////////////////////////// Table for EDIT and EDIT_UPDATE Finish

		if(($action != "EDIT_CONCEPT")) {

?>               
    </body>
</html>
<?php

		}
	}

///////////////////////////////////////////////////////////// Finish

	include("./ar.dbdisconnect.php");
	
?>