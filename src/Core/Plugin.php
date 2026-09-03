<?php
/**
 * Core plugin.
 *
 * Encapsulates core service initialization: container, settings, notifications,
 * and lifecycle. Framework-agnostic; no WordPress hook registration here.
 *
 * @package NiyiWPCore
 */

declare( strict_types=1 );

namespace NiyiWPCore\Core;

use NiyiWPCore\Core\Bootstrap\LifecycleManager;
use NiyiWPCore\Core\Bootstrap\LifecycleState;
use NiyiWPCore\Core\Container\Container;
use NiyiWPCore\Core\Container\ContainerInterface;
use NiyiWPCore\Core\CoreServiceProvider;
use NiyiWPCore\Core\Settings\Settings;
use NiyiWPCore\Core\Notifications\NotificationManagerInterface;
use NiyiWPCore\Core\Settings\SettingsInterface;

/**
 * Core plugin base class.
 */
class Plugin {

	/**
	 * Service container.
	 *
	 * @var ContainerInterface
	 */
	protected ContainerInterface $container;

	/**
	 * Shared container instance for global access.
	 *
	 * @var ContainerInterface|null
	 */
	private static ?ContainerInterface $container_instance = null;

	/**
	 * Settings manager.
	 *
	 * @var SettingsInterface
	 */
	protected SettingsInterface $config;

	/**
	 * Notification manager.
	 *
	 * @var NotificationManagerInterface
	 */
	protected NotificationManagerInterface $notifications;

	/**
	 * Lifecycle state manager.
	 *
	 * @var LifecycleManager
	 */
	protected LifecycleManager $lifecycle;

	/**
	 * Build the core plugin.
	 */
	public function __construct() {
		$this->lifecycle = new LifecycleManager();
	}

	/**
	 * Core startup sequence.
	 *
	 * @return void
	 */
	public function boot(): void {
		if ( LifecycleState::PRE_BOOT !== $this->lifecycle->current() ) {
			return;
		}

		$this->lifecycle->start();

		$this->initializeContainer();
		$this->registerServices();
		$this->loadSettings();
		$this->loadNotifications();
		$this->bootProviders();

		$this->lifecycle->ready();
	}

	/**
	 * Core cleanup sequence.
	 *
	 * @return void
	 */
	public function shutdown(): void {
		if ( $this->lifecycle->isShutdown() ) {
			return;
		}

		if ( $this->lifecycle->isReady() ) {
			$this->lifecycle->shutdown();
		}
	}

	/**
	 * Load settings from the container.
	 *
	 * @return void
	 */
	protected function loadSettings(): void {
		$this->config = $this->container->get( SettingsInterface::class );
	}

	/**
	 * Load notification manager from the container.
	 *
	 * @return void
	 */
	protected function loadNotifications(): void {
		$this->notifications = $this->container->get( NotificationManagerInterface::class );
	}

	/**
	 * Create the service container.
	 *
	 * @return void
	 */
	protected function initializeContainer(): void {
		$this->container = new Container();

		self::$container_instance = $this->container;
	}

	/**
	 * Retrieve the shared container instance.
	 *
	 * @return ContainerInterface|null
	 */
	public static function container_instance(): ?ContainerInterface {
		return self::$container_instance;
	}

	/**
	 * Register core services in the container.
	 *
	 * @return void
	 */
	protected function registerCoreServices(): void {
		$this->container->singleton(
			SettingsInterface::class,
			fn() => new Settings()
		);
	}

	/**
	 * Register core service providers.
	 *
	 * @return void
	 */
	protected function registerServiceProviders(): void {
		( new CoreServiceProvider() )->register( $this->container );
	}

	/**
	 * Register all services.
	 *
	 * @return void
	 */
	protected function registerServices(): void {
		$this->registerCoreServices();
		$this->registerServiceProviders();
	}

	/**
	 * Boot registered service providers.
	 *
	 * @return void
	 */
	protected function bootProviders(): void {
	}

	/**
	 * The service container.
	 *
	 * @return ContainerInterface
	 */
	public function container(): ContainerInterface {
		return $this->container;
	}

	/**
	 * The settings manager.
	 *
	 * @return SettingsInterface
	 */
	public function config(): SettingsInterface {
		return $this->config;
	}

	/**
	 * The lifecycle state manager.
	 *
	 * @return LifecycleManager
	 */
	public function lifecycle(): LifecycleManager {
		return $this->lifecycle;
	}
}
