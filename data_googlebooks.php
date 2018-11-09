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
//  8-9 November 2018
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
        $bookSearch = $_GET["bookSearch"];
		$_GET = array();
		$_POST = array();
	}

///////////////////////////////////////////////////////////// Get Google Books Data
		
	$i = 0;
	$shuffle = array();
    if(($refresh != "y")) {
        echo "<div ";
        echo "id=\"googlebooksGallery\" ";
        echo "class=\"googlebooksGallery\" ";
        echo "style=\"";
        echo "padding: 20px; ";
        echo "text-align: center; ";
        echo "vertical-align: middle; ";
        echo "overflow-y: scroll; ";
        echo "overflow-x: hidden; ";
        echo "height: 90.8vh; ";
        echo "max-height: 90.8vh; ";
        echo "background-color: #c9d1d7; ";
        echo "width: 100%; ";
        echo "\">";

//////////////////////////////// Search Bar

        echo "<div class=\"input-group\" style=\"margin-bottom: 15px; width: 50%; \">";
        echo "<input id=\"bookSearch\" ";
        echo "type=\"text\" ";
        echo "class=\"form-control\" ";
        echo "name=\"bookSearch\" ";
        echo "value=\"\" ";
        echo "placeholder=\"Book Title / Author Search\" ";
        echo "style=\"\" ";
        echo "onclick=\"var clearThis = $('#bookSearch').val('');\" ";
        echo ">";
        echo "<span class=\"input-group-addon\" ";
        echo "style=\"background-color: #000000; color: #FFFFFF;\">";
        echo "<i class=\"glyphicon glyphicon-search\"></i>";
        echo "</span>";
        echo "</div>";

//////////////////////////////// Masonry wrapper
	
        echo "<div ";
        echo "id=\"bookSearchResults\" ";
        echo "style=\"position: relative; ";
        echo "-moz-column-count: 5; ";
        echo "-webkit-column-count: 5; ";
        echo "column-count: 5; ";
        echo "width: 100%; ";
        echo "\">";
    }

//////////////////////////////// Start DB Query
	
	$queryD = "SELECT ";
	$queryD .= "datasource_googlebooks.ID, ";
	$queryD .= "datasource_googlebooks.ID, ";
	$queryD .= "datasource_googlebooks.volumeInfo_title, ";
	$queryD .= "datasource_googlebooks.value_string, ";
	$queryD .= "datasource_googlebooks.volumeInfo_title, ";
	$queryD .= "datasource_googlebooks.volumeInfo_authors, ";
	$queryD .= "datasource_googlebooks.volumeInfo_description, ";
	$queryD .= "datasource_googlebooks.volumeInfo_thumbnail ";
	$queryD .= "FROM datasource_googlebooks ";
	$queryD .= "WHERE datasource_googlebooks.volumeInfo_thumbnail != \"\" ";
    if(($bookSearch != "")) {
        $queryD .= "AND (datasource_googlebooks.volumeInfo_title LIKE \"%".$bookSearch."%\" ";
        $queryD .= "OR datasource_googlebooks.volumeInfo_authors LIKE \"%".$bookSearch."%\") ";
    }
    if(($bookSearch == "")) {
        $queryD .= "ORDER BY RAND() ";
        $queryD .= "LIMIT 30";
    } else {
        $queryD .= "ORDER BY datasource_googlebooks.volumeInfo_title ASC ";
        $queryD .= "LIMIT 30";
    }
	$mysqli_resultD = mysqli_query($mysqli_link, $queryD);
	while($rowD = mysqli_fetch_row($mysqli_resultD)) { 
		$shuffleTemp = "";
		$img  = preg_replace("/ /","_","$rowD[3]");
		$img = "./img_googlebooks/".$img.".jpg";
			
//////////////////////////////// Open Div			
			
        $shuffleTemp = "<div ";
        $shuffleTemp .= "style=\"display: inline-block; padding-bottom: 15px;\" ";
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
        $shuffleTemp .= "alt=\"\" ";
        $shuffleTemp .= "title=\"";
        $shuffleTemp .= "\" ";
        $shuffleTemp .= "style=\"";
        $shuffleTemp .= "border: 3px solid #000000; ";
        $shuffleTemp .= "border-radius: 0px; ";
        $shuffleTemp .= "width: 100%; ";
        $shuffleTemp .= "\">";
			
//////////////////////////////// Close Href			
			
        $shuffleTemp .= "</a>";
			
//////////////////////////////// Close Div			
			
        $shuffleTemp .= "</div>";
        $shuffle[] = $shuffleTemp;
        $i++;
		
	}

//////////////////////////////// Build display

	$shuffle = array_unique($shuffle);
	for($x=0;$x<30;$x++) {
		echo $shuffle[$x];
	}
	
//////////////////////////////// Close masonry wrapper and content divs	
	
    if(($refresh != "y")) {
        echo "</div>\n";
        echo "</div>\n";

///////////////////////////////////////////////////////////// Load page scripts

?>
    <script language="javascript" type="text/javascript" >

        function delay(callback, ms) {
          var timer = 0;
          return function() {
            var context = this, args = arguments;
            clearTimeout(timer);
            timer = setTimeout(function () {
              callback.apply(context, args);
            }, ms || 0);
          };
        }
        
        $(document).ready(function() {

            $('[data-toggle="popover"]').tooltip({'trigger':'hover','placement': 'right'});

            $('#bookSearch').keyup(delay(function (event) {	
                var myLength = $("#bookSearch").val().length;
                if(myLength => 3) {
                    var searchBar = $('#bookSearch').val();	
                    var dataE = 'refresh=y&bookSearch='+searchBar;		
                    var doDiv = $('#bookSearchResults').fadeOut('fast', function(){
                        var searchVal = $('#bookSearchResults').load('./data_googlebooks.php',dataE, function(){
                            var doDivAlso = $('#bookSearchResults').fadeIn('slow');
                        });
                    });						
                }
            },650));
            $('#bookSearch').keypress(function(event) {
                if (event.keyCode == 13) {
                    event.preventDefault();
                }
            });        

        });

    </script>
<?php
        
    } else {
       
?>
    <script language="javascript" type="text/javascript" >
       
         $(document).ready(function() {
             $('[data-toggle="popover"]').tooltip({'trigger':'hover','placement': 'right'});
         });
        
    </script>
<?php
        
    }

///////////////////////////////////////////////////////////// Finish

	if(($IMGreload == "")) {
		include("./ar.dbdisconnect.php");
	}

?>