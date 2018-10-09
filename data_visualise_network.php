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
//  26 April 2017
//
//
/////////////////////////////////////////////////////////// Clean post and get

	define('MyConstInclude', TRUE);
	$doCalc = "y";

/////////////////////////////////////////////////////////// Collect session data

	$MerdUser = session_id();
	if(empty($MerdUser)) { session_start(); }
	$SIDmerd = session_id(); 
	
/////////////////////////////////////////////////////////// Clean post and get	
	
	include("./ar.config.php");
	include("./ar.dbconnect.php");
	include("./index_functions.php");
	header("Cache-Control: no-cache");
	header("Pragma: no-cache");
	mb_internal_encoding("UTF-8");
	if (!mysqli_set_charset($mysqli_link, "utf8")) {
		echo "PROBLEM WITH CHARSET!";
		die;
	}

/////////////////////////////////////////////////////////// Do page

?>
<!DOCTYPE html>
<html>
<meta charset="utf-8">
<script src="./js/d3.js"></script>
<script src="./js/jquery.min.js"></script>
<style>

	.link {
		stroke: #bbbbbb;
	}
	
	.node text {
		stroke: #333;
		cursos: pointer;
	}
	
	.node circle {
		stroke: #ffffff;
		stroke-width: 3px;
		fill: #1b746c;
	}

</style>
<body>
<script>

	var width = $(document).width();
	var height = $(document).height();

	var svg = d3.select("body").append("svg")
		.attr("width", width)
		.attr("height", height);

	var force = d3.layout.force()
		.gravity(.50)
		.distance(25)
		.charge(-1500)
		.size([width, height]);

	d3.json("data_visualise_network_json.php", function(json) {
		
  		force.nodes(json.nodes).links(json.links).start();

		var link = svg.selectAll(".link")
			.data(json.links)
			.enter().append("line")
			.attr("class", "link")
			.style("stroke-width", function(d) { return Math.sqrt(d.weight); });
	
		var node = svg.selectAll(".node")
			.data(json.nodes)
			.enter().append("g")
			.attr("class", "node")
			.call(force.drag);
	
		node.append("circle").attr("r","6");
	
		node.append("text")
			.attr("dx", 12)
			.attr("dy", ".35em")
			.text(function(d) { return d.name });
	
		force.on("tick", function() {
			link.attr("x1", function(d) { return d.source.x; })
			.attr("y1", function(d) { return d.source.y; })
			.attr("x2", function(d) { return d.target.x; })
			.attr("y2", function(d) { return d.target.y; });
			node.attr("transform", function(d) { return "translate(" + d.x + "," + d.y + ")"; });
		});
		
	});

</script>
</body>
</html>
<?php

/////////////////////////////////////////////////////////// Finish	
	
	include("./ar.dbdisconnect.php");

?>