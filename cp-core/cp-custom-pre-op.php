<?php

//define the default component
//define("BP_DEFAULT_COMPONENT","activity");// Now when you click on a user name link, You will land on User's wall not user's activity page


// customize the login page
function theme_custom_login() {
	global $bp, $wpdb;
	echo '<style type="text/css"/>html, body {height: 100%;background:#' . get_theme_mod( 'background_color', $default ) . ' url(' . get_theme_mod( 'background_image', $default ) . ') !important;}
h1 a { background-image:url(' . $bp->root_domain . get_site_option('cp-logo-url') . ') !important; width: 320px !important; height: 60px !important; padding-bottom: 10px !important;}
#backtoblog {display: none;}
#login {background-color: #fff; margin-top: 20px; padding: 15px;-moz-border-radius: 10px; -webkit-border-radius: 10px; -khtml-border-radius: 10px; border-radius: 10px; -moz-box-shadow: 0 0 5px 1px #000;    -webkit-box-shadow: 0 0 5px 1px #000; box-shadow: 0 0 5px 1px #000;}
form {box-shadow: 0 0 0 !important; border: 0 !important;}
form label {color: #9C7C67;}
#wp-submit{background:' . get_site_option('cp-bar-color') . ' url(' . $bp->root_domain . get_site_option('cp-bar-img') . ') !important;border:none !important;}
</style>';


}
add_action('login_head', 'theme_custom_login');
function cp_login_headerurl(){
	return home_url('/');
}
add_filter( 'login_headerurl', 'cp_login_headerurl');

?>