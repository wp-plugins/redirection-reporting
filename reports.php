<?php

class redirector_reporting_class_reports {
	function draw_report_normal_summary($url, $startdate, $enddate) {
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
			$sql = "select a.url, count(*) AS 'ct' FROM {$wpdb->prefix}redirection_logs_view a join `{$wpdb->prefix}redirection_items` b ON a.redirection_id = b.id WHERE regex = 0 AND created BETWEEN '$startdate' AND '$enddate' GROUP BY a.url ORDER BY $order $direction";

			$columns = array("URL", "Count");
		} else {
			$sql = "select url, count(*) AS 'ct' FROM {$wpdb->prefix}redirection_logs_view a WHERE  redirection_id = $url AND created BETWEEN '$startdate' AND '$enddate' GROUP BY url  ORDER BY $order $direction";

			$columns = array("URL", "Count");
		}

		$rows = $wpdb->get_results("$sql");
		//echo "<BR>$sql<BR>";
		
		$this->draw_report_generic ($rows, $columns, 'RegEx', $startdate, $enddate);

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
			$sql = "select a.url, count(*) AS 'ct' FROM {$wpdb->prefix}redirection_logs_view a join `{$wpdb->prefix}redirection_items` b ON a.redirection_id = b.id WHERE regex = 1 AND created BETWEEN '$startdate' AND '$enddate' GROUP BY a.url ORDER BY $order $direction";

