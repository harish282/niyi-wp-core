# Core

Framework-agnostic foundation services for Niyi Woo Smart Upsells.

All core services are registered as singletons through `CoreServiceProvider` and resolved from the container. Features should depend on these interfaces, not concrete implementations.

---

## Services

| Service | Interface | Purpose |
|---------|-----------|---------|
| Settings | `SettingsInterface` | Persistent plugin settings via WordPress Options API |
| Logger | `LoggerInterface` | Logging with PSR-3 levels |
| Events | `EventDispatcherInterface` | In-memory synchronous event dispatching |
| HTTP | `HTTPClientInterface` | Outbound HTTP requests via `wp_remote_request` |
| Scheduler | `SchedulerInterface` | Recurring and one-time tasks via WP Cron |
| Cache | `CacheInterface` | Runtime caching via WP Object Cache and Transients |
| Hooks | `HookManagerInterface` | WordPress action/filter registration wrapper |
| Assets | `AssetManagerInterface` | Script/style registration and enqueueing |
| Validation | `ValidatorInterface` | Input validation and sanitization |
| Queue | `QueueInterface` | Background job dispatch and processing |
| Notifications | `NotificationManagerInterface` | Admin notice rendering |

---

## Usage

### Resolving from the container

```php
/** @var ContainerInterface $container */

$settings = $container->get( \NiyiWPCore\Core\Settings\SettingsInterface::class );
$settings->set( 'provider', 'ollama' )->save();

$logger = $container->get( \NiyiWPCore\Contracts\LoggerInterface::class );
$logger->info( 'Settings saved.', array( 'provider' => 'ollama' ) );

$hooks = $container->get( \NiyiWPCore\Core\Hooks\HookManagerInterface::class );
$hooks->action( 'init', [ $this, 'boot' ] );

$assets = $container->get( \NiyiWPCore\Core\Assets\AssetManagerInterface::class );
$assets->register_script( 'my-script', $assets->asset_url( 'js/admin.js' ), array( 'jquery' ) );
```

### Event dispatching

```php
$dispatcher = $container->get( \NiyiWPCore\Core\Events\EventDispatcherInterface::class );

$dispatcher->listen( \MyPlugin\Events\ProductSaved::class, function ( $event ) {
    // Handle event.
} );

$dispatcher->dispatch( new \MyPlugin\Events\ProductSaved( $product_id ) );
```

### Queueing jobs

```php
$queue = $container->get( \NiyiWPCore\Core\Queue\QueueInterface::class );

$queue->dispatch( new \MyPlugin\Jobs\GenerateRecommendations( $product_id ) );
$queue->dispatchLater( new \MyPlugin\Jobs\CleanupLogs(), 300 );

$worker = $container->get( \NiyiWPCore\Core\Queue\QueueWorker::class );
$worker->process( $queue );
```

### Validating input

```php
$validator = $container->get( \NiyiWPCore\Core\Validation\ValidatorInterface::class );

$result = $validator->validate( $input, array(
    'provider'   => 'required|string',
    'model'      => 'required|string',
    'batch_size' => 'integer|min:1|max:100',
) );

if ( $result->fails() ) {
    return $result->errors();
}

$validated = $result->validated();
```

### Notifications

```php
$notifications = $container->get( \NiyiWPCore\Core\Notifications\NotificationManagerInterface::class );

$notifications->success( 'Recommendations generated successfully.' );
$notifications->error( 'Unable to connect to AI provider.' );
```

### Facades

Core provides static Facade classes for simplified access to services:

```php
use NiyiWPCore\Core\Facade\View;
use NiyiWPCore\Core\Facade\Config;
use NiyiWPCore\Core\Facade\Log;
use NiyiWPCore\Core\Facade\Hooks;
use NiyiWPCore\Core\Facade\Notifications;
use NiyiWPCore\Core\Facade\Queue;
use NiyiWPCore\Core\Facade\Cache;
use NiyiWPCore\Core\Facade\Events;
use NiyiWPCore\Core\Facade\Http;
use NiyiWPCore\Core\Facade\Scheduler;
use NiyiWPCore\Core\Facade\Assets;
use NiyiWPCore\Core\Facade\Validation;

// Views
View::render( 'Admin.Settings.index', array( 'settings' => $settings ) );
View::share( 'pluginVersion', NIYI_WOO_SMART_UPSELLS_VERSION );

// Settings
Config::set( 'provider', 'ollama' );
Config::get( 'provider', 'default' )->save();

// Logging
Log::info( 'Settings saved.', array( 'provider' => 'ollama' ) );
Log::error( 'API connection failed.' );

// Hooks
Hooks::action( 'init', [ $this, 'boot' ] );
Hooks::filter( 'the_content', [ $this, 'filter_content' ] );

// Notifications
Notifications::success( 'Recommendations generated.' );
Notifications::error( 'AI provider unreachable.' );

// Queue
Queue::dispatch( new GenerateRecommendationsJob( $product_id ) );
Queue::dispatchLater( new CleanupLogsJob(), 300 );

// Cache
Cache::set( 'key', 'value', 3600 );
Cache::get( 'key', 'default' );

// Events
Events::listen( ProductSaved::class, function ( $event ) {
    // Handle event.
} );
Events::dispatch( new ProductSaved( $product_id ) );

// HTTP
$response = Http::get( 'https://api.example.com/data' );

// Scheduler
Scheduler::schedule( 'cleanup_logs', 'daily' );
Scheduler::scheduleOnce( 'one_time_task', time() + 300 );

// Assets
Assets::register_script( 'admin-js', Assets::asset_url( 'js/admin.js' ), array( 'jquery' ) );
Assets::localize_script( 'admin-js', 'pluginData', array( 'rest_url' => admin_url( 'admin-ajax.php' ) ) );

// Validation
$result = Validation::validate( $input, array(
    'provider'   => 'required|string',
    'batch_size' => 'integer|min:1|max:100',
) );

if ( $result->fails() ) {
    return $result->errors();
}
```

---

## Architecture

Core services follow these rules:

- **Interfaces first**: every service has a contract in `src/Core/Contracts/` or alongside the service
- **Singleton scope**: services are registered once via `CoreServiceProvider`
- **Framework boundary**: WordPress APIs are wrapped inside Core, never leaked to Features
- **No business logic**: Core provides infrastructure only

### Core Plugin vs Bootstrap Plugin

Every plugin using Core must create a Bootstrap Plugin that extends Core\Plugin:

```php
<?php
// src/Bootstrap/Plugin.php

declare( strict_types=1 );

namespace NiyiWPCore\Bootstrap;

use NiyiWPCore\Core\Plugin as CorePlugin;

class Plugin extends CorePlugin {
    // Add your plugin-specific bootstrap logic here
    // Requirements, installer, hooks, etc.
}
```

**Required bootstrap sequence:**

```php
$plugin = new \NiyiWPCore\Bootstrap\Plugin();
$plugin->init();
```

This is required because:

1. Facades (`Core\Facade\*`) resolve services from a shared container instance
2. The container is initialized by `Core\Plugin::boot()`
3. `Core\Plugin` must be booted before any Facade can resolve services

`Core\Plugin` is framework-agnostic. It initializes the container, registers core services, loads settings and notifications, and manages the lifecycle state.

`Bootstrap\Plugin` extends `Core\Plugin` and adds framework-specific concerns: WordPress requirements checking, installer integration, activation/deactivation hooks, text domain loading, and admin notices.
