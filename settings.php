<?php

class redirector_reporting_class_settings {

	function register_settings() {
		register_setting( 'redirection_reporting', 'redirection_reporting', array(&$this,'settings_validate') );
		add_settings_section('redirection_reporting_main', __( 'Settings', '' ), array(&$this, 'setting_section'), 'redirection_reporting');
		add_settings_field('default_report', __( 'Default Report:', '' ), array(&$this, 'setting_default_report'), 'redirection_reporting', 'redirection_reporting_main');
		add_settings_field('default_date', __('Default Date:',  ''), array(&$this, 'setting_default_date'), 'redirection_reporting', 'redirection_reporting_main');
		add_settings_field('default_max_url_length', __('Maximum URL Length: ', ''), array(&$this, 'setting_max_url_length'), 'redirection_reporting', 'redirection_reporting_main');
		add_settings_field('default_info_newtab', __('Show Information Pages in New Window: ', ''), array(&$this, 'setting_info_newtab'), 'redirection_reporting', 'redirection_reporting_main');
		add_settings_field('default_info_show_date', __('Show Referrer Data By Date: ', ''), array(&$this, 'setting_info_show_date'), 'redirection_reporting', 'redirection_reporting_main');

		add_settings_section('rr_archiver', __( 'Archiving Settings', ''), array(&$this, 'archiver_section'), 'redirection_reporting');
		add_settings_field('archive_enabled', __( 'Archive Process Enabled:', '' ), array(&$this, 'setting_archive_enabled'), 'redirection_reporting', 'rr_archiver');
		add_settings_field('days_to_keep', __( 'Days To Keep in Main Table:', '' ), array(&$this, 'setting_days_to_keep'), 'redirection_reporting', 'rr_archiver');

	}

	function archiver_section() {
		global $wpdb;

		echo "Turning on the auto-archive settings will automatically begin archiving data in a background thread.
  This will put load on the database and can cause the website to run slower.  As this background process is run by the cron 
within wordpress there is no easy way to drop the process without either restarting the webserver, or killing the process 
within MySQL.  Please keep this in mind if you turn on the archive process.  Depending on how much data you have in your current redirection log table the archive process could run for a very long time the first time that it runs.<p>If you would like to run the archive process manually, enable it then disable it right away as this will create all the needed database objects.  Then log into MySQL manually and run the stored procedure named `{$wpdb->prefix}ArchiveRedirectionData`.  This procedure accepts a single input value which is the number of days of data to keep.  Your MySQL command will look something like `CALL {$wpdb->prefix}ArchiveRedirectionData (30);` depending on how many days worth of data you wish to keep in the main table. After the stored procedure has completed then enable the archive process so that data is moved daily at this point.<p>Having data archived or not does not impact the performance of report execution, it will make the loading of the RegEx menus faster. If you aren't using RegEx turning this on or not does nothing for you.";
	}

	function setting_days_to_keep() {
		$options = get_option('redirection_reporting');
		echo "<input type='text' value='";
		echo $options['days_to_keep'];
		echo "' name='redirection_reporting[days_to_keep]'>";

	}

	function setting_archive_enabled() {
		$options = get_option('redirection_reporting');
		echo "<input type='checkbox' value='true' name='redirection_reporting[archive_enabled]'";
		if ($options['archive_enabled']=="true") {
			echo " checked";
		}
		echo ">";
	}

	function show_settings_page() {
		echo '<div class="wrap">';
		echo '<H2>Redirection Reporting Settings</h2>';
		echo '<form action="options.php" method="post">';
		settings_fields('redirection_reporting');
		do_settings_sections('redirection_reporting');
		echo '<p class="submit"><input name="submit" type="submit" class="button-primary" value="Save Changes" /></form></div>';

	}

	function setting_info_show_date() {
		$options = get_option('redirection_reporting');
		echo "<input type='checkbox' value='true' name='redirection_reporting[default_info_show_date]'";
		if ($options['default_info_show_date']=="true") {
			echo " checked";
		}
		echo ">";
	}

	function setting_max_url_length() {
		$options = get_option('redirection_reporting');
		echo "<input type='text' value='";
		echo $options['default_max_url_length'];
		echo "' name='redirection_reporting[default_max_url_length]'>";
	}

