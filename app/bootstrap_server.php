<?php

use Nette\Application\Routers\Route;
use Nette\Application\Routers\RouteList;

// Load Nette Framework
require LIBS_DIR . '/Nette/loader.php';

$configurator = new Nette\Config\Configurator;

$configurator->enableDebugger(__DIR__ . '/../log');
$configurator->setTempDirectory(__DIR__ . '/../temp');

$configurator->createRobotLoader()
    ->addDirectory(APP_DIR)
    ->addDirectory(LIBS_DIR)
    ->register();

$configurator->addConfig(__DIR__ . '/config/config.neon');
$container = $configurator->createContainer();

// Setup router
/*
$container->router[] = new RouteList;
$container->router[] = new Route('index.php', 'Front:Default:default', Route::ONE_WAY);
$container->router[] = new Route('<modul>/<presenter>/<action>[/<id>]', 'Front:Default:default');
*/

$container->router[] = $backendrouter = new RouteList("Admin");
$backendrouter[] = new Route("admin/index.php", "Default:", Route::ONE_WAY);
$backendrouter[] = new Route("admin[/<presenter>][/<action>][/<id>]", "Default:");


$container->router[] = $frontrouter = new RouteList("Front");
$frontrouter[] = new Route("index.php", "Default:", Route::ONE_WAY);
$frontrouter[] = new Route('', 'Default:', Route::ONE_WAY);
$frontrouter[] = new Route("<presenter>/<action>[/<id>]", "Default:default");





// Configure and run the application!
$container->application->run();
