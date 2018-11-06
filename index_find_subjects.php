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
//  3-4 April 2017
//	1-2 May 2017
//	30 May 2017
//	8-15 August 2018
//  24 October 2018
//
//
/////////////////////////////////////////////////////////// Clean post and get

	if(($reload == "")) {
		error_reporting(0);
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
		$action = $_GET["action"];
		$searchPhrase = $_GET["searchPhrase"];
		$searchTerm = $_GET["searchTerm"];
		$_GET = array();
		$_POST = array();
	}	
	
///////////////////////////////////////////////////////////// Start Table
	
	echo "<input ";
//	echo "type=\"text\" ";
	echo "type=\"hidden\" ";
	echo "style=\"font-size: 1.0em; border: 1px solid #000000; height: 2.5em; \" ";
	echo "class=\"form-control\" ";
	echo "id=\"searchTags\" ";
	echo "placeholder=\"Type to search\">";
//	echo "<br />";
	if(($searchTerm == "") && ($action != "breadcrumb")) {
		echo "<p style=\"padding: 4px;\"></p>";
	}
	echo "<table id=\"mentionsList\" class=\"table table-hover\" ";
	echo "style=\"border: 4px solid #1b746c; background-color: #f8f9fa;\" ";
	echo "cellspacing=\"0\" width=\"100%\">";
	echo "<tbody>";
	
