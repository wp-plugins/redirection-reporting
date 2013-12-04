<?php
/*
Plugin Name: Redirection Reporting
Version: 2.2
Plugin URI: http://dcac.co/go/RedirectionReporting
Description: Allows for more details reporting for the "Redirection" plugin by John Godley.  This plugin will not do anything for you without using the Redirection plugin to handle your 301 redirections.  This plugin was built to fix a gap in the reporting which the Redirection plugin has.
Author: Denny Cherry
Author URI: http://dcac.co/
*/

class redirector_reporting_class {

	function activation() {

		// Default options
		$options = array (
			'donate' => '',
			'default_report' => 'RegEx',
			'default_dates' => ''
		);
    
		// Add options
		add_option('redirection_reporting', $options);
	 }

	function tools_menu() {
		$redirector_reporting_class = new redirector_reporting_class();

		add_submenu_page('tools.php', __('Redirection Reporting', 'redirection.php.reporting'), __('Redirection Reporting', 'redirection.php.reporting'), 'manage_options', 'redirection.php.reporting', array($redirector_reporting_class, 'show_tools_page'));
	}

	function settings_menu() {
		$redirector_reporting_class = new redirector_reporting_class();

		add_submenu_page('options-general.php', __('Redirection Reporting', 'redirection.php.reporting'), __('Redirection Reporting', 'redirection.php.reporting'), 'manage_options', 'redirection.php.reporting.settings', array($redirector_reporting_class, 'show_settings_page'));
		
	}

	function show_settings_page() {
		echo '<div class="wrap">';
		echo '<H2>Redirection Reporting Settings</h2>';
		echo '<form action="options.php" method="post">';
		settings_fields('redirection_reporting');
		do_settings_sections('redirection_reporting');
		echo '<p class="submit"><input name="submit" type="submit" class="button-primary" value="Save Changes" /></form></div>';

	}

	function init_settings(){

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( __('You are not allowed to access this part of the site') );
		}


		register_setting( 'redirection_reporting', 'redirection_reporting', array(&$this,'settings_validate') );
		add_settings_section('redirection_reporting_main', __( 'Settings', '' ), array(&$this, 'setting_section'), 'redirection_reporting');
		add_settings_field('default_report', __( 'Default Report:', '' ), array(&$this, 'setting_default_report'), 'redirection_reporting', 'redirection_reporting_main');
		add_settings_field('default_date', __('Default Date:',  ''), array(&$this, 'setting_default_date'), 'redirection_reporting', 'redirection_reporting_main');
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

	function get_default_date(&$startdate, &$enddate) {
		$options = get_option('redirection_reporting');
		if ($options['default_date'] == 'None') {
			return;
		}
		if ($options['default_date'] == 'Today') {
			$startdate = mktime(0, 0, 0, date("m")  , date("d"), date("Y"));
			$enddate = mktime(0, 0, 0, date("m")  , date("d")+1, date("Y"));
		}
		if ($options['default_date'] == 'Yesterday') {
			$startdate = mktime(0, 0, 0, date("m")  , date("d")-1, date("Y"));
			$enddate = mktime(0, 0, 0, date("m")  , date("d"), date("Y"));
		}
		if ($options['default_date'] == 'Two Days Ago') {
			$startdate = mktime(0, 0, 0, date("m")  , date("d")-2, date("Y"));
			$enddate = mktime(0, 0, 0, date("m")  , date("d")-1, date("Y"));
		}
		if ($options['default_date'] == 'This Month') {
			$startdate = mktime(0, 0, 0, date("m")  , 1, date("Y"));
			$enddate = mktime(0, 0, 0, date("m")  , date("d")+1, date("Y"));
		}
		$startdate = date("Y-m-d", $startdate);
		$enddate = date("Y-m-d", $enddate);
	}

	function settings_validate($input) {
		return $input;
	}

