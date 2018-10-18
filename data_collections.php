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
//  6 January 2017
//  8-9 January 2017
//  12-13 January 2017
//  14 March 2017
//  3 April 2017
//  26 April 2017
//	11 May 2017
//	24-25 May 2017
//	22-23 June 2017
//	28 June 2017
//	5 February 2018
//	6-9 August 2018
// 	18 October 2018
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
		$_GET = array();
		$_POST = array();
	}

///////////////////////////////////////////////////////////// Start Table
	
?>
	Please click on a manuscript title below to display items in the selected collection.
	<table id="dt-basic" class="table table-striped table-hover" cellspacing="0" width="99%" style="table-layout: fixed; 
		width: 99% !important; margin-top: 0px; padding-top: 0px;">
  		<thead>
      		<tr>
        		<th style="width:85%; border-bottom: 8px solid #1b746c; text-align:left; font-size: 0.01em; padding-left: 8px;">&nbsp;</th>
               	<!-- <th class="mediaTable" style="width:15%; border-bottom: 8px solid #1b746c; text-align:right; font-size: 0.8em;">M.</th> //-->
               	<!-- <th class="mediaTable" style="width:15%; border-bottom: 8px solid #1b746c; text-align:right; font-size: 0.8em;">V.</th> //-->
              	<!-- <th class="mediaTable mediaTableB" style="width:15%; border-bottom: 8px solid #1b746c; text-align:right; font-size: 0.8em;">N.</th> //-->
               	<!-- <th style="width:15%; border-bottom: 8px solid #1b746c; text-align:right; font-size: 0.8em;">Yr.</th> //-->
        	</tr>
       	</thead>
    	<tbody>
<?php                                                
	
///////////////////////////////////////////////////////////// List Collections Query

	$queryD = "SELECT * FROM collections ";
