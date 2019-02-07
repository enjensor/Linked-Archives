<?php

/////////////////////////////////////////////////////////// Source
//
//
//	DialogFlow Webhook : Archiver Project
//	Library, Western Sydney University
//  October 2018
//
//	Who:	Dr Jason Ensor
//	Email: 	jasondensor@gmail.com
//	Mobile: + 61 (0)419 674 770
//
//
/////////////////////////////////////////////////////////// Turn On for Debug
//
//	define('MyConstInclude', TRUE);
//	include("./ar.config.php");
//	include("./ar.dbconnect.php");
//	include('./webhook.php');
//
/////////////////////////////////////////////////////////// Get Params

	$requestType = $wh->get_parameter('requestType');

/////////////////////////////////////////////////////////// Query

	$countAuthors = 0;
	$queryA = "SELECT COUNT(DISTINCT(value_string)) ";
	$queryA .= "FROM annotations ";
	$queryA .= "WHERE rdfs_label = \"person\" ";
	$mysqli_resultA = mysqli_query($mysqli_link, $queryA);
	while($rowA = mysqli_fetch_row($mysqli_resultA)) {
		$countAuthors = $rowA[0];
	}

	$countBooks = 0;
	$queryA = "SELECT COUNT(DISTINCT(value_string)) ";
	$queryA .= "FROM annotations ";
	$queryA .= "WHERE rdfs_label = \"book\" ";
	$mysqli_resultA = mysqli_query($mysqli_link, $queryA);
	while($rowA = mysqli_fetch_row($mysqli_resultA)) {
		$countBooks = $rowA[0];
	}

	$countOrgs = 0;
	$queryA = "SELECT COUNT(DISTINCT(value_string)) ";
	$queryA .= "FROM annotations ";
	$queryA .= "WHERE rdfs_label = \"formalOrganisation\" ";
	$mysqli_resultA = mysqli_query($mysqli_link, $queryA);
	while($rowA = mysqli_fetch_row($mysqli_resultA)) {
		$countOrgs = $rowA[0];
	}

	$countSubjects = 0;
	$queryA = "SELECT COUNT(DISTINCT(value_string)) ";
	$queryA .= "FROM annotations ";
	$queryA .= "WHERE rdfs_label = \"subject\" ";
	$mysqli_resultA = mysqli_query($mysqli_link, $queryA);
	while($rowA = mysqli_fetch_row($mysqli_resultA)) {
		$countSubjects = $rowA[0];
	}

/////////////////////////////////////////////////////////// Format Numbers

	$countAuthors = number_format($countAuthors);
	$countBooks = number_format($countBooks);
	$countOrgs = number_format($countOrgs);
	$countSubjects = number_format($countSubjects);
	$clearMem = mysqli_free_result($mysqli_resultA);

