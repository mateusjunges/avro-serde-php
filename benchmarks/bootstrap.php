<?php

require __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createUnsafeMutable(paths: __DIR__ . DIRECTORY_SEPARATOR . '..', names: '.env.dist');
$dotenv->load();
