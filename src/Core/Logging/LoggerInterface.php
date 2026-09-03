<?php
/**
 * Core logging contract.
 *
 * Extends the base logger contract with no additional methods; it exists so
 * framework-agnostic services can type-hint the core logging interface while
 * remaining compatible with any implementation.
 *
 * @package NiyiWooSmartUpsells
 */

declare( strict_types=1 );

namespace NiyiWooSmartUpsells\Core\Logging;

/**
 * Contract for core logging services.
 */
interface LoggerInterface extends \NiyiWooSmartUpsells\Contracts\LoggerInterface {
}
