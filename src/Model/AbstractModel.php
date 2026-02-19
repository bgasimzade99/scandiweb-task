<?php

declare(strict_types=1);

namespace App\Model;

use PDO;

abstract class AbstractModel
{
    protected PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }
}
