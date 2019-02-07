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
/////////////////////////////////////////////////////////// Start

	define('MyConstInclude', TRUE);
	include("./ar.config.php");
	include("./ar.dbconnect.php");
	include('./webhook.php');

/////////////////////////////////////////////////////////// Get Intent

	$wh = new Webhook('archiver-bot');
	$intent = $wh->get_intent();

/////////////////////////////////////////////////////////// Swith Intents

	if(($intent == "user_request_author")){
		
		include("user_request_author.php");
		
	} else if (($intent == "user_request_suggestions")){
		
		include("user_request_suggestions.php");
		
	} else if($intent == ""){
		
		$textToSpeech = "I am not clear what you have asked or said. ";
		$textToSpeech .= "Can you please try again? ";
		$displayText = $textToSpeech;
		$wh->build_simpleResponse($textToSpeech, $displayText);
	}

/////////////////////////////////////////////////////////// Send Response

	$wh->respond();

/////////////////////////////////////////////////////////// Close

	include("./ar.dbdisconnect.php");

?>