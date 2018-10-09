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
//	4-8 June 2017
//	22 June 2017
//
//
/////////////////////////////////////////////////////////// Clean post and get	
	
	if(($IMGreload == "")) {
		define('MyConstInclude', TRUE);
		$MerdUser = session_id();
		if(empty($MerdUser)) { session_start(); }
		$SIDmerd = session_id();
		header("Cache-Control: no-cache");
		header("Pragma: no-cache");
		header("Content-type: text/html;charset=UTF-8");
		mb_internal_encoding("UTF-8");
		include("./ar.config.php");
		include("./ar.dbconnect.php");
		include("./index_functions.php");
		if (!mysqli_set_charset($mysqli_link, "utf8")) {
			echo "PROBLEM WITH CHARSET!";
			die;
		}
		$refresh = $_GET["refresh"];
		$_GET = array();
		$_POST = array();
	}

///////////////////////////////////////////////////////////// Get Google Books Data
		
	$i = 0;
	$shuffle = array();
	echo "<div ";
	echo "id=\"googlebooksGallery\" ";
	echo "class=\"googlebooksGallery\" ";
	echo "style=\"";
	echo "padding: 7px; ";
	echo "text-align: center; ";
	echo "vertical-align: middle; ";
	echo "overflow-y: scroll; ";
	echo "overflow-x: hidden; ";
	echo "height: 90.5vh; ";
	echo "max-height: 90.5vh; ";
	echo "background-color: #222222; ";
	echo "width: 100%; ";
	echo "\">";

//////////////////////////////// Masonry wrapper
	
	echo "<div ";
	echo "style=\"position: relative; ";
	echo "-moz-column-count: 5; ";
	echo "-webkit-column-count: 5; ";
	echo "column-count: 5; ";
	echo "width: 100%; ";
	echo "\">";

//////////////////////////////// Start DB Query
	
	$queryD = "SELECT ";
	$queryD .= "g1.ID, ";
	$queryD .= "g2.ID, ";
	$queryD .= "g1.volumeInfo_title, ";
	$queryD .= "g2.value_string, ";
	$queryD .= "g1.volumeInfo_title, ";
	$queryD .= "g1.volumeInfo_authors, ";
	$queryD .= "g1.volumeInfo_description, ";
	$queryD .= "g1.volumeInfo_thumbnail ";
	$queryD .= "FROM datasource_googlebooks g1 ";
	$queryD .= "JOIN datasource_googlebooks g2 ON g1.ID = g2.ID ";
	$queryD .= "AND g1.volumeInfo_title LIKE g2.value_string ";
	$queryD .= "AND g1.volumeInfo_thumbnail != \"\" ";
	$queryD .= "ORDER BY RAND() LIMIT 70";
	$mysqli_resultD = mysqli_query($mysqli_link, $queryD);
	while($rowD = mysqli_fetch_row($mysqli_resultD)) { 
		$shuffleTemp = "";
		$img  = preg_replace("/ /","_","$rowD[2]");
		$img = "./img_googlebooks/".$img.".jpg";
		if(file_exists($img)) {
			
//////////////////////////////// Open Div			
			
			$shuffleTemp = "<div ";
			$shuffleTemp .= "style=\"display: inline-block; padding: 7px;\" ";
			$shuffleTemp .= ">";
			
//////////////////////////////// Open Href			
			
			$shuffleTemp .= "<a href=\"javascript: ";
			$shuffleTemp .= "var dataE = 'action=find&search=".$rowD[3]."&searchPhrase=';	";		
			$shuffleTemp .= "var doDiv = $('#tableResultsContainer').fadeOut('fast', function(){ ";
			$shuffleTemp .= "var searchVal = $('#tableResultsContainer').load('./data_subjects.php',dataE, function(){ ";
			$shuffleTemp .= "var doDivAlso = $('#tableResultsContainer').fadeIn('slow'); ";
			$shuffleTemp .= "}); ";
			$shuffleTemp .= "}); ";
			$shuffleTemp .= "var dataSearchD = 'searchTerm=".$rowD[3]."&searchPhrase=';	";
			$shuffleTemp .= "var doDivSearchE = $('#titleTags').fadeOut('fast', function(){ ";
			$shuffleTemp .= "var doDivSearchF = $('#titleTags').load('./index_find_subjects.php',dataSearchD, function(){ ";
			$shuffleTemp .= "var doDivSearchG = $('#titleTags').fadeIn('slow'); ";
			$shuffleTemp .= "}); ";
			$shuffleTemp .= "}); ";
			$shuffleTemp .= "\" ";
			$shuffleTemp .= "data-toggle=\"popover\" ";
			$shuffleTemp .= "title=\"";
			
//////////////////////////////// Popover content start			
			
			$shuffleTemp .= "<span style='font-size:1.2em;'>";
			$shuffleTemp .= "$rowD[3]";
			$shuffleTemp .= "</span>";
			$shuffleTemp .= "<br />";
			$shuffleTemp .= "<span style='font-size:0.9em;'><strong>$rowD[5]</strong>";
			if(($rowD[6] != "")) {
				$rowD[6] = preg_replace("/\"/","","$rowD[6]");
				$rowD[6] = preg_replace("/\'/","","$rowD[6]");
				if (strlen($rowD[6]) > 500) {
   					$rowD[6]= substr($rowD[6], 0, 500) . ' ...';
				}
				$shuffleTemp .= "<br />";
				$shuffleTemp .= "$rowD[6]";
			}
			$shuffleTemp .= "</span>";
			
//////////////////////////////// Popover content end			
			
			$shuffleTemp .= "\" ";
			$shuffleTemp .= "data-content=\"\" ";
			$shuffleTemp .= "data-html=\"true\" ";
			$shuffleTemp .= "class=\"red-tooltip\" ";
			$shuffleTemp .= ">";
			
//////////////////////////////// Image			
			
			$shuffleTemp .= "<img src=\"".$img."\" ";
//			$shuffleTemp .= "width=\"120\" ";
//			$shuffleTemp .= "height=\"200\" ";
			$shuffleTemp .= "alt=\"\" ";
			$shuffleTemp .= "title=\"";
			$shuffleTemp .= "\" ";
			$shuffleTemp .= "style=\"";
			$shuffleTemp .= "border: 3px solid #FFFFFF; ";
			$shuffleTemp .= "border-radius: 5px; ";
			$shuffleTemp .= "width: 100%; ";
			$shuffleTemp .= "\">";
			
//////////////////////////////// Close Href			
			
			$shuffleTemp .= "</a>";
			
//////////////////////////////// Close Div			
			
			$shuffleTemp .= "</div>";
			$shuffle[] = $shuffleTemp;
			$i++;
		}
	}
	$shuffle = array_unique($shuffle);
	for($x=0;$x<50;$x++) {
		echo $shuffle[$x];
	}
	
//////////////////////////////// Close masonry wrapper and content divs	
	
	echo "</div>\n";
	echo "</div>\n";

///////////////////////////////////////////////////////////// Load page scripts

?>
<script language="javascript" type="text/javascript" >

	$(document).ready(function() {
		$('[data-toggle="popover"]').tooltip({'trigger':'hover','placement': 'right'});
	});

</script>
<?php

///////////////////////////////////////////////////////////// Finish

	if(($IMGreload == "")) {
		include("./ar.dbdisconnect.php");
	}

?>