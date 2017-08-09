<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" <?php language_attributes(); ?>>
<head profile="http://gmpg.org/xfn/11">
<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo('charset'); ?>" />
<title><?php elegant_titles(); ?></title>
<?php elegant_description(); ?>
<?php elegant_keywords(); ?>
<?php elegant_canonical(); ?>

<link rel="stylesheet" href="<?php bloginfo('stylesheet_directory'); ?>/style.css" type="text/css" media="screen" />
<link rel="alternate" type="application/rss+xml" title="<?php bloginfo('name'); ?> RSS Feed" href="<?php bloginfo('rss2_url'); ?>" />
<link rel="alternate" type="application/atom+xml" title="<?php bloginfo('name'); ?> Atom Feed" href="<?php bloginfo('atom_url'); ?>" />
<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />

<!--[if lt IE 7]>
	<link rel="stylesheet" type="text/css" href="<?php bloginfo('stylesheet_directory'); ?>/css/ie6style.css" />
	<script type="text/javascript" src="<?php bloginfo('stylesheet_directory'); ?>/js/DD_belatedPNG_0.0.8a-min.js"></script>
	<script type="text/javascript">DD_belatedPNG.fix('img#logo, #header #search-form, #slogan, a#left_arrow, a#right_arrow, div.slide img.thumb, div#controllers a, a.readmore, a.readmore span, #services .one-third, #services .one-third.first img.icon, #services img.icon, div.sidebar-block .widget ul li');</script>
<![endif]-->
<!--[if IE 7]>
	<link rel="stylesheet" type="text/css" href="<?php bloginfo('stylesheet_directory'); ?>/css/ie7style.css" />
<![endif]-->

<script type="text/javascript">
	document.documentElement.className = 'js';
</script>

<?php wp_head(); ?>

</head>
<body<?php if (is_front_page()) echo(' id="home"'); ?>>
	<div id="page-wrap">

		<?php if(is_page() || is_category()) { ?>
        	      <div id="pagediv"><h1 class="pagetitle">Invite Facebook Friends</h1></div>
		<?php } ?>
        
        		
		<div id="content" class="clearfix pagefull_width">
			<div id="content-area">			
				<div class="entry clearfix">					
<div class="facebook">
<p>Invite your friends from Facebook. Just make sure your logged in at facebook.com too.</p>
  <fb:serverfbml style="width: 100%;">
	    <script type="text/fbml">
	      <fb:fbml>
	          <fb:request-form
	                    action="<?php echo get_option('siteurl'); ?>"
	                    method="GET"
	                    invite="true"
	                    type="<?php echo get_option('blogname');?>"
	                    content="<?php echo get_option('blogname')." : ".get_option('blogdescription'); ?>
	                 <fb:req-choice url='<?php echo get_option('siteurl'); ?>'
	                       label='<?php _e('Become a Member!', 'fbconnect') ?>' />
	              "
	              >

	                    <fb:multi-friend-selector
						rows="6"
						email_invite="true"
						cols="3"
	                    showborder="false"
	                    actiontext="<?php _e('Invite Your Friends To Our Site', 'fbconnect') ?>">

	        </fb:request-form>
	      </fb:fbml>

	    </script>
	  </fb:serverfbml>
</div>
				</div> <!-- end .entry -->
												
			</div> <!-- end #content-area -->	

		</div> <!-- end #content --> 
		
	<?php get_footer(); ?>
	