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

/////////////////////////////////////////////////////////// Start iFrame Page
	
?>
<!DOCTYPE html>
<html lang="en">
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]> 	   <html class="no-js"> <![endif]-->
    <head>   
    	<title>Upload Items</title>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta http-equiv='cache-control' content='no-cache'>
		<meta http-equiv='expires' content='0'>
		<meta http-equiv='pragma' content='no-cache'>
        <meta name="description" content="Manuscripts - Categorisation, Western Sydney University. Development: Dr Jason Ensor" />
        <meta name="robots" content="noindex,nofollow" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">    
        <meta name="theme-color" content="#ffffff" />  
		<meta name="msapplication-TileColor" content="#ffffff" />
		<meta name="msapplication-TileImage" content="./icons/ms-icon-144x144.png" />
		<link rel="shortcut icon" type="image/x-icon" href="./icons/favicon.ico" />     
        <link rel="manifest" href="./manifest.json" /> 
        <link rel="apple-touch-icon" href="./icons/apple-icon.png" />
		<link rel="apple-touch-icon" sizes="57x57" href="./icons/apple-icon-57x57.png" />
		<link rel="apple-touch-icon" sizes="60x60" href="./icons/apple-icon-60x60.png" />
		<link rel="apple-touch-icon" sizes="72x72" href="./icons/apple-icon-72x72.png" />
		<link rel="apple-touch-icon" sizes="76x76" href="./icons/apple-icon-76x76.png" />
		<link rel="apple-touch-icon" sizes="114x114" href="./icons/apple-icon-114x114.png" />
		<link rel="apple-touch-icon" sizes="120x120" href="./icons/apple-icon-120x120.png" />
		<link rel="apple-touch-icon" sizes="144x144" href="./icons/apple-icon-144x144.png" />
		<link rel="apple-touch-icon" sizes="152x152" href="./icons/apple-icon-152x152.png" />
		<link rel="apple-touch-icon" sizes="180x180" href="./icons/apple-icon-180x180.png" />
		<link rel="icon" type="image/png" sizes="192x192" href="./icons/android-icon-192x192.png" />
		<link rel="icon" type="image/png" sizes="32x32" href="./icons/favicon-32x32.png" />
		<link rel="icon" type="image/png" sizes="96x96" href="./icons/favicon-96x96.png" />
		<link rel="icon" type="image/png" sizes="16x16" href="./icons/favicon-16x16.png" />
        <link rel="icon" type="image/x-icon" href="./icons/favicon.ico" />  
        <link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700&amp;subset=latin">
        <link rel="stylesheet" type="text/css" href="./js/jquery-ui/themes/base/jquery.ui.all.css">
		<link rel="stylesheet" type="text/css" href="./css/bootstrap.min.css">
		<link rel="stylesheet" type="text/css" href="./plugins/themify-icons/themify-icons.min.css">
        <link rel="stylesheet" type="text/css" href="./plugins/ionicons/css/ionicons.min.css">
		<link rel="stylesheet" type="text/css" href="./css/pace.min.css">
        <link rel="stylesheet" type="text/css" href="./plugins/ionicons/css/ionicons.min.css" >
        <link rel="stylesheet" type="text/css" href="./plugins/chosen/chosen.min.css">
        <link rel="stylesheet" type="text/css" href="./plugins/bootstrap-select/bootstrap-select.min.css">
		<script language="javascript" type="text/javascript" src="./js/pace.min.js"></script>
        <script language="javascript" type="text/javascript" src="./js/jquery-2.2.4.min.js"></script>
		<script language="javascript" type="text/javascript" src="./js/bootstrap.min.js"></script>
		<script language="javascript" type="text/javascript" src="./js/nifty.min.js"></script>
        <script language="javascript" type="text/javascript" src="./plugins/datatables/media/js/jquery.dataTables.js"></script>
        <script language="javascript" type="text/javascript" src="./plugins/chosen/chosen.jquery.min.js"></script>
        <script language="javascript" type="text/javascript" src="./plugins/bootstrap-select/bootstrap-select.min.js"></script>
        <script language="javascript" type="text/javascript" src="./plupload/js/plupload.full.min.js"></script>
<?php

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// Manual CSS Interventions

