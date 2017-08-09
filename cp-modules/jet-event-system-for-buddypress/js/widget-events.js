jQuery(document).ready( function() {
	jQuery(".widget div#events-list-options a").live('click',
		function() {
			jQuery('#ajax-loader-events').toggle();

			jQuery(".widget div#events-list-options a").removeClass("selected");
			jQuery(this).addClass('selected');

			jQuery.post( ajaxurl, {
				action: 'widget_events_list',
				'cookie': encodeURIComponent(document.cookie),
				'_wpnonce': jQuery("input#_wpnonce-events").val(),
				'max_events': jQuery("input#events_widget_max").val(),
				'filter': jQuery(this).attr('id')
			},
			function(response)
			{
				jQuery('#ajax-loader-events').toggle();
				events_wiget_response(response);
			});

			return false;
		}
	);
});

function events_wiget_response(response) {
	response = response.substr(0, response.length-1);
	response = response.split('0[[SPLIT]]');

	if ( response[0] != "-1" ) {
		jQuery(".widget ul#events-list").fadeOut(200,
			function() {
				jQuery(".widget ul#events-list").html(response[1]);
				jQuery(".widget ul#events-list").fadeIn(200);
			}
		);

	} else {
		jQuery(".widget ul#events-list").fadeOut(200,
			function() {
				var message = '<p>' + response[1] + '</p>';
				jQuery(".widget ul#events-list").html(message);
				jQuery(".widget ul#events-list").fadeIn(200);
			}
		);
	}
}