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
//	30 June 2017
//	7 July 2017
//  7 November 2018
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
		$dc_identifier = $_GET["data_dc_identifier"];
		$data_dc_description = $_GET["data_dc_description"];
		$scanTitle = $_GET["scanTitle"];
		$action = $_GET["action"];
		$itemTags = array();	
		$_GET = array();
		$_POST = array();
		$contributor = "contrib41T71U4BZZ";
	}
	$hosting = $_SERVER["HTTP_HOST"];

///////////////////////////////////////////////////////////// Do Scan Routine

	if(($action == "SCAN") && ($dc_identifier != "") && ($scanTitle != "")) {
		$imgFile = "data/items/".$scanTitle.".jpg";
		if(file_exists($imgFile)) {
			$api_key = 'AIzaSyB1CnX6aWH7wIFtltoA1PbwL8auFL2th1g';
			$url = "https://vision.googleapis.com/v1/images:annotate?key=" . $api_key;
//			$detection_type = "TEXT_DETECTION";
			$detection_type = "DOCUMENT_TEXT_DETECTION";
			$image = file_get_contents($imgFile);
			$image_base64 = base64_encode($image);
			$json_request ='{
				"requests": [
					{
					  "image": {
						"content":"' . $image_base64. '"
					  },
					  "features": [
						  {
							"type": "' .$detection_type. '",
							"maxResults": 200
						  }
					  ]
					}
				]
			}';
			$curl = curl_init();
			curl_setopt($curl, CURLOPT_URL, $url);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($curl, CURLOPT_HTTPHEADER, array("Content-type: application/json"));
			curl_setopt($curl, CURLOPT_POST, true);
			curl_setopt($curl, CURLOPT_POSTFIELDS, $json_request);
			$json_response = curl_exec($curl);
			$status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
			curl_close($curl);
			if($status != 200) {
				die("Something when wrong.<br />Status code: $status<br />&nbsp;<br />");
			} else {
				$response = json_decode($json_response, true);
				$data_dc_description = $response['responses'][0]['textAnnotations'][0]['description'];
				$action = "UPDATE";
			}
		} else {
			die("Something when wrong.<br />File does not appear to exist.<br />$dc_identifier<br />&nbsp;<br />");
		}
	}
	
