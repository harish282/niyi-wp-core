<?php
/**
 * In-memory wpdb used by repository and service tests.
 *
 * Stores rows per table and emulates the small subset of SQL the core
 * library emits: SELECT with WHERE/ORDER BY/LIMIT, UPDATE ... SET ... WHERE,
 * DELETE ... WHERE, and COUNT. Queries are asserted against the table
 * snapshot after prepare() substitutes placeholders with literal values.
 *
 * @package NiyiWPCore
 */

declare( strict_types=1 );

namespace NiyiWPCore\Tests\Support;

/**
 * Fake database for tests.
 */
class FakeWpdb extends \wpdb {

	/**
	 * Table name => rows keyed by the row's primary id.
	 *
	 * @var array<string, array<int, array<string, mixed>>>
	 */
	public array $tables = array();

	/**
	 * Auto-increment counter for insert().
	 *
	 * @var int
	 */
	public int $insert_id = 0;

	/**
	 * Build a fake query string with placeholders substituted by literals.
	 *
	 * @param mixed $query SQL with placeholders.
	 * @param mixed ...$args Placeholder values.
	 * @return string
	 */
	public function prepare( $query, ...$args ) {
		$query = (string) $query;

		if ( ! empty( $args ) && is_array( $args[0] ) ) {
			$args = $args[0];
		}

		foreach ( $args as $arg ) {
			$positions = array_filter(
				array( strpos( $query, '%d' ), strpos( $query, '%f' ), strpos( $query, '%s' ) ),
				static fn ( $position ): bool => false !== $position
			);

			if ( empty( $positions ) ) {
				break;
			}

			$position    = min( $positions );
			$placeholder = ( $position === strpos( $query, '%d' ) ) ? '%d'
				: ( ( $position === strpos( $query, '%f' ) ) ? '%f' : '%s' );

			$value = '%s' === $placeholder
				? "'" . str_replace( "'", "''", (string) $arg ) . "'"
				: (string) ( '%d' === $placeholder ? (int) $arg : (float) $arg );

			$query = substr_replace( $query, $value, $position, 2 );
		}

		return $query;
	}

	/**
	 * Emulate SELECT with WHERE, ORDER BY, LIMIT, and projected columns.
	 *
	 * @param mixed $query  SQL.
	 * @param mixed $output Output format (ARRAY_A, OBJECT, ...).
	 * @return array<mixed>
	 */
	public function get_results( $query, $output = null ) {
		$query = (string) $query;
		$table = $this->table_from( $query );
		$rows  = $this->filtered( $table, $query );

		if ( str_contains( $query, 'DISTINCT product_id' ) ) {
			return $rows;
		}

		$this->sort( $rows, $query );

		if ( str_contains( $query, 'id, recommended_id' ) ) {
			$rows = array_map(
				static fn ( array $row ): array => array(
					'id'             => (int) $row['id'],
					'recommended_id' => (int) $row['recommended_id'],
				),
				$rows
			);
		}

		if ( null === $output || OBJECT === $output ) {
			$rows = array_map( static fn ( array $row ): object => (object) $row, $rows );
		}

		return array_values( $rows );
	}

	/**
	 * Emulate SELECT ... LIMIT 1.
	 *
	 * @param mixed $query  SQL.
	 * @param mixed $output Output format.
	 * @return array<string, mixed>|null
	 */
	public function get_row( $query, $output = null ) {
		$rows = $this->get_results( $query, $output );

		return $rows[0] ?? null;
	}

	/**
	 * Emulate SELECT DISTINCT <column>.
	 *
	 * Supports the product_id and post_id columns used by the core library.
	 *
	 * @param mixed $query SQL.
	 * @return array<int, mixed>
	 */
	public function get_col( $query ) {
		$query = (string) $query;
		$rows  = $this->filtered( $this->table_from( $query ), $query );

		$column = str_contains( $query, 'post_id' ) ? 'post_id' : 'product_id';

		return array_values( array_unique( array_map( static fn ( array $row ) => (int) $row[ $column ], $rows ) ) );
	}

	/**
	 * Emulate scalar queries such as COUNT(*).
	 *
	 * @param mixed $query SQL.
	 * @return string
	 */
	public function get_var( $query ) {
		$query = (string) $query;
		$rows  = $this->filtered( $this->table_from( $query ), $query );

		if ( str_contains( $query, 'COUNT(*)' ) ) {
			return (string) count( $rows );
		}

		return '0';
	}

