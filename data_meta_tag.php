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
//  15 January 2017
//  9 February 2017
//  14 February 2017
//  24 February 2017
//  27-28 February 2017
//  1-2 March 2017
//  14 March 2017
//  18 April 2017
//	2 May 2017
//	25 May 2017
//	8 August 2018
//	20 August 2018
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
		$dc_identifier = $_GET["dc_identifier"];
		$action = $_GET["action"];
		$iana_UUID = $_GET["iana_UUID"];
		$input_tag_value = $_GET["input_tag_value"];
		$input_tag_key = $_GET["input_tag_key"];
		$items_dc_identifier = $_GET["items_dc_identifier"];
		$items_UUID = $_GET["items_UUID"];
		$data_apply_tag_all = $_GET["data_apply_tag_all"];
		$itemTags = array();	
		$_GET = array();
		$_POST = array();
		$contributor = "contrib41T71U4BZZ";
	}
	
/////////////////////////////////////////////////////////// DELETE Function and AUDIT Function

	if(($reload == "")) {
		if(($action == "DELETE") && ($dc_identifier != "") && ($iana_UUID != "") && ($_SESSION["administrator"] == "yes")) {
			$queryD = "INSERT INTO audit SELECT * FROM annotations WHERE iana_UUID = \"$iana_UUID\" ;";
			$mysqli_resultD = mysqli_query($mysqli_link, $queryD);
			$queryD = "DELETE FROM annotations WHERE iana_UUID = \"$iana_UUID\" ";
			$mysqli_resultD = mysqli_query($mysqli_link, $queryD);
		}
	}

/////////////////////////////////////////////////////////// ADD Function Individual
	
	if(($reload == "")) {
		if(($action == "ADD") && ($input_tag_value != "") && ($input_tag_key != "") && ($items_dc_identifier != "") && ($items_UUID != "") && ($_SESSION["administrator"] == "yes")) {
			$new_iana_UUID = guidv4();
			$queryW = "SELECT * FROM items WHERE dc_identifier = \"$items_dc_identifier\" ";
			$mysqli_resultW = mysqli_query($mysqli_link, $queryW);
			while($rowW = mysqli_fetch_row($mysqli_resultW)) {
				$items_UUID = $rowW[1];
			}
			$keys = explode(":","$input_tag_key");
			$queryD = "INSERT INTO annotations VALUES (";
			$queryD .= "\"0\", ";
			$queryD .= "\"$new_iana_UUID\", ";
			$queryD .= "\"$items_UUID\", ";
			$queryD .= "\"$items_dc_identifier\", ";
			$queryD .= "\"$items_dc_identifier\", ";
			$queryD .= "\"".$keys[0]."\", ";
			$queryD .= "\"".$keys[1]."\", ";
			$queryD .= "\"$input_tag_value\", ";
			$queryD .= "\"\", ";
			$queryD .= "\"\", ";
			$queryD .= "\"$contributor\", ";
			$queryD .= "NOW() ";
			$queryD .= ");";
			$mysqli_resultD = mysqli_query($mysqli_link, $queryD);

/////////////////////////////////////////////////////////// ADD Function Multiple
			
			if(($data_apply_tag_all == "ALL") && ($_SESSION["administrator"] == "yes")) {
				$queryW = "SELECT * FROM items WHERE dc_identifier = \"$items_dc_identifier\" ";
				$mysqli_resultW = mysqli_query($mysqli_link, $queryW);
				while($rowW = mysqli_fetch_row($mysqli_resultW)) {
					$collections_dc_identifier = $rowW[4];
				}
				$queryW = "SELECT * FROM items WHERE collections_dc_identifier = \"$collections_dc_identifier\" AND dc_identifier != \"$items_dc_identifier\" ";
				$mysqli_resultW = mysqli_query($mysqli_link, $queryW);
				while($rowW = mysqli_fetch_row($mysqli_resultW)) {
					$new_items_UUID = $rowW[1];
					$new_items_dc_identifier = $rowW[2];
					$new_iana_UUID = guidv4();
					$characters = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
					$iana = "";
					for ($i = 0; $i < 12; $i++) {
						$iana .= $characters[mt_rand(0, 36)];
					}
					$notFound = "";
					$queryCheck = "SELECT * FROM annotations WHERE ";
					$queryCheck .= "items_dc_identifier = \"".$new_items_dc_identifier."\" AND ";
					$queryCheck .= "reg_uri = \"".$keys[0]."\" AND ";
					$queryCheck .= "rdfs_label = \"".$keys[1]."\" ";
					$mysqli_resultCheck = mysqli_query($mysqli_link, $queryCheck);
					while($rowCheck = mysqli_fetch_row($mysqli_resultCheck)) {
						$notFound = "false";
					}
					if(($notFound == "")) {
						$queryD = "INSERT INTO annotations VALUES (";
						$queryD .= "\"0\", ";
						$queryD .= "\"$new_iana_UUID\", ";
						$queryD .= "\"$new_items_UUID\", ";
						$queryD .= "\"$new_items_dc_identifier\", ";
						$queryD .= "\"$new_items_dc_identifier\", ";
						$queryD .= "\"".$keys[0]."\", ";
						$queryD .= "\"".$keys[1]."\", ";
						$queryD .= "\"$input_tag_value\", ";
						$queryD .= "\"\", ";
						$queryD .= "\"\", ";
						$queryD .= "\"$contributor\", ";
						$queryD .= "NOW() ";
						$queryD .= ");";
						$mysqli_resultD = mysqli_query($mysqli_link, $queryD);
					}
				}
			}		
		}
	}
	
