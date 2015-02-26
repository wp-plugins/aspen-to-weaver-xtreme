<?php
/*
Aspen Themeworks - Plugin admin file
*/

function aspen2wx_process() {
    // add a nonced form for each needed action
    // TAB 1 Options


	if (aspen2wx_submitted('uploadtheme') &&  isset($_POST['uploadit']) && $_POST['uploadit'] == 'yes') {
		if ( aspen2wx_loadtheme() ) {
			if ( aspen2wx_converttheme() )
				aspen2wx_save_msg("Aspen theme options converted and ready to download to your computer.");
				//@@@@echo '<pre>'; print_r(aspen2wx_getopt('wx_converted')); echo '</pre>';
		}
    }

    if (aspen2wx_submitted('clear_settings')) {
		aspen2wx_delete_all_options();
		aspen2wx_save_msg('Previous Conversion Cleared');
    }

	if (aspen2wx_submitted('report_perpp')) {
		require(dirname( __FILE__ ) . '/convert_pp.php'); // load the conversion definitions
		aspen2wx_convert_pp('report');
		aspen2wx_save_msg('Per Page and Per Post conversion report generated.');
    }

	if (aspen2wx_submitted('convert_perpp')) {
		require(dirname( __FILE__ ) . '/convert_pp.php'); // load the conversion definitions
		aspen2wx_convert_pp('convert');
		aspen2wx_save_msg('Per Page and Per Post settings converted.');
    }
}

//==============================================================
// admin page

function aspen2wx_admin_page() {
    if ( !current_user_can( 'manage_options' ) )  {
	wp_die('You do not have sufficient permissions to access this page.');
    }

    // process commands
    aspen2wx_process();

    // display forms
?>
    <div class="atw-wrap">
	<div id="icon-themes" class="icon32"></div>
	<div style="float:left;padding-top:8px;"><h2>Aspen to Weaver Xtreme Admin</h2></div>
	<div style="clear:both;"></div>
	<p>
		This tool will non-destructively convert Aspen to Weaver Xtreme settings. <strong>Please</strong>,
		read the instructions on the "Help" tab before proceeding. While it is safe to directly convert a production site
		directly, it is always safer and less disruptive to visitors if you can first convert a development site.
	</p>
    <div style="clear:both;"></div>

<?php
    aspen2wx_tabs_container('generic-tab', 'padding-left:5px;');		// start of tabs definition

	aspen2wx_tabs_tab('tab1','Convert');		// define tab - needs id to match later aspen2wx_tabs_content + tab name
	aspen2wx_tabs_tab('tab2','Help');

	aspen2wx_tabs_content('tab1','aspen2wx_admin_tab1',true);	// match tab id + name of tab content function 1st one true
	aspen2wx_tabs_content('tab2','aspen2wx_admin_tab2');

    aspen2wx_tabs_end('generic-tab', '1');	// close it all up.
}

//========================================================================
// Tab 1

