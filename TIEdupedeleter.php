<?php

/*
Plugin Name: TIEdupedeleter
Plugin URI: http://www.setupmyvps.com/tiedupedeleter
Description: Simple duplicate post deleter. Trashes duplicate posts based on status and category criteria.
Version: 1.0.2
Author: TIEro
Author URI: http://www.setupmyvps.com
License: GPL2
*/


// Register the hooks for plugin activation and deactivation.
register_activation_hook(__FILE__, 'do_TIEdupedeleter_activation');
register_deactivation_hook(__FILE__, 'do_TIEdupedeleter_deactivation');

// Add actions to define scheduled job and place settings menu on the Dashboard.
add_action('my_expiry_job', 'do_TIEdupedeleter');
add_action('admin_menu', 'TIEdupedeleter_settings_page');

// On plugin activation, schedule the hourly dupe deletion job. Set initialisation options.
function do_TIEdupedeleter_activation() {
	if( !wp_next_scheduled( 'TIEdupedeleter_job' ) ) {
		wp_schedule_event( current_time ( 'timestamp' ), 'hourly', 'TIEdupedeleter_job' ); 
	}
	add_option('TIEdupedeleter_powerbutton', 'off');
	add_option('TIEdupedeleter_status_published', 'publish');
	add_option('TIEdupedeleter_catsradio', 'include');
	add_option('TIEdupedeleter_newoldradio','MIN');
}

// On plugin deactivation, remove the scheduled job.
function do_TIEdupedeleter_deactivation() {
	// Remove scheduled expiry job
	wp_clear_scheduled_hook( 'TIEdupedeleter_job' );
}

// Define the Settings page function for options.
function TIEdupedeleter_settings_page() {
  add_menu_page('Dupe Deleter', 'Dupe Deleter', 'administrator', 'TIEdupedeleter_settings', 'TIEdupedeleter_option_settings');
}

// This is the scheduled job that runs every hour. It just calls one function, but that might change one day.
function do_TIEdupedeleter() {
	expireduplicates();
}

