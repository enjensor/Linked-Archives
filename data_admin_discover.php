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
//  8 November 2018
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
	$_GET = array();
	$_POST = array();
	$contributor = "contrib41T71U4BZZ";
	$msg = "";

/////////////////////////////////////////////////////////// Main Routines

    if(($_SESSION["administrator"] == "yes")) {
        
///////////////////////////////// Discover Tags        
        
        $rzr = 0;
        $tags = array();
        $descriptions = array();
        $matches = array();
        $queryD = "SELECT DISTINCT(value_string), reg_uri, rdfs_label ";
        $queryD .= "FROM annotations ";
        $queryD .= "WHERE (value_string != \"F.L.\" AND value_string != \"R.W.\") ";
        $queryD .= "AND rdfs_label != \"type\" ";
        $queryD .= "AND rdfs_label != \"relatesToDocument\" ";
        $queryD .= "AND rdfs_label != \"letter\" ";
        $queryD .= "AND rdfs_label != \"EmotionCategory\" ";
        $queryD .= "AND rdfs_label != \"publishingRole\" ";
        $queryD .= "GROUP BY value_string, reg_uri, rdfs_label ";
        $queryD .= "ORDER BY value_string, reg_uri, rdfs_label";
        $mysqli_resultD = mysqli_query($mysqli_link, $queryD);
        while($rowD = mysqli_fetch_row($mysqli_resultD)) {
            $tags[] = "$rowD[0]|$rowD[1]|$rowD[2]";
        }
        $queryD = "SELECT items.dc_description, items.dc_identifier ";
        $queryD .= "FROM items ";
        $queryD .= "WHERE items.dc_description != \"\" ";
        $queryD .= "AND items.dc_description IS NOT NULL ";
        $queryD .= "AND CHAR_LENGTH(items.dc_description) > 25 ";
        $queryD .= "ORDER BY items.dc_description ASC; ";
        $mysqli_resultD = mysqli_query($mysqli_link, $queryD);
        while($rowD = mysqli_fetch_row($mysqli_resultD)) {
            $descriptions[] = "$rowD[0]*[|]*$rowD[1]";
        }
        $azr = 0;
        $bzr = 0;
        foreach($tags as $t) {
            $azr++;
            $words = explode("|","$t");
            foreach($descriptions as $d) {
                $records = explode("*[|]*","$d");
                if(preg_match("/$words[0]/i","$records[0]")) {
                    $found = "n";
                    $queryD = "SELECT * ";
                    $queryD .= "FROM annotations ";
                    $queryD .= "WHERE items_dc_identifier = \"$records[1]\" ";
                    $queryD .= "AND value_string = \"$words[0]\" ";
                    $queryD .= "AND reg_uri = \"$words[1]\" ";
                    $queryD .= "AND rdfs_label = \"$words[2]\" ";
                    $queryD .= "LIMIT 1";  
                    $mysqli_resultD = mysqli_query($mysqli_link, $queryD);
                    while($rowD = mysqli_fetch_row($mysqli_resultD)) {
                        $found = "y";
                        $bzr++;
                    }
                    if(($found == "n")) {
                        $matches[] = $t;
                        $rzr++;
                        $iana_UUID = guidv4();	
                        $characters = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
                        $iana = "";
                        for ($i = 0; $i < 12; $i++) {
                            $iana .= $characters[mt_rand(0, 36)];
                        }
                        $iana = time().$iana;
                        $queryE = "INSERT INTO annotations VALUES (";
                        $queryE .= "\"0\", ";
                        $queryE .= "\"$iana_UUID\", ";
                        $queryE .= "\"".time()."_".$iana."\", ";
                        $queryE .= "\"$records[1]\", ";
                        $queryE .= "\"$records[1]\", ";
                        $queryE .= "\"$words[1]\", ";
                        $queryE .= "\"$words[2]\", ";
                        $queryE .= "\"$words[0]\", ";
                        $queryE .= "\"\", ";
                        $queryE .= "\"OCR\", ";
                        $queryE .= "\"$contributor\", ";
                        $queryE .= "NOW() ";
                        $queryE .= "); ";
                        $mysqli_resultE = mysqli_query($mysqli_link, $queryE);
                    }
                }
            }
        }
        $tagMsg = "<li>$bzr existing matched tags</li><li>$rzr new matches.</li>";
        
///////////////////////////////// Display Result         
        
        echo "<div class=\"alert alert-warning\" role=\"alert\">";
        echo "<strong>Routine Completed</strong><br /><br /><ul>$tagMsg</ul>";
        echo "</div>";
    }

/////////////////////////////////////////////////////////// Finish        
      
    include("./ar.dbdisconnect.php");

/////////////////////////////////////////////////////////// Close

?>