function aspen2wx_admin_tab1() {

	$fname = aspen2wx_getopt('filename');
?>
<h3 style="color:blue;">Conversion Options</h3>
<form enctype="multipart/form-data" action="<?php echo $_SERVER["REQUEST_URI"]; ?>" method="POST">
		<table>
			<tr><td><strong>Upload and convert Aspen theme settings from .ath or .abu file saved on your computer.</strong><br /><br /></td></tr>
<tr><td>
	Since you are converting an existing Aspen settings file, you don't have to be running on a live site,
	nor will the settings conversion disrupt your current site.
</td></tr>
<?php
		if ( $fname )
			echo "<tr><td><small><strong style='margin-left:10px;'>Current file: <em>{$fname}</em>. You can select and upload a new file to convert a different one.</strong></small><br /></td></tr>";
?>
				<tr valign="top">
						<td>Select .ath or .abu file to upload: <input style="border:1px solid black;" name="uploaded" type="file" />
						<input type="hidden" name="uploadit" value="yes" />
						</td>
				</tr>
				<tr><td><span class='submit'><input name="uploadtheme" type="submit" value="Upload and Convert theme/backup" /></span>&nbsp;<strong>Upload and Convert a Aspen settings file from your computer.</strong></td></tr>
				<tr><td>&nbsp;</td></tr>
		</table>
		<?php aspen2wx_nonce_field('uploadtheme'); ?>
</form>


<?php
	$fname = aspen2wx_getopt('filename');
	if ( !$fname ) {
?>
<br /><strong style="color:#000088;font-size:150%;">Please select file to convert first.</strong>
<?php
	} else {
		//echo esc_html('dump:' . print_r(aspen2wx_getopt('aspen_options'), true));
?>
<br /><strong style="color:#000088;font-size:130%;">Download Converted settings from <em><?php echo $fname; ?></em> to your computer.</strong>

<p>You converted settings have been saved in the WordPress database. As long as you can see this message, the
conversions from the listed Aspen settings file are available for download. By clicking the following Download link,
the converted settings will downloaded to the location of your choice on your own computer.
You can then use the Weaver Xtreme Save/Restore tab to load these converted
settings to your Weaver Xtreme site. <strong>Be sure to save your existing Weaver Xtreme settings first.</strong></p>
<?php
	$nonce = wp_create_nonce('aspen2wx_download');
	$time = date('Y-m-d-Hi');
	if (strpos( $fname, '.ath') !== FALSE ) {
		$dname = str_replace('.ath','',$fname);
		$ext = 'wxt';
	} else {
		$dname = str_replace('.abu','',$fname);
		$ext = 'wxb';
	}


	aspen2wx_download_link( '<strong>Save Conversion</strong> - Download converted settings to your computer',
		$dname, $ext, $nonce, $time );
?>

<hr />
<h3>Convert Aspen [aspen_xxx] Shortcodes to Weaver Xtreme equivalents</h3>
<p>If you've used Aspen shortcodes (e.g., [aspen_hide_if]), you will likely have the shortcodes
scattered throughout your content. Rather than try to convert these, a new plugin called <em>Weaver Theme
Compatibility</em> is available automatically supports the old Aspen shortcodes.
In fact, that plugin will allow you to use most Aspen, Weaver II, and even Weaver Xtreme shortcodes, with any
other WP theme.</p>
	<hr />
<?php
	}
?>
	<h3>Convert Per Page and Per Post Settings</h3>
	<p>Both Aspen and Weaver Xtreme support Per Page and Per Post settings. Most of Aspen Per Page/Post settings
	are supported by Weaver Xtreme, but with different internal names. This option will permanently, but non-destructively, copy your
	Aspen Per Page/Post settings to equivalent settings used by Weaver Xtreme.
	These new Custom Field settings are permanent, and can't easily be removed from your database.
	The old Aspen Per Page/Post settings will not be deleted, so you can switch back to using Aspen if needed.
	You will find that you will probably want to run the Per Page/Post conversion on your production site.
	<strong>But remember</strong>, it is always a very good idea to make a backup of your WP Database first.
	</p>

	<form id="aspen2wx_form4" name="aspen2wx_form4" action="<?php echo $_SERVER["REQUEST_URI"]; ?>" method="post">
		<span class="submit"><input type="submit" name="report_perpp" value="Generate Per Page/Post Pre-Conversion Report"/></span>
		-- This generates a report of all Pages and Posts that have Per Page/Post settings that need conversion to Weaver Xtreme.
		<?php aspen2wx_nonce_field('report_perpp'); ?>
	</form><br />

	<form id="aspen2wx_form4" name="aspen2wx_form4" action="<?php echo $_SERVER["REQUEST_URI"]; ?>" method="post">
		<span class="submit"><input type="submit" name="convert_perpp" value="Convert Per Page/Post Settings"/
		nSubmit="return confirm('Warning: this process will permanently add new values to your Page and Post Custom Field settings, and cannot be undone. You should backup the database first. Are you sure you want to do this now?');"></span>
		-- This action will copy all the Aspen Per Page and Per Post settings to new Weaver Xtreme values.
		<?php aspen2wx_nonce_field('convert_perpp'); ?>
	</form>
	<hr />

<?php
$set_name = '<em>Per Page/Post conversion</em>';
if ($fname)
	$set_name .= ' and <em>' . $fname . '</em>';
?>

<hr />
	<h3>Clear Current Conversion settings</h3>
	<form id="aspen2wx_form3" name="aspen2wx_form3" action="<?php echo $_SERVER["REQUEST_URI"]; ?>" method="post">
		<span class="submit"><input type="submit" name="clear_settings" value="Clear Conversion Settings"/></span>
		-- This will clear the conversion settings from <?php echo $set_name; ?>.
<?php aspen2wx_nonce_field('clear_settings'); ?>
	</form>
	<hr />
<?php
}

