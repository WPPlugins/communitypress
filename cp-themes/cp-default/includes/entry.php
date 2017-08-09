<?php $thumb = ''; 	  

	$width = get_option('minimal_thumbnail_width_usual');
	$height = get_option('minimal_thumbnail_height_usual');
	$classtext = 'thumbnail-post alignleft';
	$titletext = get_the_title();
	
	$thumbnail = get_thumbnail($width,$height,$classtext,$titletext,$titletext);
	$thumb = $thumbnail["thumb"]; ?>
	
<?php global $post;
	  $page_result = is_search() && ($post->post_type == 'page') ? true : false; ?>	
	  
<h2 class="title<?php if ($page_result) echo(' page_result'); ?>"><a href="<?php the_permalink() ?>" title="<?php printf(__ ('Permanent Link to %s', 'Minimal'), $titletext) ?>"><?php the_title(); ?></a></h2>

<?php if ((get_option('minimal_postinfo1') <> '') && !($page_result)) { ?>
	<?php include(TEMPLATEPATH . '/includes/postinfo.php'); ?>
<?php }; ?>

<?php if($thumb <> '' && get_option('minimal_thumbnails_index') == 'on') { ?>
	<a href="<?php the_permalink() ?>" title="<?php printf(__ ('Permanent Link to %s', 'Minimal'), $titletext) ?>">
		<?php print_thumbnail($thumb, $thumbnail["use_timthumb"], $titletext , $width, $height, $classtext); ?>
	</a>
<?php }; ?>

<?php if (get_option('minimal_blog_style') == 'on') the_content(""); else { ?>
	<p><?php truncate_post(400); ?></p>
<?php }; ?>
<a class="readmore" href="<?php the_permalink(); ?>"><span><?php _e('Read More','Minimal'); ?></span></a>
<div class="clear"></div>