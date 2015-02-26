<?php
/*
Aspen to Weaver Xtreme - Core functions
*/

//===============================================================
// connect plugin to WP
// Set up Admin
// 1. Add Admin Menu and Admin scripts
// 2. Add runtime scripts

add_action('admin_menu', 'aspen2wx_add_page' /* priority: ,6 */);

function aspen2wx_add_page() {
    // the 'aspen_switcher' is the ?page= name for forms - use different if not add_theme_page

    //$page = add_theme_page(
	$page = add_management_page(
		'Aspen to Weaver Xtreme','Aspen X Converter','manage_options','aspen2wx_tools', 'aspen2wx_admin');
    add_action('admin_print_styles-' . $page, 'aspen2wx_load_admin_scripts');
}

function aspen2wx_admin() {       // This is the Appearance -> Aspen Plus admin menu
    require_once(dirname( __FILE__ ) . '/includes/aspen2wx_admin.php'); // NOW - load the admin stuff
    require_once(dirname( __FILE__ ) . '/includes/aspen2wx_admin_lib.php'); // NOW - load the plugin admin lib
    aspen2wx_admin_page();
}

function aspen2wx_load_admin_scripts() {
    // include any style sheets needed for admin side
    wp_enqueue_script('aspen2wx_Yetii', aspen2wx_plugins_url('/js/yetii',ASPEN2WX_MINIFY.'.js'));

    wp_enqueue_style('aspen2wx_admin_Stylesheet', aspen2wx_plugins_url('/aspen2wx_admin_style', ASPEN2WX_MINIFY . '.css'), array(), ASPEN2WX_VERSION);

    // @@@ wp_enqueue_style ("thickbox");	// @@@@ if we use media browser...
    // @@@ wp_enqueue_script ("thickbox");
}


//---- 2. Add any scripts needed by the plugin runtime

add_action('wp_enqueue_scripts', 'aspen2wx_enqueue_scripts' );    // enqueue runtime scripts
function aspen2wx_enqueue_scripts() {	// action definition

    //-- Aspen PLus js lib - requires jQuery...

    wp_enqueue_script('aspen2wxJSLib', aspen2wx_plugins_url('/js/aspen2wx_jslib', ASPEN2WX_MINIFY . '.js'),array('jquery'),ASPEN2WX_VERSION);

    // add plugin CSS here, too.

    wp_register_style('aspen2wx-style-sheet',aspen2wx_plugins_url('aspen2wx_style', ASPEN2WX_MINIFY.'.css'),ASPEN2WX_VERSION,'all');
    wp_enqueue_style('aspen2wx-style-sheet');
}

// =============================== utility functions =============================

if (!function_exists('aspen2wx_plugins_url')) {      // this must be in the plugin root to work right
function aspen2wx_plugins_url( $file,$ext ) {
    return plugins_url($file,__FILE__) . $ext;
}
}

require_once(dirname( __FILE__ ) . '/includes/aspen2wx_runtime_lib.php'); // NOW - load the plugin

?>
