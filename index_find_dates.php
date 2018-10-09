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
//  3 April 2017
//	6 August 2018
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
	}
	
///////////////////////////////////////////////////////////// Set vars

	$action = $_GET["action"];
	$years = array();
	$months = array("01","02","03","04","05","06","07","08","09","10","11","12");
	$monthsN = array("Jan","Feb","March","April","May","June","July","Aug","Sept","Oct","Nov","Dec");
	$days = array("01","02","03","04","05","06","07","08","09","10","11","12","13","14","15","16","17","18","19","20","21","22","23","24","25","26","27","28","29","30","31");
	$dbQuery = "SELECT DISTINCT(dc_created) FROM items ORDER BY dc_created ASC";
	$mysqli_result = mysqli_query($mysqli_link, $dbQuery);
	while($row = mysqli_fetch_row($mysqli_result)) { 
		if(($row[0] != "")) {
			$temp = explode("-","$row[0]");
			$years[] = $temp[0];
		}
	}
	$years = array_unique($years);
	sort($years);
	
//////////////////////////// Years	
	
	if(($_GET["StartYear"] == "")) { $_GET["StartYear"] = $years[0]; }
	if(($_GET["StartMonth"] == "")) { $_GET["StartMonth"] = $months[0]; }
	if(($_GET["StartDay"] == "")) { $_GET["StartDay"] = $days[0]; }
	if(($_GET["FinYear"] == "")) { $_GET["FinYear"] = $years[3]; }
	if(($_GET["FinMonth"] == "")) { $_GET["FinMonth"] = $months[11]; }
	if(($_GET["FinDay"] == "")) { $_GET["FinDay"] = $days[30]; }
	if(($action == "")) {
		$action = "LIST";	
	}
	if(($action != "")) {
		$rangeA = $_GET["StartYear"]."-".$_GET["StartMonth"]."-".$_GET["StartDay"];
		$rangeB = $_GET["FinYear"]."-".$_GET["FinMonth"]."-".$_GET["FinDay"];
	}
	
//////////////////////////// Result pages	
	
	$n = 0;
	$m = 0;
	$T = 100;
	if((!$_GET["page"])) { $_GET["page"] = "1"; }
	$p = $_GET["page"];
	$u = $_GET["page"];
	$limit_end = ($p * $T);
	$limit_start = ($limit_end - $T);
	if(($limit_start == "")) { $limit_start = "0"; }
	if(($limit_start == "0")) { $limit_end = "99"; } 
	if(($limit_start == "0")) { $A = 1; $B = 100; } else { $A = ($limit_start +1); $B = $limit_end; }
	if(($limit_start != "0")) { $limit_end = ($limit_end - 1); }
	$p++;
	$pre = ($_GET["page"] - 1);
	$_GET["page"] = $p;	
		
///////////////////////////////////////////////////////////// Prepare sources

	$extra = "";
	$archives = array();
	$archivesB = array();
	$sourceX = array();
	$dbQuery = "SELECT * FROM collections ORDER BY skos_collection ASC, bibo_volume ASC, disco_startDate ASC";
	$mysqli_result = mysqli_query($mysqli_link, $dbQuery);
	while($row = mysqli_fetch_row($mysqli_result)) { 
		$rx = $row[2];
		$archives[$rx] = "$row[6], Vol $row[8]";
		if(($_GET["ExcludeList"]["$rx"] == "yes")) {
			$extra .= "items.collections_dc_identifier != \"$rx\" AND ";
		}
	}
	if(($extra)) {
		$eQuery = "(".$extra.")";
		$eQuery = str_replace(" AND )","","$eQuery");
		$eQuery = "AND $eQuery".")";
	}
	mysqli_free_result($mysqli_result);
	
///////////////////////////////////////////////////////////// Start Panel
	
	echo "<div class=\"panel\" style=\"background-color: #FFFFFF; border: 2px solid #1B4F74; padding-top: 12px; background-color: #dfdfdf;\">";
	echo "<div class=\"panel-body\">";
	