//	$queryD .= "WHERE skos_orderedCollection ";
//	$queryD .= "LIKE \"%MS%\" ";
	$queryD .= "ORDER BY skos_collection ASC";		
	$mysqli_resultD = mysqli_query($mysqli_link, $queryD);
	while($rowD = mysqli_fetch_row($mysqli_resultD)) { 

/////////////////////////// Start Record Vars		
		
		$docs = "?";
		$ocr_docs = "000";
		$lock_docs = "000";
		$docs_acronym = $rowD[11];
		$tooltip = "";
		$tempAcr = "";
		$dc_identifier = $rowD[2];
		$rowD[7] = preg_replace("/ML MSS /i","","$rowD[7]");

/////////////////////////// Get Number of Docs - OCT 2018 Commenting Out
//		
//		$queryX = "SELECT COUNT(*) FROM items WHERE collections_dc_identifier = \"$dc_identifier\" ";
//		$mysqli_resultX = mysqli_query($mysqli_link, $queryX);
//		while($rowX = mysqli_fetch_row($mysqli_resultX)) { 
//			$docs = $rowX[0];
//		}
//	
/////////////////////////// Archive Suffix Modifier START		
		
		if(preg_match("/MS/i","$rowD[7]")) {
			$rowD[7] = preg_replace("/MS /i","","$rowD[7]");
			$rowHeader = "<span style=\"font-weight: 400; font-size: 0.9em; color: #888888; \">W & T / $docs_acronym</span><br />";
			$rowD[6] = "<span style=\"color: #9C2929;\">".$rowD[6]."</span>";
			$tempAcr = "W & T / ";
		} else {
			$rowHeader = "<span style=\"font-weight: 400; font-size: 0.9em; color: #888888; \">A & R / $docs_acronym</span><br />";
			$rowD[6] = "<span style=\"color: #9C2929;\">".$rowD[6]."</span>";
			$tempAcr = "A & R / ";
		}
		
/////////////////////////// Get Acronym for Collection
//		
//		$queryDXi = "SELECT dc_title FROM items WHERE ";
//		$queryDXi .= "collections_dc_identifier = \"$dc_identifier\" ";
//		$queryDXi .= "ORDER BY dc_title ASC LIMIT 1 ";
//		$mysqli_resultX = mysqli_query($mysqli_link, $queryDXi);
//		while($rowX = mysqli_fetch_row($mysqli_resultX)) { 
//			$temp_acronym = $rowX[0];
//			$temp_acronyms = explode(":",$temp_acronym);
//			$docs_acronym = $temp_acronyms[0];
//		}
//		
/////////////////////////// Detail OCR + Locked Docs		
		
		$queryDXi = "SELECT COUNT(*) FROM items WHERE ";
		$queryDXi .= "dc_description != \"\" AND ";
		$queryDXi .= "collections_dc_identifier = \"$dc_identifier\" ";
		$mysqli_resultX = mysqli_query($mysqli_link, $queryDXi);
		while($rowX = mysqli_fetch_row($mysqli_resultX)) { 
			$ocr_docs = $rowX[0];
		}
		
		$queryDXi = "SELECT COUNT(*) FROM items WHERE ";
		$queryDXi .= "dct_accessRights = \"restricted\" AND ";
		$queryDXi .= "collections_dc_identifier = \"$dc_identifier\" ";
		$mysqli_resultX = mysqli_query($mysqli_link, $queryDXi);
		while($rowX = mysqli_fetch_row($mysqli_resultX)) { 
			$lock_docs = $rowX[0];
		}
		
		if(($ocr_docs == "0")) { 
			$ocr_docs = "000"; 
		}
		if(($lock_docs == "0")) { 
			$lock_docs = "000"; 
		}
		if(($ocr_docs > "0") AND ($ocr_docs != "000") AND ($ocr_docs < 10)) { 
			$ocr_docs = "00".$ocr_docs; 
		}
		if(($ocr_docs > "9") AND ($ocr_docs != "000") AND ($ocr_docs < 100)) { 
			$ocr_docs = "0".$ocr_docs; 
		}		
		if(($lock_docs > "0") AND ($lock_docs != "000") AND ($lock_docs < 10)) { 
			$lock_docs = "00".$lock_docs; 
		}
		if(($lock_docs > "9") AND ($lock_docs != "000") AND ($lock_docs < 100)) { 
			$lock_docs = "0".$lock_docs; 
		}
		$tooltip = $tempAcr.$docs_acronym."\n".$ocr_docs." Documents OCR\n".$lock_docs." Documents Locked";
		
/////////////////////////// Archive Suffix Modifier FINISH		
		
		echo "<tr>";
		echo "<td width=\"100%\" style=\"border-bottom: 0px solid #768697; border-left: 0px solid #768697; ";
		echo "text-align:left; color:#000080; font-size: 0.9em; word-break: break-word; \">";
		echo "$rowHeader";
		echo "<a ";
		echo "data-toggle=\"tooltip\" title=\"".$tooltip."\" ";
		echo "href=\"javascript: ";
		echo "var dataE = 'collections_dc_identifier=".$dc_identifier."';	";		
        echo "var doDiv = $('#tableResultsContainer').fadeOut('fast', function(){ ";
        echo "var searchVal = ";
        echo "$('#tableResultsContainer').load('./data_items.php',dataE, function(){ ";
        echo "var doDivAlso = ";
        echo "$('#tableResultsContainer').fadeIn('slow'); ";
        echo "}); ";
        echo "}); ";
		echo "\" style=\"font-weight: 700; text-decoration:none; text-align: justify; \" target=\"_self\">";
		echo "$rowD[6]";
		echo "</a>";
		echo "<br />";
		echo "<span style=\"font-weight: 400; font-size: 1.0em; color: #111111; \">";
		if(($rowD[3] != "")) {
			echo "$rowD[3]";
		}
		if(($rowD[5] != "")) {
			echo ", $rowD[5]";
		}
		echo "<br />MSS $rowD[7]/$rowD[8]<br />";
		if(($rowD[9] == $rowD[10])){
			echo "<strong>$rowD[9]</strong>";
		} else {
			echo "<strong>$rowD[9]-$rowD[10]</strong>";
		}
		echo "</span></td>";
		
/////////////////////////// Manuscript Location Details		
		
		echo "</td>";
		
/////////////////////////// OCT 2018 Commenting Out		
//		
//		echo "<td class=\"mediaTable\" style=\"border-bottom: 0px solid #768697; ";
//		echo "text-align:right; font-size: 0.8em;\">$rowD[7]</td>";
//		echo "<td nowrap class=\"mediaTable\" style=\"border-bottom: 0px solid #768697; ";
//		echo "text-align:right; font-size: 0.8em;\">$rowD[8]</td>";
//		echo "<td nowrap class=\"mediaTable mediaTableB\" style=\"border-bottom: 0px solid #768697; ";
//		echo "text-align:right; font-size: 0.8em;\">$docs</td>";
//		echo "<td nowrap style=\"border-bottom: 0px solid #768697; ";
//		echo "text-align:right; font-size: 0.8em; \" nowrap>$rowD[9]<br />-$rowD[10]</td>";
//
//
/////////////////////////// OCT 2018 Commenting Out
		
		echo "</tr>";
		
	}
	