	function show_tools_page() {

		$options = get_option('redirection_reporting');
		$Normal = $options['default_report'];

		if ($_POST['Normal'] <> '') {
			$Normal = $_POST['Normal'];
		} elseif ($_GET['Normal'] <> '') {
			$Normal = $_GET['Normal'];
		}

		if ($Normal == '') {
			$Normal = 'Normal';
		}

		echo "<br><form name='report_type' method='post'><input type='submit' name='Normal' value='normal' name='ReportType'";
		if ($Normal=='normal') {
			echo " disabled";
		}
		echo ">  <input type='submit' name='Normal' value='RegEx'";
		if ($Normal=='RegEx') {
			echo " disabled";
		} 
		echo ">  <input type='submit' name='Normal' value='RegEx Parent Child'";
		if ($Normal=='RegEx Parent Child') {
			echo "disabled";
		}
		echo ">  <input type='submit' name='Normal' value='RegEx Summary'";
		if ($Normal=='RegEx Summary') {
			echo "disabled";
		}
		echo "></form><br>";

		if ($Normal == 'RegEx') {
			$this->show_page_regex();
		} elseif ($Normal == 'RegEx Parent Child') {
			$this->show_page_regex_parent();
		} elseif ($Normal == 'RegEx Summary') {
			$this->show_page_regex_summary();
		}
		else {
			$this->show_page_url();
		}
	}

	function show_page_regex_summary() {
		$options = get_option('redirection_reporting');
		$Normal = $options['default_report'];

		if ($_POST['Normal'] <> '') {
			$Normal = $_POST['Normal'];
			$url = $_POST['url'];
			$order = $_POST['order'];
			$direction = $_POST['direction'];
		} elseif ($_GET['Normal'] <> '') {
			$Normal = $_GET['Normal'];
			$url=$_GET['url'];
			$order = $_GET['order'];
			$direction = $_GET['direction'];
		}

		global $wpdb;
		$sql = "select distinct b.url, id from `{$wpdb->prefix}redirection_items` b where b.regex = 1 order by b.url";
		echo "<form name='selection_form' method='post'>";
		echo "<input type='hidden' name='Normal' value='{$Normal}' />";
		echo "<table border='0'><tr><td>Select URL:</td><td><select name='url'>";
		echo "<option value='--all--'>All URLs</option>";

		$rows = $wpdb->get_results($sql);

		foreach ($rows as $row) {
			echo "<option value='";
			echo $row->id;
			if ($url == $row->id) {
				echo "' SELECTED>";
			} else {
				echo "'>";
			}
			//echo "<option>";
			echo $row->url;
			echo "</option>";
		}
		echo "</select></td></tr>";
		echo "<tr><td>Order By:</td>";
		echo "<td><select name='order'>";
			echo "<option>URL</option>";
			echo "<option";
			if ($order == 'Count') { 
				echo " selected";
			}
			echo ">Count</option>";
		echo "</select>";
		echo "<select name='direction'>";
			echo "<option value='asc'>Ascending</option>";
			echo "<option value='desc'";
			if ($direction == 'desc') { 
				echo " selected";
			}
			echo ">Descending</option>";
		echo "</select>";
		echo "</td></tr>";

		$this->end_of_form(0);

	}

	function show_page_regex_parent() {

		$options = get_option('redirection_reporting');
		$Normal = $options['default_report'];

		if ($_POST['Normal'] <> '') {
			$Normal = $_POST['Normal'];
			$url = $_POST['url'];
		} elseif ($_GET['Normal'] <> '') {
			$Normal = $_GET['Normal'];
			$url=$_GET['url'];
		}

		global $wpdb;
		$sql = "select distinct b.url, id from `{$wpdb->prefix}redirection_items` b where b.regex = 1 order by b.url";
		echo "<form name='selection_form' method='post'>";
		echo "<input type='hidden' name='Normal' value='{$Normal}' />";
		echo "<table border='0'><tr><td>Select URL:</td><td><select name='url'>";
		echo "<option value='--all--'>All URLs</option>";

		$rows = $wpdb->get_results($sql);

		foreach ($rows as $row) {
			echo "<option value='";
			echo $row->id;
			if ($url == $row->id) {
				echo "' SELECTED>";
			} else {
				echo "'>";
			}
			//echo "<option>";
			echo $row->url;
			echo "</option>";
		}
		echo "</select></td></tr>";
		$this->end_of_form(1);

	}

