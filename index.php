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
//  5-6 January 2017
//  8-13 January 2017
//  15-16 January 2017
//  9 February 2017
//  13 February 2017
//  22-23 February 2017
//  27-28 February 2017
//  1-2 March 2017
//  14 March 2017
//  3-4 April 2017
//  18 April 2017
//  26 April 2017
//	11 May 2017
//	23-25 May 2017
//	30 May 2017
//	2-8 June 2017
//	22-23 June 2017
//	28-29 June 2017
//	6-7 July 2017
//	6-13 August 2018
//	20 August 2018
//  29 October 2018
//  6-8 November 2018
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
	if(isset($_POST["userlogin"])){
		$userlogin = $_POST["userlogin"];
		$userpassword = $_POST["userpassword"];
		$userlogout = $_POST["userlogout"];
	}
	if(isset($_GET["userlogin"])){
		$userlogin = $_GET["userlogin"];
		$userpassword = $_GET["userpassword"];
		$userlogout = $_GET["userlogout"];
	}
	if(isset($_GET["userlogout"])){
		$userlogout = $_GET["userlogout"];
	} else {
		$userlogout = $_POST["userlogout"];
	}
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
					$_SESSION["credential_loginName"] = "$rowD[9]";
					$_SESSION["contributor"] = "$rowD[2]";
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
	
	$alphabet = array("a","b","c","d","e","f","g","h","i","j","k","l","m","n","o","p","q","r","s","t","u","v","w","x","y","z");
	$_GET = array();
	$_POST = array();

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// Page header

?>
<!--


	ANDS HV COLLECTION PROJECT

	Title: Angus & Robertson Categorisation Toolkit
	Team: Jason Ensor, Helen Bones, Michael Gonzalez, Simon Burrows, Stephen Hannan
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
	Last updated: 6 November 2018




















//-->
<!DOCTYPE html>
<html lang="en">
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]> 	   <html class="no-js"> <![endif]-->
    <head>   
    	<title>LINKED ARCHIVES: Western Sydney University, Dr Jason Ensor and Dr Helen Bones</title>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta http-equiv='cache-control' content='no-cache'>
		<meta http-equiv='expires' content='0'>
		<meta http-equiv='pragma' content='no-cache'>
        <meta name="description" content="LINKED ARCHIVES, Western Sydney University, Dr Jason Ensor and Dr Helen Bones: Funded by the Australian National Data Service (ANDS) and using the State Library of New South Wale's signature holdings on Angus and Robertson's operations throughout Australia as its central study, this project is about how we make better use of paper-based historical collections through digital technologies in ways that sustain and enhance archival practice and principles. Focusing on the diverse activities of Angus and Robertson's competition, cooperation and conflict with other Australian firms and individuals during the twentieth century becomes a way to focus on the interdependence of publishing organisations and players, in which each participant is part of a larger and complex whole. With so many interactions distributed among multiple volumes, understanding Angus and Robertson's total business through a historically tuned cultural analysis requires a step change in how research exploits digital technologies. Parallel with teasing out the complexities of Angus and Robertson's activities nationwide with other Australian authors, booksellers and publishers is the key principle of linking collections in new ways, where a document's relationship to other items in a volume is not only maintained but its relationship to other volumes and collections is exposed in ways better suited to our networked, data-intensive knowledge landscape." />
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
		<script language="javascript" type="text/javascript" src="https://d3js.org/d3.v4.min.js"></script>
<?php

///////////////////////////////////////////////////////////////////////////////// Manual CSS Interventions

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
            
            .btn-navigation {
				background-color: #90322e !important;
                font-weight: 700 !important;
                padding: 3px !important;
			}
            
            .btn-selected-navigation {
                font-weight: 700 !important;
                padding: 3px !important;
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
				white-space:pre-wrap;
			}
			
			.tooltip.right .tooltip-arrow {
				top: 50%;
				left: 0;
				margin-top: -5px;
				border-right-color: #9c2929;
				border-width: 5px 5px 5px 0;
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
            
            #imageLoader {
			  overflow: visible!important;
			}

