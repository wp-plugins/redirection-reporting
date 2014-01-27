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
		echo '<option value="Normal"';
		if ($options['default_report'] == 'Normal') {
			echo " selected";
		}
		echo '>Page Report</option>';
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



		return $input;
	}


}