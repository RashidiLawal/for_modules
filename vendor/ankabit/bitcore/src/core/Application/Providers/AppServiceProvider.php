<?php

declare(strict_types=1);

namespace BitCore\Application\Providers;

use BitCore\Application\Providers\ProviderAbstract;
use BitCore\Application\Services\Settings\SystemConfig;
use BitCore\Foundation\Database\ConnectionResolver;
use BitCore\Foundation\Database\DatabaseMigrationRepository;
use BitCore\Foundation\Database\Manager as Capsule;
use BitCore\Foundation\Database\Migrator;
use BitCore\Foundation\Events\Dispatcher;
use BitCore\Foundation\Filesystem\FilesystemManager;
use BitCore\Foundation\Translation\FileLoader;
use BitCore\Foundation\Translation\Translator;
use BitCore\Foundation\Validation\Factory as ValidationFactory;
use BitCore\Application\Services\FileUploader;
use BitCore\Foundation\Filesystem\LocalFilesystem;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Monolog\Processor\UidProcessor;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;

/**
 * Registers core services and components within the application container.
 *
 * This service provider is responsible for registering essential services
 * such as the logger, database, filesystem, translation, validation, and queue.
 */
class AppServiceProvider extends ProviderAbstract
{
    /**
     * {@inheritDoc}
     */
    public function register()
    {
        $container = $this->app->getContainer();

        // Register the logger
        $container->singleton(
            LoggerInterface::class,
            function (ContainerInterface $container) {
                $settings = $container->get(SystemConfig::class);
                $loggerSettings = $settings->get('logger');

                $logger = new Logger($loggerSettings['name']);
                $logger->pushProcessor(new UidProcessor());
                $logger->pushHandler(new StreamHandler($loggerSettings['path'], $loggerSettings['level']));

                return $logger;
            }
        );

        // Register the database capsule (Eloquent)
        $container->singleton('db', function (ContainerInterface $container) {
            $settings = $container->get(SystemConfig::class);
            $dbSettings = $settings->get('database');

            $capsule = new Capsule();
            $capsule->addConnection($dbSettings['connections'][$dbSettings['default']]);
            $capsule->setAsGlobal();

            return $capsule;
        });

        // Register the database connection resolver (AFTER 'db' is registered)
        $container->singleton(ConnectionResolver::class, function (ContainerInterface $container) {
            $resolver = new ConnectionResolver();
            $resolver->addConnection('default', $container->get('db')->getConnection());
            $resolver->setDefaultConnection('default');
            return $resolver;
        });

        // Register filesystems
        $container->singleton(FilesystemManager::class, function (ContainerInterface $container) {
            $settings = $container->get(SystemConfig::class);
            $manager = new FilesystemManager($container);
            $manager->setConfig((array)$settings->get('filesystems'));
            return $manager;
        });

        $container->singleton('localFilesystem', fn () => new LocalFilesystem());

        // Register translator
        $container->singleton(Translator::class, function (ContainerInterface $container) {
            $loader = new FileLoader(
                $container->get('localFilesystem'),
                BITCORE_LANG_PATHS // Default folders
            );
            return new Translator($loader, 'en');
        });

        // Register validator
        $container->singleton(ValidationFactory::class, function (ContainerInterface $container) {
            return new ValidationFactory(
                $container->get(Translator::class),
                $container
            );
        });

        // Register file uploader
        $container->singleton(FileUploader::class, fn () => new FileUploader(storage()));
    }

    /**
     * {@inheritDoc}
     */
    public function boot()
    {
        $container = $this->app->getContainer();

        // Boot Eloquent AFTER all services are registered
        $container->get('db')->bootEloquent();

        // Register migrator (depends on booted database)
        $container->singleton('migrator', function (ContainerInterface $container) {
            $settings = $container->get(SystemConfig::class);
            $dbSettings = $settings->get('database');

            return new Migrator(
                new DatabaseMigrationRepository(
                    $container->get(ConnectionResolver::class),
                    is_array($dbSettings['migrations'])
                        ? ($dbSettings['migrations']['table'] ?? null)
                        : $dbSettings['migrations']
                ),
                $container->get(ConnectionResolver::class),
                $container->get('localFilesystem'),
                $container->get(Dispatcher::class)
            );
        });
    }
}