/////////////////////////////////////////////////////////// Get	

	if(($reload == "")) {								
		$queryD = "SELECT * FROM annotations WHERE dc_references = \"$dc_identifier\" ";
		$mysqli_resultD = mysqli_query($mysqli_link, $queryD);
		while($rowD = mysqli_fetch_row($mysqli_resultD)) { 
			$itemTags[] = $rowD[7]."|".$rowD[5]."|".$rowD[6]."|".$rowD[1]."|".$rowD[0];
		}
		sort($itemTags);
		$itemFields["dc_title"] = preg_replace("/\:/i","_",$itemFields["dc_title"]);
	}
	
///////////////////////////////////////////////////////////// Get Metadata

	if(($reload == "")) {
		$queryD = "SHOW COLUMNS FROM items";
		$mysqli_resultD = mysqli_query($mysqli_link, $queryD);
		while($rowD = mysqli_fetch_row($mysqli_resultD)) {
			$fields[] = $rowD[0];
		}
		$queryD = "SELECT * FROM items WHERE dc_identifier = \"$dc_identifier\" ";
		$mysqli_resultD = mysqli_query($mysqli_link, $queryD);
		while($rowD = mysqli_fetch_row($mysqli_resultD)) { 
			foreach($fields as $f) {
				$itemFields["$f"] = $rowD[$a];
				$a++;
			}
			$item_found = "y";
		}	
		if(($_SESSION["administrator"] != "yes")) {
			$itemFields["dct_accessRights"] = "restricted";
		}
	}

///////////////////////////////////////////////////////////// List Existing tags	
	