	/**
	 * Emulate UPDATE and DELETE statements.
	 *
	 * @param mixed $query SQL.
	 * @return int Number of affected rows.
	 */
	public function query( $query ) {
		$query   = (string) $query;
		$table   = $this->table_from( $query );
		$matches = $this->matched_indexes( $table, $query );

		if ( 0 === strncasecmp( $query, 'UPDATE', 6 ) ) {
			$sets = $this->extract_sets( $query );

			foreach ( $matches as $index ) {
				foreach ( $sets as $field => $value ) {
					$this->tables[ $table ][ $index ][ $field ] = ( 'NULL' === $value ) ? null : $value;
				}
			}
		}

		if ( 0 === strncasecmp( $query, 'DELETE', 6 ) ) {
			rsort( $matches );

			foreach ( $matches as $index ) {
				unset( $this->tables[ $table ][ $index ] );
			}
		}

		return count( $matches );
	}

	/**
	 * Emulate INSERT.
	 *
	 * @param mixed $table  Table name.
	 * @param mixed $data   Row data.
	 * @param mixed $format Column formats.
	 * @return bool
	 */
	public function insert( $table, $data, $format = null ) {
		$table = (string) $table;
		$this->insert_id++;
		$this->tables[ $table ][ $this->insert_id ] = array_merge( (array) $data, array( 'id' => $this->insert_id ) );

		return true;
	}

	/**
	 * Emulate UPDATE via a generated SQL statement.
	 *
	 * @param mixed $table        Table name.
	 * @param mixed $data         Columns to set.
	 * @param mixed $where        Where conditions.
	 * @param mixed $format       Column formats.
	 * @param mixed $where_format Where formats.
	 * @return int Number of affected rows.
	 */
	public function update( $table, $data, $where, $format = null, $where_format = null ) {
		$table = (string) $table;
		$sets  = array();

		foreach ( (array) $data as $field => $value ) {
			$sets[] = $field . ' = ' . $this->literal( $value );
		}

		$conditions = array();

		foreach ( (array) $where as $field => $value ) {
			$conditions[] = $field . ' = ' . $this->literal( $value );
		}

		$sql = sprintf(
			'UPDATE %s SET %s WHERE %s',
			$table,
			implode( ', ', $sets ),
			implode( ' AND ', $conditions )
		);

		return (int) $this->query( $sql );
	}

	/**
	 * Emulate DELETE via a generated SQL statement.
	 *
	 * @param mixed $table        Table name.
	 * @param mixed $where        Where conditions.
	 * @param mixed $where_format Where formats.
	 * @return int Number of deleted rows.
	 */
	public function delete( $table, $where, $where_format = null ) {
		$table      = (string) $table;
		$conditions = array();

		foreach ( (array) $where as $field => $value ) {
			$conditions[] = $field . ' = ' . $this->literal( $value );
		}

		$sql = sprintf( 'DELETE FROM %s WHERE %s', $table, implode( ' AND ', $conditions ) );

		return (int) $this->query( $sql );
	}

	/**
	 * Format a PHP value as a SQL literal.
	 *
	 * @param mixed $value Value.
	 * @return string
	 */
	private function literal( $value ): string {
		if ( null === $value ) {
			return 'NULL';
		}

		if ( is_int( $value ) || is_float( $value ) ) {
			return (string) $value;
		}

		return "'" . str_replace( "'", "''", (string) $value ) . "'";
	}

	/**
	 * Extract rows from a table matching the query's WHERE clause.
	 *
	 * @param string $table Table name.
	 * @param string $query SQL.
	 * @return array<int, array<string, mixed>>
	 */
	private function filtered( string $table, string $query ): array {
		$where   = $this->extract_where( $query );
		$matched = array();

		foreach ( $this->tables[ $table ] ?? array() as $row ) {
			if ( $this->matches( $row, $where ) ) {
				$matched[] = $row;
			}
		}

		return $matched;
	}

	/**
	 * Get the row indexes in a table matching a query's WHERE clause.
	 *
	 * @param string $table Table name.
	 * @param string $query SQL.
	 * @return array<int, int>
	 */
	private function matched_indexes( string $table, string $query ): array {
		$where   = $this->extract_where( $query );
		$indexes = array();

		foreach ( $this->tables[ $table ] ?? array() as $index => $row ) {
			if ( $this->matches( $row, $where ) ) {
				$indexes[] = $index;
			}
		}

		return $indexes;
	}

	/**
	 * Extract the table name referenced by a query.
	 *
	 * @param string $query SQL.
	 * @return string
	 */
	private function table_from( string $query ): string {
		if ( preg_match( '/(?:FROM|UPDATE|INTO)\s+(wp_[a-z0-9_]+)/', $query, $m ) ) {
			return $m[1];
		}

		return 'wp_niyi_wsu_recommendations';
	}

