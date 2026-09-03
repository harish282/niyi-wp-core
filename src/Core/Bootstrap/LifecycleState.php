<?php
/**
 * Lifecycle state enum.
 *
 * Enumerates the application's runtime phases and the allowed transitions
 * between them. Used by the Lifecycle Manager to enforce a predictable
 * startup and shutdown sequence.
 *
 * @package NiyiWPCore
 */

declare( strict_types=1 );

namespace NiyiWPCore\Core\Bootstrap;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Application lifecycle states.
 */
enum LifecycleState: string {

	case PRE_BOOT = 'pre_boot';
	case BOOTING  = 'booting';
	case BOOTED   = 'booted';
	case READY    = 'ready';
	case SHUTDOWN = 'shutdown';

	/**
	 * States that may follow the current state.
	 *
	 * @return array<int, self>
	 */
	public function allowedNext(): array {
		return match ( $this ) {
			self::PRE_BOOT => array( self::BOOTING ),
			self::BOOTING  => array( self::BOOTED ),
			self::BOOTED   => array( self::READY ),
			self::READY    => array( self::SHUTDOWN ),
			self::SHUTDOWN => array(),
		};
	}

	/**
	 * Whether a transition to the given state is permitted.
	 *
	 * @param self $next Target state.
	 * @return bool
	 */
	public function canTransitionTo( self $next ): bool {
		return in_array( $next, $this->allowedNext(), true );
	}
}
