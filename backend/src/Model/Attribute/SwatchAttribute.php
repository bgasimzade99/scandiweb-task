<?php

declare(strict_types=1);

namespace App\Model\Attribute;

use PDO;

class SwatchAttribute extends AbstractAttribute
{
    public static function getType(): string
    {
        return 'swatch';
    }

    public static function create(PDO $pdo): self
    {
        return new self($pdo);
    }
}
