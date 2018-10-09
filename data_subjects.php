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
//	25 May 2017
//	30 May 2017
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
		$search = $_GET["search"];
		$searchPhrase = $_GET["searchPhrase"];		
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
	echo "Mention(s) : ";
	if(($search != "") && ($searchPhrase == "")) {
		echo $search;
	} else {
		if(($searchPhrase != "")) {
			$findmeAgain = "";
			if(($search != "")) {
				$searchComplete = $search."|".$searchPhrase;
			} else {
				$searchComplete = $searchPhrase;
			}
			$headerSearch = "";
			$searches = explode("|","$searchComplete");
			$searches = array_filter($searches);
			$having = (count($searches));
			$doComplexSearch = "y";
			foreach($searches as $w) {
				$i++;
				if(($i == $having)) {
					$headerSearch .= "$w.";
					$findmeAgain .= "annotations.value_string = \"$w\"";
				} else {
					$headerSearch .= "$w; ";
					$findmeAgain .= "annotations.value_string = \"$w\" OR ";
				}
			}
			echo ucwords($headerSearch);
		} else {
			echo "No Details Provided";	
		}
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
	
///////////////////////////////////////////////////////////// Start Queries

	$b = 1;
	if(($doComplexSearch != "y")) {
		
//////////////////////////////// One Search Term		
		
		$queryK = "SELECT annotations.items_dc_identifier, ";
		$queryK .= "items.dc_created ";
		$queryK .= "FROM annotations, items ";
		$queryK .= "WHERE annotations.items_dc_identifier = items.dc_identifier ";
		$queryK .= "AND annotations.value_string = \"$search\" ";
		$queryK .= "ORDER BY items.dc_created ASC, ";
		$queryK .= "items.bibo_pages ASC";
	} else {
		
//////////////////////////////// Multiple Search Terms		
		
		$queryK = "SELECT ";
		$queryK .= "annotations.dc_references, ";
		$queryK .= "items.dc_identifier, ";
		$queryK .= "COUNT(annotations.dc_references) AS goal ";
		$queryK .= "FROM annotations ";
		$queryK .= "LEFT JOIN items ";
		$queryK .= "ON annotations.dc_references = items.dc_identifier ";
		$queryK .= "WHERE ($findmeAgain) ";
		$queryK .= "GROUP BY annotations.dc_references ";
		$queryK .= "HAVING goal = $having ";
		$queryK .= "ORDER BY items.dc_created ASC, ";
		$queryK .= "items.bibo_pages ASC";		
	}
	
//////////////////////////////// Run Query	
	
	$mysqli_resultK = mysqli_query($mysqli_link, $queryK);
	while($rowK = mysqli_fetch_row($mysqli_resultK)) { 

///////////////////////////////////////////////////////////// List Items

		$queryD = "SELECT * FROM items WHERE dc_identifier = \"$rowK[0]\" ORDER BY bibo_pages ASC";
		$mysqli_resultD = mysqli_query($mysqli_link, $queryD);
		while($rowD = mysqli_fetch_row($mysqli_resultD)) { 	
			$items_dc_identifier = $rowD[2];
			$title = $rowD[6];
			echo "<tr>";		
			echo "<td style=\"border-bottom: 1px solid #768697; border-left: 0px solid #768697; text-align:right; color:#000080; font-size: 0.8em;\">";
			echo "$b";
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
			echo "<strong>$titleTemp</strong>";
			echo "</a><br />";
			$b++;
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
			echo "<br /><span style=\"color: #008000; font-weight: 900; \">$rowD[16]</span>";
			echo "</td>";
			echo "<td style=\"border-bottom: 1px solid #768697; border-right: 0px solid #768697; text-align:right; font-size: 0.8em;\" nowrap>";
			
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
			}
			
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