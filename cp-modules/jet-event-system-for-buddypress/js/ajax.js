// AJAX Functions
var jesjq = jQuery;

// Global variable to prevent multiple AJAX requests
// var bp_ajax_request = null;

jesjq(document).ready( function($) {

	var object = 'events';
	
	/**** Directory Search ****************************************************/

	/* The search form on all directory pages */
	jesjq('div.dir-search').click( function(event) {
		if ( jesjq(this).hasClass('no-ajax') )
			return;

		var target = jesjq(event.target);

		if ( target.attr('type') == 'submit' ) {
			var css_id = jesjq('div.item-list-tabs li.selected').attr('id').split( '-' );
			var object = css_id[0];

			bp_filter_request( object, jesjq.cookie('bp-' + object + '-filter'), jesjq.cookie('bp-' + object + '-scope') , 'div.' + object, target.parent().children('label').children('input').val(), 1, jesjq.cookie('bp-' + object + '-extras') );

			return false;
		}
	});

	/**** Tabs and Filters ****************************************************/

	/* When a navigation tab is clicked - e.g. | All Groups | My Groups | */
	jesjq('div.item-list-tabs').click( function(event) {
		if ( jesjq(this).hasClass('no-ajax') )
			return;

		var target = jesjq(event.target).parent();

		if ( 'LI' == event.target.parentNode.nodeName && !target.hasClass('last') ) {
			var css_id = target.attr('id').split( '-' );
			var object = css_id[0];

			if ( 'activity' == object )
				return false;

			var scope = css_id[1];
			var filter = jesjq("#" + object + "-order-select select").val();
			var search_terms = jesjq("#" + object + "_search").val();

			bp_filter_request( object, filter, scope, 'div.' + object, search_terms, 1, jesjq.cookie('bp-' + object + '-extras') );

			return false;
		}
	});

	/* When the filter select box is changed re-query */
	jesjq('li.filter select').change( function() {
		if ( jesjq('div.item-list-tabs li.selected').length )
			var el = jesjq('div.item-list-tabs li.selected');
		else
			var el = jesjq(this);

		var css_id = el.attr('id').split('-');
		var object = css_id[0];
		var scope = css_id[1];
		var filter = jesjq(this).val();
		var search_terms = false;

		if ( jesjq('div.dir-search input').length )
			search_terms = jesjq('div.dir-search input').val();

		if ( 'friends' == object )
			object = 'members';

		bp_filter_request( object, filter, scope, 'div.' + object, search_terms, 1, jesjq.cookie('bp-' + object + '-extras') );

		return false;
	});

	/* All pagination links run through this function */
	jesjq('div#content').click( function(event) {
		var target = jesjq(event.target);

		if ( target.hasClass('button') )
			return true;

		if ( target.parent().parent().hasClass('pagination') && !target.parent().parent().hasClass('no-ajax') ) {
			if ( target.hasClass('dots') || target.hasClass('current') )
				return false;

			if ( jesjq('div.item-list-tabs li.selected').length )
				var el = jesjq('div.item-list-tabs li.selected');
			else
				var el = jesjq('li.filter select');

			var page_number = 1;
			var css_id = el.attr('id').split( '-' );
			var object = css_id[0];
			var search_terms = false;

			if ( jesjq('div.dir-search input').length )
				search_terms = jesjq('div.dir-search input').val();

			if ( jesjq(target).hasClass('next') )
				var page_number = Number( jesjq('div.pagination span.current').html() ) + 1;
			else if ( jq(target).hasClass('prev') )
				var page_number = Number( jesjq('div.pagination span.current').html() ) - 1;
			else
				var page_number = Number( jesjq(target).html() );

			bp_filter_request( object, jq.cookie('bp-' + object + '-filter'), jesjq.cookie('bp-' + object + '-scope'), 'div.' + object, search_terms, page_number, jesjq.cookie('bp-' + object + '-extras') );

			return false;
		}

	});
	
	/** Invite Friends Interface ****************************************/

	/* Select a user from the list of friends and add them to the invite list */
	jesjq("div#event-invite-list input").click( function() {
		jesjq('.ajax-loader').toggle();

		var friend_id = jesjq(this).val();

		if ( jesjq(this).attr('checked') == true )
			var friend_action = 'invite';
		else
			var friend_action = 'uninvite';

		jesjq('div.item-list-tabs li.selected').addClass('loading');

		jesjq.post( ajaxurl, {
			action: 'events_invite_user',
			'friend_action': friend_action,
			'cookie': encodeURIComponent(document.cookie),
			'_wpnonce': jesjq("input#_wpnonce_invite_uninvite_user").val(),
			'friend_id': friend_id,
			'event_id': jesjq("input#event_id").val()
		},
		function(response)
		{
			if ( jesjq("#message") )
				jesjq("#message").hide();

			jesjq('.ajax-loader').toggle();

			if ( friend_action == 'invite' ) {
				jesjq('#event-friend-list').append(response);
			} else if ( friend_action == 'uninvite' ) {
				jesjq('#event-friend-list li#uid-' + friend_id).remove();
			}

			jesjq('div.item-list-tabs li.selected').removeClass('loading');
		});
	});

	/* Remove a user from the list of users to invite to a event */
	jesjq("#event-friend-list li a.remove").live('click', function() {
		jesjq('.ajax-loader').toggle();

		var friend_id = jesjq(this).attr('id');
		friend_id = friend_id.split('-');
		friend_id = friend_id[1];

		jesjq.post( ajaxurl, {
			action: 'events_invite_user',
			'friend_action': 'uninvite',
			'cookie': encodeURIComponent(document.cookie),
			'_wpnonce': jesjq("input#_wpnonce_invite_uninvite_user").val(),
			'friend_id': friend_id,
			'event_id': jesjq("input#event_id").val()
		},
		function(response)
		{
			jesjq('.ajax-loader').toggle();
			jesjq('#event-friend-list li#uid-' + friend_id).remove();
			jesjq('#invite-list input#f-' + friend_id).attr('checked', false);
		});

		return false;
	});
});