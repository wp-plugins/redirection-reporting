<?php
/*
Plugin Name: Redirection Reporting
Version: 3.0.2
Plugin URI: http://dcac.co/go/RedirectionReporting
Description: Allows for more details reporting for the "Redirection" plugin by John Godley.  This plugin will not do anything for you without using the Redirection plugin to handle your 301 redirections.  This plugin was built to fix a gap in the reporting which the Redirection plugin has.
Author: Denny Cherry
Author URI: http://dcac.co/
*/

require_once dirname( __FILE__ ) .'/forms.php';
require_once dirname( __FILE__ ) .'/reports.php';
require_once dirname( __FILE__ ) .'/settings.php';
require_once dirname( __FILE__ ) .'/archive.php';

class redirector_reporting_class {

	function activation() {

		// Default options
		$options = array (
			'donate' => '',
			'default_report' => 'RegEx',
			'default_dates' => '',
			'default_info_newtab' => "",
			'default_max_url_length' => 100,
			'default_info_show_date' => "true",
			'days_to_keep' => 90,
			'archive_enabled' => "false"
		);
    
		// Add options
		add_option('redirection_reporting', $options);

		$archive = new redirector_reporting_class_archive();

		$archive->create_db_objects();

		add_action('redirection_reporting_archive_data' , array($archive, 'archive_data'));
	 }

	function deactivation() {
		$archive = new redirector_reporting_class_archive();
		$archive->unscheduled_archiving();

		delete_option('redirection_reporting');
	}

	function upgrade() {
		$archive = new redirector_reporting_class_archive();
		$archive->create_db_objects();
	}

	function is_redirection_installed() {
		if ( !is_plugin_active( 'redirection/redirection.php' ) ) {
			echo '<div if="message" class="error"><p>Redirection plugin is not active.</p></div>';
		} 

	}

	function tools_menu() {
		$forms = new redirector_reporting_class_forms();

		add_submenu_page('tools.php', __('Redirection Reporting', 'redirection.php.reporting'), __('Redirection Reporting', 'redirection.php.reporting'), 'manage_options', 'redirection.php.reporting', array($forms, 'show_tools_page'));
	}

	function settings_menu() {
		$settings = new redirector_reporting_class_settings();

		add_submenu_page('options-general.php', __('Redirection Reporting', 'redirection.php.reporting'), __('Redirection Reporting', 'redirection.php.reporting'), 'manage_options', 'redirection.php.reporting.settings', array($settings, 'show_settings_page'));
		
	}


	function settings_menu_add_settings_link ($links, $file) {
	// Add "Settings" link to the plugins page
		if ( $file != plugin_basename( __FILE__ ))
			return $links;

		$settings_link = sprintf( '<a href="options-general.php?page=redirection.php.reporting.settings">%s</a>', __( 'Settings', '' ) );

		array_unshift( $links, $settings_link );

		return $links;
	}

	function init_settings(){
		$settings = new redirector_reporting_class_settings();
		$settings->register_settings();

	}
} //End Class

$redirector_reporting_class = new redirector_reporting_class();
$archive = new redirector_reporting_class_archive();

register_activation_hook(__FILE__, array($redirector_reporting_class, 'activation'));
add_action('admin_menu', array($redirector_reporting_class, 'tools_menu'));
add_action('admin_menu', array($redirector_reporting_class, 'settings_menu'));
add_action('admin_init', array($redirector_reporting_class, 'init_settings'), 1);
add_filter('plugin_action_links', array($redirector_reporting_class, 'settings_menu_add_settings_link'),10,2);
add_action('admin_head', array($redirector_reporting_class, 'is_redirection_installed')); 
register_deactivation_hook( __FILE__, array($redirector_reporting_class, 'deactivation' ));
add_action('upgrader_post_install', array($redirector_reporting_class, 'upgrade')); //Deploy database proc on upgrade as needed.

add_action('redirection_reporting_archive_data' , array($archive, 'archive_data'));