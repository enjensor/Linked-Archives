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
//	23 June 2017
//	27-29 June 2017
//
//
/////////////////////////////////////////////////////////// Prevent Direct Access of Included Files

	define('MyConstInclude', TRUE);
	
/////////////////////////////////////////////////////////// Clean post and get	
	
	include("./ar.config.php");
	include("./ar.dbconnect.php");
	include("./index_functions.php");
	mb_internal_encoding("UTF-8");
	if (!mysqli_set_charset($mysqli_link, "utf8")) {
		echo "PROBLEM WITH CHARSET!";
		die;
	}	
	
/////////////////////////////////////////////////////////// Login routine

	$MerdUser = session_id();
	if(empty($MerdUser)) { 
		session_start(); 
	}
	$userlogin = $_POST["userlogin"];
	$userpassword = $_POST["userpassword"];
	$userlogout = $_POST["userlogout"];
	if(($userlogout == "yes")) {
		$_SESSION = array();
		if(ini_get("session.use_cookies")) {
    		$params = session_get_cookie_params();
    		setcookie(session_name(), '', time() - 42000, $params["path"], $params["domain"], $params["secure"], $params["httponly"]);
			session_destroy();
		}
	} else {
		if(($userlogin != "") && ($userpassword != "")) {
			$queryD = "SELECT * FROM contributors WHERE vcard_email = \"$userlogin\" AND credential_passPhrase = \"$userpassword\"; ";
			$mysqli_resultD = mysqli_query($mysqli_link, $queryD);
			while($rowD = mysqli_fetch_row($mysqli_resultD)) { 
				if(($rowD[5] == "Administrator")) {
					session_regenerate_id(true);
					$_SESSION["userlogin"] = "$userlogin";
					$_SESSION["userpassword"] = "$userpassword";
					$_SESSION["username"] = "$rowD[8]"." "."$rowD[7]";
					$_SESSION["administrator"] = "yes";
				}
			}
		}	
	}
	
/////////////////////////////////////////////////////////// Header	
	
	header("Cache-Control: no-cache");
	header("Pragma: no-cache");
	
/////////////////////////////////////////////////////////// View routine	
	
	$view_item = "";
	$view_metadata = "";
	$view_collection = "";
	$view = $_GET["view"];
	if(($view != "")) {
		$queryD = "SELECT * FROM items WHERE dc_identifier = \"$view\"; ";
		$mysqli_resultD = mysqli_query($mysqli_link, $queryD);
		while($rowD = mysqli_fetch_row($mysqli_resultD)) { 
			$view_item = $rowD[2];
			$view_metadata = $rowD[2];
			$view_collection = $rowD[5];
		}
	}
	
/////////////////////////////////////////////////////////// Destroy get and post arrays after use	
	
	$phpSelf = preg_replace("/data_doc.php/i","",$_SERVER["PHP_SELF"]);
	$phpSelf = preg_replace("/data_view_doc.php/i","",$phpSelf);
	$phpSelf = preg_replace("/index_view.php/i","",$phpSelf);
	$phpSelf = preg_replace("/index.php/i","",$phpSelf);
	$URI = "https://".$_SERVER["HTTP_HOST"].$phpSelf."item/".$view_item."";
	$_GET = array();
	$_POST = array();

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// Page header

?>
<!--


	ANDS HV COLLECTION PROJECT

	Title: Angus & Robertson Categorisation Toolkit
	Team: Jason Ensor, Helen Bones, Simon Burrows, Michael Gonzalez, Stephen Hannan
	Base: Western Sydney University, Digital Humanities Research Group, School of Humanities and Communication Arts
	Methodology: Procedural Scripting PHP | MySQL | JQuery



	FOR ALL ENQUIRIES ABOUT PROJECT

	Who:	Dr Jason Ensor
	Email: 	j.ensor@westernsydney.edu.au | jasondensor@gmail.com
	Web: 	http://www.jasonensor.com
	Mobile:	0419 674 770



  	WEB FRAMEWORK

  	Bootstrap Twitter | http://getbootstrap.com/
	JQuery | http://jquery.com/download/
    Nifty Responsive Admin Template | https://wrapbootstrap.com/theme/nifty-responsive-admin-template-WB0048JF7



  	VERSION 0.1
    
  	Development Started: 5 January 2017
	Last updated: 29 June 2017




















