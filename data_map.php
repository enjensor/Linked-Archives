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
//	30 May 2017
//	2 June 2017
//	4 June 2017
//
//	Trigger Refresh Via:
//	./data_map.php?refresh=yes
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
	
///////////////////////////////////////////////////////////// Create Small DB If Refresh

	if(($refresh == "yes")) {
		
//////////////////////////////////////// Empty Table		
		
		$queryD = "TRUNCATE TABLE datasource_sites;";
		$mysqli_resultD = mysqli_query($mysqli_link, $queryD);
		
//////////////////////////////////////// Save Item Origins		
		
		$queryD = "SELECT DISTINCT(gn_name), ";
		$queryD .= "COUNT(gn_name) ";
		$queryD .= "FROM items ";
		$queryD .= "WHERE gn_name != \"\" ";
		$queryD .= "GROUP BY gn_name ";
		$queryD .= "ORDER BY gn_name ASC";
		$mysqli_resultD = mysqli_query($mysqli_link, $queryD);
		while($rowD = mysqli_fetch_row($mysqli_resultD)) { 
			$queryH = "SELECT latitude, longitude FROM datasource_cities WHERE combined = \"$rowD[0]\" LIMIT 1";
			$mysqli_resultH = mysqli_query($mysqli_link, $queryH);
			while($rowH = mysqli_fetch_row($mysqli_resultH)) {
				$queryA = "INSERT INTO datasource_sites VALUES (\"0\", \"$rowD[0]\", \"$rowH[0]\", \"$rowH[1]\", \"Origin\", \"$rowD[1]\");";
				$mysqli_resultA = mysqli_query($mysqli_link, $queryA);
			}
		}
		
//////////////////////////////////////// Save Item Destinations		
		
		$queryD = "SELECT DISTINCT(mads_associatedLocale), ";
		$queryD .= "COUNT(mads_associatedLocale) ";
		$queryD .= "FROM items ";
		$queryD .= "WHERE mads_associatedLocale != \"\" ";
		$queryD .= "GROUP BY mads_associatedLocale ";
		$queryD .= "ORDER BY mads_associatedLocale ASC";
		$mysqli_resultD = mysqli_query($mysqli_link, $queryD);
		while($rowD = mysqli_fetch_row($mysqli_resultD)) { 
			$queryH = "SELECT latitude, longitude FROM datasource_cities WHERE combined = \"$rowD[0]\" LIMIT 1";
			$mysqli_resultH = mysqli_query($mysqli_link, $queryH);
			while($rowH = mysqli_fetch_row($mysqli_resultH)) {
				$queryA = "INSERT INTO datasource_sites VALUES (\"0\", \"$rowD[0]\", \"$rowH[0]\", \"$rowH[1]\", \"Destination\", \"$rowD[1]\");";
				$mysqli_resultA = mysqli_query($mysqli_link, $queryA);
			}
		}
		
//////////////////////////////////////// Save Item Mentions

		$queryD = "SELECT DISTINCT(value_string), ";
		$queryD .= "COUNT(value_string) ";
		$queryD .= "FROM annotations ";
		$queryD .= "WHERE rdfs_label ";
		$queryD .= "LIKE \"geographicNote\" ";
		$queryD .= "GROUP BY value_string ";
		$queryD .= "ORDER BY value_string ASC";
		$mysqli_resultD = mysqli_query($mysqli_link, $queryD);
		while($rowD = mysqli_fetch_row($mysqli_resultD)) { 
			$queryH = "SELECT latitude, longitude FROM datasource_cities WHERE combined = \"$rowD[0]\" LIMIT 1";
			$mysqli_resultH = mysqli_query($mysqli_link, $queryH);
			while($rowH = mysqli_fetch_row($mysqli_resultH)) {
				$queryA = "INSERT INTO datasource_sites VALUES (\"0\", \"$rowD[0]\", \"$rowH[0]\", \"$rowH[1]\", \"Mention\", \"$rowD[1]\");";
				$mysqli_resultA = mysqli_query($mysqli_link, $queryA);
			}
		}
		
//////////////////////////////////////// Fin
		
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
			
			var map = L.map('map').setView([13.5317, 87.5396], 2);
			
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
			
			L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
}).addTo(map);
			
			<?php

//////////////////////////////////////// Get Item Origins
			
				$queryD = "SELECT * FROM datasource_sites WHERE dc_type = \"Origin\"; ";
				$mysqli_resultD = mysqli_query($mysqli_link, $queryD);
				while($rowD = mysqli_fetch_row($mysqli_resultD)) { 
				
					echo "L.marker([".$rowD[2];
					echo ", ".$rowD[3];
					echo "], {icon: greenIcon}).addTo(map).bindPopup('";
					echo "Origin: ".$rowD[1]." [";
					echo $rowD[5]."]');\n";
					
				}

//////////////////////////////////////// Get Item Destinations	

				$queryD = "SELECT * FROM datasource_sites WHERE dc_type = \"Destination\"; ";
				$mysqli_resultD = mysqli_query($mysqli_link, $queryD);
				while($rowD = mysqli_fetch_row($mysqli_resultD)) { 
				
					echo "L.marker([".$rowD[2];
					echo ", ".$rowD[3];
					echo "], {icon: redIcon}).addTo(map).bindPopup('";
					echo "Destination: ".$rowD[1]." [";
					echo $rowD[5]."]');\n";
					
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