///////////////////////////////////////////////////////////// Search Start Date Form
	
    echo "<div class=\"col-lg-2 col-md-2 col-sm-2\" ";
	echo "style=\"";
	echo "padding: 5px; ";
	echo "text-align: center; ";
	echo "vertical-align: middle; ";
	echo "valign: middle; ";
	echo "color: #000000; ";
	echo "font-size: 0.9em; ";
	echo "height: 100%;";
	echo "\">";
	echo "<strong>Start</strong>";
	echo "</div>";
	
///////////////////////////////////////////////////////////// StartYear	
	
	echo "<div class=\"col-lg-4 col-md-4 col-sm-4\" ";
	echo "style=\"";
    echo "font-size: 0.9em; ";
    echo "padding-bottom: 4px; ";
	echo "padding-right: 4px; ";
	echo "padding-left: 4px; ";
	echo "padding-top: 0px; ";
    echo "\">";
	echo "<select ";
	echo "style=\"font-size: 1.0em; ";
	echo "font-weight; 900;\" ";
	echo "class=\"show-tick\" ";
	echo "data-size=\"12\" ";
	echo "data-width=\"100%\" ";
	echo "id=\"StartYear\" ";
	echo "name=\"StartYear\" ";
	echo ">";
	foreach($years as $y) {
		if(($_GET["StartYear"] == $y)) {
			echo "<option style=\"font-size: 0.9em; ";
			echo "\" ";
			echo "value=\"$y\" ";
			echo "selected>";
			echo "$y";
			echo "</option>\n";
		} else {
			echo "<option style=\"font-size: 0.9em; ";
			echo "\" ";
			echo "value=\"$y\">";
			echo "$y";
			echo "</option>\n";
		}
	}	
	echo "</select>\n";
	echo "</div>";
	
///////////////////////////////////////////////////////////// StartMonth

	echo "<div class=\"col-lg-3 col-md-3 col-sm-3\" ";
	echo "style=\"";
	echo "font-size: 0.9em; ";
	echo "padding-bottom: 4px; ";
	echo "padding-right: 4px; ";
	echo "padding-left: 0px; ";
	echo "padding-top: 0px; ";
	echo "\">";
	echo "<select ";
	echo "style=\"font-size: 1.0em; ";
	echo "font-weight; 900;\" ";
	echo "class=\"show-tick\" ";
	echo "data-size=\"12\" ";
	echo "data-width=\"100%\" ";
	echo "id=\"StartMonth\" ";
	echo "name=\"StartMonth\" ";
	echo ">";
	foreach($months as $y) {
		if(($_GET["StartMonth"] == $y)) {
			echo "<option style=\"font-size: 0.9em; ";
			echo "\" ";
			echo "value=\"$y\" ";
			echo "selected>";
			echo "$monthsN[$n]";
			echo "</option>\n";
		} else {
			echo "<option style=\"font-size: 0.9em; ";
			echo "\" ";
			echo "value=\"$y\">";
			echo "$monthsN[$n]";
			echo "</option>\n";
		}
		$n++;
	}	
	echo "</select>\n";
	echo "</div>";

///////////////////////////////////////////////////////////// StartDay	

	echo "<div class=\"col-lg-3 col-md-3 col-sm-3\" ";
	echo "style=\"";
	echo "font-size: 0.9em; ";
	echo "padding-bottom: 4px; ";
	echo "padding-left: 0px; ";
	echo "padding-right: 0px; ";
	echo "padding-top: 0px; ";
	echo "\">";
	echo "<select ";
	echo "style=\"font-size: 1.0em; ";
	echo "font-weight; 900;\" ";
	echo "class=\"show-tick\" ";
	echo "data-size=\"12\" ";
	echo "data-width=\"100%\" ";
	echo "id=\"StartDay\" ";
	echo "name=\"StartDay\" ";
	echo ">";
	foreach($days as $y) {
		if(($_GET["StartDay"] == $y)) {
			echo "<option style=\"font-size: 0.9em; ";
			echo "\" ";
			echo "value=\"$y\" ";
			echo "selected>";
			echo "$y";
			echo "</option>\n";
		} else {
			echo "<option style=\"font-size: 0.9em; ";
			echo "\" ";
			echo "value=\"$y\">";
			echo "$y";
			echo "</option>\n";
		}
	}	
	echo "</select>\n";
	echo "</div><br />";	
	