/////////////////////////////////////////////////////////// Prepare Response

	if(($requestType == "author")){
		
//////////////////////////////// Author		
		
		$authors = array();
		$queryB = "SELECT DISTINCT(value_string) ";
		$queryB .= "FROM annotations ";
		$queryB .= "WHERE rdfs_label = \"person\" ";
		$queryB .= "GROUP BY value_string ";
		$queryB .= "ORDER BY RAND() ";
		$queryB .= "LIMIT 3";
		$mysqli_resultB = mysqli_query($mysqli_link, $queryB);
		while($rowB = mysqli_fetch_row($mysqli_resultB)) {
			$authors[] = $rowB[0];
		}
		$clearMem = mysqli_free_result($mysqli_resultB);
		$textToSpeech = "There are $countAuthors writers, including reknowned authors, ";
		$textToSpeech .= "from across the Australian literary and publishing landscape ";
		$textToSpeech .= "in the Linked Archives project. ";
		$textToSpeech .= "You can research about, for example, ";
		$textToSpeech .= "$authors[0], $authors[1] and $authors[2]. ";
		$textToSpeech .= "Who would you like to search for? ";
		
	} else if(($requestType == "book")){
		
//////////////////////////////// Book		
		
		$books = array();
		$queryB = "SELECT DISTINCT(value_string) ";
		$queryB .= "FROM annotations ";
		$queryB .= "WHERE rdfs_label = \"book\" ";
		$queryB .= "GROUP BY value_string ";
		$queryB .= "ORDER BY RAND() ";
		$queryB .= "LIMIT 3";
		$mysqli_resultB = mysqli_query($mysqli_link, $queryB);
		while($rowB = mysqli_fetch_row($mysqli_resultB)) {
			$books[] = $rowB[0];
		}
		$clearMem = mysqli_free_result($mysqli_resultB);
		$textToSpeech = "There are $countBooks books, including some very well known titles, ";
		$textToSpeech .= "from Australian literature and abroad ";
		$textToSpeech .= "in the Linked Archives project. ";
		$textToSpeech .= "You can find more information on, for example, ";
		$textToSpeech .= "$books[0], $books[1] and $books[2]. ";
		$textToSpeech .= "What book would you like to know more about? ";
		
	} else if(($requestType == "organisation")){
		
//////////////////////////////// Organisation		
		
		$orgs = array();
		$queryB = "SELECT DISTINCT(value_string) ";
		$queryB .= "FROM annotations ";
		$queryB .= "WHERE rdfs_label = \"formalOrganisation\" ";
		$queryB .= "GROUP BY value_string ";
		$queryB .= "ORDER BY RAND() ";
		$queryB .= "LIMIT 3";
		$mysqli_resultB = mysqli_query($mysqli_link, $queryB);
		while($rowB = mysqli_fetch_row($mysqli_resultB)) {
			$orgs[] = $rowB[0];
		}
		$clearMem = mysqli_free_result($mysqli_resultB);
		$textToSpeech = "The Linked Archives project collects internal memos and documents ";
		$textToSpeech .= "from $countOrgs publishers, associations and organisations, ";
		$textToSpeech .= "both national and international. ";
		$textToSpeech .= "Some of the companies you can research on include, for example, ";
		$textToSpeech .= "$orgs[0], $orgs[1] and $orgs[2]. ";
		$textToSpeech .= "Which organisation shall I search for? ";
		
	} else if(($requestType == "subject")){
		
//////////////////////////////// Subject		
		
		$subjects = array();
		$queryB = "SELECT DISTINCT(value_string) ";
		$queryB .= "FROM annotations ";
		$queryB .= "WHERE rdfs_label = \"subject\" ";
		$queryB .= "GROUP BY value_string ";
		$queryB .= "ORDER BY RAND() ";
		$queryB .= "LIMIT 3";
		$mysqli_resultB = mysqli_query($mysqli_link, $queryB);
		while($rowB = mysqli_fetch_row($mysqli_resultB)) {
			$subjects[] = $rowB[0];
		}
		$clearMem = mysqli_free_result($mysqli_resultB);
		$textToSpeech = "The Linked Archives project discusses $countSubjects topics and ";
		$textToSpeech .= "themes related to Australian publishing. ";
		$textToSpeech .= "Some example topics you can research on are";
		$textToSpeech .= "$subjects[0], $subjects[1] and $subjects[2]. ";
		$textToSpeech .= "What subject are you interested in? ";
		
	} else {
		
//////////////////////////////// Unknown		
		
		$textToSpeech = "The Linked Archives project contains letters and memos ";
		$textToSpeech .= "by $countAuthors writers from $countOrgs organisations, ";
		$textToSpeech .= "discussing $countSubjects subjects and $countBooks books. ";
		$textToSpeech .= "You can ask questions about a book title, a particular writer, ";
		$textToSpeech .= "a subject or an organisation. If you're not sure where to begin, ";
		$textToSpeech .= "just ask me to research a random author, publisher or subject. ";
	}

/////////////////////////////////////////////////////////// Build Reponse

	$displayText = $textToSpeech;
	$wh->build_simpleResponse($textToSpeech, $displayText);

/////////////////////////////////////////////////////////// Close
//
//	include("./ar.dbdisconnect.php");
//
/////////////////////////////////////////////////////////// Done

?>