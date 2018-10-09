<?php

/////////////////////////////////////////////////////////// Source
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
//  24-25 May 2017
//	30 May 2017
//	23 June 2017
//	27-28 June 2017
//	30 June 2017
//	10 August 2018
//
//
/////////////////////////////////////////////////////////// Prevent Direct Access

	if(($session_reload == "")) {
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
	}	

/////////////////////////////////////////////////////////// Default Login Panel

	if(($_SESSION["userlogin"] != "") && ($_SESSION["userpassword"] != "")) {

?>
<form id="signout" class="navbar-form pull-right" role="form" method="POST">
	<div class="input-group"><input id="usersearch" type="text" class="form-control" 
    	name="usersearch" value="" placeholder="Quick Tag Search" style="width: 200px;" onclick="var clearThis = $('#usersearch').val('');">
    <span class="input-group-addon"><i class="glyphicon glyphicon-search"></i></span></div>
    <div class="input-group"><input id="standinName" type="text" class="form-control" name="standinName" value="<?php echo $_SESSION["username"]; ?>" readonly style="color: #000000;"></div>
	<input id="userlogout" type="hidden" class="form-control" name="userlogout" value="yes">                                        
	<button type="submit" class="btn btn-info">Logout</button>
</form>
<script language="javascript" type="text/javascript" >

	$(document).ready(function() {
		$('#usersearch').bind('keyup input',function(event) {	
			var myLength = $("#usersearch").val().length;
			if(myLength > 3) {					
				$("#usersearch").autocomplete({
					source: function(request, response){
						$.ajax({
							url: "./data_annotations_search.php",
							dataType: "json",
							data: {
								term : request.term,
								variation : "ANNOTATIONS"
							},
							success: function (data) {
								response(data);
							}
						});
					},
					minLength: 3,
					delay: 5, 
					maxCacheLength: 3, 
					select: function(event, ui) {
						if(ui.item){
							var valink = ui.item.label;
							var lablink = ui.item.value;
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
					}
				});							
			}
		});
		$('#usersearch').keypress(function(event) {
   			if (event.keyCode == 13) {
        		event.preventDefault();
    		}
		});
	});
	
</script>
<?php
		
	} else {
		
?>
<form id="signin" class="navbar-form pull-right" role="form" method="POST">
    <div class="input-group"><input id="mygosearch" type="text" class="form-control" name="mygosearch" value="" 
    	placeholder="Quick Tag Search" style="width: 200px;" onclick="var clearThis = $('#usersearch').val('');" autocomplete="off">
    <span class="input-group-addon"><i class="glyphicon glyphicon-search"></i></span></div>
	<div class="input-group"><input id="userlogin" type="email" class="form-control" name="userlogin" value="" placeholder="Email Address" style="width: 260px;"></div>
	<div class="input-group"><input id="userpassword" type="password" class="form-control" name="userpassword" value="" placeholder="Password"></div>
	<button type="submit" class="btn btn-success">Login</button>
</form>
<script language="javascript" type="text/javascript" >

	$(document).ready(function() {
		$('#mygosearch').attr('autocomplete', 'off');
		$('#mygosearch').bind('keyup input',function(event) {	
			var myLength = $("#mygosearch").val().length;
			if(myLength > 3) {					
				$("#mygosearch").autocomplete({
					source: function(request, response){
						$.ajax({
							url: "./data_annotations_search.php",
							dataType: "json",
							data: {
								term : request.term,
								variation : "ANNOTATIONS"
							},
							success: function (data) {
								response(data);
							}
						});
					},
					minLength: 3,
					delay: 5, 
					maxCacheLength: 3, 
					select: function(event, ui) {
						if(ui.item){
							var valink = ui.item.label;
							var lablink = ui.item.value;
							var cleanBarB = $('#mygosearch').val(''+valink);	
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
					}
				});							
			}
		});
		$('#usersearch').keypress(function(event) {
   			if (event.keyCode == 13) {
        		event.preventDefault();
    		}
		});
	});
	
</script>
<?php
		
	}
	
/////////////////////////////////////////////////////////// Finish	
	
	if(($session_reload == "")) {
		include("./ar.dbdisconnect.php");
	}
	
?>