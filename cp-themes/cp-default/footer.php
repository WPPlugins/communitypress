<?php if ( "on" == get_site_option('cp-foot-widgets') ) : ?>
<div id="pagediv"></div>
<div id="topfooter">
    <div class="footer1">
        <ul id="footerwidgeted-1">
            <?php if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar('Footer #1') ) : ?>
			<li id="link-list-1">
                <h4><?php _e("Footer #1 Widget", 'studiopress'); ?></h4>
                	<p>Suggested widget to place here: <br /> Recently Active Member Avatars</p>
            </li>
			<?php endif; ?> 
        </ul>	
    </div>	
    
    <div class="footer2">
        <ul id="footerwidgeted-2">
            <?php if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar('Footer #2') ) : ?>
			<li id="link-list-2">
                <h4><?php _e("Footer #2 Widget", 'studiopress'); ?></h4>
                	<p>Suggested widget to place here: <br /> Events</p>
            </li>
			<?php endif; ?> 
        </ul>	
    </div>	
    
    <div class="footer3">
        <ul id="footerwidgeted-3">
            <?php if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar('Footer #3') ) : ?>
			<li id="link-list-3">
                <h4><?php _e("Footer #3 Widget", 'studiopress'); ?></h4>
                	<p>Suggested widget to place here: <br /> Who's Online Avatars</p>
            </li>
			<?php endif; ?> 
        </ul>	
    </div>	
    
    <div class="footer4">
        <ul id="footerwidgeted-4">
            <?php if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar('Footer #4') ) : ?>
			<li id="link-list-4">
                <h4><?php _e("Footer #4 Widget", 'studiopress'); ?></h4>
                	<p>Suggested widget to place here: <br /> Groups</p>
            </li>
			<?php endif; ?> 
        </ul>	
    </div>
<div class="clear"></div>
</div>
<?php endif; ?>

	 <div id="footer2" >
		<div id="footer-content">
			
				<?php global $is_footer;
				$is_footer = true; ?>
				
				<?php $menuClass = 'bottom-menu';
				$footerNav = '';
				
				if (function_exists('wp_nav_menu')) $footerNav = wp_nav_menu( array( 'theme_location' => 'footer-menu', 'container' => '', 'fallback_cb' => '', 'menu_class' => $menuClass, 'echo' => false, 'depth' => '1' ) );
				if ($footerNav == '') show_page_menu($menuClass);
				else echo($footerNav); ?>
			
			<p id="copyright">Designed by <a href="http://jessesoffice.com">Jesse LaReaux</a></p>
		</div> <!-- end #footer-content -->
	</div> <!-- end #footer -->
</div> <!-- end #page-wrap -->
	 
				
	<?php include(TEMPLATEPATH . '/includes/scripts.php'); ?>

	<?php wp_footer(); ?>	
</body>
</html>