///////////////////////////////////////////////////////////// Search End Date Form
	
    echo "<div class=\"col-lg-2 col-md-2 col-sm-2\" ";
	echo "style=\"";
	echo "padding: 5px; ";
	echo "text-align: center; ";
	echo "vertical-align: middle; ";
	echo "valign: middle; ";
	echo "color: #000000; ";
	echo "font-size: 0.9em; ";
	echo "height: 100%;";
	echo "\">";
	echo "<strong>End</strong>";
	echo "</div>";
	
///////////////////////////////////////////////////////////// FinYear	
	
	echo "<div class=\"col-lg-4 col-md-4 col-sm-4\" ";
	echo "style=\"";
    echo "font-size: 0.9em; ";
    echo "padding-bottom: 4px; ";
	echo "padding-right: 4px; ";
	echo "padding-left: 4px; ";
	echo "padding-top: 0px; ";
    echo "\">";
	echo "<select ";
	echo "style=\"font-size: 0.9em; ";
	echo "font-weight; 900;\" ";
	echo "class=\"show-tick\" ";
	echo "data-size=\"12\" ";
	echo "data-width=\"100%\" ";
	echo "id=\"FinYear\" ";
	echo "name=\"FintYear\" ";
	echo ">";
	foreach($years as $y) {
		if(($_GET["FinYear"] == $y)) {
			echo "<option style=\"font-size: 0.9em; ";
			echo "\" ";
			echo "value=\"$y\" ";
			echo "selected>";
			echo "$y";
			echo "</option>\n";
		} else {
			echo "<option style=\"font-size: 0.9em; ";
			echo "\" ";
			echo "value=\"$y\">";
			echo "$y";
			echo "</option>\n";
		}
	}	
	echo "</select>\n";
	echo "</div>";
	
///////////////////////////////////////////////////////////// FinMonth

	echo "<div class=\"col-lg-3 col-md-3 col-sm-3\" ";
	echo "style=\"";
	echo "font-size: 0.9em; ";
	echo "padding-bottom: 4px; ";
	echo "padding-right: 4px; ";
	echo "padding-left: 0px; ";
	echo "padding-top: 0px; ";
	echo "\">";
	echo "<select ";
	echo "style=\"font-size: 0.9em; ";
	echo "font-weight; 900;\" ";
	echo "class=\"show-tick\" ";
	echo "data-size=\"12\" ";
	echo "data-width=\"100%\" ";
	echo "id=\"FinMonth\" ";
	echo "name=\"FinMonth\" ";
	echo ">";
	foreach($months as $y) {
		if(($_GET["FinMonth"] == $y)) {
			echo "<option style=\"font-size: 0.9em; ";
			echo "\" ";
			echo "value=\"$y\" ";
			echo "selected>";
			echo "$monthsN[$m]";
			echo "</option>\n";
		} else {
			echo "<option style=\"font-size: 0.9em; ";
			echo "\" ";
			echo "value=\"$y\">";
			echo "$monthsN[$m]";
			echo "</option>\n";
		}
		$m++;
	}	
	echo "</select>\n";
	echo "</div>";

///////////////////////////////////////////////////////////// FinDay	

	echo "<div class=\"col-lg-3 col-md-3 col-sm-3\" ";
	echo "style=\"";
	echo "font-size: 0.9em; ";
	echo "padding-bottom: 4px; ";
	echo "padding-right: 0px; ";
	echo "padding-left: 0px; ";
	echo "padding-top: 0px; ";
	echo "\">";
	echo "<select ";
	echo "style=\"font-size: 0.9em; ";
	echo "font-weight; 900;\" ";
	echo "class=\"show-tick\" ";
	echo "data-size=\"12\" ";
	echo "data-width=\"100%\" ";
	echo "id=\"FinDay\" ";
	echo "name=\"FinDay\" ";
	echo ">";
	foreach($days as $y) {
		if(($_GET["FinDay"] == $y)) {
			echo "<option style=\"font-size: 0.9em; ";
			echo "\" ";
			echo "value=\"$y\" ";
			echo "selected>";
			echo "$y";
			echo "</option>\n";
		} else {
			echo "<option style=\"font-size: 0.9em; ";
			echo "\" ";
			echo "value=\"$y\">";
			echo "$y";
			echo "</option>\n";
		}
	}	
	echo "</select>\n";
	echo "</div><br />";	
	