?>         
        <style type="text/css" rel="stylesheet">
			
			pre {
    			white-space: pre-wrap; 
    			white-space: -moz-pre-wrap;
    			white-space: -pre-wrap;
    			white-space: -o-pre-wrap;
    			word-wrap: break-word;
				tab-size: 0;
				-moz-tab-size: 0;
    			-o-tab-size: 0;
				padding: 20px;
				font-size: 0.8em;
			}
            
            small {
                width: 100%!important;
                text-align: right!important;   
            }
			
			body, html {
				background-color: #f9f9f9;
				width: 100%;
                height: 100%; 
				padding: 0px;
				margin: 0px;
                font-size: 0.90em!important;
			}
			
			.btn-default {
				margin-bottom: 2px;
				margin-right: 2px;
				min-width: 55px;	
			}
			
			.input-sm {
				max-width: 100px;	
			}
			
			::-webkit-scrollbar {
    			-webkit-appearance: none;
    			width: 12px;
			}
	
			::-webkit-scrollbar-thumb {
    			border-radius: 4px;
    			background-color: rgba(0,0,0,0.20);
    			-webkit-box-shadow: 0 0 1px rgba(255,255,255,.5);
			}
			
		</style>
    </head>   
	<body>
        <div class="container" id="EditItemPanel" style="padding: 15px; margin-left: 0px; margin-right: 0px;">
            <div class="row">
            	<div class="col-lg-12 col-md-12 col-sm-12">
                    <div style="padding: 6px;" id="ItemAddPanel">
                        <p style="text-align: justify;"><strong>UPLOAD ITEMS</strong><br /><br />This is the second part to adding a new collection into Linked Archives where you upload images into the collection you just created. For this part, you need access to your local folder of photographs, scanned or downloaded images. Please select the collection that your images will be loaded into.<br /><br />For best results, please also ensure that your images are no larger than 1200 pixels by 1200 pixels, are ideally black and white, and their filesize is no greater than 500 KB.<br /><br /><strong>TARGET COLLECTION</strong><br />&nbsp;</p>
                        <table width="100%" border="0" style="width: 100%; border: 1px solid #c9d1d7;">
                            <tbody>
                            <?php 
                                
                                echo "<tr>";
                                echo "<td style=\"";
                                echo "text-align: right; ";
                                echo "color: #FFFFFF; ";
                                echo "background-color: #c9d1d7; "; 
                                echo "vertical-align: top; ";
                                echo "padding: 8px; ";
                                echo "\" ";
                                echo ">";	
                                echo "<strong>#</strong>";
                                echo "</td>";
                                echo "<td style=\"";
                                echo "text-align: left; ";
                                echo "color: #FFFFFF; ";
                                echo "background-color: #c9d1d7; "; 
                                echo "vertical-align: top; ";
                                echo "padding: 8px; ";
                                echo "\" ";
                                echo ">";	
                                echo "<strong>Collection</strong>";
                                echo "</td>";
                                echo "<td style=\"";
                                echo "text-align: ;left; ";
                                echo "color: #FFFFFF; ";
                                echo "background-color: #c9d1d7; "; 
                                echo "vertical-align: top; ";
                                echo "padding: 8px; ";
                                echo "\" ";
                                echo ">";	
                                echo "<strong>Items</strong>";
                                echo "</td>";
                                echo "</tr>";
                                
                                $found = "n";
                                $queryD = "SELECT * FROM collections ORDER BY ID DESC LIMIT 1 ";
                                $mysqli_resultD = mysqli_query($mysqli_link, $queryD);
                                while($rowD = mysqli_fetch_row($mysqli_resultD)) { 
                                    $empty = "y";
                                    $found = "y";
                                    echo "<tr>";
                                    echo "<td style=\"";
                                    echo "text-align: right; ";
                                    echo "color: #1b4f74; ";
                                    echo "background-color: #FFFFFF; "; 
                                    echo "vertical-align: top; ";
                                    echo "padding: 8px; ";
                                    echo "border-bottom: 1px solid #c9d1d7; ";
                                    echo "\" ";
                                    echo ">";	
                                    echo "<strong>$rowD[0]</strong>";
                                    echo "</td>";
                                    echo "<td style=\"";
                                    echo "text-align: left; ";
                                    echo "color: #1b4f74; ";
                                    echo "background-color: #FFFFFF; "; 
                                    echo "vertical-align: top; ";
                                    echo "padding: 8px; ";
                                    echo "border-bottom: 1px solid #c9d1d7; ";
                                    echo "\" ";
                                    echo ">";		
                                    echo "$rowD[6], ";
                                    if(($rowD[8] != "")) {
                                        echo "$rowD[7]"."/"."$rowD[8], ";
                                    } else {
                                        echo "$rowD[7], ";
                                    }
                                    if(($rowD[9] != $rowD[10])) {
                                        echo "$rowD[9]"."-"."$rowD[10]. ";
                                    } else {
                                        echo "$rowD[9]. ";
                                    }
                                    echo "[$rowD[11]]";
                                    echo "</td>";
                                    echo "<td style=\"";
                                    echo "text-align: right; ";
                                    echo "color: #1b4f74; ";
                                    echo "background-color: #FFFFFF; "; 
                                    echo "vertical-align: top; ";
                                    echo "padding: 8px; ";
                                    echo "border-bottom: 1px solid #c9d1d7; ";
                                    echo "\" ";
                                    echo ">";
                                    $total = 0;
                                    $queryE = "SELECT COUNT(*) FROM items WHERE collections_dc_identifier = \"$rowD[2]\"; ";
                                    $mysqli_resultE = mysqli_query($mysqli_link, $queryE);
                                    while($rowE = mysqli_fetch_row($mysqli_resultE)) { 
                                        $total = $rowE[0];
                                        $empty = "n";
                                    }
                                    if(($empty == "y")) {
                                        echo "<em>Empty</em>";
                                    } else {
                                        echo "$total";
                                    }
                                    echo "</td>";
                                    echo "</tr>";
                                }  

                            ?>
                            </tbody>
                        </table>
                        <p>&nbsp;</p>
                        <?php if(($found == "y")) { ?>
                        <div id="container" style="text-align: center;">
                            <a id="pickfiles" href="javascript:;"><button class="btn btn-primary" style="width: 49%;">Select files</button></a> 
                            <a id="uploadfiles" href="javascript:;"><button class="btn btn-success" style="width: 49%;">Upload files</button></a>
                        </div>
                        <p>&nbsp;</p>
                        <p><strong>FILES TO UPLOAD</strong><br>&nbsp;</p>
                        <div id="filelist" style="padding: 1.0em; border: 1px solid #c9d1d7; background-color: #FFFFFF;">Your browser does support Flash, Silverlight or HTML5.</div>
                        <?php } else { ?>
                        <p style="text-align: justify;"><strong>Your administration session has expired.</strong></p>
                        <?php } ?>
                        <pre id="console" style="display: none; padding: 20px; margin: 0px; ">Upload Errors Console<br /></pre>
                    </div>
                </div>
            </div>
        </div>
        <script language="javascript" type="text/javascript" >