//-->
<!DOCTYPE html>
<html lang="en">
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]> 	   <html class="no-js"> <![endif]-->
    <head>   
    	<title>ARCHIVER: Western Sydney University, Dr Jason Ensor and Dr Helen Bones</title>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta http-equiv='cache-control' content='no-cache'>
		<meta http-equiv='expires' content='0'>
		<meta http-equiv='pragma' content='no-cache'>
		<meta name="description" content="ARCHIVER, Western Sydney University, Dr Jason Ensor and Dr Helen Bones: Funded by the Australian National Data Service (ANDS) and using the State Library of New South Wale's signature holdings on Angus and Robertson's operations throughout Australia as its central study, this project is about how we make better use of paper-based historical collections through digital technologies in ways that sustain and enhance archival practice and principles. Focusing on the diverse activities of Angus and Robertson's competition, cooperation and conflict with other Australian firms and individuals during the twentieth century becomes a way to focus on the interdependence of publishing organisations and players, in which each participant is part of a larger and complex whole. With so many interactions distributed among multiple volumes, understanding Angus and Robertson's total business through a historically tuned cultural analysis requires a step change in how research exploits digital technologies. Parallel with teasing out the complexities of Angus and Robertson's activities nationwide with other Australian authors, booksellers and publishers is the key principle of linking collections in new ways, where a document's relationship to other items in a volume is not only maintained but its relationship to other volumes and collections is exposed in ways better suited to our networked, data-intensive knowledge landscape." />
       	<meta name="robots" content="INDEX,FOLLOW" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=2.0, user-scalable=1">    
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
        <link rel="stylesheet" type="text/css" href="./js/jquery-ui/themes/base/jquery.ui.all.css">
  		<link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700&amp;subset=latin">
		<link rel="stylesheet" type="text/css" href="./css/bootstrap.min.css">
		<link rel="stylesheet" type="text/css" href="./css/nifty.min.css">
		<link rel="stylesheet" type="text/css" href="./plugins/themify-icons/themify-icons.min.css">
        <link rel="stylesheet" type="text/css" href="./plugins/ionicons/css/ionicons.min.css">
		<link rel="stylesheet" type="text/css" href="./css/pace.min.css">
        <link rel="stylesheet" type="text/css" href="./css/themes/type-c/theme-well-red.min.css">
        <link rel="stylesheet" type="text/css" href="./plugins/datatables/media/css/dataTables.bootstrap.css">
		<link rel="stylesheet" type="text/css" href="./plugins/datatables/extensions/Responsive/css/dataTables.responsive.css">
        <link rel="stylesheet" type="text/css" href="./plugins/ionicons/css/ionicons.min.css" >
        <link rel="stylesheet" type="text/css" href="./js/bootstrap-tagmanager/tagmanager.css">
        <link rel="stylesheet" type="text/css" href="./js/fancybox/jquery.fancybox.css" media="screen" />
        <link rel="stylesheet" type="text/css" href="./js/x-editable/dist/bootstrap3-editable/css/bootstrap-editable.css">  
        <link rel="stylesheet" type="text/css" href="./plugins/bootstrap-tour/build/css/bootstrap-tour.min.css">
        <link rel="stylesheet" type="text/css" href="./plugins/bootstrap-datepicker/bootstrap-datepicker.min.css">
        <link rel="stylesheet" type="text/css" href="./plugins/chosen/chosen.min.css">
        <link rel="stylesheet" type="text/css" href="./plugins/bootstrap-select/bootstrap-select.min.css">
        <link rel="stylesheet" type="text/css" href="./plugins/jquery-confirm/dist/jquery-confirm.min.css">
        <link rel="stylesheet" type="text/css" href="./leaflet/leaflet.css" />
		<script language="javascript" type="text/javascript" src="./js/pace.min.js"></script>
        <script language="javascript" type="text/javascript" src="./js/jquery-2.2.4.min.js"></script>
		<script language="javascript" type="text/javascript" src="./js/bootstrap.min.js"></script>
		<script language="javascript" type="text/javascript" src="./js/nifty.min.js"></script>
        <script language="javascript" type="text/javascript" src="./plugins/datatables/media/js/jquery.dataTables.js"></script>
		<script language="javascript" type="text/javascript" src="./plugins/datatables/media/js/dataTables.bootstrap.js"></script>
		<script language="javascript" type="text/javascript" src="./plugins/datatables/extensions/Responsive/js/dataTables.responsive.min.js"></script>
        <script language="javascript" type="text/javascript" src="./js/bootstrap-tagmanager/tagmanager.js"></script>
        <script language="javascript" type="text/javascript" src="./js/fancybox/jquery.fancybox.pack.js"></script> 
        <script language="javascript" type="text/javascript" src="./js/x-editable/dist/bootstrap3-editable/js/bootstrap-editable.js"></script> 
        <script language="javascript" type="text/javascript" src="./plugins/chosen/chosen.jquery.min.js"></script>
        <script language="javascript" type="text/javascript" src="./plugins/bootstrap-tour/build/js/bootstrap-tour.min.js"></script>	 
        <script language="javascript" type="text/javascript" src="./js/shiftzoom/shiftzoom.js"></script>	 
        <script language="javascript" type="text/javascript" src="./plugins/panzoom/dist/jquery.panzoom.min.js"></script>
        <script language="javascript" type="text/javascript" src="./plugins/bootstrap-datepicker/bootstrap-datepicker.min.js"></script>
        <script language="javascript" type="text/javascript" src="./plugins/bootstrap-select/bootstrap-select.min.js"></script>
        <script language="javascript" type="text/javascript" src="./plugins/jquery-confirm/dist/jquery-confirm.min.js"></script>
		<script language="javascript" type="text/javascript" src="./leaflet/leaflet.js"></script>
