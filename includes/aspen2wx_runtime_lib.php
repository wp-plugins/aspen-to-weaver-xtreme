<?php

// # Aspen SW Globals ==============================================================
$aspen2wx_opts_cache = false;	// internal cache for all settings


// ===============================  options =============================

function aspen2wx_getopt($opt) {
    global $aspen2wx_opts_cache;
    if (!$aspen2wx_opts_cache)
        $aspen2wx_opts_cache = get_option('aspen2wx_settings' ,array());

    if (!isset($aspen2wx_opts_cache[$opt]))	// handles changes to data structure
      {
	return false;
      }
    return $aspen2wx_opts_cache[$opt];
}

function aspen2wx_setopt($opt, $val, $save = true) {
    global $aspen2wx_opts_cache;
    if (!$aspen2wx_opts_cache)
        $aspen2wx_opts_cache = get_option('aspen2wx_settings' ,array());

    $aspen2wx_opts_cache[$opt] = $val;
    if ($save)
		aspen2wx_wpupdate_option('aspen2wx_settings',$aspen2wx_opts_cache);
}

function aspen2wx_save_all_options() {
    global $aspen2wx_opts_cache;
    aspen2wx_wpupdate_option('aspen2wx_settings',$aspen2wx_opts_cache);
}

function aspen2wx_delete_all_options() {
    global $aspen2wx_opts_cache;
    $aspen2wx_opts_cache = false;
    if (current_user_can( 'manage_options' ))
		delete_option( 'aspen2wx_settings' );
}

function aspen2wx_wpupdate_option($name,$opts) {
    if (current_user_can( 'manage_options' )) {
		update_option($name, $opts);
    }
}

// =============================== transient options =============================

if (!function_exists('aspen2wx_globals')) {
function aspen2wx_globals($glb) {
    return isset($GLOBALS[$glb]) ? $GLOBALS[$glb] : '';
}
}

if (!function_exists('aspen2wx_t_set')) {
function aspen2wx_t_set($opt, $val) {
    $GLOBALS['aspen_temp_opts'][$opt] = $val;
}
}

if (!function_exists('aspen2wx_t_get')) {
function aspen2wx_t_get($opt) {
    return isset($GLOBALS['aspen_temp_opts'][$opt]) ? $GLOBALS['aspen_temp_opts'][$opt] : '';
}
}

if (!function_exists('aspen2wx_t_clear')) {
function aspen2wx_t_clear($opt) {
    unset($GLOBALS['aspen_temp_opts'][$opt]);
}
}

if (!function_exists('aspen2wx_t_clear_all')) {
function aspen2wx_t_clear_all() {
    unset($GLOBALS['aspen_temp_opts']);
}
}

function waspen2wx_alert($msg) {
	echo "<script> alert('" . $msg . "'); </script>";
	// echo "<h1>*** $msg ***</h1>\n";
}
?>
