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
//	8 November 2018
//  11-14 November 2018
//
//
/////////////////////////////////////////////////////////// Clean post and get       
        
    define('MyConstInclude', TRUE);
    $MerdUser = session_id();
    if(empty($MerdUser)) { session_start(); }
    $SIDmerd = session_id();
    header("Content-type: text/html;charset=UTF-8");
    mb_internal_encoding("UTF-8");
    include("./ar.config.php");
    include("./ar.dbconnect.php");
    include("./index_functions.php");
    $contributor = "contrib41T71U4BZZ";
    if (!mysqli_set_charset($mysqli_link, "utf8")) {
        echo "PROBLEM WITH CHARSET!";
        die;
    } 
    $_GET = array();
    $_POST = array();

/////////////////////////////////////////////////////////// Build page

    echo "<div ";
	echo "id=\"googlebooksGallery\" ";
	echo "class=\"googlebooksGallery\" ";
	echo "style=\"";
	echo "padding: 0px; ";
	echo "text-align: justify; ";
	echo "vertical-align: middle; ";
	echo "overflow-y: scroll; ";
	echo "overflow-x: hidden; ";
	echo "height: 90.8vh; ";
	echo "max-height: 90.8vh; ";
	echo "background-color: #c9d1d7; ";
	echo "width: 100%; ";
    echo "color: #000000; ";
	echo "\">";