/* XS MEDIA QUERY */

			
			@media (max-width: 575px) {
				#titleTags {
					min-height:0; 
					max-height: none; 
					text-align: left;
					height: auto!important;
				}
				#panel-titles {
					min-height:0; 
					max-height: none;
					height: auto!important; 
				}
				#panel-detail {
					min-height:0; 
					max-height: none; 
					height: auto!important; 
				}
				#focal {
					min-height:0; 
					max-height: none; 
					height: auto!important;
					background: none;
				}
				#focalImg {
					min-height:0; 
					max-height: none; 
					height: auto!important;
					background: none;
				}
				#collectionName {
					padding-top: 15px;	
				}
				body {
					padding-bottom: 25px;	
				}
				.navbar-top-links {
					font-size: 0.8em;
				}
			}

/* SM MEDIA QUERY */
			
			@media (min-width: 576px) and (max-width: 767px) {
				#titleTags {
					min-height:0; 
					max-height: none; 
					text-align: left;
					height: auto!important;
				}
				#panel-titles {
					min-height:0; 
					max-height: none;
					height: auto!important; 
				}
				#panel-detail {
					min-height:0; 
					max-height: none; 
					height: auto!important; 
				}
				#focal {
					min-height:0; 
					max-height: none; 
					height: auto!important;
					background: none;
				}
				#focalImg {
					min-height:0; 
					max-height: none; 
					height: auto!important;
					background: none;
				}
				#collectionName {
					padding-top: 15px;	
				}
				body {
					padding-bottom: 0px;	
				}
			}

/* MD MEDIA QUERY */
			
			@media (min-width: 768px) and (max-width: 991px) {
				#titleTags {
					min-height:0; 
					max-height: none; 
					text-align: left; 
					height: auto!important;
				}
				#panel-titles {
					min-height:0; 
					max-height: none;
					height: auto!important; 
				}
				#panel-detail {
					min-height:0; 
					max-height: none; 
					height: auto!important; 
				}
				#focal {
					min-height:0; 
					max-height: none; 
					height: auto!important;
					background: none;
				}
				#focalImg {
					min-height:0; 
					max-height: none; 
					height: auto!important;
					background: none;
				}
				#collectionName {
					padding-top: 15px;	
				}
				body {
					padding-bottom: 0px;	
				}
			}
	
/* LG MEDIA QUERY */	
			
			@media (min-width: 992px) and (max-width: 1199px) {
				#titleTags {
					overflow-x: hidden!important; 
					height: 81vh!important; 
					overflow-y: scroll!important; 
					text-align: left; 
					font-size: 0.7em;
				}	
				#panel-titles {
					min-height: 91vh!important; 
					max-height: 91vh!important;
				}
				#panel-detail {
					min-height: 91vh!important;  
					max-height: 91vh!important;  
				}
				#focal {
					overflow: hidden; 
					min-height: 90.8vh; 
					max-height: 90.8vh; 
					background-color: #000000; 
				}
				#focalImg {
					background-color: #000000; 
					min-height: 90.8vh; 
					max-height: 90.8vh; 
				}
				#detailsInsert {
					font-size: 0.8em;	
				}
				#collectionName {
					padding-left: 15px;
					font-size: 0.8em;	
				}
				#dt-basic_wrapper {
					font-size: 0.7em;
				}
				.btn {
					font-size: 0.7em;
					padding: 2px;
				}
				.mediaTable {
					display:none;
					width:0;
					height:0;
					opacity:0;
					visibility: collapse;
				}
			}

/* XL MEDIA QUERY */
			
			@media (min-width: 1200px) and (max-width: 1399px) {
				#titleTags {
					overflow-x: hidden!important; 
					height: 81vh!important; 
					overflow-y: scroll!important; 
					text-align: left; 
				}
				#panel-titles {
					min-height: 91vh!important; 
					max-height: 91vh!important;
				}
				#panel-detail {
					min-height: 91vh!important;  
					max-height: 91vh!important;  
				}
				#focal {
					overflow: hidden; 
					min-height: 90.8vh; 
					max-height: 90.8vh; 
					background-color: #000000; 
					background: transparent url(./loading_spinner.gif) no-repeat scroll center center; 
				}
				#focalImg {
					background-color: #000000; 
					min-height: 90.8vh; 
					max-height: 90.8vh; 
				}
				#detailsInsert {
					font-size: 0.9em;	
				}
				.btn {
					font-size: 0.8em;
					padding: 2px;
				}
				.mediaTableB {
					display:none;
					width:0;
					height:0;
					opacity:0;
					visibility: collapse;
				}
			}
			
