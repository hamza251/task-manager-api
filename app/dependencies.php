<?php

declare(strict_types=1);

use App\Application\Settings\SettingsInterface;
use DI\ContainerBuilder;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Monolog\Processor\UidProcessor;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;

return function (ContainerBuilder $containerBuilder) {
    $containerBuilder->addDefinitions([
        LoggerInterface::class => function (ContainerInterface $c) {
            $settings = $c->get(SettingsInterface::class);

            $loggerSettings = $settings->get('logger');
            $logger = new Logger($loggerSettings['name']);

            $processor = new UidProcessor();
            $logger->pushProcessor($processor);

            $handler = new StreamHandler($loggerSettings['path'], $loggerSettings['level']);
            $logger->pushHandler($handler);

            return $logger;
        },

         // --- PDO CONNECTION ---
         PDO::class => function (ContainerInterface $c) {
            $settings = $c->get(SettingsInterface::class)->get('db'); // Get DB settings section

            $host = $settings['host'];
            $dbname = $settings['dbname'];
            $user = $settings['user'];
            $pass = $settings['pass'];
            $port = $settings['port'];
            $charset = $settings['charset'] ?? 'utf8';

            $dsn = "pgsql:host=$host;port=$port;dbname=$dbname;";

            // setup pdo options
            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // throw exceptions on errors
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,      // fetch results as associative arrays
                PDO::ATTR_EMULATE_PREPARES   => false,                // use native prepared statements
            ];

            try {
                $pdo = new PDO($dsn, $user, $pass, $options);
                return $pdo;
            } catch (\PDOException $e) {
                 error_log("Database Connection Error: " . $e->getMessage());
                 throw new \PDOException($e->getMessage(), (int)$e->getCode());
            }
        },
        // --- End PDO Connection ---
    ]);
};
