<?php
/*
Plugin Name: Redirection Reporting
Version: 1.9
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

		if ($_POST['Normal'] <> '') {
			$Normal = $_POST['Normal'];
		} elseif ($_GET['Normal'] <> '') {
			$Normal = $_GET['Normal'];
		}

		if ($Normal == '') {
			$Normal = 'Normal';
		}

		echo "<br><form name='report_type' method='post'><input type='submit' name='Normal' value='normal' name='ReportType'";
		if ($Normal=='Normal') {
			echo " disabled";
		}
		echo ">  <input type='submit' name='Normal' value='RegEx'";
		if ($Normal=='RegEx') {a
			echo " disabled";
		} 
		echo ">  <input type='submit' name='Normal' value='RegEx Parent Child'";
		if ($Normal=='RegEx Parent Child') {
			echo "disabled";
		}
		echo "></form><br>";

		if ($Normal == 'RegEx') {
			$this->show_page_regex();
		} elseif ($Normal == 'RegEx Parent Child') {
			$this->show_page_regex_parent();
		}
		else {
			$this->show_page_url();
		}
	}

	function show_page_regex_parent() {

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
		$this->end_of_form();

	}

	function show_page_regex() {
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
		$this->end_of_form();
	}

	function show_page_url() {
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
		$this->end_of_form();
		
	}

	function end_of_form() {
		if ($_POST['Normal'] <> '') {
			$Normal = $_POST['Normal'];
			$url = $_POST['url'];
			$startdate = $_POST['startdate'];
			$enddate = $_POST['enddate'];
		} elseif ($_GET['Normal'] <> '') {
			$Normal = $_GET['Normal'];
			$url = $_GET['url'];
			$startdate = $_GET['startdate'];
			$enddate = $_GET['enddate'];
		}

		echo "<tr><td>Report Start Date:</td><td><input name='startdate' type='text' value='{$startdate}'></td></tr>";
		echo "<tr><td>Report End Date:</td><td><input name='enddate' type='text' value='{$enddate}'></td></tr>";
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
				} else {
					$this->draw_report_url($url, $startdate, $enddate);
				}


			}
		}
	}

	function draw_report_regex_parent ($url, $startdate, $enddate) {
	global $wpdb;

		$url_name = $this->return_url_from_id($url);
		echo "Report for: $url_name";
		
		$startdate = date("Y-m-d", strtotime($startdate));
		$enddate = date("Y-m-d", strtotime($enddate));

		if ($url == "--all--") {
			$sql = "select url, dt, ct, (@curRow := @curRow +1)%2 as row_number from (select a.url, DATE(created) AS 'dt', count(*) AS 'ct' FROM {$wpdb->prefix}redirection_logs a join `{$wpdb->prefix}redirection_items` b ON a.redirection_id = b.id WHERE regex = 1 AND created BETWEEN '$startdate' AND '$enddate' GROUP BY a.url, DATE(created) with rollup) d";

			$columns = array("URL", "Date", "Count");
		} else {
			$sql = "select url, DATE(created) AS 'dt', count(*) AS 'ct' FROM {$wpdb->prefix}redirection_logs WHERE  redirection_id = $url AND created BETWEEN '$startdate' AND '$enddate' GROUP BY url, DATE(created) with rollup";
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
			$sql = "select url, dt, ct from (select a.url, DATE(created) AS 'dt', count(*) AS 'ct' FROM {$wpdb->prefix}redirection_logs a join `{$wpdb->prefix}redirection_items` b ON a.redirection_id = b.id JOIN (SELECT @curRow := 0) r WHERE regex = 1 AND created BETWEEN '$startdate' AND '$enddate' GROUP BY a.url, DATE(created) with rollup) d";

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
			$sql = "select url, dt, ct, (@curRow := @curRow +1)%2 as row_number from (select url, DATE(created) AS 'dt', count(*) AS 'ct' FROM {$wpdb->prefix}redirection_logs WHERE redirection_id <> 0 AND created BETWEEN '$startdate' AND '$enddate' GROUP BY url, DATE(created) with rollup) a";

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

} //End Class

$redirector_reporting_class = new redirector_reporting_class();

register_activation_hook(__FILE__, array($redirector_reporting_class, 'activation'));
add_action('admin_menu',  array($redirector_reporting_class, 'tools_menu'));