///////////////////////////////////////////////////////////// Exclude List
	
	if(($showExclude == "yes")) {
		echo "<div class=\"col-lg-2 col-md-2 col-sm-2\" ";
		echo "style=\"";
		echo "padding: 5px; ";
		echo "text-align: center; ";
		echo "vertical-align: middle; ";
		echo "valign: middle; ";
		echo "color: #000000; ";
		echo "font-size: 0.9em; ";
		echo "height: 100%;";
		echo "\">";
		echo "<strong>Exclude</strong>";
		echo "</div>";	
		echo "<div class=\"col-lg-10 col-md-10 col-sm-10\" ";
		echo "style=\"";
		echo "font-size: 0.9em; ";
		echo "padding-bottom: 4px; ";
		echo "padding-right: 0px; ";
		echo "padding-left: 0px; ";
		echo "padding-top: 0px; ";
		echo "margin: 0px; ";
		echo "\">";
		echo "<select multiple ";
		echo "size=\"6\" ";
		echo "style=\"font-size: 0.9em; ";
		echo "white-space: normal!important; ";
		echo "word-wrap: break-word!important; ";
		echo "white-space: -moz-pre-wrap!important; ";
		echo "white-space: pre-wrap!important; ";
		echo "font-weight; 900;\" ";
		echo "class=\"show-tick\" ";
		echo "data-size=\"12\" ";
		echo "data-width=\"100%\" ";
		echo "id=\"ExcludeList\" ";
		echo "name=\"ExcludeList\" ";
		echo ">";
		ksort($archives);
		foreach($archives as $excludeKey => $excludeValue) {
			if(($_GET["ExcludeList"]["$excludeKey"] != "")) {
				$ck = "selected";
			} else {
				$ck = "";
			}
			echo "<option style=\"font-size: 0.9em; ";
			echo "white-space: normal!important; ";
			echo "word-wrap: break-word!important; ";
			echo "white-space: -moz-pre-wrap!important; ";
			echo "white-space: pre-wrap!important; ";
			echo "\" ";
			echo "value=\"$excludeKey\" ";
			echo "$ck >";
			echo "$excludeValue";
			echo "</option>\n";
		}
		echo "</select>\n";
		echo "</div><br />";
	}
	
///////////////////////////////////////////////////////////// List or Group

	echo "<div class=\"col-lg-2 col-md-2 col-sm-2\" ";
	echo "style=\"";
	echo "padding: 5px; ";
	echo "text-align: center; ";
	echo "vertical-align: middle; ";
	echo "valign: middle; ";
	echo "color: #000000; ";
	echo "font-size: 0.9em; ";
	echo "height: 100%;";
	echo "\">";
	echo "&nbsp;";
	echo "</div>";
	
	echo "<div class=\"col-lg-10 col-md-10 col-sm-10\" ";
	echo "style=\"";
	echo "font-size: 0.9em; ";
	echo "padding-bottom: 4px; ";
	echo "padding-right: 2px; ";
	echo "padding-left: 0px; ";
	echo "padding-top: 0px; ";
	echo "\">";
	echo "<a id=\"listDates\" name=\"listDates\" class=\"btn btn-primary\" style=\"color: #FFFFFF; width: 100%;\">";
	echo "<strong>List</strong>";
	echo "</a>";
	echo "</div>";
	
//	echo "<div class=\"col-lg-5 col-md-5 col-sm-5\" ";
//	echo "style=\"";
//	echo "font-size: 0.9em; ";
//	echo "padding-bottom: 4px; ";
//	echo "padding-right: 4px; ";
//	echo "padding-left: 2px; ";
//	echo "padding-top: 0px; ";
//	echo "\">";
//	echo "<a href=\"#\" class=\"btn btn-info\" style=\"color: #FFFFFF; width: 100%;\">";
//	echo "<strong>Group</strong>";
//	echo "</a>";
//	echo "</div>";
//	
///////////////////////////////////////////////////////////// Close Panel	
	
	echo "</div>";	
	echo "</div>";
	
