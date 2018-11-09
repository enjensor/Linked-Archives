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
        
///////////////////////////////// Data Match With Google        
        
        $g = 0;
		$h = 0;
		$queryD = "SELECT DISTINCT(value_string) ";
		$queryD .= "FROM annotations ";
		$queryD .= "WHERE rdfs_label = \"book\" ";
		$queryD .= "GROUP BY value_string ";
		$queryD .= "ORDER BY value_string ASC";
		$mysqli_resultD = mysqli_query($mysqli_link, $queryD);
		while($rowD = mysqli_fetch_row($mysqli_resultD)) { 
			$h++;
			$found = "n";
			$queryView = "SELECT * ";
			$queryView .= "FROM datasource_googlebooks ";
			$queryView .= "WHERE value_string = \"rowD[0]\"; ";
			$mysqli_resultView = mysqli_query($mysqli_link, $queryView);
			while($rowView = mysqli_fetch_row($mysqli_resultView)) { 
				$found = "y";
			}
			if(($found == "n")) {
				$url = "https://www.googleapis.com/books/v1/volumes?q=";
				$url .= $rowD[0];
				$url .= "&maxResults=1";
				$url .= "&printType=books";
				$url .= "&fields=kind,items(id,etag,selfLink,volumeInfo(title,authors,description,imageLinks(smallThumbnail,thumbnail)))";
				$url = preg_replace("/ /","+","$url");
				$json = file_get_contents($url);
				$book_data = json_decode($json);
				$google_kind = $book_data->kind;
				$google_id = $book_data->items[0]->id;
				$google_etag = $book_data->items[0]->etag;
				$google_selfLink = $book_data->items[0]->selfLink;
				$volumeInfo_title = $book_data->items[0]->volumeInfo->title;
				$volumeInfo_authors = $book_data->items[0]->volumeInfo->authors[0];
				$volumeInfo_description = $book_data->items[0]->volumeInfo->description;
				$volumeInfo_smallThumbnail = $book_data->items[0]->volumeInfo->imageLinks->smallThumbnail;
				$volumeInfo_thumbnail = $book_data->items[0]->volumeInfo->imageLinks->thumbnail;
				$volumeInfo_description = preg_replace("/\"/","'","$volumeInfo_description");
				$value_string = $rowD[0];
				$newQuery = "INSERT INTO datasource_googlebooks ";
				$newQuery .= "VALUES (0, ";
				$newQuery .= "\"$google_kind\", ";
				$newQuery .= "\"$google_id\", ";
				$newQuery .= "\"$google_etag\", ";
				$newQuery .= "\"$google_selfLink\", ";
				$newQuery .= "\"$volumeInfo_title\", ";
				$newQuery .= "\"$volumeInfo_authors\", ";
				$newQuery .= "\"$volumeInfo_description\", ";
				$newQuery .= "\"$volumeInfo_smallThumbnail\", ";
				$newQuery .= "\"$volumeInfo_thumbnail\", ";
				$newQuery .= "\"$value_string\", ";
				$newQuery .= "\"\");";
				if(($google_id != "")) {
					$mysqli_resultNew = mysqli_query($mysqli_link, $newQuery);
					$g++;
				}
			}
		}
		$tagMsg = "<li>Found $g googlebooks</li><li>Modified $h records</li>";        
 
///////////////////////////////// Cache Google Book Covers        
        
        $i = 0;
		$queryD = "SELECT volumeInfo_thumbnail, ";
		$queryD .= "value_string ";
		$queryD .= "FROM datasource_googlebooks ";
		$queryD .= "WHERE volumeInfo_thumbnail != \"\" ";
		$queryD .= "ORDER BY volumeInfo_thumbnail ASC";
		$mysqli_resultD = mysqli_query($mysqli_link, $queryD);
		while($rowD = mysqli_fetch_row($mysqli_resultD)) {
			$url = $rowD[0];
			$name = preg_replace("/ /","_","$rowD[1]");
			$save = "./img_googlebooks/".$name.".jpg";
			if(!file_exists($save)) {
				$content = file_get_contents($url);
				$fp = fopen("$save", "w");
				fwrite($fp, $content);
				fclose($fp);
				$i++;
			}
		}
		$tagMsg .= "<li>$i Images cached</li>";

///////////////////////////////// Display Result        
        
        echo "<div class=\"alert alert-danger\" role=\"alert\">";
        echo "<strong>Routine Completed</strong><br /><br /><ul>$tagMsg</ul>";
        echo "</div>";
    }

/////////////////////////////////////////////////////////// Finish        
      
    include("./ar.dbdisconnect.php");

/////////////////////////////////////////////////////////// Close

?>