			$columns = array("URL", "Count");
		} else {
			$sql = "select url, count(*) AS 'ct' FROM {$wpdb->prefix}redirection_logs_view a WHERE  redirection_id = $url AND created BETWEEN '$startdate' AND '$enddate' GROUP BY url  ORDER BY $order $direction";

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
			$sql = "select url, dt, ct, (@curRow := @curRow +1)%2 as row_number from (select a.url, DATE(created) AS 'dt', count(*) AS 'ct' FROM {$wpdb->prefix}redirection_logs_view a join `{$wpdb->prefix}redirection_items` b ON a.redirection_id = b.id WHERE regex = 1 AND created BETWEEN '$startdate' AND '$enddate' GROUP BY a.url, DATE(created) ";

			if ($_POST['hide_rollup'] == '') {
				$sql = $sql." with rollup";
			}

			$sql = $sql.") d";

			$columns = array("URL", "Date", "Count");
		} else {
			$sql = "select url, DATE(created) AS 'dt', count(*) AS 'ct' FROM {$wpdb->prefix}redirection_logs_view WHERE  redirection_id = $url AND created BETWEEN '$startdate' AND '$enddate' GROUP BY url, DATE(created)";

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
			$sql = "select url, dt, ct from (select a.url, DATE(created) AS 'dt', count(*) AS 'ct' FROM {$wpdb->prefix}redirection_logs_view a join `{$wpdb->prefix}redirection_items` b ON a.redirection_id = b.id JOIN (SELECT @curRow := 0) r WHERE regex = 1 AND created BETWEEN '$startdate' AND '$enddate' GROUP BY a.url, DATE(created) ";

			if ($_POST['hide_rollup'] == '') {
				$sql = $sql." with rollup";
			}

			$sql = $sql.") d";

			$columns = array("URL", "Date", "Count");
		} else {
			$sql = "select DATE(created) AS 'dt', count(*) AS 'ct', (@curRow := @curRow + 1)%2 AS row_number FROM {$wpdb->prefix}redirection_logs_view  WHERE url = '$url' AND created BETWEEN '$startdate' AND '$enddate' GROUP BY DATE(created) ORDER BY DATE(created)";

			$columns = array("Date", "Count");
		}

		$rows = $wpdb->get_results("$sql");
		//echo "<BR>$sql";
		
		$this->draw_report_generic ($rows, $columns, "targeturl", $startdate, $enddate, 2);
	}

	function draw_report_url ($id, $startdate, $enddate) {
		global $wpdb;

		$url_name = $this->return_url_from_id($id);
		echo "Report for: $url_name";
		
		$startdate = date("Y-m-d", strtotime($startdate));
		$enddate = date("Y-m-d", strtotime($enddate));
		if ($id == "--all--") {
			$sql = "select url, dt, ct, (@curRow := @curRow +1)%2 as row_number from (select url, DATE(created) AS 'dt', count(*) AS 'ct' FROM {$wpdb->prefix}redirection_logs_view WHERE redirection_id <> 0 AND created BETWEEN '$startdate' AND '$enddate' GROUP BY url, DATE(created) ";

			if ($_POST['hide_rollup'] == '') {
				$sql = $sql." with rollup";
			}

			$sql = $sql.") a";

			$columns = array ("URL", "Date", "Hit Count");

		} else {
			$sql = "select DATE(created) AS 'dt', count(*) AS 'ct' FROM {$wpdb->prefix}redirection_logs_view JOIN    (SELECT @curRow := 0) r WHERE redirection_id = $id AND created BETWEEN '$startdate' AND '$enddate' GROUP BY DATE(created) ORDER BY DATE(created)";


			$columns = array ("Date", "Hit Count");
		}

		$rows = $wpdb->get_results("$sql");
		//echo $sql;
		
		$this->draw_report_generic ($rows, $columns, "targeturl", $startdate, $enddate, 2);
	}

	function draw_report_generic ($rows, $columns, $targetreport='', $startdate='', $enddate='', $nativeurl=0) {
		$options = get_option('redirection_reporting');
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
					if ($thing != "") {
						if ($i == 0) {
							$this->generate_html_tags ($targetreport, $thing, $startdate, $enddate, $i, $htmlstart, $htmlend, $nativeurl, $options['default_info_newtab'], $options['default_max_url_length']);
						} else {
							$htmlstart = "";
							$htmlend = "";
						}
					} else {
						if ($i == 0) {
							$thing = "{No Referrer Specified In Logs}";
						}
						$htmlstart = "";
						$htmlend = "";
					}
					echo "<td>$htmlstart $thing $htmlend</td>";
				}
				$i+=1;
			}
			echo "</TR>";
		}

		echo "</table>";
	}

	function generate_html_tags ($targetreport, &$reporturl, $startdate, $enddate, $column, &$htmlstart, &$htmlend, $nativeurl, $info_newtab, $maxurllength) {

		$htmlstart='';
		$htmlend='';

		if ($targetreport=='') {
			return;
		}

		if ($column != 0) {
			return;
		}

		if ($nativeurl==0) {
			$htmlstart="<a href=\"tools.php?page=redirection.php.reporting&Normal=$targetreport&url=$reporturl&startdate=$startdate&enddate=$enddate\">";

			$htmlend = "</a>&nbsp;&nbsp;<a href=\"tools.php?page=redirection.php.reporting&mode=info&url=$reporturl&startdate=$startdate&enddate=$enddate\" ";
			if ($info_newtab=="true") {
				$htmlend=$htmlend."target=\"UrlInfo\" ";
			}
			#$htmlend=$htmlend."><img src=\"../wp-content/plugins/redirection-reporting/info.png\" height=\"15\" width=\"15\" valign=\"center\"></a>";
			$htmlend=$htmlend."><img src=\"".plugins_url( 'info.png', __FILE__ )."\" height=\"15\" width=\"15\" valign=\"center\"></a>";
			
		}

		if ($nativeurl==1) {
			$htmlstart="<a href=\"$reporturl\">";
			$htmlend = "</a>";
			$reporturl = substr($reporturl, 0, $maxurllength);
		}

		if ($nativeurl==2) {
			$htmlend = "<a href=\"tools.php?page=redirection.php.reporting&mode=info&url=$reporturl&startdate=$startdate&enddate=$enddate\" ";
			if ($info_newtab=="true") {
				$htmlend=$htmlend."target=\"UrlInfo\" ";
			}
			$htmlend=$htmlend."><img src=\"../wp-content/plugins/redirection-reporting/info.png\" height=\"15\" width=\"15\" valign=\"center\"></a>";
		}
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

	function draw_info_page($url, $startdate, $enddate) {
		global $wpdb;
		$options = get_option('redirection_reporting');
		$default_info_show_date = $options['default_info_show_date'];
		echo "Details for: $url<br>";
		echo "From $startdate To $enddate<br>";

		if ($default_info_show_date=="true") {
			$sql = "select referrer, DATE(created) AS 'dt', count(*) AS 'ct' FROM {$wpdb->prefix}redirection_logs_view WHERE url = '$url' AND created BETWEEN '$startdate' AND '$enddate' GROUP BY referrer, DATE(created) ORDER BY referrer, DATE(created)";

			$columns = array ("Referrer", "Date", "Hit Count");
		} else {
			$sql = "select referrer, count(*) AS 'ct' FROM {$wpdb->prefix}redirection_logs_view WHERE url = '$url' AND created BETWEEN '$startdate' AND '$enddate' GROUP BY referrer ORDER BY referrer";

			$columns = array ("Referrer", "Hit Count");
		}

		

		$rows = $wpdb->get_results("$sql");
		//echo $sql;
		
		$this->draw_report_generic ($rows, $columns, "targeturl", "", "", 1);


	}

}