	function show_page_regex() {

		$options = get_option('redirection_reporting');
		$Normal = $options['default_report'];

		if ($_POST['Normal'] <> '') {
			$Normal = $_POST['Normal'];
			$url = $_POST['url'];
		} elseif ($_GET['Normal'] <> '') {
			$Normal = $_GET['Normal'];
			$url = $_GET['url'];
		}

		global $wpdb;
		$sql = "select distinct a.url from {$wpdb->prefix}redirection_logs a join `{$wpdb->prefix}redirection_items` b ON a.redirection_id = b.id where b.regex = 1 order by a.url";
		echo "<form name='selection_form' method='post'>";
		echo "<input type='hidden' name='Normal' value='{$Normal}' />";
		echo "<table border='0'><tr><td>Select URL:</td><td><select name='url'>";
		echo "<option value='--all--'>All URLs</option>";

		$rows = $wpdb->get_results($sql);

		foreach ($rows as $row) {
			echo "<option ";
			if ($url == $row->url) {
				echo " SELECTED>";
			} else {
				echo ">";
			}
			//echo "<option>";
			echo $row->url;
			echo "</option>";
		}
		echo "</select></td></tr>";
		$this->end_of_form(1);
	}

	function show_page_url() {

		$options = get_option('redirection_reporting');
		$Normal = $options['default_report'];


		if ($_POST['Normal'] <> '') {
			$Normal = $_POST['Normal'];
			$url= $_POST['url'];
		} elseif ($_GET['Normal'] <> '') {
			$Normal = $_GET['Normal'];
			$url=$_GET['url'];
		}

		global $wpdb;
		echo "<form name='selection_form' method='post'>";
		echo "<input type='hidden' name='Normal' value='{$Normal}' />";
		echo "<table border='0'><tr><td>Select URL:</td><td><select name='url'>";
		echo "<option value='--all--'>All URLs</option>";
		$rows = $wpdb->get_results("select url, id from `{$wpdb->prefix}redirection_items` order by url");
		foreach ($rows as $row) {
			echo "<option value='";
			echo $row->id;
			if ($url == $row->id) {
				echo "' SELECTED>";
			} else {
				echo "'>";
			}
			//echo "<option>";
			echo $row->url;
			echo "</option>";
		}
		echo "</select></td></tr>";
		$this->end_of_form(1);
		
	}

	function end_of_form($ShowHideForm) {
		if ($_POST['Normal'] <> '') {
			$Normal = $_POST['Normal'];
			$url = $_POST['url'];
			$startdate = $_POST['startdate'];
			$enddate = $_POST['enddate'];
			$hide_rollup = $_POST['hide_rollup'];
		} elseif ($_GET['Normal'] <> '') {
			$Normal = $_GET['Normal'];
			$url = $_GET['url'];
			$startdate = $_GET['startdate'];
			$enddate = $_GET['enddate'];
			$hide_rollup = $_GET['hide_rollup'];
		}

		if ($startdate == '') {
			$this->get_default_date($startdate, $enddate);
		}

		echo "<tr><td>Report Start Date:</td><td><input name='startdate' type='text' value='{$startdate}'></td></tr>";
		echo "<tr><td>Report End Date:</td><td><input name='enddate' type='text' value='{$enddate}'></td></tr>";
		if ($ShowHideForm == 1) {
			echo "<tr><td colspan='2'><input type='checkbox' name='hide_rollup' value='true'";
			if ($hide_rollup != '') {
				echo " checked";
			}
			echo "> Hide Rollup Values (when possible)</td></tr>";
		}
		echo "<tr><td colspan='2'><input type='submit' name='submit' value='Run Report'></td></tr>";
		echo "</table></form>";

		if ($url != "") {
			$error = false;
			if(strtotime($startdate) == false) {
				echo '<div if="message" class="error">Please enter a valid start date.<br></div>';
				$error=true;
			}
			if(strtotime($enddate) == false) {
				echo '<div if="message" class="error">Please enter a valid end date.<br></div>';
				$error=true;
			}

			if ($error==false) {
				if ($Normal == 'RegEx') {
					$this->draw_report_regex($url, $startdate, $enddate);
				} elseif ($Normal == 'RegEx Parent Child') {
					$this->draw_report_regex_parent($url, $startdate, $enddate);
				} elseif ($Normal == 'RegEx Summary') {
					$this->draw_report_regex_summary($url, $startdate, $enddate);
				} else {
					$this->draw_report_url($url, $startdate, $enddate);
				}


			}
		}
	}

