<?php if (get_option('minimal_duplicate') == 'false') {
		$args=array(
		   'showposts'=>get_option('minimal_homepage_posts'),
		   'post__not_in' => $ids,
		   'paged'=>$paged,
		   'category__not_in' => get_option('minimal_exlcats_recent'),
		);
	} else {
		$args=array(
		   'showposts'=>get_option('minimal_homepage_posts'),
		   'paged'=>$paged,
		   'category__not_in' => get_option('minimal_exlcats_recent'),
		);
	};
	query_posts($args);
	if (have_posts()) : while (have_posts()) : the_post(); ?>
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