	function setting_info_newtab() {
		$options = get_option('redirection_reporting');
		echo "<input type='checkbox' value='true' name='redirection_reporting[default_info_newtab]'";
		if ($options['default_info_newtab']=="true") {
			echo " checked";
		}
		echo ">";
	}

	function setting_section() {
		echo "Please select the default Redirection Reporting settings";
	}

	function setting_default_report() {
		$options = get_option('redirection_reporting');

		echo '<select id="default_report" name="redirection_reporting[default_report]">';
		echo '<option value="normal"';
		if ($options['default_report'] == 'normal') {
			echo " selected";
		}
		echo '>Normal Report</option>';
		echo '<option value="Normal Summary"';
		if ($options['default_report'] == 'Normal Summary') {
			echo " selected";
		}
		echo '>Normal Summary Report</option>';
		echo '<option value="RegEx"';
		if ($options['default_report'] == 'RegEx') {
			echo " selected";
		}		
		echo '>RegEx Reporting</option>';	
		echo '<option value="RegEx Parent Child"';
		if ($options['default_report'] == 'RegEx Parent Child') {
			echo " selected";
		}		
		echo '>RegEx Parent Child</option>';
		echo '<option value="RegEx Summary"';
		if ($options['default_report'] == 'RegEx Summary') {
			echo " selected";
		}		
		echo '>RegEx Summary</option>';
		echo '</select>';

	}

	function setting_default_date() {
		$options = get_option('redirection_reporting');
		echo '<select id="default_date" name="redirection_reporting[default_date]">';
		echo '<option>None</option>';
		echo '<option ';
		if ($options['default_date'] == 'Today') { echo ' selected'; }
		echo '>Today</option>';
		echo '<option';
		if ($options['default_date'] == 'Yesterday') { echo ' selected'; }
		echo '>Yesterday</option>';
		echo '<option';
		if ($options['default_date'] == 'Two Days Ago') { echo ' selected'; }
		echo '>Two Days Ago</option>';
		echo '<option';
		if ($options['default_date'] == 'This Month') { echo ' selected'; }
		echo '>This Month</option>';
		echo '</select>';
		echo '    (Dates used will be based on server time. If your timezone is different from the servers the dates may be "funky".)';
	}

	function settings_validate($input) {
		$options = get_option('redirection_reporting');
		if ($input['default_max_url_length'] == ''){
			add_settings_error('default_max_url_length', 'error_max_url_length', 'The Maximum URL Length field can not be blank.', 'error');
			$input['default_max_url_length'] = $options['default_max_url_length'];
		}

		if (!is_numeric($input['default_max_url_length'])) {
			add_settings_error('default_max_url_length', 'error_max_url_length', 'The Maximum URL Length must be a number.', 'error');
			$input['default_max_url_length'] = $options['default_max_url_length'];
		}

		if (!is_numeric($input['days_to_keep'])) {
			add_settings_error('default_days_to_keep', 'error_days_to_keep', 'The Days To Keep In Main Table must be a number.', 'error');
			$input['days_to_keep'] = $options['days_to_keep'];
			$input['archive_enabled'] = 'false';
		}

		if (is_numeric($input['days_to_keep'])) {
			if ($input['days_to_keep'] < 1) {
				add_settings_error('default_days_to_keep', 'error_days_to_keep', 'The Days To Keep In Main Table must be a positive number.', 'error');
				$input['days_to_keep'] = $options['days_to_keep'];
				$input['archive_enabled'] = 'false';
			}
		}

		if (!$input['default_info_newtab']) {
			$input['default_info_newtab'] = 'false';
		}

		if (!$input['default_info_show_date']) {
			$input['default_info_show_date'] = 'false';
		}

		if ($input['archive_enabled'] == 'true') {
			$archive = new redirector_reporting_class_archive();
			$archive->create_db_objects();
			$archive->schedule_archiving();
		} else {
			$archive = new redirector_reporting_class_archive();
			$archive->unscheduled_archiving();
			#$archive->put_archive_back();
			$input['archive_enabled'] = 'false';
		}

		return $input;
	}


}