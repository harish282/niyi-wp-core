<?php
/**
 * Lifecycle manager.
 *
 * Tracks the plugin's current lifecycle state and enforces valid transitions
 * through the startup and shutdown phases. It fires a WordPress action on each
 * transition so future modules can subscribe. It tracks state only — it never
 * creates services, touches the database, or runs business logic.
 *
 * @package NiyiWPCore
 */

declare( strict_types=1 );

namespace NiyiWPCore\Core\Bootstrap;

/**
 * Manages application lifecycle state.
 */
class LifecycleManager {

	/**
	 * Prefix used for lifecycle transition hooks.
	 *
	 * @var string
	 */
	protected string $hook_prefix = 'niyi_core_lifecycle';

	/**
	 * Current lifecycle state.
	 *
	 * @var LifecycleState
	 */
	protected LifecycleState $state = LifecycleState::PRE_BOOT;

	/**
	 * Begin the startup sequence: PRE_BOOT → BOOTING → BOOTED.
	 *
	 * @return void
	 */
	public function start(): void {
		$this->transition( LifecycleState::BOOTING );
		$this->transition( LifecycleState::BOOTED );
	}

	/**
	 * Mark the application fully ready. Cannot be entered twice.
	 *
	 * @return void
	 */
	public function ready(): void {
		$this->transition( LifecycleState::READY );
	}

	/**
	 * Enter the shutdown state. Runs only once.
	 *
	 * @return void
	 */
	public function shutdown(): void {
		if ( LifecycleState::SHUTDOWN === $this->state ) {
			return;
		}

		$this->transition( LifecycleState::SHUTDOWN );
	}

	/**
	 * Transition to a new state after validating the move.
	 *
	 * @param LifecycleState $next Target state.
	 * @return void
	 * @throws \LogicException When the transition is not allowed.
	 */
	public function transition( LifecycleState $next ): void {
		$this->validateTransition( $next );

		$previous    = $this->state;
		$this->state = $next;

		$this->fireEvent( $previous, $next );
	}

	/**
	 * The current lifecycle state.
	 *
	 * @return LifecycleState
	 */
	public function current(): LifecycleState {
		return $this->state;
	}

	/**
	 * Whether the application has at least booted.
	 *
	 * @return bool
	 */
	public function isBooted(): bool {
		return in_array(
			$this->state,
			array( LifecycleState::BOOTED, LifecycleState::READY ),
			true
		);
	}

	/**
	 * Whether the application is fully ready.
	 *
	 * @return bool
	 */
	public function isReady(): bool {
		return LifecycleState::READY === $this->state;
	}

	/**
	 * Whether the application has shut down.
	 *
	 * @return bool
	 */
	public function isShutdown(): bool {
		return LifecycleState::SHUTDOWN === $this->state;
	}

	/**
	 * Ensure a transition is permitted from the current state.
	 *
	 * @param LifecycleState $next Target state.
	 * @return void
	 * @throws \LogicException When the transition is not allowed.
	 */
	protected function validateTransition( LifecycleState $next ): void {
		if ( ! $this->state->canTransitionTo( $next ) ) {
			throw new \LogicException(
				wp_kses_post(
					sprintf(
						'Invalid lifecycle transition from "%s" to "%s".',
						$this->state->value,
						$next->value
					)
				)
			);
		}
	}

	/**
	 * Fire the lifecycle transition event.
	 *
	 * @param LifecycleState $previous State left behind.
	 * @param LifecycleState $next     State entered.
	 * @return void
	 */
	protected function fireEvent( LifecycleState $previous, LifecycleState $next ): void {
		if ( function_exists( 'do_action' ) ) {
			do_action( $this->hook_prefix . '_transition', $next, $previous, $this );
			do_action( $this->hook_prefix . '_' . $next->value, $this );
		}
	}
}
