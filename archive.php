<?php

class redirector_reporting_class_archive {

	function __construct() {
	}

	function create_db_objects() {
		$this->create_table();
		$this->create_view();
		$this->drop_procedure();
		$this->create_procedure();
	}

	function put_archive_back() {
		global $wpdb;

		$sql = "INSERT INTO `{$wpdb->prefix}redirection_logs` 
			SELECT * 
			FROM `{$wpdb->prefix}redirection_logs_archive`;";

		$wpdb->query( $sql );

		$sql = "DELETE FROM `{$wpdb->prefix}redirection_logs_archive`;";

		$wpdb->query( $sql );
	}

	function get_charset() {
		global $wpdb;

		$charset_collate = '';
		if ( ! empty($wpdb->charset) )
			$charset_collate = "DEFAULT CHARACTER SET $wpdb->charset";

		if ( ! empty($wpdb->collate) )
			$charset_collate .= " COLLATE $wpdb->collate";

		return $charset_collate;
	}


	function create_table() {
		global $wpdb;

		$charset_collate = $this->get_charset();

		$sql = 	"CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}redirection_logs_archive`(
			  `id` int(11) unsigned NOT NULL,
			  `created` datetime NOT NULL,
			  `url` mediumtext NOT NULL,
			  `sent_to` mediumtext,
			  `agent` mediumtext NOT NULL,
			  `referrer` mediumtext,
			  `redirection_id` int(11) unsigned default NULL,
			  `ip` varchar(17) NOT NULL default '',
			  `module_id` int(11) unsigned NOT NULL,
				`group_id` int(11) unsigned default NULL,
			  PRIMARY KEY ( `id`),
			  KEY `created` (`created`),
			  KEY `redirection_id` (`redirection_id`),
			  KEY `ip` (`ip`),
			  KEY `group_id` (`group_id`),
			  KEY `module_id` (`module_id`)
			) $charset_collate";

		$wpdb->query( $sql );
	}

	function drop_procedure() {	
		global $wpdb;
		$sql = "DROP PROCEDURE IF EXISTS `{$wpdb->prefix}ArchiveRedirectionData`";
		$wpdb->query( $sql );
	}

	function create_procedure() {
		global $wpdb;

		$sql = "CREATE PROCEDURE `{$wpdb->prefix}ArchiveRedirectionData` (IN days_to_keep int unsigned)
		BEGIN
			set @rows = 1;

			SET @days = days_to_keep*-1;

			WHILE @rows != 0 DO
				CREATE TEMPORARY TABLE processing
				SELECT id, created, url, sent_to, agent, referrer, redirection_id, ip, module_id, group_id
				FROM {$wpdb->prefix}redirection_logs
				WHERE created NOT BETWEEN DATE_ADD(CURDATE(), INTERVAL @days DAY) AND DATE_ADD(CURDATE(), INTERVAL 1 DAY)
				LIMIT 1000;

				INSERT INTO {$wpdb->prefix}redirection_logs_archive
				(id, created, url, sent_to, agent, referrer, redirection_id, ip, module_id, group_id)
				SELECT id, created, url, sent_to, agent, referrer, redirection_id, ip, module_id, group_id
				FROM processing;
		
				DELETE FROM {$wpdb->prefix}redirection_logs
				WHERE id IN (SELECT id from processing);
	
				SET @rows = ROW_COUNT();

				DROP TEMPORARY TABLE processing;
			END WHILE;
	END ;";
		$wpdb->query( $sql );
	}

	function create_view() {
		global $wpdb;

		$sql = "CREATE OR REPLACE VIEW `{$wpdb->prefix}redirection_logs_view` AS 
			SELECT * FROM `{$wpdb->prefix}redirection_logs`
			UNION ALL
			SELECT * FROM `{$wpdb->prefix}redirection_logs_archive`";

		$wpdb->query( $sql );
	}

	function schedule_archiving() {

		if (!has_action('redirection_reporting_archive_data') ) {
			add_settings_error('action_not_enabled', 'error_action_not_enabled', 'The cron is not setup correctly.  Try deactivating and reactivating the plugin.', 'error');
		}

		if ( ! wp_next_scheduled( 'redirection_reporting_archive_data') ) {
			$time=time();
			$time=$time+60;
			#register_activation_hook( __FILE__, array($this, 'archive_data'));
			wp_schedule_event($time, 'daily', 'redirection_reporting_archive_data');
		}
	}

	function unscheduled_archiving() {
		$timestamp = wp_next_scheduled( 'redirection_reporting_archive_data');
		#register_deactivation_hook( __FILE__, array($this, 'archive_data'));
		wp_unschedule_event( $timestamp, 'redirection_reporting_archive_data');
	}



	public static function archive_data() {
		//write_log('redirection-reporting.archive.archive_data starting');

		global $wpdb;
		$options = get_option('redirection_reporting');
		$days_to_keep = $options['days_to_keep'];

		$sql = "CALL `{$wpdb->prefix}ArchiveRedirectionData` ($days_to_keep)";

		$wpdb->query( $sql );


	}
}