///////////////////////////////////////////////////////////// Build Queries	
	
	if(($searchTerm == "") && ($action != "breadcrumb")) {
		
//////////////////////////////// No Search Term		
		
		$queryA = "SELECT ";
		$queryA .= "DISTINCT(annotations.value_string), ";
		$queryA .= "COUNT(annotations.value_string) AS TheCount, ";
		$queryA .= "annotations.reg_uri, ";
		$queryA .= "annotations.rdfs_label ";
		$queryA .= "FROM ";
		$queryA .= "annotations ";
		$queryA .= "GROUP BY ";
		$queryA .= "annotations.value_string, ";
		$queryA .= "annotations.reg_uri, ";
		$queryA .= "annotations.rdfs_label ";
		$queryA .= "ORDER BY ";
//		$queryA .= "RAND() ";
		$queryA .= "TheCount DESC, ";
		$queryA .= "annotations.value_string ASC ";
		$queryA .= "LIMIT 14";
		
		echo "<tr>";
		echo "<td ";
		echo "style=\"border-bottom: 1px solid #cccccc; ";
		echo "text-align: left; ";
		echo "vertical-align: top; ";
		echo "valign: top; ";
		echo "color: #FFFFFF; ";
		echo "background-color: #1B746C; ";
		echo "font-size: 0.9em; ";
		echo "\" colspan=\"4\">";
		echo "<strong>";
		echo "Example Mentions";
		echo "</strong>";
		echo "</td>";
		
//////////////////////////////// Search Terms Check	
		
	} else {
		$i = "";
		$findmeAgain = "";
		$doComplexSearch = "";
		$searches = "";
		$IDs = array();
		if(($searchTerm != "")) {
			$searchComplete = $searchTerm."|".$searchPhrase;
		} else {
			$searchComplete = $searchPhrase;
		}
		$searchComplete = trim($searchComplete);
		if(preg_match("/\|/i","$searchComplete")) {
			$searches = explode("|","$searchComplete");
			$searches = array_filter($searches);
			$doComplexSearch = "y";
		}

//////////////////////////////// One Search Term
		
		if(($doComplexSearch != "y")) {
			$queryA = "SELECT DISTINCT(annotations.dc_references) ";
			$queryA .= "FROM annotations ";
			$queryA .= "WHERE annotations.value_string = \"$searchTerm\" ";
			$queryA .= "GROUP BY annotations.dc_references ";
			$queryA .= "ORDER BY annotations.dc_references ASC";
			$mysqli_resultA = mysqli_query($mysqli_link, $queryA);
			while($rowA = mysqli_fetch_row($mysqli_resultA)) {
				$IDs[] = $rowA[0];
			}
			$having = (count($IDs));
			foreach($IDs as $w) {
				$i++;
				if(($i == $having)) {
					$findmeAgain .= "annotations.dc_references = \"$w\"";
				} else {
					$findmeAgain .= "annotations.dc_references = \"$w\" OR ";
				}
			}
			$queryA = "SELECT DISTINCT(annotations.value_string), ";
			$queryA .= "COUNT(annotations.value_string) AS TheCount, ";
			$queryA .= "annotations.reg_uri, ";
			$queryA .= "annotations.rdfs_label ";
			$queryA .= "FROM annotations ";
			$queryA .= "WHERE ($findmeAgain) ";
			$queryA .= "GROUP BY ";
			$queryA .= "annotations.value_string, ";
			$queryA .= "annotations.reg_uri, ";
			$queryA .= "annotations.rdfs_label ";
			$queryA .= "ORDER BY ";
			$queryA .= "TheCount DESC, ";
			$queryA .= "annotations.value_string ASC";
		} else {

//////////////////////////////// Multiple Search Terms

			$headerTags = "";
			$having = (count($searches));
			foreach($searches as $w) {
				$i++;
				if(($i == $having)) {
					$dataSearchD = "";
					foreach($searches as $s) {
						if(($s != "$w")) {
							$dataSearchD .= "$s"."|";
						}
					}
					$headerTags .= "<a style=\"color: #007700; \" href=\"javascript: ";
					if(($dataSearchD != "")) {
						$headerTags .= "var dataSearchD = 'searchPhrase=".$dataSearchD."&action=breadcrumb';	 ";
					} else {
						$headerTags .= "var dataSearchD = ''; ";
					}
					$headerTags .= "var doDivSearchE = $('#titleTags').fadeOut('fast', function(){ ";
					$headerTags .= "var doDivSearchF = $('#titleTags').load('./index_find_subjects.php',dataSearchD, function(){ ";
					$headerTags .= "var doDivSearchG = $('#titleTags').fadeIn('slow'); ";
					$headerTags .= "}); ";
					$headerTags .= "}); ";
					if(($dataSearchD != "")) {
						$headerTags .= "var dataE = 'action=find&search=&searchPhrase=".$dataSearchD."';	";
						$headerTags .= "var doDiv = $('#tableResultsContainer').fadeOut('fast', function(){ ";
						$headerTags .= "var searchVal = $('#tableResultsContainer').load('./data_subjects.php',dataE, function(){ ";
						$headerTags .= "var doDivAlso = $('#tableResultsContainer').fadeIn('slow'); ";
						$headerTags .= "}); ";
						$headerTags .= "}); ";	
					}					
					$headerTags .= "\">";
					$headerTags .= ucwords($w);
					$headerTags .= "</a>";
					$findmeAgain .= "annotations.value_string = \"$w\"";
					$headerTags .= "";
					$findmeAgain .= "";
				} else {
					$dataSearchD = "";
					foreach($searches as $s) {
						if(($s != "$w")) {
							$dataSearchD .= "$s"."|";
						}
					}
					$headerTags .= "<a style=\"color: #007700; \" href=\"javascript: ";
					if(($dataSearchD != "")) {
						$headerTags .= "var dataSearchD = 'searchPhrase=".$dataSearchD."&action=breadcrumb';	 ";
					} else {
						$headerTags .= "var dataSearchD = ''; ";
					}
					$headerTags .= "var doDivSearchE = $('#titleTags').fadeOut('fast', function(){ ";
					$headerTags .= "var doDivSearchF = $('#titleTags').load('./index_find_subjects.php',dataSearchD, function(){ ";
					$headerTags .= "var doDivSearchG = $('#titleTags').fadeIn('slow'); ";
					$headerTags .= "}); ";
					$headerTags .= "}); ";
					if(($dataSearchD != "")) {
						$headerTags .= "var dataE = 'action=find&search=&searchPhrase=".$dataSearchD."';	";
						$headerTags .= "var doDiv = $('#tableResultsContainer').fadeOut('fast', function(){ ";
						$headerTags .= "var searchVal = $('#tableResultsContainer').load('./data_subjects.php',dataE, function(){ ";
						$headerTags .= "var doDivAlso = $('#tableResultsContainer').fadeIn('slow'); ";
						$headerTags .= "}); ";
						$headerTags .= "}); ";	
					}
					$headerTags .= "\">";
					$headerTags .= ucwords($w);
					$headerTags .= "</a>";
					$findmeAgain .= "annotations.value_string = \"$w\"";
					$headerTags .= " + ";
					$findmeAgain .= " OR ";
				}
			}
			echo "<p style=\"padding: 5px;\">".$headerTags."</p>";       
			$queryA = "SELECT ";
			$queryA .= "annotations.dc_references, ";
			$queryA .= "items.dc_identifier, ";
//			$queryA .= "COUNT(annotations.dc_references) AS goal ";
            $queryA .= "COUNT(DISTINCT annotations.value_string) AS goal ";
			$queryA .= "FROM annotations ";
			$queryA .= "LEFT JOIN items ";
			$queryA .= "ON annotations.dc_references = items.dc_identifier ";
			$queryA .= "WHERE ($findmeAgain) ";
			$queryA .= "GROUP BY annotations.dc_references ";
			$queryA .= "HAVING goal = $having ";
			$queryA .= "ORDER BY items.dc_title ASC ";
			$i = "";
			$findmeAgain = "";
			$doComplexSearch = "";
			$searches = "";
			$IDs = array();
			$mysqli_resultA = mysqli_query($mysqli_link, $queryA);
			while($rowA = mysqli_fetch_row($mysqli_resultA)) {
				$IDs[] = $rowA[0];
			}
			$having = (count($IDs));
			foreach($IDs as $w) {
				$i++;
				if(($i == $having)) {
					$findmeAgain .= "annotations.dc_references = \"$w\"";
				} else {
					$findmeAgain .= "annotations.dc_references = \"$w\" OR ";
				}
			}
			$queryA = "SELECT DISTINCT(annotations.value_string), ";
			$queryA .= "COUNT(annotations.value_string) AS TheCount, ";
			$queryA .= "annotations.reg_uri, ";
			$queryA .= "annotations.rdfs_label ";
			$queryA .= "FROM annotations ";
			$queryA .= "WHERE ($findmeAgain) ";
			$queryA .= "GROUP BY ";
			$queryA .= "annotations.value_string, ";
			$queryA .= "annotations.reg_uri, ";
			$queryA .= "annotations.rdfs_label ";
			$queryA .= "ORDER BY ";
			$queryA .= "TheCount DESC, ";
			$queryA .= "annotations.value_string ASC";           

//////////////////////////////// Finish
			
		}
	}
	