//========================================================================
// Tab 2

function aspen2wx_admin_tab2() {

?>
    <h2>Aspen to Weaver Xtreme Instructions</h2>
	<p>Aspen and Weaver Xtreme are very different themes. While they share a family history, they are
	as different as any two members of a "real" family. Weaver Xtreme is the newest theme, and has been
	updated to take advantage of the latest in web design technology.</p>
	<p>While these themes are different, they share enough history that it is possible to automatically
	convert many of your Aspen setting to mostly compatible Weaver Xtreme settings. Please note that
	this conversion is nowhere near 100% complete - but it will convert maybe 90% of your settings. This
	conversion has been designed is such a way that the converted settings will allow you to then take
	advantage of many of Weaver Xtreme's new features without being overly burdened with legacy
	constraints of Aspen.</p>
	<p>Note that the conversions done by this tool are non-destructive. You will always be able to go
	back to Aspen and have all your settings intact.</p>
	<h3>Converting Settings</h3>
	<p>Some of the main differences between Aspen and Weaver Xtreme are in 4 areas: Menus, Sidebars,
	the Header, and Font handling. Mostly, menus will convert quite well, but if you have custom CSS for you menus,
	it will likely have to be redone. </p>
	<p>Weaver Xtreme does not support separate widget areas (Left and Right)
	for two column widget areas - this is accomplished using Columns per widget are in Xtreme. Thus, sidebar
	widget area settings are all set to the Primary or Secondary sidebar areas as appropriate.
	The visual styling of widget areas and widgets is
	largely intact, but you will likely want to manually setup the new layouts. You may have to reorganize
	which widgets go where. This is usually a fairly simple process.
	</p>
	<p>Weaver Xtreme offers far greater
	customization of the Header area, and you may need to recreate your header, especially if you've
	used the header widget area or HTML insertion. Finally, since Weaver Xtreme has
	so much more flexibility in handling fonts than Aspen, and uses a completely different font
	family stack. The conversion will map the old font
	selections to similar fonts in Xtreme. You may need to manually reset your fonts. Custom
	Google Fonts are not converted.</p>

<ol>
	<li>You should have both Aspen and Weaver Xtreme installed.</li>
	<li>It is <strong>highly</strong> recommended that you create a backup of your WP DataBase first.</li>
	<li>Using Aspen's Save/Restore tab, download your settings - probably all settings to a .w2b file on your computer.</li>
	<li>Open Aspen to Weaver Xtreme from the Dashboard tools tab.</li>
	<li>From the Convert tab, use the Choose File button to select the Aspen settings file you want to convert.</li>
	<li>Click the "Upload and Convert theme/backup button. This will load the file, and convert it to Weaver Xtreme settings.</li>
	<li>At the top of the refreshed page, you will get a "CONVERSION REPORT". This report is <strong>important</strong>! It contains
	a summary of the incompatible settings, and more importantly, a list of settings that require manual conversion. You might want to
	copy/paste this report to an editor, or even a temporary WP page. If you lose track of this information, simply repeat the
	above conversion process. Nothing is lost.</li>
	<li>Finally, you should download the converted settings to your computer. This will create a .wxt or .wxb file
	that you can now upload to from the Weaver Xtreme Save/Restore tab.</li>
</ol>

<h3>Converting Per Page and Per Post Settings</h3>
<p>
	This converter will also convert your per page and per post settings from Aspen to Weaver Xtreme.
	This is a two step process. First, generate the report. This will give you a list of the pages that have
	Per Page/Post settings to convert. Then click the Convert button. This conversion is non-destructive - all
	your original Aspen settings remain intact. A new set of per page/post settings is added that is
	compatible with Weaver Xtreme.
</p>
<p>
	The report (and, in fact, the conversion itself) will show which settings were converted, which might need
	some manual tweaking, and which are not convertible. You can run the converter more than once - it won't create
	duplicates, but will convert any new per page/post settings you might have created while switching back
	to Aspen.
</p>
<h3>Converting Aspen Shortcodes</h3>
<p>A new plugin, Weaver Theme Compatibility, is available on WordPress.org. This new plugin supports most Aspen shortcodes for <em>any</em> theme, including Weaver Xtreme. This new plugin was
not released at the time this version of this converter version was released.</p>

<h3>More Conversion Information</h3>
<p>There also is a fairly detailed discussion of the conversion process found <a href="//forum.weavertheme.com/discussion/11303/converting-a-weaver-ii-pro-site-to-weaver-xtreme" target="_blank" alt="Conversion Discussion">
<strong>here</strong></a> on our forum.
</p>

	<hr />

<?php
}

