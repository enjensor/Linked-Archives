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
//	10-13 August 2018
//	20 August 2018
//
//
/////////////////////////////////////////////////////////// Clean post and get	
	
	if(($reload == "")) {
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
		$_GET = array();
		$_POST = array();
	}

/////////////////////////////////////////////////////////// Start Table

	echo "<table class=\"table\" width=\"99%\" ";
	echo "style=\"border: 0px solid #ffffff;\" border=\"0\">";
	echo "<thead>";
	echo "<tr>";
	echo "<th nowrap style=\"color: #FFFFFF; background-color: #000000;\">#</th>";
	echo "<th style=\"width: 100%; border-left: 1px solid #ffffff; ";
	echo "color: #FFFFFF; background-color: #000000;\"\">ITEM</th>";
	echo "</tr>";
	echo "</thead>";
    echo "<tbody>";
	
/////////////////////////////////////////////////////////// Viewed

	$col_UUID = "";
	$col_bf_heldBy = "";
	$col_bf_subLocation = "";
	$col_bf_physicalLocation = "";
	$col_skos_Collection = "";	
	$col_skos_OrderedCollection = "";	
	$col_skos_member = "";	
	$col_disco_startDate = "";	
	$col_disco_endDate = "";
	$dc_creator = "";
	$marc_addressee = "";
	$dc_created = "";
	$me = $_SESSION["contributor"];
	$queryA = "SELECT * FROM ";
	$queryA .= "audit_viewed ";
	$queryA .= "WHERE ";
	$queryA .= "dct_contributor = \"$me\" ";
	$queryA .= "ORDER BY ID DESC ";
	$queryA .= "LIMIT 6";
	$mysqli_resultA = mysqli_query($mysqli_link, $queryA);
	while($rowA = mysqli_fetch_row($mysqli_resultA)) {
		$queryD = "SELECT * FROM ";
		$queryD .= "collections ";
		$queryD .= "WHERE ";
		$queryD .= "dc_identifier = \"".$rowA[4]."\" ";
		$mysqli_resultD = mysqli_query($mysqli_link, $queryD);
		while($rowD = mysqli_fetch_row($mysqli_resultD)) { 
			$col_UUID = $rowD[2];
			$col_bf_heldBy = $rowD[3];
			$col_bf_subLocation = $rowD[4];
			$col_bf_physicalLocation = $rowD[5];
			$col_skos_Collection = $rowD[6];	
			$col_skos_OrderedCollection = $rowD[7];	
			$col_skos_member = $rowD[8];	
			$col_disco_startDate = $rowD[9];	
			$col_disco_endDate = $rowD[10];
		}
		$queryD = "SELECT dc_creator, marc_addressee, dc_created FROM ";
		$queryD .= "items ";
		$queryD .= "WHERE ";
		$queryD .= "dc_identifier = \"".$rowA[2]."\" ";
		$mysqli_resultD = mysqli_query($mysqli_link, $queryD);
		while($rowD = mysqli_fetch_row($mysqli_resultD)) { 
			$dc_creator = $rowD[0];
			if(($rowD[1] != "")) {
				$marc_addressee = " to ".$rowD[1];
			}
			if(($rowD[2] != "")){
				$dc_created = ", ".$rowD[2];
			}
		}
		echo "<tr>";
		echo "<td nowrap style=\"color: #FFFFFF; background-color: #D04949;\">".$rowA[0]."</td>";
		echo "<td style=\"color: #FFFFFF; width: 100%; border-left: 1px solid #ffffff; ";
		echo "background-color: #D04949;\">";
		echo "<strong>";
		
////////////////////////////////// Load Metadata Panel	
		
		echo "<a href=\"javascript: ";
		echo "var dataF = 'dc_identifier=".$rowA[2]."&reload=';	";
		echo "var doDivM = $('#titleTags').fadeOut('fast', function(){ ";
        echo "var doDivN = $('#titleTags').load('./data_meta.php',dataF, function(){ ";
        echo "var doDivO = $('#titleTags').fadeIn('slow'); ";
        echo "}); ";
        echo "}); ";
		
///////////////////////////////////// Load Image		
		
		echo "var dataG = 'dc_identifier=".$rowA[2]."';	";		
        echo "var doDivK = $('#doc_detail').fadeOut('fast', function(){ ";
        echo "var doDivP = $('#doc_detail').load('./data_doc.php',dataG, function(){ ";
        echo "var doDivW = $('#doc_detail').fadeIn('slow'); ";
        echo "}); ";
        echo "}); ";		
		
////////////////////////////////// Load Items Panel

		echo "var dataE = 'collections_dc_identifier=".$rowA[4]."';	";		
        echo "var doDiv = $('#tableResultsContainer').fadeOut('fast', function(){ ";
        echo "var searchVal = $('#tableResultsContainer').load('./data_items.php',dataE, function(){ ";
        echo "var doDivAlso = $('#tableResultsContainer').fadeIn('slow'); ";
        echo "}); ";
        echo "}); ";
		
////////////////////////////////// List Details		
		
		echo "\" ";
		echo "style=\"color: #FFFFFF;\">";
		echo $rowA[6];
		echo "</strong><br /><span style=\"font-size: 0.9em;\">";
		echo "</a>";
		if(($dc_creator != "")) { echo $dc_creator.$marc_addressee.$dc_created.". "; }
		if(($col_skos_Collection != "")) { echo $col_skos_Collection.". "; }
		if(($col_skos_OrderedCollection != "")) { echo $col_skos_OrderedCollection.". "; }
		if(($col_bf_subLocation != "")) { echo $col_bf_subLocation.". "; }
		if(($col_bf_physicalLocation != "")) { echo $col_bf_physicalLocation.". "; }	
		echo"</span></td>";
		echo "</tr>";
	}

/////////////////////////////////////////////////////////// Close Table
	
	echo "</tbody>";
	echo "</table>";

/////////////////////////////////////////////////////////// Finish

	if(($reload == "")) {
		include("./ar.dbdisconnect.php");
	}

?>