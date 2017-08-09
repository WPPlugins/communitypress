<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" <?php language_attributes(); ?>>
<head profile="http://gmpg.org/xfn/11">
<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo('charset'); ?>" />
<title><?php elegant_titles(); ?></title>
<?php elegant_description(); ?>
<?php elegant_keywords(); ?>
<?php elegant_canonical(); ?>
<script type='text/javascript' src='http://ajax.googleapis.com/ajax/libs/jquery/1.4.4/jquery.min.js'></script>
<script type='text/javascript' src='https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.7/jquery-ui.min.js'></script>
<script type='text/javascript' src='<?php global $bp; echo $bp->root_domain; ?>/wp-content/plugins/communitypress/cp-modules/cp-photo-tag/jquery.tag.js'></script>
<link media="screen" rel="stylesheet" href="<?php echo $bp->root_domain; ?>/wp-content/plugins/communitypress/cp-modules/cp-photo-tag/css/jquery.jtag.css" type="text/css" />
<link media="screen" rel="stylesheet" href="<?php echo $bp->root_domain; ?>/wp-content/plugins/communitypress/cp-modules/cp-photo-tag/css/jquery-ui.custom.css" type="text/css" />
<script type="text/javascript">
function showHint(str)
{
document.getElementById("livesearchform").style.color="#333";
if (str.length==0)
  { 
 	 document.getElementById("livesearch").innerHTML="";
 	 document.getElementById("livesearch").style.border="0px";
  	return;
  }
if (window.XMLHttpRequest)
  {// code for IE7+, Firefox, Chrome, Opera, Safari
  xmlhttp=new XMLHttpRequest();
  }
else
  {// code for IE6, IE5
  xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
  }
xmlhttp.onreadystatechange=function()
  {
  if (xmlhttp.readyState==4 && xmlhttp.status==200)
    {
		document.getElementById("livesearch").innerHTML=xmlhttp.responseText;
    	document.getElementById("livesearch").style.border="0px solid #A5ACB2";
	}
  }
xmlhttp.open("GET","<?php global $bp; echo $bp->root_domain; ?>/wp-content/plugins/communitypress/cp-modules/cp-livesearch/gethint.php?q="+str,true);
xmlhttp.send();
}
function hideHintUntimed()
{
 	 document.getElementById("livesearch").innerHTML="";
 	 document.getElementById("livesearch").style.border="0px";
	 document.getElementById("livesearchform").style.color="#CCC";

}
function hideHint()
{
setTimeout("hideHintUntimed()", 300);
}
</script>
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

<?php if ( is_singular() ) wp_enqueue_script( 'comment-reply' ); ?>
<?php wp_head(); ?>

</head>
<body<?php if (is_front_page()) echo(' id="home"'); ?>>
	<div id="page-wrap">