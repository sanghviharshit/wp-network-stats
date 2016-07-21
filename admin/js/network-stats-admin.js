
/**
 * Hack for bootstrap inside a container
 */
var bootstrapCss = 'bootstrapCss';
if (!document.getElementById(bootstrapCss))
{
	var head = document.getElementsByTagName('head')[0];
	var bootstrapWrapper = document.createElement('link');
	bootstrapWrapper.id = bootstrapCss;
	bootstrapWrapper.rel = 'stylesheet/less';
	bootstrapWrapper.type = 'text/css';
	bootstrapWrapper.href = '../css/bootstrap-wrapper.less';
	bootstrapWrapper.media = 'all';
	head.appendChild(bootstrapWrapper);

	/*
	 var lessjs = document.createElement('script');
	 lessjs.type = 'text/javascript';
	 lessjs.src = '../wp-content/plugins/myplugin/scripts/less.min.js';
	 head.appendChild(lessjs);
	 */

	//load other stylesheets that override bootstrap styles here, using the same technique from above
	/*
	 var customStyles = document.createElement('link');
	 customStyles.id = "customStyles";
	 customStyles.rel = 'stylesheet';
	 customStyles.type = 'text/css';
	 customStyles.href = '../wp-content/plugins/myplugin/css/styles.css';
	 customStyles.media = 'all';
	 head.appendChild(customStyles);
	 */
}


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