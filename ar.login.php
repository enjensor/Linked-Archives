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
//  5-6 November 2018
//  8 November 2018
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
    <div class="dropdown" style="display:inline-block; z-index: 5000;">
      <button class="btn btn-default dropdown-toggle" type="button" data-toggle="dropdown" style="width: 240px; height: 32px; background-color: #EEEEEE; color: #000000; font-size: 1.0em;">Search Selected Archives
      <span class="caret"></span></button>
      <ul class="dropdown-menu" id="usercollections" name="usercollections">
        <?php
            $ck = 1;
            $thisQ = "SELECT skos_orderedCollection, bf_heldBy ";
            $thisQ .= "FROM collections GROUP BY skos_orderedCollection, bf_heldBy ";
            $thisQ .= "ORDER BY bf_heldBy ASC ";
            $mysqli_thisQ = mysqli_query($mysqli_link, $thisQ);
            while($Qrow = mysqli_fetch_row($mysqli_thisQ)) { 
                if((preg_match("/Allen/i","$Qrow[0]") or preg_match("/UGD/i","$Qrow[0]"))) {
                    $checked = "";
                } else {
                    $checked = "checked";
                }
                echo "<li style=\"background-color: #FFFFFF; padding-top: 4px; padding-bottom: 4px; width: 240px;\">";
                echo "<a href=\"#\">";
                echo "<input type=\"checkbox\" ";
                echo "value=\"$Qrow[0]\" ";
                echo "id=\"usercol_".$ck."\" ";
                echo "name=\"usercol_".$ck."\" ";
                echo "$checked style=\"transform: scale(1.4);\" />";
                echo "&nbsp;&nbsp;&nbsp;";
                echo "<strong>$Qrow[0]</strong><br />";
                $heldAt = explode(",","$Qrow[1]");
                echo "<span style=\"padding-left: 2.0em; font-size: 0.9em;\">$heldAt[0]</span>";
                echo "</a></li>"; 
                $ck++;
            }
        ?>
      </ul>
    </div>
	<div class="input-group"><input id="usersearch" type="text" class="form-control" 
    	name="usersearch" value="" placeholder="Quick Tag Search" style="width: 130px;" onclick="var clearThis = $('#usersearch').val('');">
    <span class="input-group-addon"><i class="glyphicon glyphicon-search"></i></span></div>
    <div class="input-group"><input id="standinName" type="text" class="form-control" name="standinName" value="<?php echo $_SESSION["username"]; ?>" readonly style="color: #000000; width: 150px;"></div>
	<input id="userlogout" type="hidden" class="form-control" name="userlogout" value="yes">                                        
	<button type="submit" class="btn btn-info" style="height: 32px; width: 60px; font-size: 1.0em;">Logout</button>
</form>
<script language="javascript" type="text/javascript" >

	$(document).ready(function() {
		$('#usersearch').bind('keyup input',function(event) {	
			var myLength = $("#usersearch").val().length;
			if(myLength > 3) {
                var userCols = "Collections";
                <?php
                    for($c=1;$c<$ck;$c++) {
                        echo "if($('#usercol_".$c."').is(\":checked\")){ ";
                        echo "var userVal = $('#usercol_".$c."').val(); ";
                        echo "if(userVal != \"\") { ";
                        echo "userCols = (userCols + \",\" + userVal); ";
                        echo "} ";          
                        echo "}\n";
                    }
                ?>
				$("#usersearch").autocomplete({
					source: function(request, response){
						$.ajax({
							url: "./data_annotations_search.php",
							dataType: "json",
							data: {
								term : request.term, variation : "" + userCols
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
    <div class="dropdown" style="display:inline-block; z-index: 5000;">
      <button class="btn btn-default dropdown-toggle" type="button" data-toggle="dropdown" style="width: 240px; height: 32px; background-color: #EEEEEE; color: #000000; font-size: 1.0em;">Search Selected Archives
      <span class="caret"></span></button>
      <ul class="dropdown-menu" id="usercollections" name="usercollections">
        <?php
            $ck = 1;
            $thisQ = "SELECT skos_orderedCollection, bf_heldBy ";
            $thisQ .= "FROM collections GROUP BY skos_orderedCollection, bf_heldBy ";
            $thisQ .= "ORDER BY bf_heldBy ASC ";
            $mysqli_thisQ = mysqli_query($mysqli_link, $thisQ);
            while($Qrow = mysqli_fetch_row($mysqli_thisQ)) { 
                if((preg_match("/Allen/i","$Qrow[0]") or preg_match("/UGD/i","$Qrow[0]"))) {
                    $checked = "";
                } else {
                    $checked = "checked";
                }
                echo "<li style=\"background-color: #FFFFFF; padding-top: 4px; padding-bottom: 4px; width: 240px;\">";
                echo "<a href=\"#\">";
                echo "<input type=\"checkbox\" ";
                echo "value=\"$Qrow[0]\" ";
                echo "id=\"usercol_".$ck."\" ";
                echo "name=\"usercol_".$ck."\" ";
                echo "$checked style=\"transform: scale(1.4);\" />";
                echo "&nbsp;&nbsp;&nbsp;";
                echo "<strong>$Qrow[0]</strong><br />";
                $heldAt = explode(",","$Qrow[1]");
                echo "<span style=\"padding-left: 2.0em; font-size: 0.9em;\">$heldAt[0]</span>";
                echo "</a></li>"; 
                $ck++;
            }
        ?>
      </ul>
    </div>
    <div class="input-group"><input id="mygosearch" type="text" class="form-control" name="mygosearch" value="" 
    	placeholder="Quick Tag Search" style="width: 130px;" onclick="var clearThis = $('#usersearch').val('');" autocomplete="off">
    <span class="input-group-addon"><i class="glyphicon glyphicon-search"></i></span></div>
	<div class="input-group"><input id="userlogin" type="email" class="form-control" name="userlogin" value="" placeholder="Email Address" style="width: 120px;"></div>
	<div class="input-group"><input id="userpassword" type="password" class="form-control" name="userpassword" value="" placeholder="Password" style="width: 120px;"></div>
	<button type="submit" class="btn btn-success" style="height: 32px; width: 60px; font-size: 1.0em;">Login</button>
</form>
<script language="javascript" type="text/javascript" >

	$(document).ready(function() {
		$('#mygosearch').attr('autocomplete', 'off');
		$('#mygosearch').bind('keyup input',function(event) {	
			var myLength = $("#mygosearch").val().length;
			if(myLength > 3) {	
                var userCols = "Collections";
                <?php
                    for($c=1;$c<$ck;$c++) {
                        echo "if($('#usercol_".$c."').is(\":checked\")){ ";
                        echo "var userVal = $('#usercol_".$c."').val(); ";
                        echo "if(userVal != \"\") { ";
                        echo "userCols = (userCols + \",\" + userVal); ";
                        echo "} ";          
                        echo "}\n";
                    }
                ?>
				$("#mygosearch").autocomplete({
					source: function(request, response){
						$.ajax({
							url: "./data_annotations_search.php",
							dataType: "json",
							data: {
								term : request.term, variation : "" + userCols
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