//====================================================================
function aspen2wx_loadtheme() {
   // upload theme from users computer
	// they've supplied and uploaded a file


	$ok = true;     // no errors so far

	if (isset($_FILES['uploaded']['name']))
		$filename = $_FILES['uploaded']['name'];
	else
		$filename = "";

	if (isset($_FILES['uploaded']['tmp_name'])) {
		$openname = $_FILES['uploaded']['tmp_name'];
	} else {
		$openname = "";
	}

	//Check the file extension
	$check_file = strtolower($filename);
	$pat = '.';                             // PHP version strict checking bug...
	$end = explode($pat, $check_file);
	$ext_check = end($end);


	if ($filename == "") {
		$errors[] = "You didn't select a file to upload.<br />";
		$ok = false;
	}

	if ($ok && $ext_check != 'ath' && $ext_check != 'abu'){
		$errors[] = "Theme files must have <em>.ath</em> or <em>.abu</em> extension.<br />";
		$ok = false;
	}

	if ($ok) {
		if (!aspen2wx_f_exists($openname)) {
			$errors[] = '<strong><em style="color:red;">'.
			 aspen2wx_t_('Sorry, there was a problem uploading your file. You may need to check your folder permissions or other server settings.' /*a*/ ).'</em></strong>'.
				"<br />(Trying to use file '$openname')";
			$ok = false;
		}
	}
	if (!$ok) {
		echo '<div id="message" class="updated fade"><p><strong><em style="color:red;">ERROR</em></strong></p><p>';
		foreach($errors as $error){
			echo $error.'<br />';
		}
		echo '</p></div>';
		return false;
	} else {    // OK - read file and save to My Saved Theme
		// $handle has file handle to temp file.
		$contents = aspen2wx_f_get_contents($openname);
		// echo 'UPLOAD:' . esc_html($contents); // @@@@@@
		aspen2wx_setopt('filename', $filename, false);
		aspen2wx_setopt('openname', $openname, false);
		aspen2wx_setopt('aspen_options', $contents, false);
		aspen2wx_save_all_options();
	}
	return true;
}

//========================================

