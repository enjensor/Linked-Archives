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
//  21 February 2017
//	4-5 June 2017
//	6-7 July 2017
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
	if (!mysqli_set_charset($mysqli_link, "utf8")) {
		echo "PROBLEM WITH CHARSET!";
		die;
	} 
	$_GET = array();
	$_POST = array();
	$contributor = "contrib41T71U4BZZ";
	$msg = "START<br />";
	$doPrior = "n";
	
/////////////////////////////////////////////////////////// Do Google	
	
	$truncateBooks = "never";
	$doBooks = "not now";
	$doImages = "not now";
	
/////////////////////////////////////////////////////////// Do OCR import

	$doOCR = "not now";

/////////////////////////////////////////////////////////// Do Acronyms

	$doAcronyms = "not now";

/////////////////////////////////////////////////////////// Acronyms

	if(($doAcronyms == "yes")){
		$docs_acronym = "";
		$queryD = "SELECT * FROM collections ";
//		$queryD .= "WHERE skos_orderedCollection ";
//		$queryD .= "LIKE \"%MS%\" ";
		$queryD .= "ORDER BY ID ASC";
		$mysqli_resultD = mysqli_query($mysqli_link, $queryD);
		while($rowD = mysqli_fetch_row($mysqli_resultD)) { 
			$dc_identifier = $rowD[2];
			$queryDXi = "SELECT dc_title FROM items WHERE ";
			$queryDXi .= "collections_dc_identifier = \"$dc_identifier\" ";
			$queryDXi .= "ORDER BY dc_title ASC LIMIT 1 ";
			$mysqli_resultX = mysqli_query($mysqli_link, $queryDXi);
			while($rowX = mysqli_fetch_row($mysqli_resultX)) { 
				$temp_acronym = $rowX[0];
				$temp_acronyms = explode(":",$temp_acronym);
				$docs_acronym = $temp_acronyms[0];
			}
			$newQ = "UPDATE collections SET dc_relation = \"".$docs_acronym."\" WHERE ID = \"".$rowD[0]."\"";
			$newQresult = mysqli_query($mysqli_link, $newQ);
			echo $newQ."<br />";
		}
	}
	
/////////////////////////////////////////////////////////// OCR Import Routines

	if(($doOCR == "yes")) {
		function listFolderFiles($dir){
			$ffs = scandir($dir);
			unset($ffs[array_search('.', $ffs, true)]);
			unset($ffs[array_search('..', $ffs, true)]);
			if (count($ffs) < 1) { return; }
			foreach($ffs as $ff) {
				if(preg_match("/txt/i","$ff")) {
					$tmps = explode(" - ","$ff");
					$tmps[1] = preg_replace("/\.txt/","",$tmps[1]);
					$tmps[1] = ltrim($tmps[1], '0');
					$tmp_ff = $tmps[0].":".$tmps[1];
					$tmp_dir = $dir."/".$ff;
					$file = file_get_contents($tmp_dir, true);
					$file = trim(preg_replace('/[^a-zA-Z0-9\s\n\r\-=+\|!@#$%^&*()`~\[\]{};:\'",.\/?]/', ' ', $file));
					$file = trim(preg_replace("/\r\n/","\n", $file));
					$file = trim(preg_replace("/\r/","\n", $file));
					$file = trim(preg_replace("/\n /","\n", $file));
					$file = trim(preg_replace('/(\r\n|\n|\r){3,}/', "$1$1", $file));
					$file = trim(preg_replace('/[ \t]+/', ' ', $file));
					$file = trim(preg_replace("/  /"," ", $file));
					$file = trim(preg_replace("/\"/","'", $file));
					$fileParts = explode("\n","$file");
					$file = "";
					foreach($fileParts as $fP) {
						$fP = trim(preg_replace("/\s+/"," ", $fP));
						$file .= $fP."\n";
					}
					$file = htmlspecialchars($file);
					if(($tmp_ff != "")) {
						$queryD = "UPDATE items SET dc_description = \".$file.\" WHERE dc_title = \"$tmp_ff\"";
						$mysqli_resultD = mysqli_query($mysqli_link, $queryD);
						echo $queryD.";\n\n";
					}
				}
				if(is_dir($dir.'/'.$ff)) listFolderFiles($dir.'/'.$ff);
			}
		}
		listFolderFiles('./data/ocr');	
	}
	