<?php

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// Manual CSS Interventions

?>         
        <style type="text/css" rel="stylesheet">
		
			.bootstrap-select {
				padding: 0px;
				margin: 0px;	
			}
		
			.tm-font {
				font-size: 1.0em;
				font-weight: bold;
			}

			.tt-query {
				-webkit-box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075);
				-moz-box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075);
				box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075);
			}
			
			.tt-hint {
				color: #999;
			}
			
			.tt-menu {
				width: 100%;
				margin: 12px 0;
				padding: 8px 0;
				background-color: #fff;
				border: 1px solid #ccc;
				border: 1px solid rgba(0, 0, 0, 0.2);
				-webkit-border-radius: 8px;
				-moz-border-radius: 8px;
				border-radius: 8px;
				-webkit-box-shadow: 0 5px 10px rgba(0,0,0,.2);
				-moz-box-shadow: 0 5px 10px rgba(0,0,0,.2);
				box-shadow: 0 5px 10px rgba(0,0,0,.2);
			}
			
			.tt-suggestion {
				padding: 3px 20px;
				font-size: 1.0em;
			}
			
			.tt-suggestion:hover {
				cursor: pointer;
				color: #fff;
				background-color: #0097cf;
			}
			
			.tt-suggestion.tt-cursor {
				color: #fff;
				background-color: #0097cf;
			}
			
			.tt-suggestion p {
				margin: 0;
			}
			
			.twitter-typeahead-jde {
				display: block!important;
			}
			
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
			
			.btn-default {
				margin-bottom: 2px;
				margin-right: 2px;
				min-width: 55px;	
			}
			
			.input-sm {
				max-width: 100px;	
			}
			
			.chosen-container-single .chosen-single {
				height: 35px;
				line-height: 34px;
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
			
			.popover[class*="tour-"] {
				background-color: #ffffff;
				border: 2px solid #000000;
				-webkit-box-shadow: 7px 7px 25px -4px rgba(0,0,0,0.75);
				-moz-box-shadow: 7px 7px 25px -4px rgba(0,0,0,0.75);
				box-shadow: 7px 7px 25px -4px rgba(0,0,0,0.75);
				-webkit-border-radius: 8px;
				-moz-border-radius: 8px;
				border-radius: 8px;
			}
			
			.popover[class*="arrow"], .popover[class*="arrow::after"] {
				border-top-width: 10px;
    			border-right-width: 10px;
    			border-bottom-width: 10px;
    			border-left-width: 10px;
				border: 2px solid #000000;
			}
			
			.fancybox-inner {
				padding: 0px;	
			}
			
			.fancybox-lock .fancybox-overlay {
    			overflow: hidden;
    			overflow-y: scroll;
			}
			
			.ui-autocomplete {
				max-height: 310px;
				line-height: 1.3em;
				font-size: 1.0em;
				padding: 3px;
				text-decoration: none;
				overflow-y: auto;
				overflow-x: hidden;
			}
			
			body .ui-autocomplete, .ui-menu-item, .ui-corner-all, .ui-corner-top, .ui-corner-left, 
			.ui-corner-tl, .ui-corner-right, .ui-corner-tr, .ui-corner-bottom, .ui-corner-bl, .ui-corner-br {
				line-height: 1.3em !important;
				font-size: 1.0em !important;
				padding: 3px !important;
				text-decoration: none !important;
				border-radius: 0px !important;
    			-webkit-border-radius: 0px !important;
    			-moz-border-radius: 0px !important;
				background-color: #FFFFFF !important;
			}
			
			.ui-state-focus {
				line-height: 1.3em !important;
				font-size: 1.0em !important;
				padding: 3px !important;
				margin: 0px !important;
				background: none !important;
				background-color: #dddddd !important;
				border: none !important;
			}
			
			.btn {
				white-space: normal;
			}
			
			img.grayscale {
				-webkit-filter: grayscale(1);
				-webkit-filter: grayscale(100%);
				filter: gray;
				filter: grayscale(100%);
			}
			
			.tooltip {
    			position: fixed;
			}
			
			.tooltip-inner {
				background-color: #9c2929;
			}
			
			.tooltip.right .tooltip-arrow {
				top: 50%;
				left: 0;
				margin-top: -5px;
				border-right-color: #9c2929;
				border-width: 5px 5px 5px 0;
			}
			
			.navbar-brand {
				width: 60px;	
			}
			
		</style>
    </head>   
	<body>
		<div id="container">
<?php

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// Header

?> 
			<header id="navbar">
				<div id="navbar-container" class="boxed" style="height: 47px;">
					<div class="navbar-header" style="height: 47px;">
                    	<a href="./" class="navbar-brand">
                        	<img src="./img/logo_trans.png" alt="WSU Logo" class="brand-icon" style="padding-left: 2px; padding-top: 5px; padding-bottom: 0px;">
                        </a>
                	</div>
                    <div class="navbar-content clearfix">
                        <ul class="nav pull-right">
                    		<li style="font-size: 0.9em; padding: 1.2em; color: #555555; word-break: break-all;"><strong><?php echo $URI; ?></strong></li>
               			</ul>
                	</div>
            	</div>
			</header>
<?php

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// Main Content

?>             
            <div class="boxed">
				<div id="content-container">
             		<div id="page-content">
                        <div class="row">
                            
                            <!-- Item Panel -->
                          	<div class="col-lg-6 col-md-6 col-sm-6" style="padding-top: 10px;">
                            	<div id="panel-detail" class="panel" style="max-height:89vh; border-radius: 0px;">
                                    <div id="doc_detail" style="background-color: #ecf0f5;">
									<?php
                                      
										$dc_identifier = $view_item;
                                        if(($dc_identifier  != "")) {
                                            include("./data_view_doc.php");
                                        } else {
                                            echo "<div id=\"titleDetail\" ";
                                            echo "class=\"panel-body text-light\" ";
                                            echo "style=\"text-align: left; overflow:hidden; max-height:86vh; border: 0px solid #263238;\">";
                                            echo "<img id=\"imgPlaceholder\" ";
                                            echo "class=\"mar-top bord-all\" ";
                                            echo "src=\"./icons/cover_image.png\" ";
                                            echo "width=\"100%\" ";
                                            echo "border=\"0\" ";
                                            echo "style=\"display:none;\">";
                                            echo "</div>";
                                        }
                                    
                                    ?>
									</div>   
                                </div>                             
                            </div>                           
                            
                            <!-- Metadata Panel -->
                          	<div class="col-lg-6 col-md-6 col-sm-6" style="padding-top: 25px; padding-right: 25px; padding-bottom: 25px; padding-left: 10px; font-size: 1.0em; line-height: 1.4em; color: #000000;">
									<?php
			
										if(($dc_identifier != "")) {
											
////////////////////////////////// Go to Editor	
		
											echo "<a href=\"./index.php?view=".$dc_identifier."\"><strong>HOME</strong></a>";
		
////////////////////////////////// Download Image		
		
											echo " | ";
											echo "<a href=\"";
											echo "./data_download.php?dc_identifier=".$dc_identifier."&type=jpg";
											echo "\">";
											echo "<strong>Download JPG</strong>";
											echo "</a>";
											
////////////////////////////////// Download CSV	

											echo " | ";
											echo "<a href=\"";
											echo "./data_download.php?dc_identifier=".$dc_identifier."&type=other&format=csv";
											echo "\">";
											echo "<strong>CSV</strong>";
											echo "</a>";											

////////////////////////////////// Download RDF XML
		
											echo " | ";
											echo "<a href=\"";
											echo "./data_download.php?dc_identifier=".$dc_identifier."&type=rdf&format=rdf";
											echo "\">";
											echo "<strong>XML</strong>";
											echo "</a>";
		
////////////////////////////////// Download RDFa
		
											echo " | ";
											echo "<a href=\"";
											echo "./data_download.php?dc_identifier=".$dc_identifier."&type=rdf&format=rdfa";
											echo "\">";
											echo "<strong>RDFa</strong>";
											echo "</a>";
		
////////////////////////////////// Download Turtle	

											echo " | ";
											echo "<a href=\"";
											echo "./data_download.php?dc_identifier=".$dc_identifier."&type=rdf&format=turtle";
											echo "\">";
											echo "<strong>Turtle</strong>";
											echo "</a>";	
		
////////////////////////////////// Download N-Triples	

											echo " | ";
											echo "<a href=\"";
											echo "./data_download.php?dc_identifier=".$dc_identifier."&type=rdf&format=ntriples";
											echo "\">";
											echo "<strong>N-Triples</strong>";
											echo "</a>";														

///////////////////////////////////////////////////////////// Get Item Details

											$queryD = "SELECT * FROM items WHERE dc_identifier = \"$dc_identifier\" ";
											$mysqli_resultD = mysqli_query($mysqli_link, $queryD);
											while($rowD = mysqli_fetch_row($mysqli_resultD)) { 
												$this_dc_references = $rowD[5];
												$this_dc_title = $rowD[6];
												$this_page = $rowD[7];
												$this_dc_format = $rowD[9];
												$this_prism_byteCount = $rowD[10];
												$this_dc_creator = $rowD[13];
												$this_org_FormalOrganisation = $rowD[14];
												$this_gn_name = $rowD[15];
												$this_dc_created = $rowD[16];
												$this_restricted = $rowD[18];
												$this_marc_addressee = $rowD[19];
												$this_rdaa_groupMemberOf = $rowD[20];
												$this_mads_associatedLocale = $rowD[21];
												$rdf_resource = $rowD[11];
												$item_found = "y";
											}

///////////////////////////////////////////////////////////// Get Collection Details
	
											$queryD = "SELECT * FROM collections WHERE dc_identifier = \"".$this_dc_references."\" ";
											$mysqli_resultD = mysqli_query($mysqli_link, $queryD);
											while($rowD = mysqli_fetch_row($mysqli_resultD)) { 
												$this_col_UUID = $rowD[2];
												$this_col_bf_heldBy	= $rowD[3];
												$this_col_bf_subLocation = $rowD[4];
												$this_col_bf_physicalLocation = $rowD[5];
												$this_col_skos_Collection = $rowD[6];	
												$this_col_skos_OrderedCollection = $rowD[7];	
												$this_col_skos_member = $rowD[8];	
												$this_col_disco_startDate = $rowD[9];	
												$this_col_disco_endDate = $rowD[10];
												$this_col_found = "y";
											}

///////////////////////////////////////////////////////////// Display Collection Metadata

											$this_dc_title = preg_replace("/:/","_","$this_dc_title");
											echo "<br /><br /><strong>COLLECTION</strong><br /><br />";
											echo "<table width=\"100%\" style=\"width: 100%;\">";
											if(($this_col_found == "y")) {
												echo "<tr><td width=\"50%\" style=\"width: 50%; vertical-align: text-top; \">";
												echo "skos:collection</td><td width=\"50%\" style=\"width: 50%; vertical-align: text-top; \">".$this_col_skos_Collection."</td></tr>";
											}
											if(($this_col_bf_heldBy != "")) {
												echo "<tr><td width=\"50%\" style=\"width: 50%; vertical-align: text-top; \">";
												echo "bf:heldBy</td><td width=\"50%\" style=\"width: 50%; vertical-align: text-top; \">".$this_col_bf_heldBy."</td></tr>";
												if(($this_col_bf_subLocation != "")) {
													echo "<tr><td width=\"50%\" style=\"width: 50%; vertical-align: text-top; \">";
													echo "bf:subLocation</td><td width=\"50%\" style=\"width: 50%; vertical-align: text-top; \">".$this_col_bf_subLocation."</td></tr>";
												}
												if(($this_col_bf_physicalLocation != "")) {
													echo "<tr><td width=\"50%\" style=\"width: 50%; vertical-align: text-top; \">";
													echo "bf:physicalLocation</td><td width=\"50%\" style=\"width: 50%; vertical-align: text-top; \">".$this_col_bf_physicalLocation."</td></tr>";
												}
												echo "<tr><td width=\"50%\" style=\"width: 50%; vertical-align: text-top; \">";
												echo "skos:orderedCollection</td><td width=\"50%\" style=\"width: 50%; vertical-align: text-top; \">".$this_col_skos_OrderedCollection."</td></tr>";
												echo "<tr><td width=\"50%\" style=\"width: 50%; vertical-align: text-top; \">";
												echo "disco:startDate</td><td width=\"50%\" style=\"width: 50%; vertical-align: text-top; \">".$this_col_disco_startDate."</td></tr>";
												echo "<tr><td width=\"50%\" style=\"width: 50%; vertical-align: text-top; \">";
												echo "disco:endDate</td><td width=\"50%\" style=\"width: 50%; vertical-align: text-top; \">".$this_col_disco_endDate."</td></tr>";
												echo "<tr><td width=\"50%\" style=\"width: 50%; vertical-align: text-top; \">";
												echo "skos:member</td><td width=\"50%\" style=\"width: 50%; vertical-align: text-top; \">".$this_col_skos_member."</td></tr>";
												echo "<tr><td width=\"50%\" style=\"width: 50%; vertical-align: text-top; \">";
												echo "bibo:pages</td><td width=\"50%\" style=\"width: 50%; vertical-align: text-top; \">".$this_page."</td></tr>";
											}
											echo "</table>";

///////////////////////////////////////////////////////////// Display Item Metadata

											echo "<br /><strong>ITEM</strong><br /><br />";
											echo "<table width=\"100%\" style=\"width: 100%;\">";
											echo "<tr><td width=\"50%\" style=\"width: 50%; vertical-align: text-top; \">dc:title</td>";
											echo "<td width=\"50%\" style=\"width: 50%; vertical-align: text-top; \">".$this_dc_title."</td></tr>";
											echo "<tr><td width=\"50%\" style=\"width: 50%; vertical-align: text-top; \">dc:identifier</td>";
											echo "<td width=\"50%\" style=\"width: 50%; vertical-align: text-top; \">".$dc_identifier."</td></tr>";
											echo "<tr><td width=\"50%\" style=\"width: 50%; vertical-align: text-top; \">dc:format</td>";
											echo "<td width=\"50%\" style=\"width: 50%; vertical-align: text-top; \">".$this_dc_format."</td></tr>";
											echo "<tr><td width=\"50%\" style=\"width: 50%; vertical-align: text-top; \">prism:byteCount</td>";
											echo "<td width=\"50%\" style=\"width: 50%; vertical-align: text-top; \">".$this_prism_byteCount."</td></tr>";
											if(($this_dc_creator != "") && ($this_dc_creator != "Nothing Specified")) {
												echo "<tr><td width=\"50%\" style=\"width: 50%; vertical-align: text-top; \">dc:creator</td>";
												echo "<td width=\"50%\" style=\"width: 50%; vertical-align: text-top; \">".$this_dc_creator."</td></tr>";
											}
											if(($this_dc_creator != "") && ($this_dc_creator != "Nothing Specified") 
												&& ($this_org_FormalOrganisation != "") && ($this_org_FormalOrganisation != "Nothing Specified")) {
												echo "<tr><td width=\"50%\" style=\"width: 50%; vertical-align: text-top; \">org:FormalOrganisation</td>";
												echo "<td width=\"50%\" style=\"width: 50%; vertical-align: text-top; \">";
												echo $this_org_FormalOrganisation."</td></tr>";
											}
											if(($this_dc_creator != "") && ($this_dc_creator != "Nothing Specified") 
												&& ($this_gn_name != "") && ($this_gn_name != "Nothing Specified")) {
												echo "<tr><td width=\"50%\" style=\"width: 50%; vertical-align: text-top; \">gn:name</td>";
												echo "<td width=\"50%\" style=\"width: 50%; vertical-align: text-top; \">";
												echo $this_gn_name."</td></tr>";
											}
											if(($this_dc_creator != "") && ($this_marc_addressee != "") 
												&& ($this_marc_addressee != "Nothing Specified") && ($this_dc_creator != "Nothing Specified")) {
												echo "<tr><td width=\"50%\" style=\"width: 50%; vertical-align: text-top; \">marc:addressee</td>";
												echo "<td width=\"50%\" style=\"width: 50%; vertical-align: text-top; \">";
												echo $this_marc_addressee."</td></tr>";
											}
											if(($this_marc_addressee != "") && ($this_marc_addressee != "Nothing Specified") 
												&& ($this_rdaa_groupMemberOf != "") && ($this_rdaa_groupMemberOf != "Nothing Specified")) {
												echo "<tr><td width=\"50%\" style=\"width: 50%; vertical-align: text-top; \">rdaa:groupMemberOf</td>";
												echo "<td width=\"50%\" style=\"width: 50%; vertical-align: text-top; \">";
												echo $this_rdaa_groupMemberOf."</td></tr>";
											}
											if(($this_marc_addressee != "") && ($this_marc_addressee != "Nothing Specified") 
												&& ($this_mads_associatedLocale != "") && ($this_mads_associatedLocale != "Nothing Specified")) {
												echo "<tr><td width=\"50%\" style=\"width: 50%; vertical-align: text-top; \">mads:associatedLocale</td>";
												echo "<td width=\"50%\" style=\"width: 50%; vertical-align: text-top; \">";
												echo $this_mads_associatedLocale."</td></tr>";
											}
											if(($this_dc_created != "")) {
												echo "<tr><td width=\"50%\" style=\"width: 50%; vertical-align: text-top; \">dc:created</td>";
												echo "<td width=\"50%\" style=\"width: 50%; vertical-align: text-top; \">";
												echo $this_dc_created."</td></tr>";
											}
											echo "</table>";
											
////////////////////////////////// Get Annotations											
											
											echo "<br /><strong>MENTIONS</strong><br /><br />";
											$queryD = "SELECT * FROM annotations WHERE dc_references = \"$dc_identifier\" ";
											$mysqli_resultD = mysqli_query($mysqli_link, $queryD);
											while($rowD = mysqli_fetch_row($mysqli_resultD)) { 
												$itemTags[] = $rowD[5]."|".$rowD[6]."|".$rowD[7];
											}
											sort($itemTags);
											echo "<table width=\"100%\" style=\"width: 100%;\">";
											foreach($itemTags as $iTz) {
												$bitz = explode("|","$iTz");
												echo "<tr><td width=\"50%\" ";
												echo "style=\"width: 50%; vertical-align: text-top; \">".$bitz[0].":".$bitz[1]."</td>";
												echo "<td width=\"50%\" style=\"width: 50%; vertical-align: text-top; \">".$bitz[2]."</td></tr>";
											}
											echo "</table>";

////////////////////////////////// QR Code

											echo "<br /><strong>QR CODE</strong><br /><br />";
											echo "<img src=\"./data_qrcode.php?qrCode=$URI\" style=\"padding: 0px;\">";
											echo "<br />&nbsp;<br />&nbsp;";

////////////////////////////////// Close
												
										}
			
                                    ?>
                            </div> 
                        </div>
                 	</div>
				</div>            
			</div>         
        </div>
<?php

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// Scripts

?>
		<script language="javascript" type="text/javascript" src="./js/jquery-ui/ui/jquery.ui.core.js"></script>
        <script language="javascript" type="text/javascript" src="./js/jquery-ui/ui/jquery.ui.widget.js"></script>
        <script language="javascript" type="text/javascript" src="./js/jquery-ui/ui/jquery.ui.position.js"></script>
		<script language="javascript" type="text/javascript" src="./js/jquery-ui/ui/jquery.ui.menu.js"></script>
        <script language="javascript" type="text/javascript" >
		
		  	(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
		  	(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
		  	m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
		  	})(window,document,'script','https://www.google-analytics.com/analytics.js','ga');
		
		  	ga('create', 'UA-10553362-15', 'auto');
		  	ga('send', 'pageview');

	</script>
    </body>
</html>
<?php

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// Finish

	include("./ar.dbdisconnect.php");

?> 