function aspen2wx_converttheme() {
	$wii = aspen2wx_getopt('aspen_options');
	if ( ! $wii ) {
		aspen2wx_error_msg('No settings to convert.');
		aspen2wx_delete_all_options();
		return false;
	}
	$file_type = substr($wii,0,10);
	if ($file_type != 'ATH-V01.00' && $file_type != 'ABU-V01.00') {
		aspen2wx_error_msg('Uploaded .ath or .abu file wrong format.');
		aspen2wx_delete_all_options();
		return false;
	}

	$wii_settings = array();
	$wii_settings = unserialize(substr($wii,10));
?>
<div style="border:1px solid black; padding:1em;background:#F8FFCC;width:95%;margin:1em;"><span style="font-size:120%;font-weight:bold;">
CONVERSION REPORT FOR <?php echo aspen2wx_getopt('filename'); ?>
</span> <br />

<?php
// ============= Actual conversion code here...

require(dirname( __FILE__ ) . '/conversions.php'); // load the conversion definitions

	$opts = $wii_settings['aspen_base'];      // fetch base opts
	$reportNS = array();
	$reportMC = array();
	$reportNC = array();
	$reportNOT = array();
	$nones = 0;
	$ns = 0;
	$mc = 0;
	$nc = 0;
	$cv = 0;
	global $aspen2wx_opts, $aspen2wx_wpad_set;
	$aspen2wx_wpad_set = false;	// no wrapper padding set...
	$aspen2wx_opts = array();


	foreach ( $opts as $opt => $value ) {
		if ( strlen($value) < 1 || $value == 'default')
			continue;
		if ( ! isset( $convert[$opt] )) {
			$report[] = aspen2wx_report("Unknown Aspen option - {$opt}:{$value}",'??');
			//$report[] = aspen2wx_report("'{$opt}' =&gt; '{$value}',");
			continue;
		}
		//continue;	// @@@@
		//echo esc_html("* {$opt}={$value}");
		if (isset($convert[$opt])) {
			$to = $convert[$opt];			// the to value
			if (strpos($to, 'none:NS:')  !== FALSE) {
				$to = substr($to, 8 );
				$opt_name = aspen2wx_fix_opt_name($opt);
				$reportNS[] = aspen2wx_report('Not Supported: ' . $to . " - [{$opt_name} = '{$value}']");
				$ns++;
				continue;
			} elseif (strpos($to, 'none:MC:') !== FALSE ) {
				$to = substr($to, 8 );
				$opt_name = aspen2wx_fix_opt_name($opt);
				$reportMC[] = aspen2wx_report('Convert Manually: ' . $to. " - [{$opt_name} = '{$value}']");
				$mc++;
				continue;
			} elseif (strpos($to, 'none:`') !== FALSE ) {
				$to = substr($to, 5 );
				$opt_name = aspen2wx_fix_opt_name($opt);
				$reportNC[] = aspen2wx_report('Incompatibility: ' . $to . " - [{$opt_name} = '{$value}']");
				$nc++;
				continue;
			} elseif (strpos($to, 'none') !== FALSE ) {
				$reportNOT[] = aspen2wx_fix_opt_name($opt);
				$nones++;
				continue;
			} elseif (strpos($to, 'admin') !== FALSE ) {
				continue;
			}

			// To here, than have something to convert

			$rules = explode(';',$to);		// split into separate rules
			foreach ($rules as $rule) {
				if ( strpos($rule,'|') !== FALSE) {
					$parts = explode('|',$rule);
					$function = 'aspen2wx_' . $parts[1];
					if (function_exists($function)) {
						$conv = $function($opt, $parts[0], $value);
						if ( $conv !== false )
							$aspen2wx_opts[$parts[0]] = $conv;
					} else {
						aspen2wx_report("Unknown conversion rule: {$rule}", "<strong>ERROR</strong>");
					}
				} else {
					$aspen2wx_opts[$rule] = $value;
				}
				$cv++;
			}

			// To here, than have something to convert
			//echo esc_html("*** [{$opt}:{$value}] -> {$to}") . '<br />';
		}
	}

	$report = "Conversion Report\nOriginal Aspen settings from " . aspen2wx_getopt('filename') . "\n\n";

	if (!empty($reportMC)) {
		echo "<h3>Settings that need <em>Manual Conversion</em> to Weaver Xtreme</h3>\n<ul style='margin-left:2em;list-style-type:disc;'>\n";
		foreach ($reportMC as $txt) {
			echo "<li>{$txt}</li>";
			$report .= $txt . "\n";
		}
		echo "</ul>\n";
	} else {
		echo "<h3>No settings need Manual Conversion to Weaver Xtreme</h3>\n";
	}

	if (!empty($reportNS)) {
		echo "<h3>Settings that are <em>Not Supported</em> by Weaver Xtreme</h3>\n<ul style='margin-left:2em;list-style-type:disc;'>\n";
		foreach ($reportNS as $txt) {
			echo "<li>{$txt}</li>";
			$report .= $txt . "\n";
		}
		echo "</ul>\n";
	}

	if (!empty($reportNC)) {
		echo "<h3>Other <em>Incompatible</em> settings with Weaver Xtreme</h3>\n<ul style='margin-left:2em;list-style-type:disc;'>\n";
		foreach ($reportNC as $txt) {
			echo "<li>{$txt}</li>";
			$report .= $txt . "\n";
		}
		echo "</ul>\n";
	}

	if (!empty($reportNOT)) {
		echo "<h4>Obsolete/incompatible settings unable to be converted - mostly sidebar (sb) and mobile options</h4>\n";
		$n = 0;
		foreach ($reportNOT as $txt) {
			$txt = str_replace('Aspen: ','',$txt);
			$n++;
			if ( $n > 5) {
				echo '<br />';
				$n = 1;
			}
			echo $txt . '; ';
		}
	}

	if ( !$aspen2wx_wpad_set ) {
		// the Aspen default for these is 10, and borders, etc. kind of depend on this happening.
		$aspen2wx_opts['wrapper_padding_B'] = 10;
		$aspen2wx_opts['wrapper_padding_L'] = 10;
		$aspen2wx_opts['wrapper_padding_R'] = 10;
		$aspen2wx_opts['wrapper_padding_T'] = 10;
	}

	if (isset($aspen2wx_opts['subtheme_notes']))
		$aspen2wx_opts['subtheme_notes'] .= "\n" . $report;
	else
		$aspen2wx_opts['subtheme_notes'] = $report;



	echo "<h4>Notes:</h4>Converted settings: {$cv}. Need Manual Conversion: {$mc}. Not supported {$ns}.<br />\n";

	echo "Other settings (mostly sidebar, mobile specific) not converted: <strong>{$nones}.</strong><br />\n";

	echo "This conversion report for basic settings will be included in the converted Weaver Xtreme <em>Advanced Options:Subtheme Notes</em> box.<br /><br />\n";
	aspen2wx_setopt('wx_converted',$aspen2wx_opts);
	echo "</div>\n";

	return true;
}