///////////////////////////////////////////////////////////// Get Results	
	
	$mysqli_resultA = mysqli_query($mysqli_link, $queryA);
	while($rowA = mysqli_fetch_row($mysqli_resultA)) {
		
//////////////////////////////// Number		
		
		echo "<tr>";
		echo "<td ";
		echo "style=\"border-bottom: 1px solid #cccccc; ";
		echo "text-align: right; ";
		echo "vertical-align: top; ";
		echo "valign: top; ";
		echo "color: #000000; ";
		echo "font-size: 0.9em; ";
		echo "\">";
		echo "<strong>";
		echo "$rowA[1]";
		echo "</strong>";
		echo "</td>";
			
//////////////////////////////// Download Data
			
		echo "<td ";
		echo "style=\"border-bottom: 1px solid #cccccc; ";
		echo "text-align: left; ";
		echo "vertical-align: top; ";
		echo "valign: top; ";
		echo "color: #000000; ";
		echo "font-size: 0.9em; ";
		echo "\">";	
		$temp_VS = preg_replace("/\&/","&amp;","$rowA[0]");
		echo "<a href=\"./data_download_mentions_csv.php?";
		echo "value_phrase=".$searchComplete;
		echo "&value_string=".$temp_VS;
		echo "&type=other";
		echo "&format=csv";
		echo "\">";
		echo "<i class=\"ti-download\" style=\"font-weight: 900; color: #1B746C;\"></i>";
		echo "</a>";
		echo "</td>";
	
//////////////////////////////// Mention Label	
			
		echo "<td ";
		echo "style=\"border-bottom: 1px solid #cccccc; ";
		echo "text-align: left; ";
		echo "vertical-align: top; ";
		echo "valign: top; ";
		echo "color: #000000; ";
		echo "font-size: 0.9em; ";
		echo "\">";
		$doLink = "";
		$searches = explode("|","$searchComplete");
		if((ucwords($rowA[0]) == ucwords($searchTerm))) {
			$doLink = "n";	
		}
		foreach($searches as $l) {
			if(($l != "")) {
				if(ucwords($rowA[0]) == ucwords($l)) {
					$doLink = "n";	
				}
			}
		}
		if(($doLink != "n")) {
			echo "<a style=\"color: #007700; \" href=\"javascript: ";
			echo "var dataE = 'action=find&search=".$rowA[0]."&searchPhrase=".$searchComplete."';	";		
			echo "var doDiv = $('#tableResultsContainer').fadeOut('fast', function(){ ";
			echo "var searchVal = $('#tableResultsContainer').load('./data_subjects.php',dataE, function(){ ";
			echo "var doDivAlso = $('#tableResultsContainer').fadeIn('slow'); ";
			echo "}); ";
			echo "}); ";
			echo "var dataSearchD = 'searchTerm=".$rowA[0]."&searchPhrase=".$searchComplete."';	";
			echo "var doDivSearchE = $('#titleTags').fadeOut('fast', function(){ ";
			echo "var doDivSearchF = $('#titleTags').load('./index_find_subjects.php',dataSearchD, function(){ ";
			echo "var doDivSearchG = $('#titleTags').fadeIn('slow'); ";
			echo "}); ";
			echo "}); ";
			echo "\">";
			echo "$rowA[0]";
			echo "</a>";
		} else {
			echo "$rowA[0]";
		}
		echo "<br /><span style=\"color: #9C2929;\">&nbsp;&nbsp;&nbsp;&nbsp;".$rowA[2]." : ".$rowA[3]."</span>";
		
//////////////////////////////// Related Concepts Edit
		
		echo "<td ";
		echo "style=\"border-bottom: 1px solid #cccccc; ";
		echo "text-align: left; ";
		echo "vertical-align: top; ";
		echo "valign: top; ";
		echo "color: #000000; ";
		echo "font-size: 0.9em; ";
		echo "\" nowrap>";	
		if(($_SESSION["userlogin"] != "") && ($_SESSION["userpassword"] != "")) {
			
//////////////////////////////// Edit Mention Text
			
			$qq++;
			echo "<a id=\"reviewMention_".$qq."\" href=\"";
			echo "./data_meta_review.php";
			echo "?reload=";
			echo "&action=EDIT";
			echo "&annotations_reg_uri=".$rowA[2];
			echo "&annotations_rdfs_label=".$rowA[3];
			echo "&annotations_value_string=".$rowA[0];
			echo "\" style=\"color: #000080; text-decoration: none;\">";
			echo "<i class=\"ti-pencil-alt\" style=\"font-weight: 900; color: #1B746C;\"></i>";
			echo "</a>&nbsp;&nbsp;&nbsp;&nbsp;";

//////////////////////////////// Edit Related Concepts			
			
			echo "<a id=\"relatedMention_".$qq."\" href=\"";
			echo "./data_meta_related.php";
			echo "?reload=";
			echo "&action=EDIT";
			echo "&annotations_reg_uri=".$rowA[2];
			echo "&annotations_rdfs_label=".$rowA[3];
			echo "&annotations_value_string=".$rowA[0];
			echo "\" style=\"color: #000080; text-decoration: none;\">";
			echo "<i class=\"ti-sharethis\" style=\"font-weight: 900; color: #1B746C;\"></i>";
			echo "</a>&nbsp;";
			
//////////////////////////////// Close			
			
		} else {
			echo "&nbsp;";
		}
		echo "</td>";		
		
//////////////////////////////// Fin		
		
		echo "</td>";	
		echo "</tr>";
	}
	
