<?php

class redirector_reporting_class_forms {
	function show_tools_page() {

		if ($_GET['mode']=='info') {
			$reports = new redirector_reporting_class_reports();
			$reports->draw_info_page($_GET['url'], $_GET['startdate'], $_GET['enddate']);
			return;
		}


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
		echo ">  <input type='submit' name='Normal' value='Normal Summary'";
		if ($Normal=='Normal Summary') {
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
		} elseif ($Normal == 'Normal Summary') {
			$this->show_page_normal_summary();
		}
		else {
			$this->show_page_url();
		}
	}

	function show_page_normal_summary() {
$options = get_option('redirection_reporting');
		$Normal = $options['default_report'];


		if ($_POST['Normal'] <> '') {
			$Normal = $_POST['Normal'];
			$url= $_POST['url'];
			$order = $_POST['order'];
			$direction = $_POST['direction'];
		} elseif ($_GET['Normal'] <> '') {
			$Normal = $_GET['Normal'];
			$url=$_GET['url'];
			$order = $_GET['order'];
			$direction = $_GET['direction'];
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
				$reports = new redirector_reporting_class_reports();
				if ($Normal == 'RegEx') {
					$reports->draw_report_regex($url, $startdate, $enddate);
				} elseif ($Normal == 'RegEx Parent Child') {
					$reports->draw_report_regex_parent($url, $startdate, $enddate);
				} elseif ($Normal == 'RegEx Summary') {
					$reports->draw_report_regex_summary($url, $startdate, $enddate);
				} elseif ($Normal == 'Normal Summary') {
					$reports->draw_report_normal_summary($url, $startdate, $enddate);
				} else {
					$reports->draw_report_url($url, $startdate, $enddate);
				}


			}
		}
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

}