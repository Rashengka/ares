<?php

declare(strict_types = 1);

namespace App;

use Nette\Configurator;
use Tracy\Debugger;

class Bootstrap
{
    public static function boot(): Configurator
    {
        if (PHP_OS == "Darwin") {
            Debugger::$editor = 'phpstorm://open?file=%file&line=%line';
        }

        $configurator = new Configurator;

        //$configurator->setDebugMode('secret@23.75.345.200'); // enable for your remote IP
        $configurator->enableTracy(__DIR__ . '/../log');

        $configurator->setTimeZone('Europe/Prague');
        $configurator->setTempDirectory(__DIR__ . '/../temp');

        $configurator->createRobotLoader()
            ->addDirectory(__DIR__)
            ->register();

        $configurator->addConfig(__DIR__ . '/config/common.neon');
        $configurator->addConfig(__DIR__ . '/config/local.neon');

        return $configurator;
    }
}
