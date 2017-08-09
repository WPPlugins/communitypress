<?php
global $user_ID;
if( is_home() && is_user_logged_in() ) {
	$current_user = wp_get_current_user();
	$user_ID = $current_user->ID;
	$user_info = get_userdata( $user_ID );
	$user_date_time = strtotime($user_info->user_registered);
	// If user_registered date/time is less than 48 hours ago
	if ( $user_date_time < ( time() - 172800 ) ) {
		global $bp;
		wp_redirect ($bp->loggedin_user->domain );
	}
}
?>
<?php get_header(); ?>
<?php //Remove Slider  if (is_front_page() && get_option('minimal_featured') == 'on') include(TEMPLATEPATH . '/includes/featured.php');?>
<?php /*Romove Services if (get_option('minimal_services') == 'on') { ?>
	<?php if (get_option('minimal_featured') == 'false') { ?>
		<div id="services_bg">
	<?php }; ?>
			<div id="services" class="clearfix">
						
				<div class="one-third first">
					<?php query_posts('page_id=' . get_pageId(html_entity_decode(get_option('minimal_service_1')))); while (have_posts()) : the_post(); ?>
						<?php include(TEMPLATEPATH . '/includes/service_content.php'); ?>
					<?php endwhile; wp_reset_query(); ?>
				</div> <!-- end .one-third -->
				
				<div class="one-third">
					<?php query_posts('page_id=' . get_pageId(html_entity_decode(get_option('minimal_service_2')))); while (have_posts()) : the_post(); ?>
						<?php include(TEMPLATEPATH . '/includes/service_content.php'); ?>
					<?php endwhile; wp_reset_query(); ?>
				</div> <!-- end .one-third -->
				
				<div class="one-third">
					<?php query_posts('page_id=' . get_pageId(html_entity_decode(get_option('minimal_service_3')))); while (have_posts()) : the_post(); ?>
						<?php include(TEMPLATEPATH . '/includes/service_content.php'); ?>
					<?php endwhile; wp_reset_query(); ?>
				</div> <!-- end .one-third -->
				
			</div> <!-- end #services -->
	<?php if (get_option('minimal_featured') == 'false') { ?>
		</div> <!-- end #services_bg -->
	<?php }; ?>		
<?php }; */ ?>



<?php $fullWidthPage = true ?>

	<div id="content" class="clearfix<?php if ( $fullWidthPage ) echo(' pagefull_width'); if (get_option('minimal_featured') == 'false' && get_option('minimal_services') == 'false') echo(' nudge'); ?>">
		<div>


<?php function cplogin(){ 
global $wpdb, $bp;
$disabled_components = get_site_option( 'bp-deactivated-components' );
?>
<div class="login_pic" style="background-image: url(<?php echo $bp->root_domain . get_site_option('cp-homebg-url'); ?>);">
<div class="home_paragraph"><?php echo get_site_option('cp-home-text') ?></div></div>
<div class="loginform">     
<h3>Login</h3>   
		<?php $args = array(
        'echo' => true,
        'redirect' => home_url(), 
        'form_id' => 'loginform',
        'label_username' => __( 'Username' ),
        'label_password' => __( 'Password' ),
        'label_remember' => __( 'Remember Me' ),
        'label_log_in' => __( 'Log In' ),
        'id_username' => 'user_login',
        'id_password' => 'user_pass',
        'id_remember' => 'rememberme',
        'id_submit' => 'wp-submit',
        'remember' => true,
        'value_username' => NULL,
        'value_remember' => false ); 
         wp_login_form( $args ); ?>
         <ul>
         <?php wp_register(); ?>
		 <li><a href="<?php echo wp_lostpassword_url( get_bloginfo('url') ); ?>" title="Lost Password">Lost Password?</a></li>
         </ul>
         
<?php if ( !isset( $disabled_components['fbconnect'] ) ){ ?>      
         <p>Or...</p>
<?php
//Show a "Login with Facebook" button
jfb_output_facebook_btn();
//Initialize the Facebook API (and any login buttons on the page)
jfb_output_facebook_init();
//Output the JS callback that redirects us to process_login.php
jfb_output_facebook_callback();
}
?>
</div><?php } 


function cpgettingstarted(){ 
global $wpdb, $bp; ?>
<div class="login_pic" style="background-image: url(<?php echo $bp->root_domain . get_site_option('cp-homebg-url'); ?>);"></div>
<?php $disabled_components = get_site_option( 'bp-deactivated-components' ); ?>
<div class="new_paragraph"><h3>Congrats!</h3>
You are now ready to:
<ul>

	<li><a href="<?php echo bp_loggedin_user_domain();?>profile/edit/group/1"> - Edit your Profile</a></li>
    
	<li><a href="<?php echo bp_loggedin_user_domain();?>profile/change-avatar/"> - Change your Avatar</a></li>
        
	<li><a href="/members/"> - Browse Members</a></li>
    
    <?php if ( !isset( $disabled_components['bp-groups.php'] ) ){?>
	<li><a href="/groups/"> - Browse Groups</a></li>
    <?php } ?>
    
	<li><a href="<?php echo bp_loggedin_user_domain();?>wall/"> - Post on your Wall</a></li>
    
    <?php if ( !isset( $disabled_components['userprivacy'] ) ) { ?>
	<li><a href="<?php echo bp_loggedin_user_domain();?>settings/privacy"> - Set your profile privacy</a></li>
    <?php } ?>
    
</ul>
This site works a lot like Facebook.com: Users have a Profile with a Wall, news feed, friend connections and private messaging. Users can <?php if ( !isset( $disabled_components['bp-groups.php'] ) ){?>create groups and <?php } ?>browse members.<?php if ( !isset( $disabled_components['bp-forums.php'] ) ){?> We also have forums for each group.<?php } ?></div>

<?php } 



global $user_ID;
if( is_home() && is_user_logged_in() ) {
	cpgettingstarted();
} else {
	cplogin();
} 
?>


</div> <!-- end #content-area -->

	</div> <!-- end #content --> 

<?php get_footer(); ob_end_flush(); ?>