///////////////////////////////////////////////////////////// List Results

	if(($action == "LIST") or ($action == "GROUP")) {
		echo "<table id=\"dt-basic\" class=\"table table-striped table-hover\" cellspacing=\"0\" width=\"100%\">";
		echo "<thead>";
		echo "<tr>";
		echo "<th width=\"5%\" style=\"width: 5%; border-bottom: 8px solid #1b746c; text-align: right; font-size: 0.9em; color: #000000;\">#</th>";
		echo "<th width=\"40%\" style=\"width: 40%; border-bottom: 8px solid #1b746c; text-align: left; font-size: 0.9em; color: #000000;\">File</th>";
		echo "<th width=\"45%\" style=\"width: 45%; border-bottom: 8px solid #1b746c; text-align: left; font-size: 0.9em; color: #000000;\">Archive</th>";
		echo "<th width=\"10%\" style=\"width: 10%; border-bottom: 8px solid #1b746c; text-align: right; font-size: 0.9em; color: #000000;\">Date</th>";
		echo "</tr>";
		echo "</thead>";
		echo "<tbody>";
		$x = 1;
		$dbQuery = "SELECT * ";
		$dbQuery .= "FROM items ";
		$dbQuery .= "WHERE (dc_created = \"$rangeA\" ";
		$dbQuery .= "OR dc_created > \"$rangeA\") ";
		$dbQuery .= "AND (dc_created < \"$rangeB\" ";
		$dbQuery .= "OR dc_created = \"$rangeB\") ";
		$dbQuery .= "AND  dc_created != \"9999-99-99\" ";
		$dbQuery .= "AND dc_created != \"\" ";
		$dbQuery .= "AND dc_created IS NOT NULL ";
		$dbQuery .= "ORDER BY dc_created ASC, ";
		$dbQuery .= "ID ASC, ";
		$dbQuery .= "dc_title ASC ";
		$mysqli_result = mysqli_query($mysqli_link, $dbQuery);
		while($rowD = mysqli_fetch_row($mysqli_result)) { 
		
///////////////////////////////////// Counter		
		
			echo "<tr>";
			echo "<td style=\"border-bottom: 1px solid #768697; border-left: 0px solid #768697; text-align:right; color:#000080; font-size: 0.8em;\">";
			echo "$x";
			echo "</td>";
			echo "<td style=\"border-bottom: 1px solid #768697; text-align:left; font-size: 0.8em;\">";
			echo "<a href=\"javascript: ";
		
///////////////////////////////////// Load Image		
			
			echo "var dataE = 'dc_identifier=".$rowD[2]."';	";		
			echo "var doDivK = $('#doc_detail').fadeOut('fast', function(){ ";
			echo "var doDivP = $('#doc_detail').load('./data_doc.php',dataE, function(){ ";
			echo "var doDivW = $('#doc_detail').fadeIn('slow'); ";
			echo "}); ";
			echo "}); ";
			
///////////////////////////////////// Load Metadata	Editor Panel
			
			echo "var dataF = 'dc_identifier=".$rowD[2]."&reload=';	";
			echo "var doDivM = $('#titleTags').fadeOut('fast', function(){ ";
			echo "var doDivN = $('#titleTags').load('./data_meta.php',dataF, function(){ ";
			echo "var doDivO = $('#titleTags').fadeIn('slow'); ";
			echo "}); ";
			echo "}); ";
			
///////////////////////////////////// Display Item Data		
			
			$titleTemp = preg_replace("/\:/i","_","$rowD[6]");
			echo "\" style=\"color:#005500; text-decoration: none;\">";
			echo "$titleTemp";		
			echo "</a>";
			echo "</td>";
			echo "<td style=\"border-bottom: 1px solid #768697; text-align:left; font-size: 0.8em;\">";
			$dc_references = $rowD[5];
			$queryE = "SELECT * FROM collections WHERE dc_identifier = \"$dc_references\" ";
			$mysqli_resultE = mysqli_query($mysqli_link, $queryE);
			while($rowE = mysqli_fetch_row($mysqli_resultE)) { 
				echo "$rowE[6]";
				echo ", ";
				echo "$rowE[7]";
				echo " / ";
				echo "$rowE[8]";
			}
			echo "</td>";
			echo "<td nowrap style=\"border-bottom: 1px solid #768697; color: #800000; text-align:right; font-size: 0.8em;\">";
			echo "$rowD[16]";
			echo "</td>";
			echo "</tr>";
			$x++;
		}	
		echo "</tbody>";
		echo "</table>";
	}
	
