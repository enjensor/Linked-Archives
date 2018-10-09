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
//  8-13 January 2017
//  16 January 2017
//  23 February 2017
//  27 February 2017
//  14 March 2017
//  3 April 2017
//	2 May 2017
//	11 May 2017
//	25 May 2017
//	22 June 2017
//	28-29 June 2017
//	7 July 2017
//	5 February 2018
//	6-7 August 2018
//
//
/////////////////////////////////////////////////////////// Clean post and get	
	
	$show_metadata = "n";
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
		$collections_dc_identifier = $_GET["collections_dc_identifier"];
		$_GET = array();
		$_POST = array();
	}
	if(($reload == "view")) {
		$collections_dc_identifier = $view_collection;
	}
	
///////////////////////////////////////////////////////////// Get Items Details

	$queryD = "SELECT * FROM collections WHERE dc_identifier = \"$collections_dc_identifier\" ";
	$mysqli_resultD = mysqli_query($mysqli_link, $queryD);
	while($rowD = mysqli_fetch_row($mysqli_resultD)) { 
		$org_FormalOrganisation	= $rowD[3];
		$col_bf_physicalLocation = $rowD[5];
		$skos_Collection = $rowD[6];	
		$skos_OrderedCollection = $rowD[7];	
		$skos_member = $rowD[8];	
		$disco_startDate = $rowD[9];	
		$disco_endDate = $rowD[10];
		$collection_found = "y";	
	}

///////////////////////////////////////////////////////////// Display Items Details
	
	if(($collection_found == "y")) {
		echo "<div class=\"row\">";
		
/////////////////////////////////////// Go Back		
		
		echo "<div class=\"col-sm-3\">";
		echo "<a href=\"javascript: ";
		echo "var dataE = 'collections_dc_identifier=".$dc_identifier."';	";		
        echo "var doDiv = $('#tableResultsContainer').fadeOut('fast', function(){ ";
        echo "var searchVal = $('#tableResultsContainer').load('./data_collections.php',dataE, function(){ ";
        echo "var doDivAlso = $('#tableResultsContainer').fadeIn('slow'); ";
        echo "}); ";
        echo "}); ";
		echo "\" style=\"text-decoration:none; color: #FFFFFF;\" target=\"_self\">";
		echo "<button class=\"btn btn-sm btn-purple btn-rounded\"><strong>Go Back</strong></button>";
		echo "</a>";
		echo "</div>";

/////////////////////////////////////// Collection Name
		
		echo "<div class=\"col-sm-9\" id=\"collectionName\">";
		echo "<p class=\"mar-btm\" style=\"text-align:left; font-size: 0.9em;\">";
		echo "<strong>";
		echo "<a href=\"javascript: ";
		echo "var dataE = 'collections_dc_identifier=".$dc_identifier."';	";		
        echo "var doDiv = $('#tableResultsContainer').fadeOut('fast', function(){ ";
        echo "var searchVal = $('#tableResultsContainer').load('./data_collections.php',dataE, function(){ ";
        echo "var doDivAlso = $('#tableResultsContainer').fadeIn('slow'); ";
        echo "}); ";
        echo "}); ";
		echo "\" style=\"text-decoration:none; color: #000060;\" target=\"_self\">";			
		echo "$skos_Collection. ";
		echo "$skos_OrderedCollection / $skos_member, ";
		echo "$disco_startDate - $disco_endDate. ";
		echo "$org_FormalOrganisation, $col_bf_physicalLocation. ";
		echo "</a>";
		echo "</strong><br />&nbsp;";
		echo "</p>";
		echo "</div>";

/////////////////////////////////////// Close
		
		echo "</div>";
	}

///////////////////////////////////////////////////////////// Start Table
	
?>
	<table id="dt-basic" class="table table-striped table-hover" cellspacing="0" width="100%">
  		<thead>
      		<tr>
            	<th style="border-bottom: 8px solid #1b746c; text-align:right; font-size: 0.8em;">#</th>
                <th style="border-bottom: 8px solid #1b746c; text-align:left; font-size: 0.8em;">Items</th>
               	<th style="border-bottom: 8px solid #1b746c; text-align:right; font-size: 0.8em;">Action</th>       	
        	</tr>
       	</thead>
    	<tbody>
<?php                                                
	
