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
//  8-9 January 2017
//  12 January 2017
//  15-16 January 2017
//  3 April 2017
//	11-12 May 2017
//	17 May 2017
//	23-25 May 2017
//	30 May 2017
//	22-23 June 2017
//	27-29 June 2017
//	5 March 2018
//  8 November 2018
//
//
/////////////////////////////////////////////////////////// Set reload var	
	
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
		$dc_identifier = $_GET["dc_identifier"];
        $random = $_GET["random"];
        if(($dc_identifier == "") && ($random == "yes")) {
            $queryDX = "SELECT dc_identifier ";
			$queryDX .= "FROM items ";
			$queryDX .= "ORDER BY RAND() ";
            $queryDX .= "LIMIT 1";
            $mysqli_resultDX = mysqli_query($mysqli_link, $queryDX);
            while($rowDX = mysqli_fetch_row($mysqli_resultDX)) { 
                $dc_identifier = $rowDX[0];
            }
        }
		$_GET = array();
		$_POST = array();
	} else {
		if(($view_item == "")) {
			$dc_identifier = $randIMG;
		} else {
			$dc_identifier = $view_item;
		}
	}
	
///////////////////////////////////////////////////////////// Get Item Details

	$queryD = "SELECT * FROM items WHERE dc_identifier = \"$dc_identifier\" ";
	$mysqli_resultD = mysqli_query($mysqli_link, $queryD);
	while($rowD = mysqli_fetch_row($mysqli_resultD)) { 
		$this_dc_references = $rowD[5];
		$this_dc_title = $rowD[6];
		$this_page = $rowD[7];
		$this_dc_creator = $rowD[13];
		$this_org_FormalOrganisation = $rowD[14];
		$this_dc_created = $rowD[16];
		$this_restricted = $rowD[18];
		$this_marc_addressee = $rowD[19];
		$this_rdaa_groupMemberOf = $rowD[20];
		$rdf_resource = $rowD[11];
		$item_found = "y";
	}

///////////////////////////////////////////////////////////// Get Collection Details
	
	$queryD = "SELECT * FROM collections WHERE dc_identifier = \"".$this_dc_references."\" ";
	$mysqli_resultD = mysqli_query($mysqli_link, $queryD);
	while($rowD = mysqli_fetch_row($mysqli_resultD)) { 
		$this_col_UUID = $rowD[2];
		$this_col_bf_heldBy	= $rowD[3];
		$this_col_bf_subLocation = $rowD[4];
		$this_col_bf_physicalLocation = $rowD[5];
		$this_col_skos_Collection = $rowD[6];	
		$this_col_skos_OrderedCollection = $rowD[7];	
		$this_col_skos_member = $rowD[8];	
		$this_col_disco_startDate = $rowD[9];	
		$this_col_disco_endDate = $rowD[10];
		$this_col_found = "y";
	}

