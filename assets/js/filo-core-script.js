/*( function( $ ) {

	$(document).ready(function(){
		
		// Function for change a param inside of a href by name
		// http://blog.adrianlawley.com/jquery-change-url-parameter-value/
		// see also: filogy/assets/js/admin/settings.js
		function change_href_param_by_name___filocustman(href, paramName, newVal) {
			if (typeof href === 'string' || href instanceof String) {
				var tmpRegex = new RegExp("(" + paramName + "=)[[A-Za-z0-9%_\\-+]*", "ig"); //we need % for hendling umlencoded string and * (inside of +) to handle empty parameter, where there is no character after =
				return href.replace(tmpRegex, "$1"+newVal);  //$1 is the original parameter, and this is completed by the new value
			} 
		}
		
		// Function for delete a param inside of a href by name
		function delete_href_param_by_name___filocustman(href, paramName) {
			if (typeof href === 'string' || href instanceof String) {
				var tmpRegex = new RegExp("(" + paramName + "=)[[A-Za-z0-9%_\\-+]*", "ig");
				return href.replace(tmpRegex, "");  //we replace the parameter by '' string
			} 
		}
		
	});

})( jQuery );
*/