/* DESKTOP MEDIA QUERY */

			@media (min-width: 1400px) {
				#titleTags {
					overflow-x: hidden!important; 
					height: 81vh!important; 
					overflow-y: scroll!important; 
					text-align: left; 
				}
				#panel-titles {
					min-height: 91vh!important; 
					max-height: 91vh!important;
				}
				#panel-detail {
					min-height: 91vh!important;  
					max-height: 91vh!important;  
				}
				#focal {
					overflow: hidden; 
					min-height: 90.8vh; 
					max-height: 90.8vh; 
					background-color: #000000; 
					background: transparent url(./loading_spinner.gif) no-repeat scroll center center; 
				}
				#focalImg {
					background-color: #000000; 
					min-height: 90.8vh; 
					max-height: 90.8vh; 
				}
				#detailsInsert {
					font-size: 0.9em;	
				}
			}										
			
		</style>
    </head>   
	<body>
		<div id="container" class="effect mainnav-sm aside-float aside-dark">
<?php
			
///////////////////////////////////////////////////////////////////////// Header
			
///////////////////////////////////////////////////////////////////////// Header

?> 
			<header id="navbar">
				<div id="navbar-container" class="boxed" style="height: 47px;">
					<div class="navbar-header" style="height: 47px;">
                    	<a href="./" class="navbar-brand">
                        	<img src="./img/logo_trans.png" alt="WSU Logo" class="brand-icon" style="padding-left: 5px; padding-top: 10px; padding-bottom: 0px;">
                        </a>
                	</div>
                    <div class="navbar-content clearfix">
                        <ul class="nav navbar-top-links pull-left">
                    		<li style="font-size: 1.4em; padding: 0.7em; color: #1b746c;" 
                            	class="text-bold">&nbsp;LINKED ARCHIVES</li>
               			</ul>
                        <?php if(($_SESSION["administrator"] == "yes")) { ?>
                        <ul class="nav navbar-top-links pull-right hidden-xs hidden-sm hidden-md">
							<li style="padding-top: 4px;"><a href="#" class="aside-toggle navbar-aside-icon" id="demo-aside-toggle"><i class="pci-ver-dots"></i></a></li>
						</ul>
                        <?php
						
						}

///////////////////////////////////////////////// Login Routine
						
						$session_reload = "yes";
						echo "<div class=\"hidden-xs hidden-sm\">";
						include("./ar.login.php");	
						echo "</div>";
													
						?>
                	</div>
            	</div>
			</header>
<?php

///////////////////////////////////////////////////////////////////////// Main Content
			
///////////////////////////////////////////////////////////////////////// Main Content

