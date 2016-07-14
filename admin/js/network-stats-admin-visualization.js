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
		/*
		 JS Date is number of milliseconds since 1970, PHP date is number of seconds since 1970.
		 
		var blog_registered_data = JSON.parse(data_to_js.blog_registered_data);
		var user_registered_data = JSON.parse(data_to_js.user_registered_data);

		timeseries('timeseries blogs', blog_registered_data, false);
		timeseries('timeseries users', user_registered_data, false);
		*/


		d3.csv("http://localhost/nyu/wp-content/uploads/ns_uploads/site-stats.csv", function(error, csv_data) {
			var data = d3.nest()
				.key(function(d) { return d.privacy;})
				.sortKeys(function(a,b) { return parseInt(b) - parseInt(a); })
				.rollup(function(d) { 
					return d3.sum(d, function(g) {return 1; });
				}).entries(csv_data);
			/*
			data.sort(function(a, b) {
        			return a.key < b.key;
    			});
			*/

			console.log(data);

			/*
			1 : I would like my blog to be visible to everyone, including search engines (like Google, Sphere, Technorati) and archivers. (default)
			0 : I would like to block search engines, but allow normal visitors.
			-1: Visitors must have a login - anyone that is a registered user of Web Publishing @ NYU can gain access.
			-2: Only registered users of this blogs can have access - anyone found under Users > All Users can have access.
			-3: Only administrators can visit - good for testing purposes before making it live.
			*/
			data.forEach(function(d) {
				if(d.key == "1") { d.key = "Public"; }
				if(d.key == "0") { d.key = "Hiddent"; }
				if(d.key == "-1") { d.key = "Require Login"; }
				if(d.key == "-2") { d.key = "Require Invite"; }
				if(d.key == "-3") { d.key = "Private"; }

				d.value = d.values;
				delete(d.values);
			});

			console.log(data);

		    var height = 350;
		    var width = 350;
		    nv.addGraph(function() {
		        var chart = nv.models.pieChart()
		            .x(function(d) { return d.key })
		            .y(function(d) { return d.value })
		            .width(width)
		            .height(height)
		            .showTooltipPercent(true);
		        d3.select("#pie_site_privacy")
		            .datum(data)
		            .transition().duration(1200)
		            .attr('width', width)
		            .attr('height', height)
		            .call(chart);
		        return chart;
		    });

			});



/*
		var testdata = [
		        {key: "One", y: 5},
		        {key: "Two", y: 2},
		        {key: "Three", y: 9},
		        {key: "Four", y: 7},
		        {key: "Five", y: 4},
		        {key: "Six", y: 3},
		        {key: "Seven", y: 0.5}
		    ];

		    var height = 350;
		    var width = 350;
		    nv.addGraph(function() {
		        var chart = nv.models.pieChart()
		            .x(function(d) { return d.key })
		            .y(function(d) { return d.value })
		            .width(width)
		            .height(height)
		            .showTooltipPercent(true);
		        d3.select("#pie_site_privacy")
		            .datum(testdata)
		            .transition().duration(1200)
		            .attr('width', width)
		            .attr('height', height)
		            .call(chart);
		        return chart;
		    });

*/
		    /*
		    nv.addGraph(function() {
		        var chart = nv.models.pieChart()
		            .x(function(d) { return d.key })
		            .y(function(d) { return d.y })
		            //.labelThreshold(.08)
		            //.showLabels(false)
		            .color(d3.scale.category20().range().slice(8))
		            .growOnHover(false)
		            .labelType('value')
		            .width(width)
		            .height(height);
		        // make it a half circle
		        chart.pie
		            .startAngle(function(d) { return d.startAngle/2 -Math.PI/2 })
		            .endAngle(function(d) { return d.endAngle/2 -Math.PI/2 });
		        // MAKES LABELS OUTSIDE OF PIE/DONUT
		        //chart.pie.donutLabelsOutside(true).donut(true);
		        // LISTEN TO CLICK EVENTS ON SLICES OF THE PIE/DONUT
		        // chart.pie.dispatch.on('elementClick', function() {
		        //     code...
		        // });
		        // chart.pie.dispatch.on('chartClick', function() {
		        //     code...
		        // });
		        // LISTEN TO DOUBLECLICK EVENTS ON SLICES OF THE PIE/DONUT
		        // chart.pie.dispatch.on('elementDblClick', function() {
		        //     code...
		        // });
		        // LISTEN TO THE renderEnd EVENT OF THE PIE/DONUT
		        // chart.pie.dispatch.on('renderEnd', function() {
		        //     code...
		        // });
		        // OTHER EVENTS DISPATCHED BY THE PIE INCLUDE: elementMouseover, elementMouseout, elementMousemove
		        // @see nv.models.pie
		        d3.select("#test2")
		            .datum(testdata)
		            .transition().duration(1200)
		            .attr('width', width)
		            .attr('height', height)
		            .call(chart);
		        // disable and enable some of the sections
		        var is_disabled = false;
		        setInterval(function() {
		            chart.dispatch.changeState({disabled: {2: !is_disabled, 4: !is_disabled}});
		            is_disabled = !is_disabled;
		        }, 3000);
		        return chart;
		    });
		    */
	});

})( jQuery );