///////////////////////////////////////////////////////////// Do Update Routine

	if(($action == "UPDATE") && ($dc_identifier != "") && ($data_dc_description != "")) {
		$data_dc_description = trim(preg_replace("/--/"," ", $data_dc_description));
		$data_dc_description = trim(preg_replace("/\^/"," ", $data_dc_description));
		$data_dc_description = trim(preg_replace("/\*/"," ", $data_dc_description));
		$data_dc_description = trim(preg_replace("/  /"," ", $data_dc_description));
		$data_dc_description = trim(preg_replace("/\"/","'", $data_dc_description));
//		if(($hosting == "localhost")) {
//			$data_dc_description = stripslashes($data_dc_description);
//		}
		$data_dc_description = trim(html_entity_decode($data_dc_description));	
		$data_dc_description = str_ireplace("<br />", "\r\n", $data_dc_description); 
		$queryText = "UPDATE items SET dc_description = \"$data_dc_description\" WHERE dc_identifier = \"$dc_identifier\"";
		$mysqli_resultText = mysqli_query($mysqli_link, $queryText);
		$new_iana_UUID = guidv4();
		$queryDee = "SELECT iana_UUID FROM items WHERE dc_identifier = \"$dc_identifier\" ";
		$mysqli_resultDee = mysqli_query($mysqli_link, $queryDee);
		while($rowDee = mysqli_fetch_row($mysqli_resultDee)) { 
			$old_iana_UUID = $rowDee[0];
		}
		$json_response = trim(preg_replace("/\"/","'", $json_response));
		$queryTextB = "INSERT INTO descriptions VALUES (";
		$queryTextB .= "\"0\", ";
		$queryTextB .= "\"$new_iana_UUID\", ";
		$queryTextB .= "\"$old_iana_UUID\", ";
		$queryTextB .= "\"$dc_identifier\", ";
		$queryTextB .= "\"$data_dc_description\", ";
		$queryTextB .= "\"$json_response\", ";
		$queryTextB .= "\"".time()."\"";
		$queryTextB .= ");";
		$mysqli_resultTextB = mysqli_query($mysqli_link, $queryTextB);
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

///////////////////////////////////////////////////////////// Show OCR	
	
	$tRows = ceil(substr_count($itemFields["dc_description"], "\n" ) * 1.1);
	$scanTitle = "";
	$scanTitle = preg_replace("/\_/i"," (",$itemFields["dc_title"]);
	$scanTitle = preg_replace("/\:/i"," (",$scanTitle);
	$scanTitle .= ")";
	$itemFields["dc_title"] = preg_replace("/\:/i","_",$itemFields["dc_title"]);
	echo "<strong>".$itemFields["dc_title"]."</strong><br /><br />";
	echo "<textarea ";
	if(($_SESSION["administrator"] == "yes")) {
		if(($status == "restricted")) {
			echo "readonly ";
		}
	} else {
		echo "readonly ";
	}
	echo "id=\"textarea-dc_description\" ";
	echo "name=\"textarea-dc_description\" ";
	echo "style=\"";
	echo "background-color: #eee; ";
	echo "outline: none; ";
	echo "resize: none; ";
	echo "overflow: scroll;";
	echo "overflow-y: scroll;";
	echo "overflow-x: hidden;";
	echo "overflow:-moz-scrollbars-vertical";
	echo "font-size: 0.8em; ";
	echo "width: 100%; ";
	echo "line-height: 1.2em; ";
	echo "padding: 10px 10px; ";
	echo "border: solid 1px #196961; ";
	echo "\" rows=\"$tRows\">";
	echo ltrim($itemFields["dc_description"], ".");
	echo "</textarea>";
	if(($_SESSION["administrator"] == "yes")) {
		if(($status != "restricted")) {
			$disableScan = "";
			echo "<button ";
			echo "class=\"btn btn-success col-sm-12 col-md-12 col-lg-12\" style=\"margin-top: 4px;\" ";
			echo "id=\"input_submit_text\">";
			echo "<strong>Save Text</strong>";
			echo "</button>";
			if(($itemFields["dc_description"] == "")) { 
				$disableScan = "btn-danger"; 
			} else {
				$disableScan = "btn-primary"; 
			}
			echo "<button ";
			echo "class=\"btn $disableScan col-sm-12 col-md-12 col-lg-12\" style=\"margin-top: 4px;\" ";
			echo "id=\"input_scan_text\">";
			echo "<strong>Recognise Text</strong>";
			echo "</button>";
            
///////////////////////////////////// Automatic Tagging Button            
            
            if(($itemFields["dc_description"] != "")) { 
                echo "<a href=\"javascript: ";
                echo "var dataF = 'dc_identifier=";
                echo $dc_identifier."&data_dc_identifier=";
                echo $dc_identifier."&autotag=yes&reload=';	";
                echo "var doDivM = $('#titleTags').fadeOut('fast', function(){ ";
                echo "var doDivN = $('#titleTags').load('./data_meta.php',dataF, function(){ ";
                echo "var doDivO = $('#titleTags').fadeIn('slow'); ";
                echo "}); ";
                echo "}); ";
                echo "\">";
                echo "<button ";
                echo "class=\"btn btn-danger col-sm-12 col-md-12 col-lg-12\" style=\"margin-top: 4px;\" ";
                echo "id=\"input_read_text\">";
                echo "<strong>Discover Tags</strong>";
                echo "</button>";
                echo "</a>";
                if(($tagMsg != "")) {
                    echo "<br />&nbsp;<br />";
                    echo "<div class=\"alert alert-dark\" role=\"alert\" style=\"text-align: center;\">";
                    echo "$tagMsg";
                    echo "</div>"; 
                }
            }
		}
	}

///////////////////////////////////// Post Page Load Scripts

?>    
	<script language="javascript" type="text/javascript" >

/////////////////////////////////////////////////////////// OnLoad Start
			
		$(document).ready(function() {
				
/////////////////////////////////////////////////////////// Submit Item Data

			$("#input_submit_text").click(function(event) {
				var data_dc_identifier = "<?php echo $dc_identifier; ?>";
				var data_dc_description = $("#textarea-dc_description").val();
				data_dc_description = data_dc_description.replace(/([^>\r\n]?)(\r\n|\n\r)/g,'<br />');
				data_dc_description = encodeURIComponent(data_dc_description);
				var dataAll = "action=UPDATE"
					+"&data_dc_description="+data_dc_description
					+"&data_dc_identifier="+data_dc_identifier;
				var doDivMa = $('#ocrContainer').fadeOut('fast', function(){
        			var doDivNa = $('#ocrContainer').load('./data_meta_ocr.php',dataAll, function(){
        				var doDivOa = $('#ocrContainer').fadeIn('slow');
        			});
        		});	
			});
			
			$("#input_scan_text").click(function(event) {
				var data_dc_identifier = "<?php echo $dc_identifier; ?>";
				var dataAll = "action=SCAN"
					+"&scanTitle=<?php echo $scanTitle; ?>"
					+"&data_dc_identifier="+data_dc_identifier;
				var doDivMa = $('#ocrContainer').fadeOut('fast', function(){
        			var doDivNa = $('#ocrContainer').load('./data_meta_ocr.php',dataAll, function(){
        				var doDivOa = $('#ocrContainer').fadeIn('slow');
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