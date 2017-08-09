<?php $icon = get_post_meta($post->ID, 'Icon', true);
$tagline = get_post_meta($post->ID, 'Tagline', true); ?>

<img src="<?php echo $icon; ?>" alt="" class="icon" />
<h3 class="title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>

<?php if ($tagline != '' ) { ?>
	<p class="tagline"><?php echo($tagline);?></p>
<?php } ?>

<?php global $more;   
	  $more = 0;
	  the_content(""); ?>
 
<a href="<?php the_permalink(); ?>" class="readmore"><span><?php _e('Read more','Minimal'); ?></span></a>