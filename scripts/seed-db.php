<?php

require_once __DIR__ . '/../vendor/autoload.php';

$argv = $GLOBALS['argv'] ?? $_SERVER['argv'] ?? [];
$productsOnly = in_array('--products-only', $argv, true);
(new \App\Script\SeedFromDataJson())->run($productsOnly);
echo "Database seeded successfully from data.json\n";