?>
	<table class="table table-striped table-condensed table-hover" width="99%">
		<tbody>
        <?php
								
			foreach($itemTags as $iTz) {
				$bitz = explode("|","$iTz");
				echo "<tr id=\"row_".$bitz[3]."\">";
				echo "<td style=\"padding:18px;font-size:0.9em;word-wrap:normal;word-wrap:break-word;display:inline-block;\" width=\"";
				if(($itemFields["dct_accessRights"] != "restricted")) {
					echo "47%";
				} else {
					echo "50%";	
				}
				echo "\">";
				echo $bitz[1];
				echo " : ";
				echo $bitz[2];
				echo "</td>";
				echo "<td style=\"padding:15px; font-size: 0.9em; word-wrap: normal;word-wrap:break-word; display:inline-block;\" width=\"";
				if(($itemFields["dct_accessRights"] != "restricted")) {
					echo "47%";
				} else {
					echo "50%";	
				}
				echo "\">";
				echo $bitz[0];
				echo "</td>";
				if(($itemFields["dct_accessRights"] != "restricted")) {
					$q++;
					echo "<td style=\"padding:15px;font-size:0.9em; text-align: right;\" width=\"6%\" nowrap>";

//////////////////////////////// Delete Mention					
					
					echo "<a href=\"javascript: ";
					echo "$.confirm({";
					echo "title: 'Delete ...',";
					echo "content: 'Are you sure you want to delete this mention?',";
					echo "buttons: {";
					echo "confirm: function () {";
					echo "var tabH = $('#row_".$bitz[3]."').height(); ";
					echo "var divH = $('#refreshCustomMeta').height()-tabH; ";
					echo "var divHa = $('#refreshCustomMeta').height(); ";
					echo "var divHb = $('#refreshCustomMeta').height(divHa); ";
					echo "var dataFa = 'dc_identifier=".$dc_identifier."&reload=";
					echo "&action=DELETE";
					echo "&iana_UUID=".$bitz[3]."'; ";
					echo "var doDivMa = $('#refreshCustomMetaMain').fadeOut('fast', function(){ ";
					echo "var doDivNa = $('#refreshCustomMetaMain').load('./data_meta_tag.php',dataFa, function(){ ";
					echo "var doDivOa = $('#refreshCustomMetaMain').fadeIn('fast'); ";		
					echo "var divHb = $('#refreshCustomMeta').height(divH); ";
					echo "}); ";
					echo "}); ";
					echo "},";
					echo "cancel: function () {}";
					echo "}";
					echo "});";
					echo "\" style=\"color: #990000;\" alt=\"DELETE\">";
					echo "<i class=\"ti-close\"></i>";
					echo "</a>";
					echo "&nbsp;&nbsp;&nbsp;&nbsp;";

//////////////////////////////// Edit Mention					
					
					echo "<strong>";
					echo "<a id=\"editMention_".$q."\" href=\"";
					echo "./data_meta_edit.php";
					echo "?dc_identifier=".$dc_identifier;
					echo "&reload=";
					echo "&action=EDIT";
					echo "&iana_UUID=".$bitz[3];
					echo "&items_dc_identifier=".$itemFields["dc_identifier"];
					echo "&items_UUID=".$itemFields["iana_UUID"];
					echo "\" style=\"color: #000080; text-decoration: none;\">";
					echo "<i class=\"ti-pencil-alt\"></i>";
					echo "</a>";
					echo "</strong>";
					echo "&nbsp;&nbsp;&nbsp;&nbsp;";
					
//////////////////////////////// Edit Related Concepts			
			
					echo "<strong>";
					echo "<a id=\"relatedMention_".$q."\" href=\"";
					echo "./data_meta_related.php";
					echo "?reload=";
					echo "&action=EDIT";
					echo "&annotations_reg_uri=".$bitz[1];
					echo "&annotations_rdfs_label=".$bitz[2];
					echo "&annotations_value_string=".$bitz[0];
					echo "\" style=\"color: #000080; text-decoration: none;\">";
					echo "<i class=\"ti-sharethis\" style=\"font-weight: 900; color: #1B746C;\"></i>";
					echo "</a>";					
					echo "</strong>";		

//////////////////////////////// Close Links					
					
					echo "<br />&nbsp;";
					echo "</td>";
				}
				echo "</tr>";
			}
								
        ?>
 		</tbody>
	</table>
<?php

