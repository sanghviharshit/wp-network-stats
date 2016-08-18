(function( $ ) {
	'use strict';

	/**
	 * All of the code for your Dashboard-specific JavaScript source
	 * should reside in this file.
	 *
	 * Note that this assume you're going to use jQuery, so it prepares
	 * the $ function reference to be used within the scope of this
	 * function.
	 *
	 * From here, you're able to define handlers for when the DOM is
	 * ready:
	 *
	 * $(function() {
	 *
	 * });
	 *
	 * Or when the window is loaded:
	 *
	 * $( window ).load(function() {
	 *
	 * });
	 *
	 * ...and so on.
	 *
	 * Remember that ideally, we should not attach any more than a single DOM-ready or window-load handler
	 * for any particular page. Though other scripts in WordPress core, other plugins, and other themes may
	 * be doing this, we should try to minimize doing that in our own work.
	 */

	$( window ).load(function() {

        var parcoords;
        var brushed_data;

        //waitingDialog.show('Please wait while your browser loads and processes the Network Stats.');

        /*
         JS Date is number of milliseconds since 1970, PHP date is number of seconds since 1970.

        var blog_registered_data = JSON.parse(data_to_js.blog_registered_data);
        var user_registered_data = JSON.parse(data_to_js.user_registered_data);

        timeseries('timeseries blogs', blog_registered_data, false);
        timeseries('timeseries users', user_registered_data, false);
        */
		var file_site_stats = data_to_js.file_site_stats;
		var file_user_stats = data_to_js.file_user_stats;

		var csv_site_data;
		var csv_user_data;

		function init_visualizations() {
			load_file_site_stats();
		}

		function load_file_site_stats() {
			d3.csv(file_site_stats, function(error, file_site_data) {
				if (error) {  //If error is not null, something went wrong.
					//console.log(error);  //Log the error.
					$('#vis_loading_block').hide();
					$('#vis_loading_error').show();
				} else {      //If no error, the file loaded correctly. Yay!
					//console.log(data);   //Log the data.
					csv_site_data = file_site_data;
					load_file_user_stats();
				}
			});
		}
		function load_file_user_stats() {
			d3.csv(file_user_stats, function (error, file_user_data) {
				if (error) {  //If error is not null, something went wrong.
					//console.log(error);  //Log the error.
					$('#vis_loading_block').hide();
					$('#vis_loading_error').show();
				} else {      //If no error, the file loaded correctly. Yay!
					csv_user_data = file_user_data;
					handle_csv_site_data()
					show_parallel_coordinates();

					$('#vis_loading_block').hide();

					d3.select('#btnExport').on('click', function () {
						export_brushed_data();
					})
				}
			});
		}

        function show_parallel_coordinates() {

            /*
             1 : I would like my blog to be visible to everyone, including search engines (like Google, Sphere, Technorati) and archivers. (default)
             0 : I would like to block search engines, but allow normal visitors.
             -1: Visitors must have a login - anyone that is a registered user of Web Publishing @ NYU can gain access.
             -2: Only registered users of this blogs can have access - anyone found under Users > All Users can have access.
             -3: Only administrators can visit - good for testing purposes before making it live.
             */
            csv_site_data.forEach(function(d) {
                    if(d.privacy == "1") { d.privacyString = "Public"; }
                    if(d.privacy == "0") { d.privacyString = "Hidden"; }
                    if(d.privacy == "-1") { d.privacyString = "Require Login"; }
                    if(d.privacy == "-2") { d.privacyString = "Require Invite"; }
                    if(d.privacy == "-3") { d.privacyString = "Private"; }
            });

            var colorgen = d3.scale.ordinal()
                .range(["#a6cee3","#1f78b4","#b2df8a","#33a02c",
                    "#fb9a99","#e31a1c","#fdbf6f","#ff7f00",
                    "#cab2d6","#6a3d9a","#ffff99","#b15928"]);

            var colors = d3.scale.category20b();

            var color = function(d) { return colors(d.group); };


            // linear color scale
            var color_range = d3.scale.linear()
                .domain([-2, 1])
                .range(["Coral", "DarkKhaki"])
                .interpolate(d3.interpolateLab);

            var dimensions = {
                "privacyString":
                {
					title: "Privacy",
                    //orient: 'left',
                    type: 'string',
                    tickPadding: 0,
                    innerTickSize: 8
                },
                "users_count":{
					title: "Users",
                	type:"number"
                },
				//"blog_id":{type:"number"},
                "active_plugins_count":{
					title: "Active Plugins",
                	type:"number"
                },
                //"current_theme":
                "posts_published":{
					title: "Posts",
                	type:"number"
                },
                "comments_count":{
					title: "Comments",
                	type:"number"
                },
                "attachments_count":{
					title: "Attachments",
                	type:"number"
                },
            };

            /*
             .hideAxis([
             "blog_id", "blog_name", "blog_description", "siteurl", "blog_url",
             "posts_future", "posts_draft", "posts_pending",
             "pages_future",	"pages_draft", "pages_pending",
             "last_updated", "admin_email"
             ])
             */
            parcoords = d3.parcoords({nullValueSeparator: "bottom"})("#parallel_multidimensional_detective")
                .data(csv_site_data)
                .dimensions(dimensions)
                //.color(color)
				.margin({ top: 30, left: 30, bottom: 30, right: 30 })
                .color(function(d) { return color_range(d['privacy']); })  // quantitative color scale
                .alpha(0.5)
                .composite("darken")
				//.rotateLabels(false)
				.render()
				.shadows()
				.mode("queue")
				.reorderable()
				.brushMode("1D-axes")
				.brushPredicate("OR")
                //.autoscale()
				//.createAxes()	//Can't use brush if called
                ;

            parcoords.on("brush", function(d) {
                set_brushed_data(d);
            });


            window.onresize = function() {
            	parcoords.width(d3.select("#parallel_multidimensional_detective").node().getBoundingClientRect().width)
            		.resize()
					.render();

            };

            function set_brushed_data(d) {
                brushed_data = d
            }

            /*
            var explore_count = 0;
            var exploring = {};
            var explore_start = false;
            parcoords.svg
                .selectAll(".dimension")
                .style("cursor", "pointer")
                .on("click", function(d) {
                    exploring[d] = d in exploring ? false : true;
                    event.preventDefault();
                    if (exploring[d]) d3.timer(explore(d,explore_count));
                });

            function explore(dimension,count) {
                if (!explore_start) {
                    explore_start = true;
                    d3.timer(parcoords.brush);
                }
                var speed = (Math.round(Math.random()) ? 1 : -1) * (Math.random()+0.5);
                return function(t) {
                    if (!exploring[dimension]) return true;
                    var domain = parcoords.yscale[dimension].domain();
                    var width = (domain[1] - domain[0])/4;

                    var center = width*1.5*(1+Math.sin(speed*t/1200)) + domain[0];

                    parcoords.yscale[dimension].brush.extent([
                        d3.max([center-width*0.01, domain[0]-width/400]),
                        d3.min([center+width*1.01, domain[1]+width/100])
                    ])(parcoords.g()
                        .filter(function(d) {
                            return d == dimension;
                        })
                    );
                };
            };
            */
        }

        function export_brushed_data() {
            var csvContent = "data:text/csv;charset=utf-8,";

            if (!brushed_data) {
                brushed_data = csv_site_data;
            }

            var dataString = d3.csv.format(brushed_data);

            csvContent += dataString;
            var encodedUri = encodeURI(csvContent);
            var link = document.createElement("a");
            link.setAttribute("href", encodedUri);
            link.setAttribute("download", "selected_sites_stats.csv");
            document.body.appendChild(link); // Required for FF

            link.click();
            //console.log(d);
        }

		function handle_csv_site_data() {

			var fitScreen = false;
			var zoom = 1;
			var height = 350;
			var width = 350;

			var legend = nv.models.legend();
			//legend.

			var privacyData = d3.nest()
				.key(function(d) { return d.privacy;})
				.sortKeys(function(a,b) { return parseInt(b) - parseInt(a); })
				.rollup(function(d) {
					return d3.sum(d, function(g) {return 1; });
				})
				.entries(csv_site_data);
			/*
			 data.sort(function(a, b) {
			 return a.key < b.key;
			 });
			 */

			/*
			 1 : I would like my blog to be visible to everyone, including search engines (like Google, Sphere, Technorati) and archivers. (default)
			 0 : I would like to block search engines, but allow normal visitors.
			 -1: Visitors must have a login - anyone that is a registered user of Web Publishing @ NYU can gain access.
			 -2: Only registered users of this blogs can have access - anyone found under Users > All Users can have access.
			 -3: Only administrators can visit - good for testing purposes before making it live.
			 */
			privacyData.forEach(function(d) {
				if(d.key == "1") { d.key = "Public"; }
				if(d.key == "0") { d.key = "Hidden"; }
				if(d.key == "-1") { d.key = "Require Login"; }
				if(d.key == "-2") { d.key = "Require Invite"; }
				if(d.key == "-3") { d.key = "Private"; }

				d.value = d.values;
				delete(d.values);
			});

			console.log('privacyData');
			console.log(privacyData);

			nv.addGraph(function() {
				var chart = nv.models.pieChart()
						.x(function(d) { return d.key })
						.y(function(d) { return d.value })
						.showTooltipPercent(true)
						.donut(true)          //Turn on Donut mode. Makes pie chart look tasty!
						.donutRatio(0.20)     //Configure how big you want the donut hole size to be.
						.width(width)
						.height(height)
						.showLabels(true)
						.labelsOutside(false)
						.showLegend(true)
				//.legend(legend)
					;

				/*
				chart.tooltip.contentGenerator(function (d) {
					var html = "<div style='color:"+d.series[0].color+"'>"
						+d.series[0].key+" : </div></div>"+d.series[0].value+"</div>";
					return html;
				});
				*/

				d3.select("#pie_privacy")
					.datum(privacyData)
					.attr('width', width)
					.attr('height', height)
					.attr('viewBox', '0 0 ' + width + ' ' + height)
					.transition().duration(500)
					.attr('perserveAspectRatio', 'xMinYMid')
					.call(chart);

				nv.utils.windowResize(chart.update);
				return chart;
			});

			var themeData = d3.nest()
				.key(function(d) { return d.current_theme;})
				.sortKeys(d3.ascending)
				.rollup(function(d) {
					return d3.sum(d, function(g) {return 1; });
				})
				.entries(csv_site_data);

			themeData.forEach(function(d) {
				d.value = d.values;
				delete(d.values);
			});

			console.log('themeData');
			console.log(themeData);

			nv.addGraph(function() {
				var chart = nv.models.pieChart()
						.x(function(d) { return d.key })
						.y(function(d) { return d.value })
						.showTooltipPercent(true)
						.width(width)
						.height(height)
						.showLabels(true)
						.showLegend(false)
						.labelsOutside(false)
					;

				console.log('number of themes - ' + themeData.length);

				if(themeData.length > 10) {
					chart.showLegend(false);
				}

				d3.select("#pie_theme")
					.datum(themeData)
					.attr('width', width)
					.attr('height', height)
					.attr('viewBox', '0 0 ' + width + ' ' + height)
					.transition().duration(500)
					.attr('perserveAspectRatio', 'xMinYMid')
					.call(chart);

				nv.utils.windowResize(chart.update);
				return chart;
			});

			var dbVersionData = d3.nest()
				.key(function(d) { return d.db_version;})
				.sortKeys(d3.ascending)
				.rollup(function(d) {
					return d3.sum(d, function(g) {return 1; });
				})
				.entries(csv_site_data);

			dbVersionData.forEach(function(d) {
				d.value = d.values;
				delete(d.values);
			});

			console.log('dbVersionData');
			console.log(dbVersionData);

			nv.addGraph(function() {
				var chart = nv.models.pieChart()
						.x(function(d) { return d.key })
						.y(function(d) { return d.value })
						.showTooltipPercent(true)
						.width(width)
						.height(height)
						.showLegend(true)
						.showLabels(true)
						.labelsOutside(false)
					;

				if(dbVersionData.length > 10) {
					chart.showLegend(false);
				}

				d3.select("#pie_db_version")
					.datum(dbVersionData)
					.attr('width', width)
					.attr('height', height)
					.attr('viewBox', '0 0 ' + width + ' ' + height)
					.transition().duration(500)
					.attr('perserveAspectRatio', 'xMinYMid')
					.call(chart);

				nv.utils.windowResize(chart.update);
				return chart;
			});

			/**
			 * The Sites Registered Timeseries
			 */

			//height = 500;
			var widthFull = 1000;

			var phpDateFormat = d3.time.format("%Y-%m-%d %H:%M:%S");
			var dateFormat = d3.time.format("%Y-%m-%d");
			var todFormat = d3.time.format("%H:%M");

			csv_site_data.forEach(function(d) {
				//console.log(d.registered);
				//console.log(format.parse(d.registered));
				d.registered = phpDateFormat.parse(d.registered);
				//d.value = +d.value;
			});

			var sitesRegisteredData = d3.nest()
				.key(function(d) { return dateFormat(d.registered);})
				.sortKeys(d3.ascending)
				.rollup(function(d) {
					return d3.sum(d, function(g) {return 1; });
				})
				.entries(csv_site_data);

			sitesRegisteredData.forEach(function(d) {
				d.x = dateFormat.parse(d.key);
				d.y = d.values;
				delete(d.values);
			});

			console.log('sitesRegisteredData')
			console.log(sitesRegisteredData);

			csv_user_data.forEach(function(d) {
				//console.log(d.registered);
				//console.log(format.parse(d.registered));
				d.user_registered = phpDateFormat.parse(d.user_registered);
				//d.value = +d.value;
			});
			var usersRegisteredData = d3.nest()
				.key(function(d) { return dateFormat(d.user_registered);})
				.sortKeys(d3.ascending)
				.rollup(function(d) {
					return d3.sum(d, function(g) {return 1; });
				})
				.entries(csv_user_data);

			usersRegisteredData.forEach(function(d) {
				d.x = dateFormat.parse(d.key);
				d.y = d.values;
				delete(d.values);
			});

			console.log('usersRegisteredData')
			console.log(usersRegisteredData);


			/*
			var x= d3.time.scale()
					.domain(d3.extent(sitesRegisteredData, function(d) { return d.x; }))
					.range([0, widthFull])
				;

			// define the y scale  (vertical)
			var y = d3.scale.linear()
				.domain([0, d3.max(sitesRegisteredData, function(d) { return d.y; })])    // values between 0 and 100
				.range([height, 0]);   // map these to the chart height, less padding.
			//REMEMBER: y axis range has the bigger number first because the y value of zero is at the top of chart and increases as you go down.

			console.log(x.domain());
			console.log(y.domain());
			*/

			nv.addGraph(function() {
				var chart = nv.models.lineWithFocusChart()
					.margin({left: 100})  //Adjust chart margins to give the x-axis some breathing room.
					.margin({right: 100})  //Adjust chart margins to give the x-axis some breathing room.
					.useInteractiveGuideline(true)  //We want nice looking tooltips and a guideline!
					.showLegend(true)       //Show the legend, allowing users to turn on/off line series.
					.showYAxis(true)        //Show the y-axis
					.showXAxis(true)        //Show the x-axis
					.width(widthFull)
					.height(height)
					.color(d3.scale.category10().range());
				;

				chart.useInteractiveGuideline(true);

				var tickMultiFormat = d3.time.format.multi([
					["%-I:%M%p", function(d) { return d.getMinutes(); }], // not the beginning of the hour
					["%-I%p", function(d) { return d.getHours(); }], // not midnight
					["%b %-d", function(d) { return d.getDate() != 1; }], // not the first of the month
					["%b %-d", function(d) { return d.getMonth(); }], // not Jan 1st
					["%Y", function() { return true; }]
				]);

				chart.xAxis     //Chart x-axis settings
					.axisLabel('Time')
					.tickFormat(function (d) { return dateFormat(new Date(d)); })

				chart.x2Axis
					.tickFormat(function (d) { return dateFormat(new Date(d)); })

				chart.yAxis     //Chart y-axis settings
					.axisLabel('Number of Registrations')
					.tickFormat(d3.format(',r'));

				/* Done setting the chart up? Time to render it!*/
				//Line chart data should be sent as an array of series objects.
				var lineChartData = [
					{values: sitesRegisteredData, key: 'Sites Registered'},
					{values: usersRegisteredData, key: 'Users Registered'}
				];

				d3.select('#line_registrations')    //Select the <svg> element you want to render the chart in.
					.datum(lineChartData)         //Populate the <svg> element with chart data...
					.attr('width', widthFull)
					.attr('height', height)
					.attr('viewBox', '0 0 ' + widthFull + ' ' + height)
					.attr('perserveAspectRatio', 'xMinYMid')
					.transition().duration(500)
					.call(chart);          //Finally, render the chart!

				//Update the chart when window resizes.
				nv.utils.windowResize(chart.update);
				return chart;
			});

			/*
			var sitesRegisteredTimeData = csv_site_data;
			sitesRegisteredTimeData.forEach(function(d) {
				d.x = dateFormat(d.registered);
				d.y = d.registered.getHours();
				d.size = 1;
			});

			console.log("sitesRegisteredTimeData");
			console.log(sitesRegisteredTimeData);

			nv.addGraph(function() {
				var chart = nv.models.scatterChart()
					.showDistX(true)
					.showDistY(true)
					.color(d3.scale.category10().range())
					;

				//Axis settings
				chart.xAxis     //Chart x-axis settings
					.axisLabel('Date')
					.tickFormat(function (d) { return dateFormat(new Date(d)); })

				chart.yAxis     //Chart y-axis settings
					.axisLabel('Hour')
					.tickFormat(function (d) { return d })

				d3.select('#scatter_site_registrations')
					.datum(sitesRegisteredTimeData)
					.transition().duration(500)
					.call(chart);

				nv.utils.windowResize(chart.update);

				return chart;
			});
			*/

			/*

			 var chart;

			 var halfBarXMin = data[0].values[0].x - barTimespan / 2 * 1000;
			 var halfBarXMax = data[0].values[data[0].values.length-1].x + barTimespan / 2 * 1000;

			 function renderChart(location, meaning) {
			 nv.addGraph(function() {
			 chart = nv.models.historicalBarChart();
			 chart
			 .xScale(d3.time.scale()) // use a time scale instead of plain numbers in order to get nice round default values in the axis
			 .color(['#68c'])
			 .forceX([halfBarXMin, halfBarXMax]) // fix half-bar problem on the first and last bars
			 .useInteractiveGuideline(true) // check out the css that turns the guideline into this nice thing
			 .margin({"left": 80, "right": 50, "top": 20, "bottom": 30})
			 .duration(0)
			 ;

			 var tickMultiFormat = d3.time.format.multi([
			 ["%-I:%M%p", function(d) { return d.getMinutes(); }], // not the beginning of the hour
			 ["%-I%p", function(d) { return d.getHours(); }], // not midnight
			 ["%b %-d", function(d) { return d.getDate() != 1; }], // not the first of the month
			 ["%b %-d", function(d) { return d.getMonth(); }], // not Jan 1st
			 ["%Y", function() { return true; }]
			 ]);
			 chart.xAxis
			 .showMaxMin(false)
			 .tickPadding(10)
			 .tickFormat(function (d) { return tickMultiFormat(new Date(d)); })
			 ;

			 chart.yAxis
			 .showMaxMin(false)
			 .tickFormat(d3.format(",.0f"))
			 ;

			 var svgElem = d3.select(location);
			 svgElem
			 .datum(data)
			 .transition()
			 .call(chart);

			 // make our own x-axis tick marks because NVD3 doesn't provide any
			 var tickY2 = chart.yAxis.scale().range()[1];
			 var lineElems = svgElem
			 .select('.nv-x.nv-axis.nvd3-svg')
			 .select('.nvd3.nv-wrap.nv-axis')
			 .select('g')
			 .selectAll('.tick')
			 .data(chart.xScale().ticks())
			 .append('line')
			 .attr('class', 'x-axis-tick-mark')
			 .attr('x2', 0)
			 .attr('y1', tickY2 + 4)
			 .attr('y2', tickY2)
			 .attr('stroke-width', 1)
			 ;

			 // set up the tooltip to display full dates
			 var tsFormat = d3.time.format('%b %-d, %Y %I:%M%p');
			 var contentGenerator = chart.interactiveLayer.tooltip.contentGenerator();
			 var tooltip = chart.interactiveLayer.tooltip;
			 tooltip.contentGenerator(function (d) { d.value = d.series[0].data.x; return contentGenerator(d); });
			 tooltip.headerFormatter(function (d) { return tsFormat(new Date(d)); });

			 // common stuff for the sections below
			 var xScale = chart.xScale();
			 var xPixelFirstBar = xScale(data[0].values[0].x);
			 var xPixelSecondBar = xScale(data[0].values[0].x + barTimespan * 1000);
			 var barWidth = xPixelSecondBar - xPixelFirstBar; // number of pixels representing time delta per bar

			 // fix the bar widths so they don't overlap when there are gaps
			 function fixBarWidths(barSpacingFraction) {
			 svgElem
			 .selectAll('.nv-bars')
			 .selectAll('rect')
			 .attr('width', (1 - barSpacingFraction) * barWidth)
			 .attr('transform', function(d, i) {
			 var deltaX = xScale(data[0].values[i].x) - xPixelFirstBar;
			 deltaX += barSpacingFraction / 2 * barWidth;
			 return 'translate(' + deltaX + ', 0)';
			 })
			 ;
			 }

			 */
			/*
			 If you're representing sample measurements spaced a certain time apart, the tick marks should
			 be in the middle of the bars and some spacing between bars is recommended to aid with interpretation.
			 On the other hand, if you want to represent a quantity measured over a span of time (one bar), you're
			 better off placing the ticks on the edge of the bar and leaving no gap in between bars.
			 */
			/*
			 function shiftXAxis() {
			 var xAxisElem = svgElem.select('.nv-axis.nv-x');
			 var transform = xAxisElem.attr('transform');
			 var xShift = -barWidth/2;
			 transform = transform.replace('0,', xShift + ',');
			 xAxisElem.attr('transform', transform);
			 }

			 if (meaning === 'instant') {
			 fixBarWidths(0.2);
			 }
			 else if (meaning === 'timespan') {
			 fixBarWidths(0.0);
			 shiftXAxis();
			 }

			 return chart;
			 });
			 }

			 renderChart('#test1', 'instant');
			 renderChart('#test2', 'timespan');

			 window.setTimeout(function() {
			 window.setTimeout(function() {
			 document.getElementById('sc-one').style.display = 'block';
			 document.getElementById('sc-two').style.display = 'none';
			 }, 0);
			 }, 0);
			 */

		}

		init_visualizations();
	});

})( jQuery );