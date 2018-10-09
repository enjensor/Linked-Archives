<?php

/////////////////////////////////////////////////////////// Source
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
//  5 January 2017
//
//
/////////////////////////////////////////////////////////// Prevent Direct Access

	if(!defined('MyConstInclude')) {
   		die('Direct access not permitted');
	}

/////////////////////////////////////////////////////////// Main Code

   	$mysqli_link = mysqli_connect("$localhost", "$username", "$password") or 
		die ("<p><b>$localhost</b> could not connect to the database with username.");
   	mysqli_select_db($mysqli_link, "$database") or 
		die ("<p>I was unable to select the database.");
	
?>