/////////////////////////////////////////////////////////// Find Google Books

	if(($doBooks == "yes")) {
		echo $msg;
		$g = 0;
		$h = 0;
		if(($truncateBooks == "y")) {
			$queryD = "TRUNCATE TABLE datasource_googlebooks";
			$mysqli_resultD = mysqli_query($mysqli_link, $queryD);
		}
		$queryD = "SELECT DISTINCT(value_string) ";
		$queryD .= "FROM annotations ";
		$queryD .= "WHERE rdfs_label = \"book\" ";
		$queryD .= "GROUP BY value_string ";
		$queryD .= "ORDER BY value_string ASC";
		$mysqli_resultD = mysqli_query($mysqli_link, $queryD);
		while($rowD = mysqli_fetch_row($mysqli_resultD)) { 
			$h++;
			$found = "n";
			$queryView = "SELECT * ";
			$queryView .= "FROM datasource_googlebooks ";
			$queryView .= "WHERE value_string = \"rowD[0]\"; ";
			$mysqli_resultView = mysqli_query($mysqli_link, $queryView);
			while($rowView = mysqli_fetch_row($mysqli_resultView)) { 
				$found = "y";
			}
			if(($found == "n")) {
				$url = "https://www.googleapis.com/books/v1/volumes?q=";
				$url .= $rowD[0];
				$url .= "&maxResults=1";
				$url .= "&printType=books";
				$url .= "&fields=kind,items(id,etag,selfLink,volumeInfo(title,authors,description,imageLinks(smallThumbnail,thumbnail)))";
				$url = preg_replace("/ /","+","$url");
				$json = file_get_contents($url);
				$book_data = json_decode($json);
				$google_kind = $book_data->kind;
				$google_id = $book_data->items[0]->id;
				$google_etag = $book_data->items[0]->etag;
				$google_selfLink = $book_data->items[0]->selfLink;
				$volumeInfo_title = $book_data->items[0]->volumeInfo->title;
				$volumeInfo_authors = $book_data->items[0]->volumeInfo->authors[0];
				$volumeInfo_description = $book_data->items[0]->volumeInfo->description;
				$volumeInfo_smallThumbnail = $book_data->items[0]->volumeInfo->imageLinks->smallThumbnail;
				$volumeInfo_thumbnail = $book_data->items[0]->volumeInfo->imageLinks->thumbnail;
				$volumeInfo_description = preg_replace("/\"/","'","$volumeInfo_description");
				$value_string = $rowD[0];
				$newQuery = "INSERT INTO datasource_googlebooks ";
				$newQuery .= "VALUES (0, ";
				$newQuery .= "\"$google_kind\", ";
				$newQuery .= "\"$google_id\", ";
				$newQuery .= "\"$google_etag\", ";
				$newQuery .= "\"$google_selfLink\", ";
				$newQuery .= "\"$volumeInfo_title\", ";
				$newQuery .= "\"$volumeInfo_authors\", ";
				$newQuery .= "\"$volumeInfo_description\", ";
				$newQuery .= "\"$volumeInfo_smallThumbnail\", ";
				$newQuery .= "\"$volumeInfo_thumbnail\", ";
				$newQuery .= "\"$value_string\", ";
				$newQuery .= "\"\");";
				if(($google_id != "")) {
					$mysqli_resultNew = mysqli_query($mysqli_link, $newQuery);
					$g++;
				}
			}
		}
		echo "Finished $g googlebooks to $h records";
	}
	
/////////////////////////////////////////////////////////// Get Google Book Images

	if(($doImages == "yes")) {
		$i = 0;
		$queryD = "SELECT volumeInfo_thumbnail, ";
		$queryD .= "value_string ";
		$queryD .= "FROM datasource_googlebooks ";
		$queryD .= "WHERE volumeInfo_thumbnail != \"\" ";
		$queryD .= "ORDER BY volumeInfo_thumbnail ASC";
		$mysqli_resultD = mysqli_query($mysqli_link, $queryD);
		while($rowD = mysqli_fetch_row($mysqli_resultD)) {
			$url = $rowD[0];
			$name = preg_replace("/ /","_","$rowD[1]");
			$save = "./img_googlebooks/".$name.".jpg";
			if(!file_exists($save)) {
				$content = file_get_contents($url);
				$fp = fopen("$save", "w");
				fwrite($fp, $content);
				fclose($fp);
				$i++;
			}
		}
		echo "<br />$i Images Done!";
		die;
	}
	