///////////////////////////////////////////////////////////// Display Item
	
	if(($item_found == "y")) {
		
///////////////////////////////////////////////////////////// Start If		
		
		$file_parts = explode("/","$rdf_resource");
		$file_parts = array_reverse($file_parts);
		
///////////////////////////////////////////////////////////// Open Panel		
		
		echo "<div style=\"";
		echo "width:100%; ";
		echo "border-left: 0px solid #263238; ";
		echo "border-right: 0px solid #263238; ";
		echo "\" ";
		echo "id=\"focal\">";
		
///////////////////////////////////////////////////////////// Image		
		
		echo "<div ";
		echo "class=\"parent\" ";
		echo "id=\"imageLoader\" ";
		echo "style=\"overflow: visible!important;\">";
		echo "<img ";
		echo "class=\"bord-all grayscale\" ";
		echo "src=\"";
		echo "./data/items/".$file_parts[0];
		echo "\" width=\"100%\" ";
		echo "border=\"0\" ";
		echo "style=\"border: 0px solid #000000;\" ";
		echo "id=\"zoomDoc\">";
		echo "</div>";
			
///////////////////////////////////////////////////////////// Close Panel		
		
		echo "</div>";
		
///////////////////////////////////////////////////////////// Details Insert
		
		$doBr = "";
		$this_dc_title = preg_replace("/:/","_","$this_dc_title");
		echo "<div id=\"detailsInsert\" ";
		echo "style=\"";
		echo "color: #FFcc00; ";
		echo "z-index: 9; ";
		echo "background-color: rgba(35, 35, 35, 0.55);";
		echo "position: absolute; ";
		echo "bottom: 15px; ";
		echo "height: 165px; ";
		echo "padding: 25px; ";
		echo "border-top-right-radius: 10px; ";
		echo "width: 85%; ";
		echo "display: none; ";
		echo "word-break: break-all;";
		echo "\">";
		if(($this_col_found == "y")) {
			echo "<strong>$this_col_skos_Collection</strong><br />";
		}
		if(($this_dc_creator != "") && ($this_dc_creator != "Nothing Specified")) {
			echo "From: $this_dc_creator";
			$doBr = "y";
		}
		if(($this_dc_creator != "") && ($this_dc_creator != "Nothing Specified") 
			&& ($this_org_FormalOrganisation != "") && ($this_org_FormalOrganisation != "Nothing Specified")) {
			echo ", $this_org_FormalOrganisation";
			$doBr = "y";
		}
		if(($this_marc_addressee != "") && ($this_marc_addressee != "Nothing Specified")) {
			echo "<br />To: $this_marc_addressee";
			$doBr = "y";
		}
		if(($this_marc_addressee != "") && ($this_marc_addressee != "Nothing Specified") 
			&& ($this_rdaa_groupMemberOf != "") && ($this_rdaa_groupMemberOf != "Nothing Specified")) {
			echo ", $this_rdaa_groupMemberOf";
			$doBr = "y";
		}
		if(($this_col_found == "y")) {
			if(($doBr == "y")) {
				echo "<br />";
			}
			echo "$this_col_skos_OrderedCollection / ";
			echo "$this_col_skos_member / ";
			echo "$this_page ";
			if(($this_dc_created != "")) {
				echo ": $this_dc_created";
			}
		}
		echo "<br />";
		
////////////////////////////////// Generate URI		
		
		$phpSelf = preg_replace("/data_doc.php/i","",$_SERVER["PHP_SELF"]);
		$phpSelf = preg_replace("/index.php/i","",$phpSelf);
		$URI = "https://".$_SERVER["HTTP_HOST"].$phpSelf."item/".$dc_identifier."";
		echo "<strong><a href=\"./index_view.php?view=".$dc_identifier."\" style=\"color: #FFFFFF; text-decoration: none;\">";
		echo "$URI";
		echo "</a></strong>";
		echo "<br />";
			
////////////////////////////////// Load Metadata Panel	
		
		echo "<a href=\"javascript: ";
		echo "var dataF = 'dc_identifier=".$dc_identifier."&reload=';	";
		echo "var doDivM = $('#titleTags').fadeOut('fast', function(){ ";
        echo "var doDivN = $('#titleTags').load('./data_meta.php',dataF, function(){ ";
        echo "var doDivO = $('#titleTags').fadeIn('slow'); ";
        echo "}); ";
        echo "}); ";
		
////////////////////////////////// Load Items Panel

		echo "var dataE = 'collections_dc_identifier=".$this_dc_references."';	";		
        echo "var doDiv = $('#tableResultsContainer').fadeOut('fast', function(){ ";
        echo "var searchVal = $('#tableResultsContainer').load('./data_items.php',dataE, function(){ ";
        echo "var doDivAlso = $('#tableResultsContainer').fadeIn('slow'); ";
        echo "}); ";
        echo "}); ";

////////////////////////////////// Finish HREF		
		
		echo "\" ";
		echo "style=\"color: #FFFFFF;\">";
		if(($_SESSION["administrator"] == "yes")) {
			echo "<strong>Edit</strong>";
		} else {
			echo "<strong>View</strong>";
		}
		echo "</a>";		
		
////////////////////////////////// Download Image		
		
		echo " | ";
		echo "<a href=\"";
		echo "./data_download.php?dc_identifier=".$dc_identifier."&type=jpg";
		echo "\" style=\"color: #FFFFFF;\">";
		echo "<strong>Download JPG</strong>";
		echo "</a>";

////////////////////////////////// Download RDF XML
		
		echo " | ";
		echo "<a href=\"";
		echo "./data_download.php?dc_identifier=".$dc_identifier."&type=rdf&format=rdf";
		echo "\" style=\"color: #FFFFFF;\">";
		echo "<strong>XML</strong>";
		echo "</a>";
		
////////////////////////////////// Download RDFa
		
		echo " | ";
		echo "<a href=\"";
		echo "./data_download.php?dc_identifier=".$dc_identifier."&type=rdf&format=rdfa";
		echo "\" style=\"color: #FFFFFF;\">";
		echo "<strong>RDFa</strong>";
		echo "</a>";
		
////////////////////////////////// Download Turtle	

		echo " | ";
		echo "<a href=\"";
		echo "./data_download.php?dc_identifier=".$dc_identifier."&type=rdf&format=turtle";
		echo "\" style=\"color: #FFFFFF;\">";
		echo "<strong>Turtle</strong>";
		echo "</a>";	
		
////////////////////////////////// Download N-Triples	

		echo " | ";
		echo "<a href=\"";
		echo "./data_download.php?dc_identifier=".$dc_identifier."&type=rdf&format=ntriples";
		echo "\" style=\"color: #FFFFFF;\">";
		echo "<strong>N-Triples</strong>";
		echo "</a>";			
		
////////////////////////////////// Download CSV	

		echo " | ";
		echo "<a href=\"";
		echo "./data_download.php?dc_identifier=".$dc_identifier."&type=other&format=csv";
		echo "\" style=\"color: #FFFFFF;\">";
		echo "<strong>CSV</strong>";
		echo "</a>";						
		
////////////////////////////////// Close Panel		
		
		echo "</div>";

///////////////////////////////////////////////////////////// Finish If
		
	}
	
