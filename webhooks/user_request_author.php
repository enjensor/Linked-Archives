<?php

/////////////////////////////////////////////////////////// Source
//
//
//	DialogFlow Webhook : Archiver Project
//	Library, Western Sydney University
//  October 2018
//
//	Who:	Dr Jason Ensor
//	Email: 	jasondensor@gmail.com
//	Mobile: + 61 (0)419 674 770
//
//
/////////////////////////////////////////////////////////// Turn On for Debug
//
//	define('MyConstInclude', TRUE);
//	include("./ar.config.php");
//	include("./ar.dbconnect.php");
//	include('./webhook.php');
//
/////////////////////////////////////////////////////////// Get Params

	$authorValue = $wh->get_parameter('authors');
	$yearValue = $wh->get_parameter('date-period');

/////////////////////////////////////////////////////////// Query

	$count = 0;
	$queryA = "SELECT COUNT(*) ";
	$queryA .= "FROM items ";
	$queryA .= "LEFT JOIN annotations ON items.dc_identifier = annotations.items_dc_identifier ";
	$queryA .= "WHERE annotations.value_string = \"$authorValue\" ";
	$queryA .= "AND annotations.rdfs_label = \"person\" ";
	$queryA .= "AND items.dc_created LIKE \"$yearValue%\" ";
	$queryA .= "ORDER BY items.dc_created ASC, items.dc_title ASC";
	$mysqli_resultA = mysqli_query($mysqli_link, $queryA);
	while($rowA = mysqli_fetch_row($mysqli_resultA)) {
		$count = $rowA[0];
	}

	$dc_identifier = "";
	if(($count >0)){
		$queryA = "SELECT dc_identifier ";
		$queryA .= "FROM items ";
		$queryA .= "LEFT JOIN annotations ON items.dc_identifier = annotations.items_dc_identifier ";
		$queryA .= "WHERE annotations.value_string = \"$authorValue\" ";
		$queryA .= "AND annotations.rdfs_label = \"person\" ";
		$queryA .= "AND items.dc_created LIKE \"$yearValue%\" ";
		$queryA .= "ORDER BY rand() ";
		$queryA .= "LIMIT 1";
		$mysqli_resultA = mysqli_query($mysqli_link, $queryA);
		while($rowA = mysqli_fetch_row($mysqli_resultA)) {
			$dc_identifier = $rowA[0];
		}
	}

/////////////////////////////////////////////////////////// Create Image

	$image = "";
	if(($dc_identifier !="") && ($count > 0)){
	//	$live_img = "https://linkedarchives.com/metadata/data_download.php?";
	//	$live_img .= "dc_identifier=".$dc_identifier."&type=jpg";
		$live_img = "https://linkedarchives.com/webhooks/assets/linked_archives_banner.jpg";
		$image = $wh->build_image(''.$live_img.'', ''.$authorValue.'', 600, 400);
	}

/////////////////////////////////////////////////////////// Create Link

	if(($count > 0)){
		$button = $wh->build_button('more info', 'https://linkedarchives.com/metadata/');
	}

/////////////////////////////////////////////////////////// Prepare Response

	if(($count > 0)){
		$wh->build_basicCard('I have found '.$count.' documents on '.$authorValue.' in the year '.$yearValue.' and have created a web link for you to visit', 'Search Results', '', ''.$response.'', $image, $button, 'DEFAULT');
	} else {
		$startYear = "";
		$finishYear = "";
		$queryA = "SELECT items.dc_created ";
		$queryA .= "FROM items ";
		$queryA .= "LEFT JOIN annotations ON items.dc_identifier = annotations.items_dc_identifier ";
		$queryA .= "WHERE annotations.value_string = \"$authorValue\" ";
		$queryA .= "AND annotations.rdfs_label = \"person\" ";
		$queryA .= "AND items.dc_created != \"\" ";
		$queryA .= "ORDER BY items.dc_created ASC, items.dc_title ASC LIMIT 1";
		$mysqli_resultA = mysqli_query($mysqli_link, $queryA);
		while($rowA = mysqli_fetch_row($mysqli_resultA)) {
			$startYear = $rowA[0];
		}
		$queryA = "SELECT items.dc_created ";
		$queryA .= "FROM items ";
		$queryA .= "LEFT JOIN annotations ON items.dc_identifier = annotations.items_dc_identifier ";
		$queryA .= "WHERE annotations.value_string = \"$authorValue\" ";
		$queryA .= "AND annotations.rdfs_label = \"person\" ";
		$queryA .= "AND items.dc_created != \"\" ";
		$queryA .= "ORDER BY items.dc_created DESC, items.dc_title DESC LIMIT 1";
		$mysqli_resultA = mysqli_query($mysqli_link, $queryA);
		while($rowA = mysqli_fetch_row($mysqli_resultA)) {
			$finishYear = $rowA[0];
		}
		$textToSpeech = "I am sorry but I could not find any record on $authorValue for the year $yearValue. Checking the collection, the earliest date that $authorValue appears is on $startYear and the latest date is $finishYear. Perhaps you would like to rephrase your search question again with a different year from within this range.";
		$displayText = $textToSpeech;
		$wh->build_simpleResponse($textToSpeech, $displayText);
	}

/////////////////////////////////////////////////////////// Close
//
//	include("./ar.dbdisconnect.php");
//
/////////////////////////////////////////////////////////// Done

?>