<?php
/**
 * Tests for the queue schema manager.
 *
 * @package NiyiWPCore
 */

declare( strict_types=1 );

namespace NiyiWPCore\Tests\Queue;

use NiyiWPCore\Core\Queue\QueueSchema;
use NiyiWPCore\Tests\Support\RecordingWpdb;
use NiyiWPCore\Tests\Support\SpyLogger;
use NiyiWPCore\Tests\TestCase;

/**
 * Queue schema tests.
 */
class QueueSchemaTest extends TestCase {

	/**
	 * In-memory database.
	 *
	 * @var RecordingWpdb
	 */
	private RecordingWpdb $db;

	/**
	 * Logger spy.
	 *
	 * @var SpyLogger
	 */
	private SpyLogger $logger;

	/**
	 * Schema manager under test.
	 *
	 * @var QueueSchema
	 */
	private QueueSchema $schema;

	/**
	 * Set up the schema manager with fakes.
	 *
	 * @return void
	 */
	protected function setUp(): void {
		parent::setUp();

		$this->db        = new RecordingWpdb();
		$GLOBALS['wpdb'] = $this->db;
		$this->logger    = new SpyLogger();
		$this->schema    = new QueueSchema( $this->logger );
	}

	/**
	 * Restore the default global database mock.
	 *
	 * @return void
	 */
	protected function tearDown(): void {
		$GLOBALS['wpdb'] = new \wpdb();

		parent::tearDown();
	}

	/**
	 * Test the schema is a CREATE TABLE statement for the queue table.
	 *
	 * @return void
	 */
	public function test_get_schema_targets_the_queue_table(): void {
		$schema = $this->schema->get_schema();

		$this->assertStringContainsString( 'CREATE TABLE wp_niyi_queue', $schema );
		$this->assertStringContainsString( 'job_id varchar(64) NOT NULL', $schema );
		$this->assertStringContainsString( 'PRIMARY KEY (id)', $schema );
	}

	/**
	 * Test install() invokes dbDelta with the schema and logs completion.
	 *
	 * @return void
	 */
	public function test_install_logs_completion(): void {
		$this->schema->install();

		$this->assertContains( 'Queue table installed.', $this->logger->messages_at( 'info' ) );
	}

	/**
	 * Test uninstall() drops the queue table.
	 *
	 * @return void
	 */
	public function test_uninstall_drops_the_queue_table(): void {
		$this->schema->uninstall();

		$this->assertContains( 'DROP TABLE IF EXISTS `wp_niyi_queue`', $this->db->queries );
	}

	/**
	 * Test uninstall() logs completion.
	 *
	 * @return void
	 */
	public function test_uninstall_logs_completion(): void {
		$this->schema->uninstall();

		$this->assertContains( 'Queue table removed.', $this->logger->messages_at( 'info' ) );
	}
}
