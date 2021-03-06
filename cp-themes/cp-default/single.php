<?php the_post(); ?>

<?php get_header(); ?>
	<?php if (get_option('minimal_integration_single_top') <> '' && get_option('minimal_integrate_singletop_enable') == 'on') echo(get_option('minimal_integration_single_top')); ?>	
	
	<div id="content" class="clearfix">
		<div id="content-area">			
			<div class="entry clearfix">
				<h1 class="title"><?php the_title(); ?></h1>
				<?php include(TEMPLATEPATH . '/includes/postinfo.php'); ?>
				
				<?php if (get_option('minimal_thumbnails') == 'on') { ?>
			
					<?php $width = get_option('minimal_thumbnail_width_posts');
						  $height = get_option('minimal_thumbnail_height_posts');
						  $classtext = 'thumbnail-post alignleft';
						  $titletext = get_the_title();
					
						  $thumbnail = get_thumbnail($width,$height,$classtext,$titletext,$titletext);
						  $thumb = $thumbnail["thumb"]; ?>
					
					<?php if($thumb <> '') print_thumbnail($thumb, $thumbnail["use_timthumb"], $titletext , $width, $height, $classtext); ?>
						
				<?php }; ?>
				
				<?php the_content(); ?>
				<?php wp_link_pages(array('before' => '<p><strong>'.__('Pages','Minimal').':</strong> ', 'after' => '</p>', 'next_or_number' => 'number')); ?>
				<?php edit_post_link(__('Edit this page','Minimal')); ?>
				
			</div> <!-- end .entry -->
			
			<?php if (get_option('minimal_integration_single_bottom') <> '' && get_option('minimal_integrate_singlebottom_enable') == 'on') echo(get_option('minimal_integration_single_bottom')); ?>		
			<?php if (get_option('minimal_468_enable') == 'on') { ?>
                      <?php if(get_option('minimal_468_adsense') <> '') echo(get_option('minimal_468_adsense'));
                    else { ?>
                       <a href="<?php echo(get_option('minimal_468_url')); ?>"><img src="<?php echo(get_option('minimal_468_image')); ?>" alt="468 ad" class="foursixeight" /></a>
               <?php } ?>   
            <?php } ?>
			
			<?php comments_template('', true); ?>
				
		</div> <!-- end #content-area -->	
	
<?php get_sidebar(); ?>
	</div> <!-- end #content --> 
	
<?php get_footer(); ?>