function aspen2wx_report($msg, $lead = '', $echo = false) {
	if ($echo)
		echo "<strong>{$lead}:&nbsp;</strong> " . esc_html($msg) . "<br />\n";
	return $msg;
}

function aspen2wx_fix_opt_name($opt) {
	$c = str_replace(array('wii_','_int','_dec'),'',$opt);
	return 'Aspen: ' . str_replace('_', ' ', $c);
}

function aspen2wx_rounded_corners($old_opt, $new_opt, $val) {
	global $aspen2wx_opts;
//echo "<br /><strong>old_opt:{$old_opt} - new_opt:{$new_opt} - val:{$val}<stong><br/>\n";
	if ($old_opt == 'wii_rounded_corners') {
		$areas = array('wrapper_rounded','primary_rounded','secondary_rounded','top_rounded','bottom_rounded',
				'header_rounded','footer_rounded');
		foreach ($areas as $area) {
			$aspen2wx_opts[$area] = '-all';
		}
		$aspen2wx_opts['m_primary_rounded'] = '-bottom';
		$aspen2wx_opts['m_secondary_rounded'] = '-top';
	} else if ($old_opt == 'wii_rounded_corners_content') {
		$aspen2wx_opts['content_rounded'] = '-all';
	}
	return false;
}

function aspen2wx_font_family($old_opt, $new_opt, $val) {
	//echo "<br /><strong>old_opt:{$old_opt} - new_opt:{$new_opt} - val:{$val}<stong><br/>\n";

	$converts = array(
			'"Helvetica Neue"' => 'sans-serif',
			'Arial' => 'sans-serif',
			'Verdana' => 'verdana',
			'Tahoma' => 'sans-serif',
			'"Arial Black"' => 'arialBlack',
			'"Avant Garde"' => 'sans-serif',
			'"Comic Sans MS"' => 'comicSans',
			'Impact' => 'arialBlack',
			'"Trebuchet MS"' => 'trebuchetMS',
			'"Century Gothic"' => 'sans-serif',
			'"Lucida Grande"' => 'lucidaSans',
			'Univers' => 'sans-serif',
			'"Times New Roman"' => 'serif',
			'"Bitstream Charter"' => 'serif',
			'Georgia' => 'georgia',
			'Palatino' => 'palatino',
			'Bookman' => 'serif',
			'Garamond' => 'garamond',
			'"Courier New"' => 'monospace',
			'"Andale Mono"' => 'consolas',
	);
	$new_font = 'sans-serif';
	foreach ( $converts as $convert => $font ) {
		if (strpos( $val, $convert ) === 0 ) {
			$new_font = $font;
			break;
		}
	}
	return $new_font;
}

