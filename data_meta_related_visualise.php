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
//  20 August 2018
//  9 November 2018
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
	$me = $_SESSION["contributor"];

///////////////////////////////////////////////////////////// Start Page
	
?>
		<div id="visualiseConcept" class="row" style="padding: 0px; margin: 0px; width: 100%; height: 90.8vh;">
			<div id="parentConcept" class="col-lg-12 col-md-12 col-sm-12" 
				 style="height: 90.8vh; width: 100%; background-color: #c9d1d7;">           
				<svg id="svgConcept" style="width:100%; height:90.8vh; border: 0px solid #F8F9FA;"></svg>
			</div>
		</div>
		<script language="javascript" type="text/javascript" >
		
			$(document).ready(function() {
				
				var svg = d3.select("svg"),
					width = $("div #visualiseConcept").width(),
					height = $("div #visualiseConcept").height();

				var color = d3.scaleOrdinal(d3.schemeCategory20);
				
				var nodeRadius = 50;			

				var simulation = d3.forceSimulation()
					.velocityDecay(0.95)
					.force("link", d3.forceLink().id(function(d) { return d.id; }))
					.force("charge", d3.forceManyBody().strength(-25))
					.force("collide", d3.forceCollide().radius(function(d) {
						return nodeRadius + 8.0; }).iterations(2))
					.force("center", d3.forceCenter(width / 0.3, height / 2));

				d3.json("data_meta_related_network_json_all.php", function(error, graph) {
				  if (error) throw error;

				  var link = svg.append("g")
					.attr("class", "links")
					.selectAll("line")
					.data(graph.links)
					.enter().append("line")
					.attr("stroke-width", "2");

				  var node = svg.append("g")
					.attr("class", "nodes")
					.selectAll("g")
					.data(graph.nodes)
					.enter().append("g")

				  var circles = node.append("circle")
					  .attr("r", 8)
					  .attr("fill", function(d) { return color(d.group); })
					  .call(d3.drag()
					  .on("start", dragstarted)
					  .on("drag", dragged)
					  .on("end", dragended));

				  var labels = node.append("text")
					  .text(function(d) {
						return d.id;
					  })
					  .on("mouseover",function(d,i){
						 $(this).css('cursor','pointer');
					  })
				  	  .on("click",function(d,i){
							var textA = d.regUri;
							var textB = d.rdfsLabel;
							var textC = d.id;
							var dataE = 'action=find&search='+encodeURI(textC)+'&searchPhrase=';		
							var doDiv = $('#tableResultsContainer').fadeOut('fast', function(){
								var searchVal = $('#tableResultsContainer').load('./data_subjects.php',dataE, function(){
									var doDivAlso = $('#tableResultsContainer').fadeIn('slow');
								});
							});
							var dataSearchD = 'searchTerm='+encodeURI(textC)+'&searchPhrase=';
							var doDivSearchE = $('#titleTags').fadeOut('fast', function(){
								var doDivSearchF = $('#titleTags').load('./index_find_subjects.php',dataSearchD, function(){
									var doDivSearchG = $('#titleTags').fadeIn('slow');
								});
							});
							return false;						  
					  })
				  	  .style("font-size", 14)
					  .attr('x', 12)
					  .attr('y', 5);

//				  node.append("title")
//					  .text(function(d) { return d.id; });

				  simulation
					  .nodes(graph.nodes)
					  .on("tick", ticked);

				  simulation.force("link")
					  .links(graph.links);

				  function ticked() {
					link
						.attr("x1", function(d) { return d.source.x; })
						.attr("y1", function(d) { return d.source.y; })
						.attr("x2", function(d) { return d.target.x; })
						.attr("y2", function(d) { return d.target.y; });

					node
						.attr("transform", function(d) {
							return "translate(" + d.x + "," + d.y + ")";
						})
				  }
				});

				function dragstarted(d) {
				  if (!d3.event.active) simulation.alphaTarget(0.3).restart();
				  d.fx = d.x;
				  d.fy = d.y;
				}

				function dragged(d) {
				  d.fx = d3.event.x;
				  d.fy = d3.event.y;
				}

				function dragended(d) {
				  if (!d3.event.active) simulation.alphaTarget(0);
				  d.fx = null;
				  d.fy = null;
				}	
				
			});
					
		</script> 
<?php

///////////////////////////////////////////////////////////// Finish

	include("./ar.dbdisconnect.php");
	
?>