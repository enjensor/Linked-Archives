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
//  13-14 November 2018
//
//
/////////////////////////////////////////////////////////// Clean post and get       
        
    $debug = "y";
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

/////////////////////////////////////////////////////////// Added Function

    function gen_md5_password($len = 12) {
		return substr(md5(rand().rand()), 0, $len);
	}

/////////////////////////////////////////////////////////// Start

    if(($_SESSION["administrator"] == "yes")) {
        
/////////////////////////////////////////////////////////// Get Last Empty Collection
        
        $found = "n";
        $queryD = "SELECT * FROM collections ORDER BY ID DESC LIMIT 1 ";
        $mysqli_resultD = mysqli_query($mysqli_link, $queryD);
        while($rowD = mysqli_fetch_row($mysqli_resultD)) { 
            $col_iana_UUID = $rowD[1];
            $col_dc_identifier = $rowD[2];
            $col_bf_heldBy = $rowD[3];
            $col_bf_subLocation = $rowD[4];
            $col_bf_physicalLocation = $rowD[5];
            $col_skos_collection = $rowD[6];
            $col_skos_orderedCollection = $rowD[7];
            $col_bibo_volume = $rowD[8];
            $col_disco_startDate = $rowD[9];
            $col_disco_endDate = $rowD[10];
            $col_dc_relation = $rowD[11];
            $found = "y";
        }
        $bibo = 0;
        if(($found == "y")) {
            $queryD = "SELECT COUNT(*) FROM items WHERE collections_dc_identifier =\"$col_dc_identifier\"; ";
            $mysqli_resultD = mysqli_query($mysqli_link, $queryD);
            while($rowD = mysqli_fetch_row($mysqli_resultD)) { 
                $bibo = $rowD[0];
            }
        }
        $bibo++;
 
/////////////////////////////////////////////////////////// Get File Vars        
        
//      $myFilename = $_FILES["file"]["name"];
        $myFilename = $col_dc_relation. " (".$bibo.").jpg";
        $myFilesize = $_FILES["file"]["size"];
        $myFiletype = $_FILES["file"]["type"];
        $myFiletemp = $_FILES["file"]["tmp_name"];
        if(($debug == "y")) {
            $txt = $found." - ".$myFilename."\n".$myFilesize."\n".$myFiletype."\n";
            $myfile = file_put_contents('./data/logs.txt', $txt.PHP_EOL , FILE_APPEND | LOCK_EX);
        }
        
/////////////////////////////////////////////////////////// Prepare Vars        
        
        $fileName = preg_replace("/ \(/i",":","$myFilename");
        $fileName = preg_replace("/\)/i","","$fileName");
        $fileName = strtoupper($fileName);
        $fileTitle = preg_replace("/\.jpg/i","","$fileName");
        $item_ID = "0";
        $item_iana_UUID = guidv4();
        $item_dc_identifier = time()."_".gen_md5_password();
        $item_col_iana_UUID = $col_iana_UUID;
        $item_col_dc_identifier = $col_dc_identifier;
        $item_dc_references = $col_dc_identifier;
        $item_dc_title = "$fileTitle";
        $item_bibo_pages = "$bibo";
        $item_dc_type = "image";
        $item_dc_format = "image/jpeg";
        $item_prism_byteCount = "$myFilesize";
        $item_rdf_resource = "$col_skos_orderedCollection"."/"."$col_skos_collection"." ";
        $item_rdf_resource .= "Vol ".$col_bibo_volume.", ".$col_disco_startDate."-";
        $item_rdf_resource .= $col_disco_endDate."/"."$myFilename";
        $item_rights_dc_identifer = "";
        $item_dc_creator = "";
        $item_org_FormalOrganisation = "";
        $item_gn_name = "";
        $item_dc_created = "";
        $item_dc_description = "";
        $item_dct_accessRights = "";
        $item_marc_addressee = "";
        $item_rdaa_groupMemberOf = "";
        $item_mads_associatedLocale = "";
        
/////////////////////////////////////////////////////////// If Collection Was Found	

        if(($found == "y")) {
            
/////////////////////////////////////////////////////////// Insert Record            
            
            $dbQuery = "INSERT INTO items VALUES ";
            $dbQuery .= "(";
            $dbQuery .= "$item_ID, ";
            $dbQuery .= "\"$item_iana_UUID\", ";
            $dbQuery .= "\"$item_dc_identifier\", ";
            $dbQuery .= "\"$item_col_iana_UUID\", ";
            $dbQuery .= "\"$item_col_dc_identifier\", ";
            $dbQuery .= "\"$item_dc_references\", ";
            $dbQuery .= "\"$item_dc_title\", ";
            $dbQuery .= "\"$item_bibo_pages\", ";
            $dbQuery .= "\"$item_dc_type\", ";
            $dbQuery .= "\"$item_dc_format\", ";
            $dbQuery .= "\"$item_prism_byteCount\", ";
            $dbQuery .= "\"$item_rdf_resource\", ";
            $dbQuery .= "\"$item_rights_dc_identifer\", ";
            $dbQuery .= "\"$item_dc_creator\", ";
            $dbQuery .= "\"$item_org_FormalOrganisation\", ";
            $dbQuery .= "\"$item_gn_name\", ";
            $dbQuery .= "\"$item_dc_created\", ";
            $dbQuery .= "\"$item_dc_description\", ";
            $dbQuery .= "\"$item_dct_accessRights\", ";
            $dbQuery .= "\"$item_marc_addressee\", ";
            $dbQuery .= "\"$item_rdaa_groupMemberOf\", ";
            $dbQuery .= "\"$item_mads_associatedLocale\"";
            $dbQuery .= "); ";      

/////////////////////////////////////////////////////////// Do Insert		
			
            $mysqli_result = mysqli_query($mysqli_link, $dbQuery);
            $scotty = mysqli_error($mysqli_link);
            if(($scotty)) { 
                if(($debug == "y")) {
                    $txt = "Could not add item \"$item_dc_title\" to the database\n\n";
                    $txt .= "$scotty\n";
                    $txt .= "$dbQuery\n\n"; 
                    $myfile = file_put_contents('./data/logs.txt', $txt.PHP_EOL , FILE_APPEND | LOCK_EX);
                }
            } 
        
/////////////////////////////////////////////////////////// Manage File
        
            require_once("./data_form_images_upload_handler.php");
            $ph = new PluploadHandler(array(
                'target_dir' => './data/items/',
                'allow_extensions' => 'jpg',
                'file_name' => "$myFilename"
            ));
            $ph->sendNoCacheHeaders();
            $ph->sendCORSHeaders();
            if ($result = $ph->handleUpload()) {
                die(json_encode(array(
                    'OK' => 1,
                    'info' => $result
                )));
            } else {
                die(json_encode(array(
                    'OK' => 0,
                    'error' => array(
                        'code' => $ph->getErrorCode(),
                        'message' => $ph->getErrorMessage()
                    )
                )));
            }
        }
    } else {
        die(json_encode(array(
            'OK' => 0,
            'error' => array(
                'code' => $ph->getErrorCode(),
                'message' => $ph->getErrorMessage()
            )
        )));
    }
        
/////////////////////////////////////////////////////////// Finish        

    $_GET = array();
    $_POST = array();
    include("./ar.dbdisconnect.php");

/////////////////////////////////////////////////////////// Close

?>