///////////////////////////////////// Start Scripts            
            
            var uploader = new plupload.Uploader({
                runtimes : 'html5,flash,silverlight,html4',
                browse_button : 'pickfiles',
                container: document.getElementById('container'),
                url : 'data_form_images_upload.php',
                flash_swf_url : '../js/Moxie.swf',
                silverlight_xap_url : '../js/Moxie.xap',

                filters : {
                    max_file_size : '10mb',
                    mime_types: [
                        {title : "Image Files", extensions : "jpg"}
                    ]
                },
                init: {
                    PostInit: function() {
                        document.getElementById('filelist').innerHTML = '';
                        document.getElementById('uploadfiles').onclick = function() {
                            uploader.start();
                            return false;
                        };
                    },
                    FilesAdded: function(up, files) {
                        plupload.each(files, function(file) {
                            document.getElementById('filelist').innerHTML += '<div id="' + file.id + '">' + file.name + ' (' + plupload.formatSize(file.size) + ') <b></b></div>';
                        });
                    },
                    UploadProgress: function(up, file) {
                        document.getElementById(file.id).getElementsByTagName('b')[0].innerHTML = "<span style='float: right;'>" + file.percent + "%</span>";
                    },
                    Error: function(up, err) {
                        document.getElementById("console").style.display = "block";
                        document.getElementById('console').appendChild(document.createTextNode("Error #" + err.code + ": " + err.message + "\n"));
                    }
                }
            });

            uploader.init();

///////////////////////////////////// Finish Scripts
		
		</script> 
    </body>
</html>
<?php
        
/////////////////////////////////////////////////////////// Finish        

    include("./ar.dbdisconnect.php");

/////////////////////////////////////////////////////////// Close

?>