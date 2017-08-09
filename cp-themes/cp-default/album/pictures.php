	<?php get_header('buddypress'); ?>
		<div id="content" class="clearfix">
			<div id="content-area">			
				<div class="entry_buddypress clearfix">

			<div id="item-header">
				<?php locate_template( array( 'members/single/member-header.php' ), true ) ?>
			</div>

			<div id="item-nav">
				<div class="item-list-tabs no-ajax" id="object-nav">
					<ul>
					<?php if ( bp_is_my_profile() ) : ?>
                        <?php bp_get_loggedin_user_nav() ?>
                    <?php else: ?>
                        <?php bp_get_displayed_user_nav() ?>
                    <?php endif; ?>
					</ul>
				</div>
			</div>

			<div id="item-body">

		<?php if ( bp_is_my_profile() ) : ?>
            <div class="item-list-tabs no-ajax" id="subnav">
                <ul>
                    <?php bp_get_options_nav() ?>
                </ul>
            </div>
		<?php endif; ?>
                
<?php if ( bp_privacy_filter("photos") ) : ?>
		<?php locate_template( array( 'members/single/not-friend.php' ), true ) ?>
	<?php else : ?>

					<?php if ( bp_album_has_pictures() ) : ?>
					
				<div class="picture-pagination">
					<?php bp_album_picture_pagination(); ?>	
				</div>			
					
				<div class="picture-gallery">												
						<?php while ( bp_album_has_pictures() ) : bp_album_the_picture(); ?>

				<div class="picture-thumb-box">
	
	                <a href="<?php bp_album_picture_url() ?>" class="picture-thumb"><img src='<?php bp_album_picture_thumb_url() ?>' /></a>
	                <a href="<?php bp_album_picture_url() ?>"  class="picture-title"><?php bp_album_picture_title_truncate() ?></a>	
				</div>
					
						<?php endwhile; ?>
				</div>			
                
					<?php else : ?>
		
					
				<div id="message" class="info">
					<p><?php echo bp_word_or_name( __('No pics here, show something to the community!', 'bp-album' ), __( "Either %s hasn't uploaded any picture yet or they have restricted access", 'bp-album' )  ,false,false) ?></p>
				</div>
				
				<?php endif; ?>
                <?php endif; ?>

			</div><!-- #item-body -->

				</div> <!-- end .entry -->		
			</div> <!-- end #content-area -->	
	<?php get_sidebar('BP-Profile'); ?>
		</div> <!-- end #content --> 
	<?php get_footer(); ?>