///////////////////////////////////////////////////////////// Close Table	

?>
		</tbody>
	</table>
<?php

///////////////////////////////////////////////////////////// Finish

	if(($reload == "")) {

?>
		<script language="javascript" type="text/javascript" >

/////////////////////////////////////////////////////////// Sort Table
			
			$(document).ready(function(e) {

				$('[data-toggle="tooltip"]').tooltip(); 
				if($(window).width() >= 1199){
					$('#dt-basic').dataTable( {
						"responsive": true,
						"sDom": '<"top">rt<"bottom"ilp><"clear">', 
						"width": "100%",
						"fixedHeader": true,
//						"order": [[ 0, "asc" ]],
						"ordering": false,
						"lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]], 
						"scrollY": "71vh",
						"scrollCollapse": false,
						"paging": false
					});	
				} else {
					$('#dt-basic').dataTable( {
						"responsive": true,
						"sDom": '<"top">rt<"bottom"ilp><"clear">', 
						"width": "100%",
						"fixedHeader": true,
//						"order": [[ 0, "asc" ]],
						"ordering": false,
						"lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]], 
						"scrollY": "65vh",
						"scrollCollapse": false,
						"paging": false
					});
				}
				
//				var doDivAZ = $(".dataTables_scrollHeadInner").css({"width":"97.65%"});	
				var doDivAZ = $(".dataTables_scrollHeadInner").css({"width":"99%"});
				var doDivAX = $(".dataTables_scrollHeadInner").css({"paddingLeft":"0px"});
				var doDivAY = $(".dataTables_scrollHeadInner").css({"paddingRight":"0px"});
//				var doDivAX = $(".dataTables_scrollHeadInner th").eq(0).css({"width":"75%"});
//				var doDivAC = $(".dataTables_scrollHeadInner th").eq(1).css({"width":"6%"});
//				var doDivAV = $(".dataTables_scrollHeadInner th").eq(1).css({"width":"6%"});
//				var doDivAB = $(".dataTables_scrollHeadInner th").eq(1).css({"width":"6%"});
//				var doDivAN = $(".dataTables_scrollHeadInner th").eq(2).css({"width":"7%"});	
//				var doDivAM = $("#tableResultsContainer").css({"overflow-x":"hidden"});
				var doDivAS = $(".table").css({"width":"99%"});
				var doDivAV = $(".table thead").css({"width":"100%"});
				
			});
		
		</script>
<?php		
		
		include("./ar.dbdisconnect.php");
	} else {
?>
		<script language="javascript" type="text/javascript" >
			
			$(document).ready(function(e) {
				$('[data-toggle="tooltip"]').tooltip();
			});
			
		</script>
<?php			
	}

?>