	function draw_report_regex_summary($url, $startdate, $enddate) {
		global $wpdb;

		$order = $_POST['order'];
		$direction = $_POST['direction'];

		if ($order == 'Count') {
			$order = 'count(*)';
		} elseif ($order == 'URL') {
			$order = 'a.url';
		} else {
			$order = 'a.url';
		}

		if ($direction == 'ASC') {
			$direction = 'ASC';
		} else {
			$direction = 'DESC';
		}

		$url_name = $this->return_url_from_id($url);
		echo "Report for: $url_name";
		
		$startdate = date("Y-m-d", strtotime($startdate));
		$enddate = date("Y-m-d", strtotime($enddate));

		if ($url == "--all--") {
			$sql = "select a.url, count(*) AS 'ct' FROM {$wpdb->prefix}redirection_logs a join `{$wpdb->prefix}redirection_items` b ON a.redirection_id = b.id WHERE regex = 1 AND created BETWEEN '$startdate' AND '$enddate' GROUP BY a.url ORDER BY $order $direction";

			$columns = array("URL", "Count");
		} else {
			$sql = "select url, count(*) AS 'ct' FROM {$wpdb->prefix}redirection_logs a WHERE  redirection_id = $url AND created BETWEEN '$startdate' AND '$enddate' GROUP BY url  ORDER BY $order $direction";

			$columns = array("URL", "Count");
		}

		$rows = $wpdb->get_results("$sql");
		//echo "<BR>$sql<BR>";
		
		$this->draw_report_generic ($rows, $columns, 'RegEx', $startdate, $enddate);

	}

	function draw_report_regex_parent ($url, $startdate, $enddate) {
	global $wpdb;

		$url_name = $this->return_url_from_id($url);
		echo "Report for: $url_name";
		
		$startdate = date("Y-m-d", strtotime($startdate));
		$enddate = date("Y-m-d", strtotime($enddate));

		if ($url == "--all--") {
			$sql = "select url, dt, ct, (@curRow := @curRow +1)%2 as row_number from (select a.url, DATE(created) AS 'dt', count(*) AS 'ct' FROM {$wpdb->prefix}redirection_logs a join `{$wpdb->prefix}redirection_items` b ON a.redirection_id = b.id WHERE regex = 1 AND created BETWEEN '$startdate' AND '$enddate' GROUP BY a.url, DATE(created) ";

			if ($_POST['hide_rollup'] == '') {
				$sql = $sql." with rollup";
			}

			$sql = $sql.") d";

			$columns = array("URL", "Date", "Count");
		} else {
			$sql = "select url, DATE(created) AS 'dt', count(*) AS 'ct' FROM {$wpdb->prefix}redirection_logs WHERE  redirection_id = $url AND created BETWEEN '$startdate' AND '$enddate' GROUP BY url, DATE(created)";

			if ($_POST['hide_rollup'] == '') {
				$sql = $sql." with rollup";
			}

			$columns = array("URL", "Date", "Count");
		}

		$rows = $wpdb->get_results("$sql");
		//echo "<BR>$sql<BR>";
		
		$this->draw_report_generic ($rows, $columns, 'RegEx', $startdate, $enddate);

	}

	function draw_report_regex ($url, $startdate, $enddate) {
		global $wpdb;
		if ($url == "--all--") {
			echo "Report for: All URLs";
		} else {
			echo "Report for: $url";
		}
		

		$startdate = date("Y-m-d", strtotime($startdate));
		$enddate = date("Y-m-d", strtotime($enddate));
		if ($url == "--all--") {
			$sql = "select url, dt, ct from (select a.url, DATE(created) AS 'dt', count(*) AS 'ct' FROM {$wpdb->prefix}redirection_logs a join `{$wpdb->prefix}redirection_items` b ON a.redirection_id = b.id JOIN (SELECT @curRow := 0) r WHERE regex = 1 AND created BETWEEN '$startdate' AND '$enddate' GROUP BY a.url, DATE(created) ";

			if ($_POST['hide_rollup'] == '') {
				$sql = $sql." with rollup";
			}

			$sql = $sql.") d";

			$columns = array("URL", "Date", "Count");
		} else {
			$sql = "select DATE(created) AS 'dt', count(*) AS 'ct', (@curRow := @curRow + 1)%2 AS row_number FROM {$wpdb->prefix}redirection_logs  WHERE url = '$url' AND created BETWEEN '$startdate' AND '$enddate' GROUP BY DATE(created) ORDER BY DATE(created)";

			$columns = array("Date", "Count");
		}

		$rows = $wpdb->get_results("$sql");
		//echo "<BR>$sql";
		
		$this->draw_report_generic ($rows, $columns);
	}