	/**
	 * Parse a WHERE clause into field/operator/value triples.
	 *
	 * @param string $query SQL.
	 * @return array<int, array{0: string, 1: string, 2: string}>
	 */
	private function extract_where( string $query ): array {
		$query = (string) preg_replace( '/\s+/', ' ', $query );

		if ( ! preg_match( '/\sWHERE\s+(.+?)(?:\sORDER BY\s|\sLIMIT\s|$)/', $query, $m ) ) {
			return array();
		}

		$conditions = array();

		foreach ( explode( ' AND ', $m[1] ) as $condition ) {
			if ( preg_match( '/^\s*([a-z_]+)\s*(=|<>|<|<=|>|>=|IN)\s*(.+?)\s*$/', $condition, $cm ) ) {
				$conditions[] = array( $cm[1], $cm[2], $cm[3] );
			}
		}

		return $conditions;
	}

	/**
	 * Parse an UPDATE ... SET clause into field/value pairs.
	 *
	 * @param string $query SQL.
	 * @return array<string, string>
	 */
	private function extract_sets( string $query ): array {
		if ( ! preg_match( '/SET (.+?) WHERE/s', $query, $m ) ) {
			return array();
		}

		$sets = array();

		foreach ( explode( ',', $m[1] ) as $assignment ) {
			if ( preg_match( '/^\s*([a-z_]+)\s*=\s*(.+?)\s*$/', $assignment, $sm ) ) {
				$sets[ $sm[1] ] = $this->unquote( $sm[2] );
			}
		}

		return $sets;
	}

	/**
	 * Check a row against parsed WHERE conditions.
	 *
	 * @param array<string, mixed>                     $row        Row data.
	 * @param array<int, array{0: string, 1: string, 2: string}> $conditions Parsed conditions.
	 * @return bool
	 */
	private function matches( array $row, array $conditions ): bool {
		foreach ( $conditions as $condition ) {
			list( $field, $operator, $raw ) = $condition;

			if ( ! array_key_exists( $field, $row ) ) {
				return false;
			}

			$actual = $row[ $field ];

			if ( 'IN' === $operator ) {
				$haystack = array_map(
					fn ( string $item ): string => $this->unquote( trim( $item ) ),
					explode( ',', trim( $raw, '() ' ) )
				);

				if ( ! in_array( (string) $actual, $haystack, true ) ) {
					return false;
				}

				continue;
			}

			$expected = $this->unquote( $raw );

			if ( '<' === $operator || '>' === $operator || '<=' === $operator || '>=' === $operator ) {
				$cmp = strcmp( (string) $actual, (string) $expected );
				$ok  = '<' === $operator ? $cmp < 0
					: ( '>' === $operator ? $cmp > 0
					: ( '<=' === $operator ? $cmp <= 0 : $cmp >= 0 ) );

				if ( ! $ok ) {
					return false;
				}

				continue;
			}

			if ( '<>' === $operator ) {
				if ( (string) $actual === (string) $expected ) {
					return false;
				}

				continue;
			}

			if ( (string) $actual !== (string) $expected ) {
				return false;
			}
		}

		return true;
	}

	/**
	 * Sort rows in place according to an ORDER BY clause.
	 *
	 * @param array<int, array<string, mixed>> $rows  Rows to sort.
	 * @param string                           $query SQL.
	 * @return void
	 */
	private function sort( array &$rows, string $query ): void {
		if ( ! preg_match( '/ORDER BY (.+?)(?:\sLIMIT\s|$)/s', $query, $m ) ) {
			return;
		}

		$criteria = array();

		foreach ( array_map( 'trim', explode( ',', $m[1] ) ) as $part ) {
			$bits = preg_split( '/\s+/', $part );

			$criteria[] = array(
				$bits[0],
				( strtoupper( $bits[1] ?? 'ASC' ) === 'DESC' ) ? SORT_DESC : SORT_ASC,
			);
		}

		usort(
			$rows,
			static function ( array $a, array $b ) use ( $criteria ): int {
				foreach ( $criteria as $c ) {
					$cmp = SORT_DESC === $c[1]
						? strcmp( (string) $b[ $c[0] ], (string) $a[ $c[0] ] )
						: strcmp( (string) $a[ $c[0] ], (string) $b[ $c[0] ] );

					if ( 0 !== $cmp ) {
						return $cmp;
					}
				}

				return 0;
			}
		);
	}

	/**
	 * Strip surrounding quotes from a literal.
	 *
	 * @param string $value Raw literal.
	 * @return string
	 */
	private function unquote( string $value ): string {
		$value = trim( $value );

		if ( strlen( $value ) >= 2 && "'" === $value[0] && "'" === substr( $value, -1 ) ) {
			return str_replace( "''", "'", substr( $value, 1, -1 ) );
		}

		return $value;
	}
}