/////////////////////////////////////////////////////////// Main Routines

    if(($_SESSION["administrator"] == "yes")) {
        
///////////////////////////////// Header
        
        echo "<div class=\"col-lg-12 col-md-12 panel-heading\">";
        echo "<h3 class=\"panel-title\"><strong>BACK OFFICE</strong></h3>";
        
///////////////////////////////// Left Column Open    
        
        echo "<div class=\"col-lg-6 col-md-6\" style=\"padding-left: 0px; padding-right: 0px;\">";

///////////////////////////////// Create Collection        
        
        echo "<div id=\"adminFunctions\" class=\"panel-body text-dark\" style=\"border-top: 1px solid #bbbbbb;\">";
        echo "<p><strong>CREATE COLLECTION</strong></p>";
        echo "<p>There are two parts to adding a new collection into Linked Archives. First, you must create the ";
        echo "collection record in the database using the button below and then you must upload images into that collection. For this first part, ";
        echo "you need textual details of the collection (such as institutional holder, collection title, manuscript ";
        echo "name, and box / volume, folder numbers).</p>";
        echo "<a href=\"./data_form_collection.php\" id=\"admin_collection_link\">";
        echo "<button ";
        echo "class=\"btn btn-danger col-sm-12 col-md-12 col-lg-12\" ";
        echo "style=\"margin-top: 1.0em; margin-bottom: 1.0em;\" ";
        echo "id=\"admin_collection\">";
        echo "<strong>Add Archive</strong>";
        echo "</button>";
        echo "</a>";
        echo "</div>";
        
///////////////////////////////// Discover Tags         
        
        echo "<div id=\"adminFunctions\" class=\"panel-body text-dark\" style=\"border-top: 0px solid #bbbbbb;\">";
        echo "<p><strong>DISCOVER TAGS</strong></p>";
        echo "<p>To automatically tag documents, Linked Archives uses Google Vision to apply optical character ";
        echo "recognition (OCR), with the results stored in the Linked Archives database. A 'discover tags' ";
        echo "routine is then run which compares the complete database of unique tags (which numbers in the ";
        echo "several thousand) against the OCR text. Any matches are recorded in the database for later ";
        echo "curating.</p>";
        echo "<p>Sometimes, however, a new tag is added into the system and it might be worthwhile to see ";
        echo "if this tag appears in items having already gone through the above described process. If you wish ";
        echo "therefore to rerun the 'discover tags' routine across the entire OCR'd collection and add new ";
        echo "matches only, please click on the button below. This may take several long minutes. While the ";
        echo "function is running, please do not navigate to another part of Linked Archives. ";
        echo "A result will appear beneath the button when the routine has completed.</p>";
        echo "<button ";
        echo "class=\"btn btn-success col-sm-12 col-md-12 col-lg-12\" ";
        echo "style=\"margin-top: 1.0em; margin-bottom: 2.0em;\" ";
        echo "id=\"admin_discover\">";
        echo "<strong>Review All OCR</strong>";
        echo "</button>";
        echo "<div id=\"admin_discover_results\" ";
        echo "class=\"col-lg-12 col-md-12\" ";
        echo "style=\"padding-left: 0px; padding-right: 0px;\">";
        echo "</div>";
        echo "</div>";
        
///////////////////////////////// Left Column Close        
        
        echo "</div>";

///////////////////////////////// Right Column Open        
        
        echo "<div class=\"col-lg-6 col-md-6\" style=\"padding-left: 0px; padding-right: 0px;\">";

///////////////////////////////// Upload Items        
        
        echo "<div id=\"adminFunctions\" class=\"panel-body text-dark\" style=\"border-top: 1px solid #bbbbbb;\">";
        echo "<p><strong>ADD ITEMS</strong></p>";
        echo "<p>This is the second part to adding a new collection into Linked Archives where you upload images into the ";
        echo "collection you just created. For this part, you need access to your local folder of photographs, ";
        echo "scanned or downloaded images.</p>";
        echo "<a href=\"./data_form_images.php\" id=\"admin_items_link\">";
        echo "<button ";
        echo "class=\"btn btn-info col-sm-12 col-md-12 col-lg-12\" ";
        echo "style=\"margin-top: 1.0em; margin-bottom: 1.0em;\" ";
        echo "id=\"admin_items\">";
        echo "<strong>Upload Images</strong>";
        echo "</button>";
        echo "</a>";
        echo "</div>";        
        
///////////////////////////////// Match Google Books        
        
        echo "<div id=\"adminFunctions\" class=\"panel-body text-dark\" style=\"border-top: 0px solid #bbbbbb;\">";
        echo "<p><strong>MATCH GOOGLE BOOKS</strong></p>";
        echo "<p>To be able to search for mentions of specific publications by choosing a book cover, Linked ";
        echo "Archives uses Google Books to match titles and obtain both author and synopsis details, with the ";
        echo "Google Books ID stored in the Linked Archives database. As new books are being discovered all the ";
        echo "time within the Linked Archives collections, this process is not 'live' but is a data-matching ";
        echo "function that is periodically run.</p>";
        echo "<p>To find if there are any new Google Books covers to add into ";
        echo "the system, please click on the button below. This may take several long minutes. While the ";
        echo "function is running, please do not navigate to another part of Linked Archives. ";
        echo "A result will appear beneath the button when the routine has completed.</p>";
        echo "<button ";
        echo "class=\"btn btn-primary col-sm-12 col-md-12 col-lg-12\" ";
        echo "style=\"margin-top: 1.0em; margin-bottom: 2.0em;\" ";
        echo "id=\"admin_google\">";
        echo "<strong>Find Book Covers</strong>";
        echo "</button>";
        echo "<div id=\"admin_google_results\" ";
        echo "class=\"col-lg-12 col-md-12\" ";
        echo "style=\"padding-left: 0px; padding-right: 0px;\">";
        echo "</div>";
        echo "</div>";
        
///////////////////////////////// Right Column Close        
        
        echo "</div>";

///////////////////////////////// Footer        
        
        echo "</div>\n\n";
        
///////////////////////////////////// Post Page Load Scripts

?>    
	<script language="javascript" type="text/javascript" >

/////////////////////////////////////////////////////////// OnLoad Start
			
		$(document).ready(function() {
            
///////////////////////////////// Create Collection
            
            $("#admin_collection_link").fancybox({
                type : 'iframe',
                autoScale : true,
                transitionIn : 'none',
                transitionOut : 'none',
                scrolling : 'yes',
                fitToView : true,
                width: '780px', 
				height: '870px',  
                autoSize : false
            });
            
///////////////////////////////// Add Items
            
            $("#admin_items_link").fancybox({
                type : 'iframe',
                autoScale : true,
                transitionIn : 'none',
                transitionOut : 'none',
                scrolling : 'yes',
                fitToView : true,
                width: '400px', 
				height: '870px',  
                autoSize : false
            });
            
///////////////////////////////// Discover Tags             
            
			$("#admin_discover").click(function(event) {
                $("#admin_discover").prop('disabled', true);
                var dataAll = "user=<?php echo $_SESSION["username"]; ?>";
				var doDivMa = $('#admin_discover_results').fadeOut('fast', function(){
        			var doDivNa = $('#admin_discover_results').load('./data_admin_discover.php',dataAll, function(){
        				var doDivOa = $('#admin_discover_results').fadeIn('slow');
        			});
        		});	
			});   
            
///////////////////////////////// Match Google Books             
            
			$("#admin_google").click(function(event) {
                $("#admin_google").prop('disabled', true);
                var dataAll = "user=<?php echo $_SESSION["username"]; ?>";
				var doDivMa = $('#admin_google_results').fadeOut('fast', function(){
        			var doDivNa = $('#admin_google_results').load('./data_admin_google.php',dataAll, function(){
        				var doDivOa = $('#admin_google_results').fadeIn('slow');
        			});
        		});	
			}); 
            
/////////////////////////////////////////////////////////// OnLoad Finish
				
		});
		
	</script>
<?php         
        
    }           

/////////////////////////////////////////////////////////// Prevent Direct Access

	if(!defined('MyConstInclude')) {
   		echo "Direct access not permitted.";
	}
        
/////////////////////////////////////////////////////////// Finish        
      
    echo "\n</div>\n";
    include("./ar.dbdisconnect.php");

/////////////////////////////////////////////////////////// Close

?>