// Code for the options page on the Dashboard.
function TIEdupedeleter_option_settings() {

	// Get all the user-defined options for all expiry types, set neutral defaults if they don't exist.
	$pubposts = (get_option('TIEdupedeleter_status_published') == 'publish') ? 'checked' : '' ;
	$draftposts = (get_option('TIEdupedeleter_status_draft') == 'draft') ? 'checked' : '' ;
	$pendingposts = (get_option('TIEdupedeleter_status_pending') == 'pending') ? 'checked' : '' ;
	$privateposts = (get_option('TIEdupedeleter_status_private') == 'private') ? 'checked' : '' ;
	$catstoinclude = (get_option('TIEdupedeleter_catsin') != '') ? get_option('TIEdupedeleter_catsin') : '0';
	$catstoexclude = (get_option('TIEdupedeleter_catsout') != '') ? get_option('TIEdupedeleter_catsout') : '0';
	$catsincludeon = (get_option('TIEdupedeleter_catsradio') == 'include') ? 'checked' : '' ;
	$catsexcludeon = (get_option('TIEdupedeleter_catsradio') == 'exclude') ? 'checked' : '' ;
	$newbutton = (get_option('TIEdupedeleter_newoldradio') == 'MAX') ? 'checked' : '' ;
	$oldbutton = (get_option('TIEdupedeleter_newoldradio') == 'MIN') ? 'checked' : '' ;
	$poweron = (get_option('TIEdupedeleter_powerbutton') == 'on') ? 'checked' : '' ;
	$poweroff = (get_option('TIEdupedeleter_powerbutton') == 'off') ? 'checked' : '' ;
	
	// The header section line of the options page, with the logo and basic info. And the donation bit. :)
	$plugname = '</pre><div class="wrap">
				 <h2><img src="' . plugins_url( 'dupe.png' , __FILE__ ) . '" border=0 alt="Dupe Deleter Settings" style="vertical-align:middle"> Duplicate Post Deletion Settings</h2>';
	$topline = '<p style="max-width:60%"><strong>This plugin is now available as part of <a href="http://wordpress.org/plugins/tietools-automatic-maintenance-kit" target="_blank">TIEtools</a>, which also includes post expiry and server log file removal.</strong>
				<p style="max-width:60%">If you like this plugin and use it on your site(s), please show your appreciation by <a href="http://wordpress.org/plugins/tieexpire-automated-post-expiry/" target="_blank">rating it at WordPress</a> or even throwing some pennies my way!<p>
				<form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top">
				<input type="hidden" name="cmd" value="_s-xclick">
				<input type="image" src="' . plugins_url( 'donate.png' , __FILE__ ) . '" border="0" name="submit" alt="PayPal – The safer, easier way to pay online.">
				<img alt="" border="0" src="https://www.paypalobjects.com/en_GB/i/scr/pixel.gif" width="1" height="1">
				<input type="hidden" name="hosted_button_id" value="ESL342R25YKLL">
				</form>' ;
		   
	// The HTML for the options page.
    $html= '<hr width=60% align="left">
			<form action="options.php" method="post" name="options">
			' . wp_nonce_field('update-options') . '
			<p><h3>The plugin is currently&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" name="TIEdupedeleter_powerbutton" id="on" value="on"' . $poweron . ' />&nbsp;<label>On</label>
			&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" name="TIEdupedeleter_powerbutton" id="off" value="off"' . $poweroff . ' />&nbsp;<label>Off</label></h3>
			<hr width=60% align="left">
			<p><h3>Post filters</h3>
			<p><input type="radio" name="TIEdupedeleter_newoldradio" id="MIN" value="MIN"' . $oldbutton . ' />&nbsp;<label>Keep oldest copy</label>
			&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" name="TIEdupedeleter_newoldradio" id="MAX" value="MAX"' . $newbutton . ' />&nbsp;<label>Keep newest copy</label>
			<p>Check for dupes in: &nbsp;&nbsp;<input type="checkbox" name="TIEdupedeleter_status_published" value="publish"' . $pubposts . ' />&nbsp;<label>Published</label>
			&nbsp;&nbsp;&nbsp;&nbsp;<input type="checkbox" name="TIEdupedeleter_status_draft" value="draft"' . $draftposts . ' />&nbsp;<label>Draft</label>
			&nbsp;&nbsp;&nbsp;&nbsp;<input type="checkbox" name="TIEdupedeleter_status_pending" value="pending"' . $pendingposts . ' />&nbsp;<label>Pending</label>
			&nbsp;&nbsp;&nbsp;&nbsp;<input type="checkbox" name="TIEdupedeleter_status_private" value="private"' . $privateposts . ' />&nbsp;<label>Private</label>
			<br>&nbsp;
			<hr width=60% align="left">
			<p><h3>Category filters</h3>
			<p style="max-width:60%">Select the filter you wish to use and enter the categories as a comma-delimited list of numbers. For example, to find dupes in categories 1-3, click the radio button and enter "1,2,3" in the first box (without quotes). Enter a zero category to switch off the filter. Note that sub-categories are treated the same as top-level items and must be listed individually.
			<p><input type="radio" name="TIEdupedeleter_catsradio" id="include" value="include"' . $catsincludeon . ' />&nbsp;<label>Categories to include in expiry checks: </label><input type="text" name="TIEdupedeleter_catsin" size=10 value="' . $catstoinclude . '" />
			<br><input type="radio" name="TIEdupedeleter_catsradio" id="exclude" value="exclude"' . $catsexcludeon . ' />&nbsp;<label>Categories to exclude from expiry checks: </label><input type="text" name="TIEdupedeleter_catsout" size=10 value="' . $catstoexclude . '" />';

	// Finish the HTML block by adding the update button and the standard hidden WP fields to store the user's options.
	$html.='<hr width=60% align="left">
			<input type="hidden" name="action" value="update" />
			<input type="hidden" name="page_options" value="TIEdupedeleter_status_published, TIEdupedeleter_status_draft, TIEdupedeleter_status_pending, TIEdupedeleter_status_private, TIEdupedeleter_catsin, TIEdupedeleter_catsout, TIEdupedeleter_catsradio, TIEdupedeleter_powerbutton, TIEdupedeleter_newoldradio" >
			<p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="Save Changes"></form></div>
			<div style="clear:both">
			<pre>
		   ';

	// Display the topline and page HTML. The IF part shows the "Settings saved" line when appropriate.
	echo $plugname;
	if( isset($_GET['settings-updated']) ) {
		echo '<p style="max-width:60%;background-color:#FFFFE0;border-color:#e6db55;border-style:solid;border-width:1px;padding:3px;line-height:200%;">Dupe deletion settings saved.' ;
	}
	echo $topline;
	echo $html;

}