///////////////////////////////////////////////////////////// Scripts

?>
	<script language="javascript" type="text/javascript" >
	
		$(document).ready(function() {	
			if($(window).width() >= 992){
				var panZooms = $("#zoomDoc").panzoom();
				var $section = $('#focal');
				var $panzoom = $section.find('#zoomDoc').panzoom();
				$panzoom.panzoom("zoom", <?php 
								 
					if((!preg_match("/MSS/i","$this_col_skos_OrderedCollection"))) {
						echo "1.1";
					} else {
						echo "1.3";
					}			 
								 
				?>, { animate: true });
				$panzoom.parent().on('mousewheel.focal', function( e ) {
					e.preventDefault();
					var delta = e.delta || e.originalEvent.wheelDelta;
					var zoomOut = delta ? delta < 0 : e.originalEvent.deltaY > 0;
					$panzoom.panzoom('zoom', zoomOut, {
						increment: 0.025,
						animate: false,
						focal: e
					});
				});
				$("#detailsInsert").fadeIn("slow");
			}
		});
		
	</script>	
<?php

///////////////////////////////////////////////////////////// Functions

	function compress_image($source_url, $quality) {
		$info = getimagesize($source_url);
		if ($info['mime'] == 'image/jpeg') {
			$image = imagecreatefromjpeg($source_url); 
		} elseif ($info['mime'] == 'image/gif') {
			$image = imagecreatefromgif($source_url);
		} elseif ($info['mime'] == 'image/png') {
			$image = imagecreatefrompng($source_url);
		}
		return imagejpeg($image, NULL, $quality);
	}

///////////////////////////////////////////////////////////// Finish

	if(($IMGreload == "")) {
		include("./ar.dbdisconnect.php");
	}

?>