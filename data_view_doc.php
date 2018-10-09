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
//  23 June 2017
//	27-29 June 2017
//
//
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

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// Display Item
	
	if(($item_found == "y")) {
		
///////////////////////////////////////////////////////////// Start If		
		
		$file_parts = explode("/","$rdf_resource");
		$file_parts = array_reverse($file_parts);
		
///////////////////////////////////////////////////////////// Open Panel		
		
		echo "<div style=\"";
		echo "width:100%; ";
		echo "border-left: 0px solid #263238; ";
		echo "border-right: 0px solid #263238; ";
		echo "overflow: hidden; ";
//		echo "min-height: 90vh; ";
		echo "max-height: 89vh; ";
//		echo "background-color: #000000; ";
		echo "\" ";
		echo "id=\"focal\">";
		
///////////////////////////////////////////////////////////// Image		
		
		echo "<div ";
		echo "class=\"parent\" ";
		echo "id=\"imageLoader\" ";
		echo "style=\"";
//		echo "background-color: #000000; ";
//		echo "min-height: 90vh; ";
		echo "max-height: 89vh; ";
		echo "\">";
		echo "<img src=\"";
		echo "./data/items/".$file_parts[0];
		echo "\" width=\"100%\" ";
		echo "border=\"0\" ";
		echo "style=\"border: 0px solid #000000;\" ";
		echo "id=\"zoomDoc\">";
		echo "</div>";
			
///////////////////////////////////////////////////////////// Close Panel		
		
		echo "</div>";

///////////////////////////////////////////////////////////// Finish If
		
	}
	
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// Scripts

?>
	<script language="javascript" type="text/javascript" >
	
//		$(document).ready(function() {	
//			var panZooms = $("#zoomDoc").panzoom();
//			var $section = $('#focal');
//    		var $panzoom = $section.find('#zoomDoc').panzoom();
//			$panzoom.panzoom("zoom", 1.3, { animate: true });
//			$panzoom.parent().on('mousewheel.focal', function( e ) {
//            	e.preventDefault();
//            	var delta = e.delta || e.originalEvent.wheelDelta;
//            	var zoomOut = delta ? delta < 0 : e.originalEvent.deltaY > 0;
//            	$panzoom.panzoom('zoom', zoomOut, {
//            		increment: 0.025,
//           			animate: false,
//         			focal: e
//            	});
//    		});
//		});
		
	</script>	
<?php

///////////////////////////////////////////////////////////// Finish

?>