///////////////////////////////////////////////////////////// Scripts

?>
    <script language="javascript" type="text/javascript" >
	
		$(document).ready(function() {	

			$('#dt-basic').dataTable( {
				"responsive": true,
				"sDom": '<"top">rt<"bottom"ilp><"clear">', 
				"width": "100%",
				"fixedHeader": true,
				"order": [[ 0, "asc" ], [ 1, "asc" ]],
				"lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]], 
				"scrollY": "48vh",
				"scrollCollapse": false,
				"paging": false
			});	
			
			$(".dataTables_scrollHeadInner").css({"width":"99%"});	
			$(".dataTables_scrollHeadInner").css({"paddingLeft":"0px"});
			$(".dataTables_scrollHeadInner").css({"paddingRight":"0px"});
			$(".dataTables_scrollHeadInner th").eq(0).css({"width":"5%"});
			$(".dataTables_scrollHeadInner th").eq(1).css({"width":"40%"});
			$(".dataTables_scrollHeadInner th").eq(1).css({"width":"45%"});
			$(".dataTables_scrollHeadInner th").eq(2).css({"width":"10%"});	
			$(".table").css({"width":"99%"});
			$(".table thead").css({"width":"100%"});
		
			$('#StartYear').selectpicker();
			$('#StartMonth').selectpicker();
			$('#StartDay').selectpicker();
			$('#FinYear').selectpicker();
			$('#FinMonth').selectpicker();
			$('#FinDay').selectpicker();
			$('#ExcludeList').selectpicker();
			
			$('#StartYear').on('change', function() {
				var yearValue = $('#StartYear').val();
				$("#FinYear option[value="+yearValue+"]").attr('selected', 'selected');
				$('#FinYear').selectpicker('refresh')
			});
			
			$("#listDates").click(function(event) {		
				var data_StartYear = $('#StartYear').val();
				var data_StartMonth = $('#StartMonth').val();
				var data_StartDay = $('#StartDay').val();
				var data_FinYear = $('#FinYear').val();
				var data_FinMonth = $('#FinMonth').val();
				var data_FinDay = $('#FinDay').val()
				var dataSearch = "StartYear="+data_StartYear
					+"&StartMonth="+data_StartMonth
					+"&StartDay="+data_StartDay
					+"&FinYear="+data_FinYear
					+"&FinMonth="+data_FinMonth
					+"&FinDay="+data_FinDay
					+"&action=LIST";
				var doDivSearchA = $('#tableResultsContainer').fadeOut('fast', function(){ 
					var doDivSearchB = $('#tableResultsContainer').load('./index_find_dates.php',dataSearch, function(){ 
						var doDivSearchC = $('#tableResultsContainer').fadeIn('slow'); 
					});
				});
			});
			
			$("#groupDates").click(function(event) {		
				var data_StartYear = $('#StartYear').val();
				var data_StartMonth = $('#StartMonth').val();
				var data_StartDay = $('#StartDay').val();
				var data_FinYear = $('#FinYear').val();
				var data_FinMonth = $('#FinMonth').val();
				var data_FinDay = $('#FinDay').val()
				var dataSearch = "StartYear="+data_StartYear
					+"&StartMonth="+data_StartMonth
					+"&StartDay="+data_StartDay
					+"&FinYear="+data_FinYear
					+"&FinMonth="+data_FinMonth
					+"&FinDay="+data_FinDay
					+"&action=GROUP";
				var doDivSearchA = $('#tableResultsContainer').fadeOut('fast', function(){ 
					var doDivSearchB = $('#tableResultsContainer').load('./index_find_dates.php',dataSearch, function(){ 
						var doDivSearchC = $('#tableResultsContainer').fadeIn('slow'); 
					});
				});
			});
			
		});
		
	</script>
<?php				

///////////////////////////////////////////////////////////// Finish

	if(($reload == "")) {
		include("./ar.dbdisconnect.php");
	}
	
?>