///////////////////////////////////////////////////////////// List Items

	$queryD = "SELECT * FROM items WHERE dc_references = \"$collections_dc_identifier\" ORDER BY bibo_pages ASC";
	$mysqli_resultD = mysqli_query($mysqli_link, $queryD);
	while($rowD = mysqli_fetch_row($mysqli_resultD)) { 	
	
		$items_dc_identifier = $rowD[2];
		$title = $rowD[6];
		
		if(($show_metadata == "y")) {
			$tags = array();
			$queryX = "SELECT * FROM annotations WHERE dc_references = \"$items_dc_identifier\" ";
			$mysqli_resultX = mysqli_query($mysqli_link, $queryX);
			while($rowX = mysqli_fetch_row($mysqli_resultX)) { 
				$tags[] = $rowX[7];
			}
		}
		echo "<tr>";		
		echo "<td style=\"";		
		echo "border-bottom: 0px solid #768697; ";	
		echo "border-top: 0px solid #768697; ";		
		echo "border-left: 0px solid #768697; ";		
		echo "border-right: 0px solid #768697; ";		
		echo "text-align:right; ";		
		echo "color:#000080; ";		
		echo "font-size: 0.8em;";		
		echo "\">";
		echo "$rowD[7]";
		echo "</td>";
		echo "<td style=\"";		
		echo "border-bottom: 0px solid #768697; ";	
		echo "border-top: 0px solid #768697; ";		
		echo "border-left: 0px solid #768697; ";		
		echo "border-right: 0px solid #768697; ";		
		echo "text-align:left; ";		
		echo "font-size: 0.8em; ";		
		echo "width: 100%;";		
		echo "\">";
		echo "<a href=\"javascript: ";
		
///////////////////////////////////// Load Image		
		
		echo "var dataE = 'dc_identifier=".$items_dc_identifier."';	";		
        echo "var doDivK = $('#doc_detail').fadeOut('fast', function(){ ";
        echo "var doDivP = $('#doc_detail').load('./data_doc.php',dataE, function(){ ";
        echo "var doDivW = $('#doc_detail').fadeIn('slow'); ";
        echo "}); ";
        echo "}); ";
		
///////////////////////////////////// Load Metadata	Editor Panel
		
		echo "var dataF = 'dc_identifier=".$items_dc_identifier."&reload=';	";
		echo "var doDivM = $('#titleTags').fadeOut('fast', function(){ ";
        echo "var doDivN = $('#titleTags').load('./data_meta.php',dataF, function(){ ";
        echo "var doDivO = $('#titleTags').fadeIn('slow'); ";
        echo "}); ";
        echo "}); ";
		
///////////////////////////////////// Display Item Data		
		
		$titleTemp = preg_replace("/\:/i","_","$rowD[6]");
		echo "\" style=\"color:#000000; text-decoration: none;\">";
		echo "<strong>Item : $titleTemp</strong>";
		echo "</a><br />";	
		if(($show_metadata == "y")) {
			echo "ID:$rowD[2]<br />";
			if((count($tags) > 0)) {
				sort($tags);
				foreach ($tags as $t) { 
					echo $t."; "; 
				}
			} else {
				echo "<button>Add Metadata</button>";	
			}
		}
		if(($rowD[16] != "")) {
			echo "$rowD[16] ";
		} else {
			echo "n.d. ";
		}
		if(($rowD[13] != "")) {
			echo "<br />";	
		}
		if(($rowD[13] != "")) {
			echo "$rowD[13]";	
		}
		if(($rowD[19] != "")) {
			if(($rowD[13] != "")) {
				echo " &gt; ";	
			}
			echo "$rowD[19]";
		}
		echo "</td>";
		echo "<td style=\"";		
		echo "border-bottom: 0px solid #768697; ";	
		echo "border-top: 0px solid #768697; ";		
		echo "border-left: 0px solid #768697; ";		
		echo "border-right: 0px solid #768697; ";		
		echo "text-align:right; ";		
		echo "font-size: 0.8em;";		
		echo "\" nowrap>";
		
		
///////////////////////////////////// Flag OCR

		if(($rowD[17] != "")) {
			echo "<i id=\"OCR_".$rowD[2]."\" class=\"ion-document-text\" ";
			echo "style=\"color:#006dc3; font-size: 1.4em;\" ></i>";
			echo "&nbsp;&nbsp;&nbsp;";
		}
		
///////////////////////////////////// Flag Report		
		
		if(($_SESSION["administrator"] == "yes")) {
			$valid = "yes";
			$queryFU = "SELECT * FROM flags WHERE dc_references = \"$rowD[2]\" ";
			$mysqli_resultFU = mysqli_query($mysqli_link, $queryFU);
			while($rowFU = mysqli_fetch_row($mysqli_resultFU)) {
				$valid = "no";
			}
			if(($valid == "yes")) {
				echo "<a href=\"#\">";
				echo "<i id=\"FLAG_".$rowD[2]."\" class=\"ion-flag btn-toggle\" ";
				echo "style=\"color:#CCCCCC; font-size: 1.4em;\" ";
				echo "data-status=\"YES\" data-id=\"".$rowD[2]."\"></i>";
				echo "</a>";
			} else {
				echo "<a href=\"#\">";
				echo "<i id=\"FLAG_".$rowD[2]."\" class=\"ion-flag btn-toggle\" ";
				echo "style=\"color:#8B0D82; font-size: 1.4em;\" ";
				echo "data-status=\"NO\" data-id=\"".$rowD[2]."\"></i>";
				echo "</a>";
			}
			echo "&nbsp;&nbsp;&nbsp;";	
		}
		
///////////////////////////////////// Save to Search Session		
//		
//		echo "<i class=\"ion-checkmark-circled\" style=\"color:#CCCCCC; font-size: 1.4em;\"></i>";
//		echo "&nbsp;&nbsp;&nbsp;";
//		
///////////////////////////////////// Lock / Unlock Metadata		

		$status = $rowD[18];
		if(($_SESSION["administrator"] == "yes")) {
			if(($status != "restricted")) {
				echo "<a href=\"#\">";
				echo "<i id=\"LOCK_".$rowD[2]."\" class=\"ion-unlocked lock-toggle\" ";
				echo "style=\"color:#D93427; font-size: 1.4em;\" ";
				echo "data-status=\"UNLOCKED\" data-id=\"".$rowD[2]."\"></i>";
				echo "</a>";
			} else {
				echo "<a href=\"#\">";
				echo "<i id=\"LOCK_".$rowD[2]."\" class=\"ion-locked lock-toggle\" ";
				echo "style=\"color:#68C970; font-size: 1.4em;\" ";
				echo "data-status=\"LOCKED\" data-id=\"".$rowD[2]."\"></i>";
				echo "</a>";
			}
		} else {
			echo "<i id=\"LOCK_".$rowD[2]."\" class=\"ion-locked\" ";
			echo "style=\"color:#cccccc; font-size: 1.4em;\" ";
			echo "data-status=\"LOCKED\" data-id=\"".$rowD[2]."\"></i>";
		}
		
///////////////////////////////////// Finish List		
		
		echo "</td>";
		echo "</tr>";
		
	}
	
