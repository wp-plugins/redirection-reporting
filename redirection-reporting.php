<?php
/*
Plugin Name: Redirection Reporting
Version: 1.6
Plugin URI: http://dcac.co/go/RedirectionReporting
Description: Allows for more details reporting for the "Redirection" plugin by John Godley.  This plugin will not do anything for you without using the Redirection plugin to handle your 301 redirections.  This plugin was built to fix a gap in the reporting which the Redirection plugin has.
Author: Denny Cherry
Author URI: http://dcac.co/
*/

class redirector_reporting_class {

	function activation() {

		// Default options
		$options = array (
			'donate' => ''
		);
    
		// Add options
		add_option('redirector_reporting_options', $options);
	 }

	function tools_menu() {
		$redirector_reporting_class = new redirector_reporting_class();

		add_submenu_page('tools.php', __('Redirection Reporting', 'redirection_reporting'), __('Redirection Reporting', 'redirection_reporting'), 'manage_options', 'redirection_reporting', array($redirector_reporting_class, 'show_page'));
	}

	function show_page() {
		global $wpdb;
		echo "<form name='selection_form' method='post'>";
		echo "<table border='0'><tr><td>Select URL:</td><td><select name='url'>";
		echo "<option value='--all--'>All URLs</option>";
		$rows = $wpdb->get_results("select url, id from `{$wpdb->prefix}redirection_items` order by url");
		foreach ($rows as $row) {
			echo "<option value='";
			echo $row->id;
			if ($_POST['url'] == $row->id) {
				echo "' SELECTED>";
			} else {
				echo "'>";
			}
			//echo "<option>";
			echo $row->url;
			echo "</option>";
		}
		echo "</select></td></tr>";
		echo "<tr><td>Report Start Date:</td><td><input name='startdate' type='text' value='{$_POST['startdate']}'></td></tr>";
		echo "<tr><td>Report End Date:</td><td><input name='enddate' type='text' value='{$_POST['enddate']}'></td></tr>";
		echo "<tr><td colspan='2'><input type='submit' name='submit' value='Run Report'></td></tr>";
		echo "</table></form>";

		if ($_POST['url'] != "") {
			$error = false;
			if(strtotime($_POST['startdate']) == false) {
				echo '<div if="message" class="error">Please enter a valid start date.<br></div>';
				$error=true;
			}
			if(strtotime($_POST['enddate']) == false) {
				echo '<div if="message" class="error">Please enter a valid end date.<br></div>';
				$error=true;
			}

			if ($error==false) {
				$this->draw_report($_POST['url'], $_POST['startdate'], $_POST['enddate']);
			}
		}
	}

	function draw_report ($id, $startdate, $enddate) {
		global $wpdb;
		if ($id == "--all--") {
			echo "Report for all URLs";
		} else {
			$rows = $wpdb->get_results("select url, id from `{$wpdb->prefix}redirection_items` where id = $id");
			foreach ($rows as $row) {
				echo "Report for: $row->url";
			}
		}
		

		$startdate = date("Y-m-d", strtotime($startdate));
		$enddate = date("Y-m-d", strtotime($enddate));
		if ($id == "--all--") {
			$sql = "select url, dt, ct, (@curRow := @curRow +1)%2 as row_number from (select url, DATE(created) AS 'dt', count(*) AS 'ct' FROM wp_redirection_logs JOIN (SELECT @curRow := 0) r WHERE redirection_id <> 0 AND created BETWEEN '$startdate' AND '$enddate' GROUP BY url, DATE(created) with rollup) a";
		} else {
			$sql = "select DATE(created) AS 'dt', count(*) AS 'ct', (@curRow := @curRow + 1)%2 AS row_number FROM {$wpdb->prefix}redirection_logs JOIN    (SELECT @curRow := 0) r WHERE redirection_id = $id AND created BETWEEN '$startdate' AND '$enddate' GROUP BY DATE(created) ORDER BY DATE(created)";

		}

		$rows = $wpdb->get_results("$sql");
		//echo $sql;
		echo "<BR>";
		echo "<table border='1' cellpadding='1' cellspacing='0'><tr>";
		if ($id == "--all--") {
			echo "<td align='center'><b>URL</b></td><td align='center'><b>Date</b></td><td align='center'><b>Hit Count</b></td></tr>";
		} else {
			echo "<td align='center'><b>Date</b></td><td align='center'><b>Hit Count</b></td></tr>";
		}
		foreach ($rows as $row) {
			echo "<tr";
			if ($row->url == "") {
				echo " bgcolor='green'";
			} else {
				if ($row->dt == "") {
					echo " bgcolor='green'";
				} else {
					if ($row->row_number==0) {
						echo " bgcolor='gray'";
					}
				}
			}
			
			if ($id == "--all--") {
				echo "><td>{$row->url}</td><td>{$row->dt}</td><td>{$row->ct}</td></tr>";
			} else {
				echo "><td>{$row->dt}</td><td>{$row->ct}</td></tr>";
			}
		}
		echo "</table>";
	}


} //End Class

$redirector_reporting_class = new redirector_reporting_class();

register_activation_hook(__FILE__, array($redirector_reporting_class, 'activation'));
add_action('admin_menu',  array($redirector_reporting_class, 'tools_menu'));