?>             
            <div class="boxed">
				<div id="content-container">
                    
              		<!-- Page Content Open -->
             		<div id="page-content">
                        <div class="row">
                            
                            <!-- Documents Panel -->
                          	<div class="col-lg-6 col-md-6" style="padding-left: 0px; padding-right: 0px;" id="arcDocPanel">
                            	<div id="panel-detail" class="panel" style="border-radius: 0px;">
                                    <div id="doc_detail" style="background-color: #000000;">
									<?php
                                    
                                        $queryDX = "SELECT dc_identifier ";
										$queryDX .= "FROM items ";
										$queryDX .= "ORDER BY RAND() ";
										$queryDX .= "LIMIT 1";
                                        $mysqli_resultDX = mysqli_query($mysqli_link, $queryDX);
                                        while($rowDX = mysqli_fetch_row($mysqli_resultDX)) { 
                                            $randIMG = $rowDX[0];
                                        }
                                        if(($randIMG != "")) {
											$IMGreload = "y";
                                            include("./data_doc.php");
                                        } else {
                                            echo "<div id=\"titleDetail\" ";
                                            echo "class=\"panel-body text-light\" ";
                                            echo "style=\"text-align: left; overflow:hidden; ";
                                            echo "max-height:86vh; border: 0px solid #263238;\">";
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
                          	<div class="col-lg-3 col-md-3" style="padding-left: 0px; padding-right: 0px;" id="arcMetaPanel">
                            	<div id="panel-detail" class="panel panel-colorful bg-gray-dark panel-bordered" style="border-radius: 0px;">
                                	<div class="panel-heading" id="panel-keywords">
                                    	<h3 class="panel-title"><a style="color:#000000;" href="javascript:<?php
                                        
										echo "var dataSearch = 'reload=yes';	";
										echo "var doDivSearchA = $('#titleTags').fadeOut('fast', ";
                                        echo "function(){ ";
										echo "var doDivSearchB = $('#titleTags').load('./data_meta.php', ";
										echo "dataSearch, function(){ ";
										echo "var doDivSearchC = $('#titleTags').fadeIn('slow'); ";
										echo "}); ";
										echo "}); ";
										
										?>">METADATA</a> / <a style="color:#000000;" href="javascript:<?php
                                        
										echo "var dataSearchD = '';	";
										echo "var doDivSearchE = $('#titleTags').fadeOut('fast', function(){ ";
										echo "var doDivSearchF = $('#titleTags')";
										echo ".load('./index_find_subjects.php',dataSearchD, function(){ ";
										echo "var doDivSearchG = $('#titleTags').fadeIn('slow'); ";
										echo "}); ";
										echo "}); ";
										
										?>">MENTIONS</a></h3>
                                	</div>
                                    <div id="titleTags" class="panel-body text-dark">
 									<?php

///////////////////////////////////////////////////////////// Load Default Metadata Panel Start

										if(($view_metadata == "")) {
											$reload = "No";
										} else {
											$reload = "view";	
										}
										include("./data_meta.php");

///////////////////////////////////////////////////////////// Load Default Metadata Panel Finish

									?>                                    
                                    </div>
								</div>
                            </div> 
                            
                            <!-- Collections and Items Panel -->
                        	<div class="col-lg-3 col-md-3" style="padding-left: 0px; padding-right: 0px;" id="arcColPanel">
                            	<div id="panel-titles" class="panel panel-bordered" style="border-radius: 0px;">
                                	<div class="panel-heading">
                                    	<h3 class="panel-title"><a href="javascript:<?php
                                        
										echo "var dataSearch = '';	";
										echo "var doDivSearchA = $('#tableResultsContainer').";
										echo "fadeOut('fast', function(){ ";
										echo "var doDivSearchB = $('#tableResultsContainer')";
										echo ".load('./data_collections.php',dataSearch, function(){ ";
										echo "var doDivSearchC = $('#tableResultsContainer').fadeIn('slow'); ";
										echo "}); ";
										echo "}); ";
										
										?>">COLLECTIONS</a> / <a href="javascript:<?php
                                        
										echo "var dataSearch = '';	";
										echo "var doDivSearchA = $('#tableResultsContainer')";
										echo ".fadeOut('fast', function(){ ";
										echo "var doDivSearchB = $('#tableResultsContainer')";
										echo ".load('./index_find_dates.php',dataSearch, function(){ ";
										echo "var doDivSearchC = $('#tableResultsContainer').fadeIn('slow'); ";
										echo "}); ";
										echo "}); ";
										
										?>">DATES</a></h3>
                                	</div>
                                    <div class="panel-body" style="text-align: justify;">
                                        <div id="tableResultsContainer">                                 		
										<?php

///////////////////////////////////////////////////////////// Load Default Data Start

											if(($view_collection == "")) {
												$reload = "No";
												include("./data_collections.php");
											} else {
												$reload = "view";	
												include("./data_items.php");
											}

///////////////////////////////////////////////////////////// Load Default Data Finish

										?>                      			
                                        </div>
                                        <div style="display:none;"><div id="theDarkCloset"></div></div>
                                    </div>
								</div>
                            </div>
                            
                    	 <!-- Page Content Close -->                                    
                        </div>
                 	</div>
                    
                    <!-- Container Close -->
				</div>
<?php

///////////////////////////////////////////////////////////////////////// Navigation

?>                 
  				<nav id="mainnav-container">
                	<div id="mainnav">
						<div id="mainnav-menu-wrap">
                        	<div class="nano">
                            	<div class="nano-content">
                                    <div id="mainnav-shortcut" style="max-height: 410px;">
                                        <ul class="list-unstyled">
                                          	<li class="col-xs-4" data-content="About ARCHIVER">
                                            	<a class="shortcut-grid extLinkB" id="extLinkB" href="./index_about.php"><i class="ion-information-circled" style="font-size: 1.2em;"></i></a></li>    
                                            <li class="col-xs-4" data-content="View Database Schema">
                                            	<a class="shortcut-grid extLink" id="extLink" href="./img/db_draft.png"><i class="ion-gear-a" style="font-size: 1.2em;"></i></a></li> 
                                            <li class="col-xs-4" data-content="Visualise Mentions">
                                            	<a class="shortcut-grid" href="javascript: <?php
																			   
													echo "var dataG = 'reload='; ";		
													echo "var doDivK = $('#doc_detail').fadeOut('fast', function(){ ";
													echo "var doDivP = $('#doc_detail').load('./data_meta_related_visualise.php',dataG, function(){ ";
													echo "var doDivW = $('#doc_detail').fadeIn('slow'); ";
													echo "}); ";
													echo "}); ";		   
																			   
												?>" target="_Network"><i class="ion-network" style="font-size: 1.2em;"></i></a></li>
                                            <li class="col-xs-4" data-content="Map Letter Origins and Destinations">
                                            	<a class="shortcut-grid" href="javascript: <?php
												
													echo "var dataE = 'refresh=no';	";		
													echo "var searchVal = $('#doc_detail').load('./data_map.php',dataE, function(){ ";
													echo "}); ";
												
												?>"><i class="ion-earth" style="font-size: 1.2em;"></i></a></li>  
                                            <li class="col-xs-4" data-content="Map Letter Mentions">
                                            	<a class="shortcut-grid" href="javascript: <?php
												
													echo "var dataE = 'refresh=no';	";		
													echo "var searchVal = $('#doc_detail').load('./data_map_mentions.php',dataE, function(){ ";
													echo "}); ";
												
												?>"><i class="ion-map" style="font-size: 1.2em;"></i></a></li>   
                                            <li class="col-xs-4" data-content="View Books">
                                            	<a class="shortcut-grid" href="javascript: <?php
												
													echo "var dataE = 'refresh=no';	";		
													echo "var searchVal = $('#doc_detail').load('./data_googlebooks.php',dataE, function(){ ";
													echo "}); ";
												
												?>"><i class="ion-document-text" style="font-size: 1.2em;"></i></a></li>
                                            <li class="col-xs-4" data-content="View Random Item">
                                            	<a class="shortcut-grid" href="javascript: <?php
												
													echo "var dataE = 'random=yes';	";		
													echo "var searchVal = $('#doc_detail').load('./data_doc.php',dataE, function(){ ";
													echo "}); ";
												
												?>"><i class="ion-help-circled" style="font-size: 1.2em;"></i></a></li>
                                            <?php 
											
///////////////////// If Admin											
											
											if(($_SESSION["administrator"] == "yes")) { ?>    
                                            <li class="col-xs-4" data-content="View Flagged Records">
                                            	<a class="shortcut-grid" href="javascript: <?php
												
													echo "var dataE = 'action=FLAGGED';	";		
													echo "var doDiv = $('#tableResultsContainer').fadeOut('fast', function(){ ";
													echo "var searchVal = $('#tableResultsContainer').load('./data_flags.php',dataE, function(){ ";
													echo "var doDivAlso = $('#tableResultsContainer').fadeIn('slow'); ";
													echo "}); ";
													echo "}); ";
												
												?>"><i class="ion-flag" style="font-size: 1.2em;"></i></a></li>
                                            <?php } ?>
                                     		<?php 
											
///////////////////// If Admin											
											
											if(($_SESSION["administrator"] == "yes")) { ?>
                                            <li class="col-xs-4" data-content="Admin">
                                            	<a class="shortcut-grid" href="#"><i class="ion-settings" style="font-size: 1.2em;"></i></a></li>
                                            <?php } ?>
                                        </ul>
                                    </div>
                                </div>
                        	</div>
                   		</div>
					</div>
           		</nav>              
<?php

///////////////////////////////////////////////////////////////////////// Aside

?>                
                <aside id="aside-container">
                    <div id="aside">
                        <div class="nano">
                            <div class="nano-content">
                                <ul class="nav nav-tabs nav-justified">
                                	<li class="active"><a href="#asd-tab-1" data-toggle="tab"><i class="ion-ios-home"></i></a></li>
                                    <li><a href="#asd-tab-2" data-toggle="tab"><i class="ion-ios-pricetag"></i></a></li>
									<li><a href="#asd-tab-3" data-toggle="tab"><i class="ion-ios-list"></i></a></li>
                                </ul>
                                <div class="tab-content">
<?php
 
///////////////////////////////////////////////////////////////////////////////////////// Progress
 
?>                               
                                	<div class="tab-pane fade in active" id="asd-tab-1">
                                        <p class="pad-all text-lg">DOCUMENTS &nbsp; <?php
										
///////////////////////////////////////////////////////////// Show Progress										
										
                                        	echo "<a href=\"javascript: ";
											echo "var doAuditA = $('#auditHistoryPanel').fadeOut('fast', function(){ ";
											echo "var doAuditB = $('#auditHistoryPanel').load('./data_history.php', '$ref=".time()."', function(){ ";
											echo "var doAuditC = $('#auditHistoryPanel').fadeIn('slow'); ";
											echo "}); ";
											echo "}); ";
											echo "\" >";
										?><i class="ion-loop text-right"></i></a><br /><span style="font-size:0.8em;">Be sure to reload this panel</span></p>
                                        <div id="auditHistoryPanel" class="pad-hor" style="text-align: left;">
											<?php
												$reload = "yes";
												include("./data_history.php");
											?>
                                        </div>
                                        <p>&nbsp;</p>
                                        <p>&nbsp;</p>
                                    </div>
<?php
 
///////////////////////////////////////////////////////////////////////////////////////// Vocabuaries
 
?>                                   
                                    <div class="tab-pane fade" id="asd-tab-2">
                                        <p class="pad-all text-lg">VOCABULARIES</p>
                                        <div class="pad-hor" style="text-align: left;">
                                            <div class="input-group-btn">
					                            <button data-toggle="dropdown" class="btn btn-block btn-dark dropdown-toggle" type="button">
					                                Select Key <i class="dropdown-caret"></i>
					                            </button>
					                            <ul class="dropdown-menu" style="width: 99%;">
												<?php
												
///////////////////////////////////////////////////////////// Get Vocabularies												
												
													$rdfPrefixes = array();
													$queryFU = "SELECT * FROM vocabularies ORDER BY reg_lexicalAlias ASC";
													$mysqli_resultFU = mysqli_query($mysqli_link, $queryFU);
													while($rowFU = mysqli_fetch_row($mysqli_resultFU)) {
														$rdfPrefixes[] = $rowFU[2];
													}
													foreach($rdfPrefixes as $rdfp) {
														echo "<li><a href=\"javascript: ";
														echo "var dataE = 'rdfPrefix=".$rdfp."&action=find'; ";
														echo "var doDivA = $('#keywordsList').fadeOut('fast', function(){ ";
														echo "var searchValA = $('#keywordsList').load('./data_keywords_desc.php', dataE, function(){ ";
														echo "var doDivAlsoA = $('#keywordsList').fadeIn('slow'); ";
														echo "}); ";
														echo "}); ";
														echo "\" style=\"color: #000000;\">".strtoupper($rdfp)."</a></li>";	
													}
												?>
					                            </ul>
					                        </div>
                                            <?php

///////////////////////////////////////////////////////////// Display Vocabularies
	
												echo "<div class=\"panel panel-bordered panel-primary mar-top\" ";
												echo "style=\"border: 1px solid #FFFFFF; background-color: #063D6B;\">";
    											echo "<div class=\"panel-body\">";
												echo "<div id=\"keywordsList\" class=\"text-light text-left mar-top mar-btm\" style=\"padding-bottom: 25px;\">";
												echo "To view a list of valid terms from a specific controlled vocabulary ";
												echo "for use with a label, please select a key ";
												echo "from the above drop-down menu. ";
												echo "</div>";
												echo "</div>";
												echo "</div>";
											?>
                                        </div>
                                        <p>&nbsp;</p>
                                        <p>&nbsp;</p>
                                    </div>
<?php
 
///////////////////////////////////////////////////////////////////////////////////////// Recently Viewed
 
?>							
									<div class="tab-pane fade" id="asd-tab-3">
                                        <p class="pad-all text-lg">RECENTLY VIEWED &nbsp; <?php
										
///////////////////////////////////////////////////////////// Show Progress										
										
                                        	echo "<a href=\"javascript: ";
											echo "var doAuditA = $('#auditViewedPanel').fadeOut('fast', function(){ ";
											echo "var doAuditB = $('#auditViewedPanel').load('./data_viewed.php', '$ref=".time()."', function(){ ";
											echo "var doAuditC = $('#auditViewedPanel').fadeIn('slow'); ";
											echo "}); ";
											echo "}); ";
											echo "\" >";
											?><i class="ion-loop text-right"></i></a><br /><span style="font-size:0.8em;">Be sure to reload this panel</span></p>
                                        <div id="auditViewedPanel" class="pad-hor" style="text-align: left;">
											<?php
												$reload = "yes";
												include("./data_viewed.php");
											?>
                                        </div>
                                        <p>&nbsp;</p>
                                        <p>&nbsp;</p>
                                    </div>						
<?php
 
///////////////////////////////////////////////////////////////////////////////////////// Finish Aside
 
?>                          	</div>
                            </div>
                        </div>
                    </div>
                </aside>
			</div>
<?php

///////////////////////////////////////////////////////////////////////// Footer

?>         
        </div>
<?php

///////////////////////////////////////////////////////////////////////// Viz Modal

?>        
        <div style="display:none"><div id="inlineViz">Nothing to see here ... yet!</div></div>
        <div style="display:none"><div id="inlineKeywordEditor" class="pad-all"></div></div>
        <div style="display:none"><div id="theDarkCloset"></div></div>
<?php

///////////////////////////////////////////////////////////////////////// Scripts

?>
		
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
		<!-- <script language="javascript" type="text/javascript" 
			src="./plugins/arbor/lib/arbor.js"></script> //-->
		<!-- <script language="javascript" type="text/javascript" 
			src="./plugins/arbor/lib/arbor-tween.js"></script> //-->
		<script language="javascript" type="text/javascript" >

/////////////////////////////////////////////////////////// OnLoad Start
		
//			var idv;
//			$(window).on('resize',function(){
//				clearTimeout(idv);
//				idv = setTimeout(doneResizing, 750);
//			});
//			
//			function doneResizing(){
//				location.reload();  
//			}
			
			$(window).on('load', function() {	
			
				<?php if(($view_collection == "")) { ?>		
				
/////////////////////////////////////////////////////////// Sort Table				

				if($(window).width() >= 1199){
					$('#dt-basic').dataTable( {
						"responsive": true,
						"sDom": '<"top">rt<"bottom"ilp><"clear">', 
						"width": "100%",
						"fixedHeader": true,
//						"order": [[ 0, "asc" ]],
						"ordering": false,
						"lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]], 
						"scrollY": "71vh",
						"scrollCollapse": false,
						"paging": false
					});	
				} else {
					$('#dt-basic').dataTable( {
						"responsive": true,
						"sDom": '<"top">rt<"bottom"ilp><"clear">', 
						"width": "100%",
						"fixedHeader": true,
//						"order": [[ 0, "asc" ]],
						"ordering": false,
						"lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]], 
						"scrollY": "65vh",
						"scrollCollapse": false,
						"paging": false
					});
				}
				
//				var doDivAZ = $(".dataTables_scrollHeadInner").css({"width":"97.65%"});	
				var doDivAZ = $(".dataTables_scrollHeadInner").css({"width":"99%"});
				var doDivAX = $(".dataTables_scrollHeadInner").css({"paddingLeft":"0px"});	
				var doDivAY = $(".dataTables_scrollHeadInner").css({"paddingRight":"0px"});
//				var doDivAX = $(".dataTables_scrollHeadInner th").eq(0).css({"width":"75%"});
//				var doDivAC = $(".dataTables_scrollHeadInner th").eq(1).css({"width":"6%"});
//				var doDivAV = $(".dataTables_scrollHeadInner th").eq(1).css({"width":"6%"});
//				var doDivAB = $(".dataTables_scrollHeadInner th").eq(1).css({"width":"6%"});
//				var doDivAN = $(".dataTables_scrollHeadInner th").eq(2).css({"width":"7%"});	
//				var doDivAM = $("#tableResultsContainer").css({"overflow-x":"hidden"});
				var doDivAS = $(".table").css({"width":"99%"});
				var doDivAV = $(".table thead").css({"width":"100%"});

				<?php } ?>

				var doDivF = $('#imgPlaceholder').fadeIn('slow');
			
/////////////////////////////////////////////////////////// JQuery fancybox popups
			
				$("#extLink").fancybox({
					type : 'image',
					autoScale : true,
					transitionIn : 'none',
					transitionOut : 'none',
					scrolling : 'yes',
					fitToView : true,
   					autoSize : true
				});	
				
				$("#extLinkB").fancybox({
					type : 'iframe',
					autoScale : true,
					transitionIn : 'none',
					transitionOut : 'none',
					scrolling : 'yes',
					fitToView : true,
					width: '90%', 
   					autoSize : true
				});			
	
/////////////////////////////////////////////////////////// OnLoad Finish
				
			});
		
		</script>
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