///////////////////////////////////////////////////////////// Close Table	

?>
		</tbody>
	</table>
<?php

///////////////////////////////////////////////////////////// Finish

	if(($reload == "") or ($reload == "view")) {
		
?>
		<script language="javascript" type="text/javascript" >
			
			$(document).ready(function(e) {

/////////////////////////////////////////////////////////// Sort Table

				if($(window).width() >= 1199){
					$('#dt-basic').dataTable( {
						"responsive": true,
						"width": "100%",
						"fixedHeader": true,
						"order": [[ 0, "asc" ]],
						"lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]], 
						"scrollY": "58vh",
						"scrollCollapse": false,
						"paging": false,
						"sDom": '<"top">rt<"bottom"ilp><"clear">', 
						"aoColumns": [
							{ "sType": "html-num" },
							{ "sType": "html" },
							{ "sType": "html" }
						]
					});	
				} else {
					$('#dt-basic').dataTable( {
						"responsive": true,
						"width": "100%",
						"fixedHeader": true,
						"order": [[ 0, "asc" ]],
						"lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]], 
						"scrollY": "53vh",
						"scrollCollapse": false,
						"paging": false,
						"sDom": '<"top">rt<"bottom"ilp><"clear">', 
						"aoColumns": [
							{ "sType": "html-num" },
							{ "sType": "html" },
							{ "sType": "html" }
						]
					});	
				}
				
//				var doDivAZ = $(".dataTables_scrollHeadInner").css({"width":"97.65%"});	
				var doDivAZ = $(".dataTables_scrollHeadInner").css({"width":"99%"});
				var doDivAX = $(".dataTables_scrollHeadInner").css({"paddingLeft":"0px"});
				var doDivAY = $(".dataTables_scrollHeadInner").css({"paddingRight":"0px"});
//				var doDivB = $(".dataTables_scrollHeadInner th").eq(0).css({"width":"5%"});
//				var doDivC = $(".dataTables_scrollHeadInner th").eq(1).css({"width":"75%"});
//				var doDivD = $(".dataTables_scrollHeadInner th").eq(2).css({"width":"20%"});			
				var doDivEZ = $(".table").css({"width":"99%"});
				var doDivAV = $(".table thead").css({"width":"100%"});

/////////////////////////////////////////////////////////// Toggle Flag
				
				$(".btn-toggle").click(function(event) {
					
 					var currentState = $(this).attr('data-status');
					if(currentState == "YES") {
						var changeID = $(this).attr('data-id');
						var changeCss = $(this).css('color','#8B0D82');
						var changeStatus = $(this).attr('data-status','NO');
						var dataE = "action=add&items_dc_identifier=" + changeID;
						var searchValP = $('#theDarkCloset').load('./data_get_flag.php', dataE, function(){});				
					} else {
						var changeID = $(this).attr('data-id');
						var changeCss = $(this).css('color','#BBBBBB');
						var changeStatus = $(this).attr('data-status','YES');
						var dataE = "action=delete&items_dc_identifier=" + changeID;
						var searchValQ = $('#theDarkCloset').load('./data_get_flag.php', dataE, function(){});
					}	
								
				});	
								
/////////////////////////////////////////////////////////// Toggle Lock				

				$(".lock-toggle").click(function(event) {
					
 					var currentState = $(this).attr('data-status');
					if(currentState == "UNLOCKED") {
						var changeID = $(this).attr('data-id');
						var changeCss = $(this).css('color','#68C970');
						var changeStatus = $(this).attr('data-status','LOCKED');
						var changeClass = $(this).removeClass('ion-unlocked').addClass('ion-locked');
						var dataE = "action=lock&items_dc_identifier=" + changeID;
						var searchValP = $('#theDarkCloset').load('./data_get_lock.php', dataE, function(){
							var dataF = "dc_identifier=" + changeID + "&reload=";
							var doDivM = $("#titleTags").fadeOut('fast', function(){
        						var doDivN = $("#titleTags").load('./data_meta.php',dataF, function(){
        							var doDivO = $('#titleTags').fadeIn('slow');
        						});
        					});
						});	
						var ItemData = 'dc_identifier=' + changeID;	
        				var ItemB = $('#doc_detail').fadeOut('fast', function(){ 
        					var ItemC = $('#doc_detail').load('./data_doc.php', ItemData, function(){ 
        						var ItemD = $('#doc_detail').fadeIn('slow'); 
        					});
        				});
					} else {
						var changeID = $(this).attr('data-id');
						var changeCss = $(this).css('color','#D93427');
						var changeStatus = $(this).attr('data-status','UNLOCKED');
						var changeClass = $(this).removeClass('ion-locked').addClass('ion-unlocked');
						var dataE = "action=unlock&items_dc_identifier=" + changeID;
						var searchValQ = $('#theDarkCloset').load('./data_get_lock.php', dataE, function(){
							var dataF = "dc_identifier=" + changeID + "&reload=";
							var doDivM = $("#titleTags").fadeOut('fast', function(){
        						var doDivN = $("#titleTags").load('./data_meta.php',dataF, function(){
        							var doDivO = $('#titleTags').fadeIn('slow');
        						});
        					});
						});
						var ItemData = 'dc_identifier=' + changeID;	
        				var ItemB = $('#doc_detail').fadeOut('fast', function(){ 
        					var ItemC = $('#doc_detail').load('./data_doc.php', ItemData, function(){ 
        						var ItemD = $('#doc_detail').fadeIn('slow'); 
        					});
        				});
					}								
				});

/////////////////////////////////////////////////////////// OnLoad Finish
				
			});
		
		</script>
<?php	

	}
	
	if(($reload == "")) {	
		include("./ar.dbdisconnect.php");
	}

?>