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
//  14 March 2017
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
		$rdfPrefix = $_GET["rdfPrefix"];
		$vocabFound = "n";
		$human_term = "";
		$vocab = "";
		$vocabs = "";
		$_GET = array();
		$_POST = array();
	}
	
///////////////////////////////////////////////////////////// Get Items Details

	if(($rdfPrefix != "")) {
		$queryD = "SELECT * FROM vocabularies WHERE reg_lexicalAlias = \"$rdfPrefix\" ";
		$mysqli_resultD = mysqli_query($mysqli_link, $queryD);
		while($rowD = mysqli_fetch_row($mysqli_resultD)) { 
			$human_term = $rowD[3];
			$vocab = $rowD[4];
			$vocabFound = "y";
		}	
	}
	
///////////////////////////////////////////////////////////// Display Items Details If Found

	if(($vocabFound == "y")) {
		echo "<p><strong>$human_term</strong><br />&nbsp;</p>";
		if(preg_match("/\|/i","$vocab")) {
			$vocabs = explode("|","$vocab");	
			foreach($vocabs as $v) {
				echo ucwords($v)."<br />";	
			}
		} else {
			echo ucwords($vocab)."<br />";		
		}

///////////////////////////////////////////////////////////// Display If Not Found
		
	} else {
		echo "No controlled vocabulary is currently available but it may be added at a later date. ";
		echo "Please check with your academic technologist or librarian to have the appropriate ";
		echo "information added to this database, or to discuss other options.";	
	}
	
/////////////////////////////////////////////////////////// Finish

	if(($reload == "")) {
		include("./ar.dbdisconnect.php");
	}	

?>