///////////////////////////////////////////////////////////// Finish Table
	
	echo "</tbody>";
	echo "</table>";

///////////////////////////////////////////////////////////// Scripts

?>
    <script language="javascript" type="text/javascript" >
	
		$(document).ready(function() {
			
/////////////////////////////////////////////////////////// JQuery Popups
			
			<?php for($ww=1;$ww<$qq+1;$ww++) { ?>
			
			$("#reviewMention_<?php echo $ww; ?>").fancybox({
				type : 'iframe', 
				autoScale : false,
				transitionIn : 'none',
				transitionOut : 'none',
				scrolling : 'yes',
				fitToView : false,
				autoSize : false,
				width: '780px', 
				height: '690px'
			});	
			
			$("#relatedMention_<?php echo $ww; ?>").fancybox({
				type : 'iframe', 
				autoScale : false,
				transitionIn : 'none',
				transitionOut : 'none',
				scrolling : 'yes',
				fitToView : false,
				autoSize : false,
				width: '880px', 
				height: '770px'
			});
			
			<?php } ?>			

/////////////////////////////////////////////////////////// JQuery Search Filter Timer			
			
			var $rows = $('#mentionsList tr');
			var timer = 0;
			$('#searchTags').bind('keyup input',function() {	
				if (timer) {
        			clearTimeout(timer);
    			}
				timer = setTimeout(function(){
					var myLength = $("#searchTags").val().length;
					if(myLength > 2) {
						var val = '^(?=.*' + $.trim($('#searchTags').val()).split(/\s+/).join('\\b)(?=.*') + ').*$', reg = RegExp(val, 'i'), text;
						$rows.show().filter(function() {
							text = $(this).text().replace(/\s+/g, ' ');
							return !reg.test(text);
						}).hide();
					} else {
						$rows.show();	
					};
				},500);
			});
		});
		
	</script>
<?php	
	
///////////////////////////////////////////////////////////// Finish

	if(($reload == "")) {
		include("./ar.dbdisconnect.php");
	}
?>