<?php

/////////////////////////////////////////////////////////// Source
//
//
//	DialogFlow Webhook, Archiver Project
//	Library, Western Sydney University
//
//	FOR ALL ENQUIRIES ABOUT CODE
//
//	Who:	Dr Jason Ensor
//	Email: 	j.ensor@westernsydney.edu.au | jasondensor@gmail.com
//	Mobile: 0419 674 770
//
//  VERSION 0.1
//  03 October 2018
//
//
/////////////////////////////////////////////////////////// Prevent Direct Access

	if(!defined('MyConstInclude')) {
   		die('Direct access not permitted');
	}

/////////////////////////////////////////////////////////// Main Code

   	$mysqli_link = mysqli_connect("$localhost", "$username", "$password") or 
		die ("<p><b>$localhost</b> could not connect to the database.");
   	mysqli_select_db($mysqli_link, "$database") or 
		die ("<p><b>$localhost</b> could not select the database");
	
?>