/////////////////////////////////////////////////////////// Clean archival cover pages

	if(($doPrior == "yes")) {
		$queryD = "UPDATE annotations SET ";
		$queryD .= "value_string = \"cover page\", ";
		$queryD .= "rdfs_label = \"type\", ";
		$queryD .= "reg_uri = \"rdfa\" ";
		$queryD .= "WHERE value_string = \"document, archival cover\" ";
		$mysqli_resultD = mysqli_query($mysqli_link, $queryD);
		$msg .= "done ... document, archival cover<br />";
	}
	
/////////////////////////////////////////////////////////// Clean locations
	
	if(($doPrior == "yes")) {
		$queryD = "UPDATE annotations SET ";
		$queryD .= "value_string = \"Addis Ababa (Ethiopia)\", ";
		$queryD .= "rdfs_label = \"geographicNote\", ";
		$queryD .= "reg_uri = \"cerl\" ";
		$queryD .= "WHERE value_string = \"location, Addis Ababa\" ";
		$mysqli_resultD = mysqli_query($mysqli_link, $queryD);
		$msg .= "done ... location, Addis Ababa<br />";
	
		$queryD = "UPDATE annotations SET ";
		$queryD .= "value_string = \"Australia\", ";
		$queryD .= "rdfs_label = \"geographicNote\", ";
		$queryD .= "reg_uri = \"cerl\" ";
		$queryD .= "WHERE value_string = \"location, Australia\" ";
		$mysqli_resultD = mysqli_query($mysqli_link, $queryD);
		$msg .= "done ... location, Australia<br />";
		
		$queryD = "UPDATE annotations SET ";
		$queryD .= "value_string = \"Canada\", ";
		$queryD .= "rdfs_label = \"geographicNote\", ";
		$queryD .= "reg_uri = \"cerl\" ";
		$queryD .= "WHERE value_string = \"location, Canada\" ";
		$mysqli_resultD = mysqli_query($mysqli_link, $queryD);
		$msg .= "done ... location, Canada<br />";
		
		$queryD = "UPDATE annotations SET ";
		$queryD .= "value_string = \"China\", ";
		$queryD .= "rdfs_label = \"geographicNote\", ";
		$queryD .= "reg_uri = \"cerl\" ";
		$queryD .= "WHERE value_string = \"location, China\" ";
		$mysqli_resultD = mysqli_query($mysqli_link, $queryD);
		$msg .= "done ... location, China<br />";
	}
	
/////////////////////////////////////////////////////////// Clean author names

	if(($doPrior == "yes")) {
		$queryD = "SELECT * FROM annotations ";
		$queryD .= "WHERE value_string LIKE \"author,%\" ";
		$queryD .= "ORDER BY value_string ASC";	
		$mysqli_resultD = mysqli_query($mysqli_link, $queryD);
		while($rowD = mysqli_fetch_row($mysqli_resultD)) { 
		
			$ID = $rowD[0];
			$iana_UUID	 = $rowD[1];
			$items_iana_UUID = $rowD[2];
			$items_dc_identifier = $rowD[3];
			$dc_referencesIndex = $rowD[4];
			$reg_uri = $rowD[5];
			$rdfs_label = $rowD[6];
			$value_string = $rowD[7];
			$value_uri = $rowD[8];
			$resource_uri	 = $rowD[9];
			$dct_contributor = $rowD[10];
			$dc_created = $rowD[11];
			
			$value_string = preg_replace("/author, /i", "", "$value_string");
			$queryE = "UPDATE annotations SET ";
			$queryE .= "value_string = \"$value_string\", ";
			$queryE .= "reg_uri = \"rdaw\", ";
			$queryE .= "rdfs_label = \"author\" ";
			$queryE .= "WHERE ID = \"$ID\" ";
			$mysqli_resultE = mysqli_query($mysqli_link, $queryE);
		}
		$msg .= "done ... author(s)<br />";
	}

