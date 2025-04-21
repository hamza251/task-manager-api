<?php

declare(strict_types=1);

use App\Application\Settings\Settings;
use App\Application\Settings\SettingsInterface;
use DI\ContainerBuilder;
use Monolog\Logger;

return function (ContainerBuilder $containerBuilder) {

    // Global Settings Object
    $containerBuilder->addDefinitions([
        SettingsInterface::class => function () {
            return new Settings([
                'displayErrorDetails' => true, // Should be set to false in production
                'logError'            => false,
                'logErrorDetails'     => false,
                'logger' => [
                    'name' => 'slim-app',
                    'path' => 'php://stdout',
                    'level' => Logger::DEBUG,
                ],
                'db' => [
                    'driver'    => 'pgsql', // driver type
                    'host'      => $_ENV['DB_HOST'] ?? 'db',
                    'port'      => $_ENV['DB_PORT'] ?? 5432,
                    'dbname'    => $_ENV['DB_DATABASE'] ?? 'tasks_db',
                    'user'      => $_ENV['DB_USERNAME'] ?? 'postgres',
                    'pass'      => $_ENV['DB_PASSWORD'] ?? 'secret',
                    'charset'   => 'utf8',
                ],
            ]);
        }
    ]);
};
