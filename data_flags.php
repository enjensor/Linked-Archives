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
//  22-23 February 2017
//  14 March 2017
//  3 April 2017
//	25 May 2017
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
		$action = $_GET["action"];
		$_GET = array();
		$_POST = array();
	}

///////////////////////////////////////////////////////////// Display Flag Header
	
	echo "<div class=\"row\">";
	echo "<div class=\"col-sm-3\">";
	echo "<a href=\"javascript: ";
	echo "var dataE = 'action=GO_BACK';	";		
    echo "var doDiv = $('#tableResultsContainer').fadeOut('fast', function(){ ";
    echo "var searchVal = $('#tableResultsContainer').load('./data_collections.php',dataE, function(){ ";
    echo "var doDivAlso = $('#tableResultsContainer').fadeIn('slow'); ";
    echo "}); ";
    echo "}); ";
	echo "\" style=\"text-decoration:none; color: #FFFFFF;\" target=\"_self\">";
	echo "<button class=\"btn btn-sm btn-purple btn-rounded\"><strong>Go Back</strong></button>";
	echo "</a>";
	echo "</div>";
	echo "<div class=\"col-sm-9\">";
	echo "<p class=\"mar-btm\" style=\"text-align:left;\">";
	echo "<strong>";
	echo "<a href=\"javascript: ";
	echo "var dataE = 'action=GO_BACK';	";		
    echo "var doDiv = $('#tableResultsContainer').fadeOut('fast', function(){ ";
    echo "var searchVal = $('#tableResultsContainer').load('./data_collections.php',dataE, function(){ ";
    echo "var doDivAlso = $('#tableResultsContainer').fadeIn('slow'); ";
    echo "}); ";
    echo "}); ";
	echo "\" style=\"text-decoration:none; color: #000060;\" target=\"_self\">";	
	if(($action == "FLAGGED")) {		
		echo "Items Marked for Review.";
	} else {
		echo "Session Saves Marked for Review.";	
	}
	echo "</a>";
	echo "</strong><br />&nbsp;";
	echo "</p>";
	echo "</div>";
	echo "</div>";

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
	
///////////////////////////////////////////////////////////// Query Flags

	$queryK = "SELECT * FROM flags ORDER BY ID ASC";
	$mysqli_resultK = mysqli_query($mysqli_link, $queryK);
	while($rowK = mysqli_fetch_row($mysqli_resultK)) { 

///////////////////////////////////////////////////////////// List Items

		$queryD = "SELECT * FROM items WHERE dc_identifier = \"$rowK[1]\" ORDER BY bibo_pages ASC";
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
			echo "<td style=\"border-bottom: 1px solid #768697; border-left: 0px solid #768697; text-align:right; color:#000080; font-size: 0.8em;\">";
			echo "$rowD[7]";
			echo "</td>";
			echo "<td style=\"border-bottom: 1px solid #768697; text-align:left; font-size: 0.8em; width: 100%;\">";
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
			echo "<strong>Document : $titleTemp</strong>";
			echo "</a><br />";
//			echo "ID:$rowD[2]<br />";
			if(($show_metadata == "y")) {
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
				echo "$rowD[16]";
			}
			if(($rowD[16] != "") && ($rowD[13] != "")) {
				echo " : ";	
			}
			if(($rowD[13] != "")) {
				echo "$rowD[13]";	
			}
			echo "</td>";
			echo "<td style=\"border-bottom: 1px solid #768697; border-right: 0px solid #768697; text-align:right; font-size: 0.8em;\" nowrap>";
			
///////////////////////////////////// Flag Report		
			
			$valid = "yes";
			$queryFU = "SELECT * FROM flags WHERE dc_references = \"$rowD[2]\" ";
			$mysqli_resultFU = mysqli_query($mysqli_link, $queryFU);
			while($rowFU = mysqli_fetch_row($mysqli_resultFU)) {
				$valid = "no";
			}
			if(($valid == "yes")) {
				echo "<a href=\"#\">";
				echo "<i id=\"".$rowD[2]."\" class=\"ion-flag btn-toggle\" ";
				echo "style=\"color:#CCCCCC; font-size: 1.4em;\" ";
				echo "data-status=\"YES\" data-id=\"".$rowD[2]."\"></i>";
				echo "</a>";
			} else {
				echo "<a href=\"#\">";
				echo "<i id=\"".$rowD[2]."\" class=\"ion-flag btn-toggle\" ";
				echo "style=\"color:#8B0D82; font-size: 1.4em;\" ";
				echo "data-status=\"NO\" data-id=\"".$rowD[2]."\"></i>";
				echo "</a>";
			}
			echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";		
			
///////////////////////////////////// Save to Search Session		
//			
//			echo "<i class=\"ion-checkmark-circled\" style=\"color:#CCCCCC; font-size: 1.4em;\"></i>";
//			echo "&nbsp;&nbsp;&nbsp;";
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
		
///////////////////////////////////////////////////////////// Close Flags		
		
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

/////////////////////////////////////////////////////////// OnLoad Start
			
			$(document).ready(function(e) {

/////////////////////////////////////////////////////////// Sort Table

				$('#dt-basic').dataTable( {
        			"responsive": true,
					"width": "100%",
					"fixedHeader": true,
					"order": [[ 0, "asc" ]],
					"sDom": '<"top">rt<"bottom"ilp><"clear">',
					"lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]], 
					"scrollY": "65vh",
					"scrollCollapse": false,
	       			"paging": false,
					"aoColumns": [
      					{ "sType": "html-num" },
      					{ "sType": "html" },
						{ "sType": "html" }
    				]
    			});	
				
				$(".dataTables_scrollHeadInner").css({"width":"97.65%"});
				$(".dataTables_scrollHeadInner th").eq(0).css({"width":"10%"});
				$(".dataTables_scrollHeadInner th").eq(1).css({"width":"75%"});
				$(".dataTables_scrollHeadInner th").eq(2).css({"width":"25%"});			
				$(".table").css({"width":"99%"});


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
					}								
				});

/////////////////////////////////////////////////////////// OnLoad Finish
				
			});
		
		</script>
<?php		
		
		include("./ar.dbdisconnect.php");
	}

?>