// Search for duplicate posts and move them to the Trash.
function expireduplicates() {

	// Check power button and skip everything if it's switched off.
	$powerbutton = (get_option('TIEdupedeleter_powerbutton') == 'on') ? 'on' : 'off';
	
	if ($powerbutton == 'on') {
	
		// Get the parameters for the query from the options settings.
		global $wpdb;
		$pubposts = (get_option('TIEdupedeleter_status_published') == 'publish') ? 'publish' : '' ;
		$draftposts = (get_option('TIEdupedeleter_status_draft') == 'draft') ? 'draft' : '' ;
		$pendingposts = (get_option('TIEdupedeleter_status_pending') == 'pending') ? 'pending' : '' ;
		$privateposts = (get_option('TIEdupedeleter_status_private') == 'private') ? 'private' : '' ;
		$catstoinclude = (get_option('TIEdupedeleter_catsin') != '') ? get_option('TIEdupedeleter_catsin') : '0';
		$catstoexclude = (get_option('TIEdupedeleter_catsout') != '') ? get_option('TIEdupedeleter_catsout') : '0';
		$catsoption = get_option('TIEdupedeleter_catsradio');
		$oldnewradio = get_option('TIEdupedeleter_newoldradio');
		
		// Build statuslist parameter for inclusion in query.
		if ($pubposts == 'publish') {
			$statuslist = "'publish'";
			if ($draftposts == 'draft') {
				$statuslist .= ",'draft'";
			}
			if ($pendingposts == 'pending') {
				$statuslist .= ",'pending'";
			}
			if ($privateposts == 'private') {
				$statuslist .= ",'private'";
			}
		}
		elseif ($draftposts == 'draft') {
			$statuslist = "'draft'";
			if ($pendingposts == 'pending') {
				$statuslist .= ",'pending'";
			}
			if ($privateposts == 'private') {
				$statuslist .= ",'private'";
			}
		}
		elseif ($pendingposts == 'pending') {
			$statuslist .= "'pending'";
			if ($privateposts == 'private') {
				$statuslist .= ",'private'";
			}
		}
		elseif ($privateposts == 'private') {
			$statuslist = "'private'";
		}
		else {
		$statuslist = '';
		}
		
		// Build query to find duplicate posts by title.
		$dupequery = "SELECT dupeposts.* FROM $wpdb->posts AS dupeposts
					  INNER JOIN (SELECT $wpdb->posts.post_title, $oldnewradio( $wpdb->posts.ID ) AS keepthisone
						FROM $wpdb->posts
						WHERE $wpdb->posts.post_type = 'post'
						AND $wpdb->posts.post_status IN ($statuslist) ";
						
		// Check for category filter to apply.
		if ($catsoption == 'include' && $catstoinclude != '' && $catstoinclude != '0') {
			$dupequery .= "AND $wpdb->posts.ID IN (SELECT DISTINCT object_id FROM $wpdb->term_relationships
						  WHERE $wpdb->term_relationships.term_taxonomy_id IN (" . $catstoinclude . ")) ";
		}
		elseif ($catsoption == 'exclude' && $catstoexclude != '' && $catstoexclude != '0') {	 
			$dupequery .= "AND $wpdb->posts.ID NOT IN (SELECT DISTINCT object_id FROM $wpdb->term_relationships
						  WHERE $wpdb->term_relationships.term_taxonomy_id IN (" . $catstoexclude . ")) ";
		}

		// Continue query construction.
		$dupequery .= "  GROUP BY post_title
					     HAVING COUNT( * ) >1 ) AS compareposts 
					   ON ( compareposts.post_title = dupeposts.post_title
					   AND compareposts.keepthisone <> dupeposts.ID )
					   WHERE dupeposts.post_type = 'post'
					   AND dupeposts.post_status IN ($statuslist)";

		// Check for category filter to apply.
		if ($catsoption == 'include' && $catstoinclude != '' && $catstoinclude != '0') {
			$dupequery .= "AND dupeposts.ID IN (SELECT DISTINCT object_id FROM $wpdb->term_relationships
						  WHERE $wpdb->term_relationships.term_taxonomy_id IN (" . $catstoinclude . "))";
		}
		elseif ($catsoption == 'exclude' && $catstoexclude != '' && $catstoexclude != '0') {	 
			$dupequery .= "AND dupeposts.ID NOT IN (SELECT DISTINCT object_id FROM $wpdb->term_relationships
						  WHERE $wpdb->term_relationships.term_taxonomy_id IN (" . $catstoexclude . "))";
		}			
		
		// Run query and move results to Trash. All done!
			$result = $wpdb->get_results($dupequery);
			foreach ($result as $post) {
				setup_postdata($post);  
				$postid = $post->ID;   
				wp_delete_post($postid);
			}
	}
}	