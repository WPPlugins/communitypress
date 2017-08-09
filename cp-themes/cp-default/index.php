<?php if (is_archive()) $post_number = get_option('minimal_archivenum_posts');
if (is_search()) $post_number = get_option('minimal_searchnum_posts');
if (is_tag()) $post_number = get_option('minimal_tagnum_posts');
if (is_category()) $post_number = get_option('minimal_catnum_posts'); ?>
<?php get_header(); ?>
	
	<?php global $query_string; 
	if (is_category()) query_posts($query_string . "&showposts=$post_number&paged=$paged&cat=$cat");
	else query_posts($query_string . "&showposts=$post_number&paged=$paged"); ?>
		
	<div id="content" class="clearfix">
		<div id="content-area"<?php if ( isset($fullWidthPage) && $fullWidthPage ) echo(' class="fullwidth_home"');?>>
			<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
			
				<div class="entry clearfix">
					<?php include(TEMPLATEPATH . '/includes/entry.php'); ?>
				</div> <!-- end .entry -->
				
			<?php endwhile; ?>
				
				<?php if(function_exists('wp_pagenavi')) { wp_pagenavi(); }
					else { ?>	
							<?php include(TEMPLATEPATH . '/includes/navigation.php'); ?>

				<?php } ?>

			<?php else : ?>
				<?php include(TEMPLATEPATH . '/includes/no-results.php'); ?>
			<?php endif; wp_reset_query(); ?>

		</div> <!-- end #content-area -->	
	
<?php get_sidebar(); ?>
	</div> <!-- end #content --> 

<?php get_footer(); ?>	