///////////////////////////////////////////////////////////// List Previous Mentions

	if(($itemFields["dct_accessRights"] != "restricted")) {
		echo "<br />"; 
		echo "<p style=\"text-align: center; font-weight: 900;\">Previous Item's Mentions<br />(Click value to add)</p>";
		echo "<table class=\"table table-condensed\" width=\"99%\" border=\"0\">";
		echo "<tbody>"; 

///////////////////////////////////// Start Previous Item Mentions Table                       
                                        
		echo "<tr>";
		echo "<td style=\"border-right: 1px solid #ffffff; color: #FFFFFF; background-color: #888888; padding: 11px; ";
		echo "font-size: 0.9em; font-weight: 900;text-align:right;\" width=\"23%\" nowrap>Key or Label</td>";
		echo "<td style=\"color: #FFFFFF; background-color: #888888; padding: 9px; ";
		echo "font-size: 0.9em; font-weight: 900;\" width=\"100%\">Value</td>";
		echo "</tr>";

/////////////////// Loop Mentions
		
		$foundPriorMentions = "";
		$old_bibo_pages = "";
		$collections_dc_identifier = "";
		$new_bibo_pages = "";
		$prior_dc_identifier = "";
		$skos_definition = "";
		$mentions_uri = array();
		$mentions_label = array();
		$mentions_value = array();
		$g = 1;
		$queryD = "SELECT * FROM items WHERE dc_identifier = \"$dc_identifier\" ";
		$mysqli_resultD = mysqli_query($mysqli_link, $queryD);
		while($rowD = mysqli_fetch_row($mysqli_resultD)) {
			$old_bibo_pages = $rowD[7];
			$collections_dc_identifier = $rowD[4];
		}
		$new_bibo_pages = ($old_bibo_pages - 1);
		if(($new_bibo_pages > 0) && ($collections_dc_identifier != "")) {
			$queryPrior = "SELECT * FROM items WHERE collections_dc_identifier = \"$collections_dc_identifier\" AND bibo_pages = \"$new_bibo_pages\" ";
			$mysqli_resultPrior = mysqli_query($mysqli_link, $queryPrior);
			while($rowP = mysqli_fetch_row($mysqli_resultPrior)) {
				$prior_dc_identifier = $rowP[2];
			}
			if(($prior_dc_identifier != "")) {
				$queryPrior = "SELECT * FROM annotations WHERE dc_references = \"$prior_dc_identifier\" ORDER BY reg_uri ASC";
				$mysqli_resultPrior = mysqli_query($mysqli_link, $queryPrior);
				while($rowP = mysqli_fetch_row($mysqli_resultPrior)) { 	
					echo "<tr>";
					echo "<td style=\"border-bottom: 1px solid #ffffff; border-right: 1px solid #ffffff; background-color: #f2f4f6; ";
					$queryZ = "SELECT skos_definition FROM labels WHERE reg_uri = \"$rowP[5]\" AND rdfs_label = \"$rowP[6]\" ";
					$mysqli_resultZ = mysqli_query($mysqli_link, $queryZ);
					while($rowZ = mysqli_fetch_row($mysqli_resultZ)) { 
						$skos_definition = $rowZ[0];
					}
					echo "font-size: 0.9em; text-align:right; border-top: 0px solid #768697; padding: 9px;\" width=\"23%\">$skos_definition</td>";
					echo "<td style=\"border-bottom: 1px solid #ffffff; background-color: #f2f4f6; font-size: 0.9em; ";
					echo "border-top: 0px solid #768697; width: 100%; padding: 9px;\">";
					echo "<a href=\"#\" style=\"color: #800000;\" id=\"submit_prior_mentions_".$g."\">";
					echo "$rowP[7]";
					echo "</a>";
					echo "</td>";
					echo "</tr>";
					$foundPriorMentions = "y";
					$mentions_uri["$g"] = $rowP[5];
					$mentions_label["$g"] = $rowP[6];
					$mentions_value["$g"] = $rowP[7];
					$g++;							
				}
			}
		}
	
/////////////////// No Previous Mentions
										
		if(($foundPriorMentions == "")) {
			echo "<tr>";
			echo "<td style=\"border-bottom: 1px solid #ffffff; border-right: 1px solid #ffffff; background-color: #f2f4f6; ";
			echo "font-size: 0.9em; text-align:right; border-top: 0px solid #768697; padding: 9px;\" colspan=\"2\">No previous information.</td>";
			echo "</tr>";
		}	

///////////////////////////////////// Close Previous Item-Level MetaData Table 
										
		echo "</tbody>";
		echo "</table>\n"; 

///////////////////////////////////// Copy and Save ALL Previous MetaData

		if(($foundPriorMentions != "") && ($prior_dc_identifier != "")) {
			$mentions_prior_dc_identifier = $prior_dc_identifier;
			echo "<button class=\"btn btn-info col-lg-12\" id=\"submit_prior_mentions_btn\"><strong>Click to Copy and Save<br />All Previous Item's Mentions</strong></button>";
		}
	}

