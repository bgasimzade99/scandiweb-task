<?php

require_once __DIR__ . '/../vendor/autoload.php';

(new \App\Script\SeedFromDataJson())->run();
echo "Database seeded successfully from data.json\n";
