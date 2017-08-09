<?php /* Querystring is set via AJAX in _inc/ajax.php - bp_dtheme_activity_loop() */ 

$wallids = '';
function get_wall_ids (&$wallids)
{
$wallquery = 'user_id=' . $bp->displayed_user->id;
if ( bp_has_activities( '$wallquery' ) ) :
while ( bp_activities() ) : bp_the_activity();
$wall_id_list_a .= bp_get_activity_id() . ',';
endwhile;
endif;
if ( bp_has_activities( 'scope=mentions' ) ) :
while ( bp_activities() ) : bp_the_activity();
$wall_id_list_b .= bp_get_activity_id() . ',';
endwhile;
endif;
$wall_id_list_all = $wall_id_list_a . $wall_id_list_b;
$wallids = substr_replace($wall_id_list_all, '', -1, 1);
}



do_action( 'bp_before_activity_loop' );



get_wall_ids ($wallids);
$wallquery = 'include=' . $wallids;
//echo $wallquery;
if ( bp_has_activities( $wallquery ) ) : ?>



	<?php /* Show pagination if JS is not enabled, since the "Load More" link will do nothing */ ?>
	<noscript>
		<div class="pagination">
			<div class="pag-count"><?php bp_activity_pagination_count() ?></div>
			<div class="pagination-links"><?php bp_activity_pagination_links() ?></div>
		</div>
	</noscript>

	<?php if ( empty( $_POST['page'] ) ) : ?>
		<ul id="activity-stream" class="activity-list item-list">
	<?php endif; ?>

	<?php while ( bp_activities() ) : bp_the_activity(); ?>

		<?php include( locate_template( array( 'activity/entry.php' ), false ) ) ?>

	<?php endwhile; ?>

	<?php if ( bp_get_activity_count() == bp_get_activity_per_page() ) : ?>
		<li class="load-more">
			<a href="#more"><?php _e( 'Load More', 'buddypress' ) ?></a> &nbsp; <span class="ajax-loader"></span>
		</li>
	<?php endif; ?>

	<?php if ( empty( $_POST['page'] ) ) : ?>
		</ul>
	<?php endif; ?>

<?php else : ?>
	<div id="message" class="info">
		<p><?php _e( 'Sorry, there was no activity found. Please try a different tab...', 'buddypress' ) ?></p>
	</div>
<?php endif; ?>

<?php do_action( 'bp_after_activity_loop' ) ?>

<form action="" name="activity-loop-form" id="activity-loop-form" method="post">
	<?php wp_nonce_field( 'activity_filter', '_wpnonce_activity_filter' ) ?>
</form>