///////////////////////////////////// Post Page Load Scripts

?>    
	<script language="javascript" type="text/javascript" >

/////////////////////////////////////////////////////////// OnLoad Start
			
		$(document).ready(function() {
				
/////////////////////////////////////////////////////////// JQuery fancybox popups
			
			<?php for($w=1;$w<$q+1;$w++) { ?>
			
			$("#editMention_<?php echo $w; ?>").fancybox({
				type : 'iframe', 
				autoScale : false,
				transitionIn : 'none',
				transitionOut : 'none',
				scrolling : 'yes',
				fitToView : false,
				autoSize : false,
				width: '780px', 
				height: '690px'
			});	
			
			$("#relatedMention_<?php echo $w; ?>").fancybox({
				type : 'iframe', 
				autoScale : false,
				transitionIn : 'none',
				transitionOut : 'none',
				scrolling : 'yes',
				fitToView : false,
				autoSize : false,
				width: '880px', 
				height: '770px'
			});
			
			<?php } ?>	
	
/////////////////////////////////////////////////////////// Submit Prior Mention and Refresh Mentions Panel
		
			<?php for($w=1;$w<$g+1;$w++) { ?>	
		
			$("#submit_prior_mentions_<?php echo $w; ?>").click(function(event) {
				var dataFa = "mentions_dc_identifier=<?php 
					echo $dc_identifier; ?>&reload=&mentions_action=SAVE_MENTION&mentions_uri=<?php 
					echo $mentions_uri["$w"]; ?>&mentions_label=<?php 
					echo $mentions_label["$w"]; ?>&mentions_value=<?php
					echo $mentions_value["$w"]; ?>&mentions_prior_dc_identifier=<?php
					echo $mentions_prior_dc_identifier; ?>";
				var doDivMa = $('#titleTags').fadeOut('fast', function(){
        			var doDivNa = $('#titleTags').load('./data_meta.php',dataFa, function(){
        				var doDivOa = $('#titleTags').fadeIn('slow');
        			});
        		});	
			});	
			
			<?php } ?>
			
/////////////////////////////////////////////////////////// Submit Prior Multiple Mentions and Refresh Mentions Panel
			
			$("#submit_prior_mentions_btn").click(function(event) {
				var dataFa = "mentions_dc_identifier=<?php 
					echo $dc_identifier; ?>&reload=&mentions_action=SAVE_MULTIPLE_MENTIONS&mentions_prior_dc_identifier=<?php
					echo $mentions_prior_dc_identifier; ?>";
				var doDivMa = $('#titleTags').fadeOut('fast', function(){
        			var doDivNa = $('#titleTags').load('./data_meta.php',dataFa, function(){
        				var doDivOa = $('#titleTags').fadeIn('slow');
        			});
        		});	
			});	
	
/////////////////////////////////////////////////////////// OnLoad Finish
				
		});
		
	</script>
<?php

///////////////////////////////////////////////////////////// Finish

	if(($reload == "")) {
		include("./ar.dbdisconnect.php");
	}
	
?>