function aspen2wx_wrapper_pad_set($old_opt, $new_opt, $val) {
	global $aspen2wx_wpad_set;
	$aspen2wx_wpad_set = true;
	return $val;
}

function aspen2wx_shadows($old_opt, $new_opt, $val) {
	return '-3';
}

function aspen2wx_fontsize_px($old_opt, $new_opt, $val) {
	return $val + 4;
}

function aspen2wx_title_fontsize($old_opt, $new_opt, $val) {	// convert to fontsize_title value
/* titles
 xxl - 2.625
 xl - 2.25
 l - 1.875
 m - 1.5
 s - 1.25
 xs - 1
 xxs - .875
 */
if ( $val >= 260)
		return 'xxl-font-size-title';
	else if ( $val >= 200)
		return 'xl-font-size-title';
	else if ( $val >= 180)
		return 'l-font-size-title';
	else if ( $val >= 150)
		return 'm-font-size-title';
	else if ( $val >= 125)
		return 's-font-size-title';
	else if ( $val >= 100)
		return 'xs-font-size-title';
	else if ( $val >= 70 )
		return 'xxs-font-size-title';
	else
		return 'm-font-size-title';
}

function aspen2wx_text_fontsize($old_opt, $new_opt, $val) {	// convert tt fontsize value
/*
 xxs- .625
 xs- .75
 s - .875
 m - 1.0
 l - 1.125
 xl - 1.25
 xxl - 1.5
  */
	if ( $val >= 150)
		return 'xxl-font-size';
	else if ( $val >= 125)
		return 'xl-font-size';
	else if ( $val >= 110)
		return 'l-font-size';
	else if ( $val >= 100)
		return 'm-font-size';
	else if ( $val >= 87)
		return 's-font-size';
	else if ( $val >= 75)
		return 'xs-font-size';
	else if ( $val >= 50 )
		return 'xxs-font-size';
	else
		return 'm-font-size';

}


