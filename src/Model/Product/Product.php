<?php

declare(strict_types=1);

namespace App\Model\Product;

use PDO;

class Product extends AbstractProduct
{
    public static function create(PDO $pdo): self
    {
        return new self($pdo);
    }
}
