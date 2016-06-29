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
		
	});

})( jQuery );

//hljs.initHighlightingOnLoad();


function handleClickCancelPrevious(cb) {
	enableElements("submit", !cb.checked);
}
function enableElements(elm, value)
{
	document.getElementById(elm).disabled=value;
}
/*
function handleClickStatsSites(cb) {
	pluginStatsPerSiteChecked = document.getElementById("cb_plugins_per_site").checked;
	userStatsPerSiteChecked = document.getElementById("cb_users_per_site").checked
	if(pluginStatsPerSiteChecked || userStatsPerSiteChecked) {
		//document.getElementById("cb_sites").disabled = false;
		cb.checked = true;
		return false;
	}
}
function handleClickStatsPerSite(cb) {
	if(cb.checked) {
		document.getElementById("cb_sites").checked = true;
		//document.getElementById("cb_sites").disabled = true;
	} else {
		pluginStatsPerSiteChecked = document.getElementById("cb_plugins_per_site").checked;
		userStatsPerSiteChecked = document.getElementById("cb_users_per_site").checked
		if(!pluginStatsPerSiteChecked && !userStatsPerSiteChecked) {
			document.getElementById("cb_sites").disabled = false;
		}
	}

}
*/