function aspen2wx_layout($old_opt, $new_opt, $val) {
	$layouts = array(
		'default' => 'default',				// default
		'right-1-col' => 'right',    		// Single column sidebar on Right</option>
		'left-1-col' => 'left',    			// >Single column sidebar on Left</option>
		'right-2-col' => 'right',    		// >Double Cols, Right (top wide)</option>
		'left-2-col' => 'left',    			// >Double Cols, Left (top wide)</option>
		'right-2-col-bottom' => 'right',	// >Double Cols, Right (bottom wide)</option>
		'left-2-col-bottom' => 'left',    	// >Double Cols, Left (bottom wide)</option>
		'split' => 'split',    				// >Split - sidebars on Right and Left</option>
		'one-column' => 'one-column',    	// >No sidebars, one column content</option>
	);
	foreach ($layouts as $layout => $new_val) {
		if ( $layout == $val ) {
			if ( $new_val == 'default' &&
				($new_opt == 'layout_default' || $new_opt == 'layout_default_archive' || $new_opt == '_pp_page_layout') ) {
				return 'right';
			}
			return $new_val;
		}
	}
	return 'right';			// fallback
}

function aspen2wx_borders($old_opt, $new_opt, $val) {
	global $aspen2wx_opts;
	$areas = array('wrapper_border','primary_border','secondary_border','top_border','bottom_border');
	foreach ($areas as $area) {
		$aspen2wx_opts[$area] = 'on';
	}
	return false;
}

function aspen2wx_fix_container_bg($old_opt, $new_opt, $val) {
	// try to compensate for #main
	global $aspen2wx_opts;
	if (isset($aspen2wx_opts[$new_opt])) {	// main_bg must have set it already - don't reset if transparent
		if ( $val == 'transparent' )
			return false;
	}
	return $val;
}

function aspen2wx_css_fix($old_opt, $new_opt, $val) {
	require(dirname( __FILE__ ) . '/map_css.php'); // load the conversion definitions
	$new_val = str_replace ( $map_css['weaverii'], $map_css['weaverx'], $val);
	return $new_val;
}

function aspen2wx_hide($old_opt, $new_opt, $val) {
	// convert hide true/false to hide all
	return 'hide';
}
function aspen2wx_post_icons($old_opt, $new_opt, $val) {
	return 'fonticons';
}

function aspen2wx_set_current_to_serialized_values($contents)  {
	global $aspen2wx_cache;        // need to mess with the cache

	if (substr($contents,0,10) == 'ATH-V01.00')
		$type = 'theme';
	else if (substr($contents,0,10) == 'ABU-V01.00')
		$type = 'backup';
	else
		return aspen2wx_alert(aspen2wx_t_("Wrong theme file format version" /*a*/ ));  /* simple check for one of ours */
	$restore = array();
	$restore = unserialize(substr($contents,10));

	if (!$restore) return waspen2wx_alert("Unserialize of Aspen Theme failed");

	$version = aspen2wx_getopt('wii_version_id');       // get something to force load

	if ($type == 'theme') {
		// need to clear some settings
		// first, pickup the per-site settings that aren't theme related...
		$new_cache = array();
		foreach (_cache as $key => $val) {
			if ($key[0] == '_') // these are non-theme specific settings
				$new_cache[$key] = $val;        // keep
		}
		$opts = $restore['aspen_base'];      // fetch base opts
		aspen2wx_delete_all_options();

		foreach ($opts as $key => $val) {
			if ($key[0] != '_')
				aspen2wx_setopt($key, $val, false);     // overwrite with saved theme values
		}

		foreach ($new_cache as $key => $val) {  // set the values we need to keep
			aspen2wx_setopt($key,$val,false);
		}
	} else if ($type == 'backup') {
		aspen2wx_delete_all_options();

		$opts = $restore['aspen_base'];      // fetch base opts
		foreach ($opts as $key => $val) {
			aspen2wx_setopt($key, $val, false); // overwrite with saved values
		}

	}
	aspen2wx_setopt('aspen_version_id',$version); // keep version, force save of db
	aspen2wx_setopt('last_option','WeaverII');
	aspen2wx_save_opts('loading theme');        // OK, now we've saved the options, update them in the DB
	return true;
}
?>