/////////////////////////////////////////////////////////// (RE)clean author names
	
	if(($doPrior == "yes")) {
		$queryD = "SELECT * FROM annotations ";
		$queryD .= "WHERE rdfs_label = \"author\" ";
		$queryD .= "ORDER BY value_string ASC";	
		$mysqli_resultD = mysqli_query($mysqli_link, $queryD);
		while($rowD = mysqli_fetch_row($mysqli_resultD)) { 
		
			$ID = $rowD[0];
			$iana_UUID	 = $rowD[1];
			$items_iana_UUID = $rowD[2];
			$items_dc_identifier = $rowD[3];
			$dc_referencesIndex = $rowD[4];
			$reg_uri = $rowD[5];
			$rdfs_label = $rowD[6];
			$value_string = $rowD[7];
			$value_uri = $rowD[8];
			$resource_uri	 = $rowD[9];
			$dct_contributor = $rowD[10];
			$dc_created = $rowD[11];
		
			$queryE = "UPDATE annotations SET ";
			$queryE .= "value_string = \"$value_string\", ";
			$queryE .= "reg_uri = \"foaf\", ";
			$queryE .= "rdfs_label = \"person\" ";
			$queryE .= "WHERE ID = \"$ID\" ";
			$mysqli_resultE = mysqli_query($mysqli_link, $queryE);	
			
		}
		$msg .= "done ... author to foaf<br />";
	}
	
/////////////////////////////////////////////////////////// Clean organisations

	if(($doPrior == "yes")) {
		$queryD = "SELECT * FROM annotations ";
		$queryD .= "WHERE value_string LIKE \"organisation,%\" ";
		$queryD .= "ORDER BY value_string ASC";	
		$mysqli_resultD = mysqli_query($mysqli_link, $queryD);
		while($rowD = mysqli_fetch_row($mysqli_resultD)) { 
		
			$ID = $rowD[0];
			$iana_UUID	 = $rowD[1];
			$items_iana_UUID = $rowD[2];
			$items_dc_identifier = $rowD[3];
			$dc_referencesIndex = $rowD[4];
			$reg_uri = $rowD[5];
			$rdfs_label = $rowD[6];
			$value_string = $rowD[7];
			$value_uri = $rowD[8];
			$resource_uri	 = $rowD[9];
			$dct_contributor = $rowD[10];
			$dc_created = $rowD[11];
		
			$value_string = preg_replace("/organisation, /i", "", "$value_string");
			$queryE = "UPDATE annotations SET ";
			$queryE .= "value_string = \"$value_string\" ";
			$queryE .= "WHERE ID = \"$ID\" ";
			$mysqli_resultE = mysqli_query($mysqli_link, $queryE);	
			
		}
		$msg .= "done ... organisation(s)<br />";
	}
	
/////////////////////////////////////////////////////////// Clean persons

	if(($doPrior == "yes")) {
		$queryD = "SELECT * FROM annotations ";
		$queryD .= "WHERE value_string LIKE \"person,%\" ";
		$queryD .= "ORDER BY value_string ASC";	
		$mysqli_resultD = mysqli_query($mysqli_link, $queryD);
		while($rowD = mysqli_fetch_row($mysqli_resultD)) { 
		
			$ID = $rowD[0];
			$iana_UUID	 = $rowD[1];
			$items_iana_UUID = $rowD[2];
			$items_dc_identifier = $rowD[3];
			$dc_referencesIndex = $rowD[4];
			$reg_uri = $rowD[5];
			$rdfs_label = $rowD[6];
			$value_string = $rowD[7];
			$value_uri = $rowD[8];
			$resource_uri	 = $rowD[9];
			$dct_contributor = $rowD[10];
			$dc_created = $rowD[11];
		
			$value_string = preg_replace("/person, /i", "", "$value_string");
			$queryE = "UPDATE annotations SET ";
			$queryE .= "value_string = \"$value_string\", ";
			$queryE .= "reg_uri = \"foaf\", ";
			$queryE .= "rdfs_label = \"person\" ";
			$queryE .= "WHERE ID = \"$ID\" ";
			$mysqli_resultE = mysqli_query($mysqli_link, $queryE);	
			
		}
		$msg .= "done ... person(s)<br />";
	}
	
