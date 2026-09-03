<?php
/**
 * Queue schema manager.
 *
 * Owns the queue table schema, creation, and cleanup. Keeps queue-specific
 * database logic inside the Queue subsystem instead of the global Installer.
 *
 * @package NiyiWPCore
 */

declare( strict_types=1 );

// phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.DirectDatabaseQuery.SchemaChange, WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.PreparedSQL.NotPrepared, WordPress.DB.PreparedSQLPlaceholders.ReplacementsWrongNumber, WordPress.DB.PreparedSQLPlaceholders.UnfinishedPrepare, PluginCheck.Security.DirectDB -- Queue schema operations: table names derive from $wpdb->prefix.

namespace NiyiWPCore\Core\Queue;

use NiyiWooSmartUpsells\Contracts\LoggerInterface;
use NiyiWooSmartUpsells\Helpers\WpLogger;

/**
 * Manages the queue database table.
 */
class QueueSchema {

	/**
	 * Logger instance.
	 *
	 * @var LoggerInterface
	 */
	private LoggerInterface $logger;

	/**
	 * Build the schema manager.
	 *
	 * @param LoggerInterface|null $logger Optional logger; defaults to WpLogger.
	 */
	public function __construct( ?LoggerInterface $logger = null ) {
		$this->logger = $logger ?? new WpLogger();
	}

	/**
	 * Create or upgrade the queue table using dbDelta.
	 *
	 * @return void
	 */
	public function install(): void {
		if ( ! function_exists( 'dbDelta' ) ) {
			require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		}

		dbDelta( $this->get_schema() );

		$this->logger->info( 'Queue table installed.' );
	}

	/**
	 * Drop the queue table.
	 *
	 * @return void
	 */
	public function uninstall(): void {
		global $wpdb;

		$table = WordPressQueue::resolve_table();

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.PreparedSQL.InterpolatedIntoWhereClause
		$wpdb->query( "DROP TABLE IF EXISTS `{$table}`" );

		$this->logger->info( 'Queue table removed.' );
	}

	/**
	 * SQL schema for the queue table.
	 *
	 * dbDelta requires each column on its own line and a trailing primary key line.
	 *
	 * @return string
	 */
	public function get_schema(): string {
		global $wpdb;

		$table   = WordPressQueue::resolve_table();
		$charset = $wpdb->get_charset_collate();

		return "CREATE TABLE {$table} (
			id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
			job_id varchar(64) NOT NULL,
			queue varchar(64) NOT NULL DEFAULT 'default',
			job_class varchar(191) NOT NULL,
			payload longtext NOT NULL,
			status varchar(20) NOT NULL DEFAULT 'pending',
			attempts int NOT NULL DEFAULT 0,
			available_at datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
			reserved_at datetime DEFAULT NULL,
			completed_at datetime DEFAULT NULL,
			failed_at datetime DEFAULT NULL,
			created_at datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
			PRIMARY KEY (id),
			KEY job_id (job_id),
			KEY status (status),
			KEY available_at (available_at)
		) {$charset};";
	}
}
