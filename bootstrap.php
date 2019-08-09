<?php

require_once  __DIR__ . '/../core/ClassLoder.php';

$loader = new ClassLoder;
$loader->register(dirname(__FILE__) . '/core');
$loader->register(dirname(__FILE__) . '/models');
$loader->register();
