<?php
/**
 * wpdb fake that records the SQL statements it executes.
 *
 * @package NiyiWPCore
 */

declare( strict_types=1 );

namespace NiyiWPCore\Tests\Support;

/**
 * Fake database that records executed statements for assertions.
 */
class RecordingWpdb extends FakeWpdb {

	/**
	 * SQL statements issued through query() and get_col().
	 *
	 * @var array<int, string>
	 */
	public array $queries = array();

	/**
	 * Record and execute a statement.
	 *
	 * @param mixed $query SQL.
	 * @return int
	 */
	public function query( $query ) {
		$this->queries[] = (string) $query;

		return parent::query( $query );
	}

	/**
	 * Record and emulate a column query.
	 *
	 * @param mixed $query SQL.
	 * @return array<int, mixed>
	 */
	public function get_col( $query ) {
		$this->queries[] = (string) $query;

		return parent::get_col( $query );
	}

	/**
	 * Record and emulate an insert.
	 *
	 * @param mixed $table  Table name.
	 * @param mixed $data   Row data.
	 * @param mixed $format Column formats.
	 * @return bool
	 */
	public function insert( $table, $data, $format = null ) {
		$this->queries[] = 'INSERT INTO ' . $table;

		return parent::insert( $table, $data, $format );
	}

	/**
	 * Record and emulate an update.
	 *
	 * @param mixed $table        Table name.
	 * @param mixed $data         Columns to set.
	 * @param mixed $where        Where conditions.
	 * @param mixed $format       Column formats.
	 * @param mixed $where_format Where formats.
	 * @return int
	 */
	public function update( $table, $data, $where, $format = null, $where_format = null ) {
		$this->queries[] = 'UPDATE ' . $table;

		return parent::update( $table, $data, $where, $format, $where_format );
	}

	/**
	 * Record and emulate a delete.
	 *
	 * @param mixed $table        Table name.
	 * @param mixed $where        Where conditions.
	 * @param mixed $where_format Where formats.
	 * @return int
	 */
	public function delete( $table, $where, $where_format = null ) {
		$this->queries[] = 'DELETE FROM ' . $table;

		return parent::delete( $table, $where, $where_format );
	}
}