	function draw_report_url ($id, $startdate, $enddate) {
		global $wpdb;

		$url_name = $this->return_url_from_id($id);
		echo "Report for: $url_name";
		
		$startdate = date("Y-m-d", strtotime($startdate));
		$enddate = date("Y-m-d", strtotime($enddate));
		if ($id == "--all--") {
			$sql = "select url, dt, ct, (@curRow := @curRow +1)%2 as row_number from (select url, DATE(created) AS 'dt', count(*) AS 'ct' FROM {$wpdb->prefix}redirection_logs WHERE redirection_id <> 0 AND created BETWEEN '$startdate' AND '$enddate' GROUP BY url, DATE(created) ";

			if ($_POST['hide_rollup'] == '') {
				$sql = $sql." with rollup";
			}

			$sql = $sql.") a";

			$columns = array ("URL", "Date", "Hit Count");

		} else {
			$sql = "select DATE(created) AS 'dt', count(*) AS 'ct' FROM {$wpdb->prefix}redirection_logs JOIN    (SELECT @curRow := 0) r WHERE redirection_id = $id AND created BETWEEN '$startdate' AND '$enddate' GROUP BY DATE(created) ORDER BY DATE(created)";


			$columns = array ("Date", "Hit Count");
		}

		$rows = $wpdb->get_results("$sql");
		//echo $sql;
		
		$this->draw_report_generic ($rows, $columns);
	}

	function draw_report_generic ($rows, $columns, $targetreport='', $startdate='', $enddate='') {
		global $wpdb;
		echo "<BR>";
		echo "<table border='1' cellpadding='1' cellspacing='0'><tr>";
		$column_count = count($columns);
		foreach ($columns as $column) {
			echo "<TD align='center'><B>$column</B></TD>";
		}
		echo "</TR>";

		foreach ($rows as $row) {
			if ($row_id == 0) {
				$row_id = 1;
			} else {
				$row_id =0;
			}
			echo "<TR";
				if ($row_id == 0) {
					echo ' bgcolor="#E6E6E6"';
				}
			echo ">";
			$i = 0;
			foreach ($row as $thing) {
				if ($i < $column_count) {
					$this->generate_html_tags ($targetreport, $thing, $startdate, $enddate, $i, $htmlstart, $htmlend);
					echo "<td>$htmlstart $thing $htmlend</td>";
				}
				$i+=1;
			}
			echo "</TR>";
		}

		echo "</table>";
	}

	function generate_html_tags ($targetreport, $reporturl, $startdate, $enddate, $column, &$htmlstart, &$htmlend) {

		$htmlstart='';
		$htmlend='';

		if ($targetreport=='') {
			return;
		}

		if ($column != 0) {
			return;
		}

		$htmlend = '</a>';
		$htmlstart="<a href=\"tools.php?page=redirection_reporting&Normal=$targetreport&url=$reporturl&startdate=$startdate&enddate=$enddate\">";
	}

	function return_url_from_id ($id) {
		global $wpdb;

		if ($id == "--all--") {
			return ("All URLs");
		} else {
			$rows = $wpdb->get_results("select url, id from `{$wpdb->prefix}redirection_items` where id = $id");
			return ($rows[0]->url);
		}
	}


	// Add "Settings" link to the plugins page
	function settings_menu_add_settings_link ($links, $file) {
		if ( $file != plugin_basename( __FILE__ ))
			return $links;

		$settings_link = sprintf( '<a href="options-general.php?page=redirection_reporting_settings">%s</a>', __( 'Settings', '' ) );

		array_unshift( $links, $settings_link );

		return $links;
	}

} //End Class

$redirector_reporting_class = new redirector_reporting_class();

register_activation_hook(__FILE__, array($redirector_reporting_class, 'activation'));
add_action('admin_menu', array($redirector_reporting_class, 'tools_menu'));
add_action('admin_menu', array($redirector_reporting_class, 'settings_menu'));
add_action('admin_init', array($redirector_reporting_class, 'init_settings'), 1);
add_filter('plugin_action_links', array($redirector_reporting_class, 'settings_menu_add_settings_link'),10,2);