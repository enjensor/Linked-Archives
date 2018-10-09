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
//	4 June 2017
//
//
/////////////////////////////////////////////////////////// Clean post and get	
	
	if(($IMGreload == "")) {
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
		$refresh = $_GET["refresh"];
		$_GET = array();
		$_POST = array();
	}

///////////////////////////////////////////////////////////// Map Frame	
		
	echo "<div class=\"parent\" id=\"map\" style=\"";
	echo "border: 13px solid #263238; ";
	echo "min-height: 90vh; ";
	echo "max-height: 90vh; ";
	echo "width: 100%; ";
	echo "z-index: 1;";
	echo "\"></div>";	
	
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// Scripts

?>
	<script language="javascript" type="text/javascript" >
	
		$(document).ready(function() {
			
			var map = L.map('map').setView([51.5074, 0.1278], 4);
			
			var greenIcon = new L.Icon({
				iconUrl: './leaflet/images/marker-icon-green.png',
				shadowUrl: './leaflet/images/marker-shadow.png',
				iconSize: [25, 41],
				iconAnchor: [12, 41],
				popupAnchor: [1, -34],
				shadowSize: [41, 41]
			});
			
			var redIcon = new L.Icon({
				iconUrl: './leaflet/images/marker-icon-red.png',
				shadowUrl: './leaflet/images/marker-shadow.png',
				iconSize: [25, 41],
				iconAnchor: [12, 41],
				popupAnchor: [1, -34],
				shadowSize: [41, 41]
			});
			
			var blueIcon = new L.Icon({
				iconUrl: './leaflet/images/marker-icon-violet.png',
				shadowUrl: './leaflet/images/marker-shadow.png',
				iconSize: [25, 41],
				iconAnchor: [12, 41],
				popupAnchor: [1, -34],
				shadowSize: [41, 41]
			});
			
			L.tileLayer('http://stamen-tiles-{s}.a.ssl.fastly.net/watercolor/{z}/{x}/{y}.{ext}', { 
				attribution: '&copy; 2017 <a href="http://osm.org/copyright">OpenStreetMap</a>', subdomains: 'abcd',minZoom: 1,maxZoom: 16,ext: 'png' }).addTo(map);
			
			<?php
				
//////////////////////////////////////// Get Item Mentions				
				
				$queryD = "SELECT * FROM datasource_sites WHERE dc_type = \"Mention\"; ";
				$mysqli_resultD = mysqli_query($mysqli_link, $queryD);
				while($rowD = mysqli_fetch_row($mysqli_resultD)) { 
				
					echo "var tlink = 'Mention(s)<br /><strong>$rowD[1]</strong> : $rowD[5]<br />";
					echo "<a onclick=\"var doNow = findMentions(\\'".$rowD[1]."\\');\" href=\"#\">View Letters</a>';\n";
					echo "L.marker([".$rowD[2];
					echo ", ".$rowD[3];
					echo "], {icon: blueIcon}).addTo(map).bindPopup(tlink);\n";
					
				}
				
			?>
			
			map.invalidateSize();
			
		});
		
		function findMentions(thisMention) {
			var valink = thisMention;
			var lablink = thisMention;
			var cleanBarB = $('#usersearch').val(''+valink);	
			var dataE = 'action=find&search='+lablink+'&searchPhrase=';		
			var doDiv = $('#tableResultsContainer').fadeOut('fast', function(){
				var searchVal = $('#tableResultsContainer').load('./data_subjects.php',dataE, function(){
					var doDivAlso = $('#tableResultsContainer').fadeIn('slow');
				});
			});	
			var dataSearchD = 'searchTerm='+lablink+'&searchPhrase=';
			var doDivSearchE = $('#titleTags').fadeOut('fast', function(){
				var doDivSearchF = $('#titleTags').load('./index_find_subjects.php',dataSearchD, function(){
					var doDivSearchG = $('#titleTags').fadeIn('slow');
				}); 
			}); 		
			return false;
		}
		
	</script>	
<?php

///////////////////////////////////////////////////////////// Finish

	if(($IMGreload == "")) {
		include("./ar.dbdisconnect.php");
	}

?>