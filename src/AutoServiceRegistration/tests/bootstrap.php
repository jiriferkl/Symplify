<?php

require_once __DIR__.'/../vendor/autoload.php';

// clear cache
register_shutdown_function(function () {
    Nette\Utils\FileSystem::delete(__DIR__.'/Symfony/cache');
    Nette\Utils\FileSystem::delete(__DIR__.'/Symfony/logs');
});