/////////////////////////////////////////////////////////// Clean book titles

	if(($doPrior == "yes")) {
		$queryD = "SELECT * FROM annotations ";
		$queryD .= "WHERE value_string LIKE \"book,%\" ";
		$queryD .= "ORDER BY value_string ASC";	
		$mysqli_resultD = mysqli_query($mysqli_link, $queryD);
		while($rowD = mysqli_fetch_row($mysqli_resultD)) { 	
		
			$ID = $rowD[0];
			$iana_UUID	 = $rowD[1];
			$items_iana_UUID = $rowD[2];
			$items_dc_identifier = $rowD[3];
			$dc_referencesIndex = $rowD[4];
			$reg_uri = $rowD[5];
			$rdfs_label = $rowD[6];
			$value_string = $rowD[7];
			$value_uri = $rowD[8];
			$resource_uri	 = $rowD[9];
			$dct_contributor = $rowD[10];
			$dc_created = $rowD[11];
			
			$value_string = preg_replace("/book, /i", "", "$value_string");
			$queryE = "UPDATE annotations SET ";
			$queryE .= "value_string = \"$value_string\", ";
			$queryE .= "reg_uri = \"bibo\", ";
			$queryE .= "rdfs_label = \"book\" ";
			$queryE .= "WHERE ID = \"$ID\" ";
			$mysqli_resultE = mysqli_query($mysqli_link, $queryE);
		
		}
		$msg .= "done ... book(s)<br />";
	}
	
/////////////////////////////////////////////////////////// Clean and add accounts

	if(($doPrior == "yes")) {
		$queryD = "SELECT * FROM annotations ";
		$queryD .= "WHERE value_string LIKE \"accounts,%\" ";
		$queryD .= "ORDER BY value_string ASC";	
		$mysqli_resultD = mysqli_query($mysqli_link, $queryD);
		while($rowD = mysqli_fetch_row($mysqli_resultD)) { 
			
			$ID = $rowD[0];
			$iana_UUID = $rowD[1];
			$items_iana_UUID = $rowD[2];
			$items_dc_identifier = $rowD[3];
			$dc_referencesIndex = $rowD[4];
			$reg_uri = $rowD[5];
			$rdfs_label = $rowD[6];
			$value_string = $rowD[7];
			$value_uri = $rowD[8];
			$resource_uri = $rowD[9];
			$dct_contributor = $rowD[10];
			$dc_created = $rowD[11];
			
			$value_string = preg_replace("/accounts, /i", "", "$value_string");
			$queryE = "UPDATE annotations SET ";
			$queryE .= "value_string = \"$value_string\", ";
			$queryE .= "reg_uri = \"dc\", ";
			$queryE .= "rdfs_label = \"subject\" ";
			$queryE .= "WHERE ID = \"$ID\" ";
			$mysqli_resultE = mysqli_query($mysqli_link, $queryE);
			
			$iana_UUID = guidv4();	
			$characters = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
			$iana = "";
			for ($i = 0; $i < 12; $i++) {
				$iana .= $characters[mt_rand(0, 36)];
			}
			$iana = time().$iana;
			
			$queryE = "INSERT INTO annotations VALUES (";
			$queryE .= "\"0\", ";
			$queryE .= "\"$iana_UUID\", ";
			$queryE .= "\"".time()."_".$iana."\", ";
			$queryE .= "\"$items_dc_identifier\", ";
			$queryE .= "\"$items_dc_identifier\", ";
			$queryE .= "\"dc\", ";
			$queryE .= "\"subject\", ";
			$queryE .= "\"accounts\", ";
			$queryE .= "\"\", ";
			$queryE .= "\"\", ";
			$queryE .= "\"$contributor\", ";
			$queryE .= "NOW() ";
			$queryE .= "); ";
			$mysqli_resultE = mysqli_query($mysqli_link, $queryE);
			
		}
		$msg .= "done ... account(s)<br />";
	}

/////////////////////////////////////////////////////////// Finish

	if(($doPrior == "yes")) {
		$msg .= "<br />FINISH<br /><br />";
		echo $msg;
	}
	include("./ar.dbdisconnect.php");

?>