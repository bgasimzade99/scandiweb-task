<?php

declare(strict_types=1);

namespace App\Model\Attribute;

use PDO;

class TextAttribute extends AbstractAttribute
{
    public static function getType(): string
    {
        return 'text';
    }

    public static function create(PDO $pdo): self
    {
        return new self($pdo);
    }
}
