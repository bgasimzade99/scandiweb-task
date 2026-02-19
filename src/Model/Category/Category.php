<?php

declare(strict_types=1);

namespace App\Model\Category;

use PDO;

class Category extends AbstractCategory
{
    public static function create(PDO $pdo): self
    {
        return new self($pdo);
    }
}
