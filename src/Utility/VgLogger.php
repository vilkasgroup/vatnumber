<?php

declare(strict_types=1);

namespace Vilkas\VatNumber\Utility;

use Monolog\Formatter\LineFormatter;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Psr\Log\NullLogger;

class VgLogger
{
    /**
     * Initialize logger, which already points to _PS_ROOT_DIR_ so no need to add it in path
     * 
     * @param string $path path without _PS_ROOT_DIR_. example: /var/logs/file.log
     */
    public static function createLogger(string $path, string $name)
    {
        if (defined('_PS_VERSION_') && defined('_PS_ROOT_DIR_')) {
            $formatter = new LineFormatter(null, null, true, true);
            $handler = new StreamHandler(_PS_ROOT_DIR_ . $path);
            $handler->setFormatter($formatter);
            $logger = new Logger($name);
            $logger->pushHandler($handler);
        } else {
            $logger = new NullLogger();
        }

        return $logger;
    }
}
