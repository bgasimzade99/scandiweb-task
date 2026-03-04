<?php

declare(strict_types=1);

namespace App\Service;

interface AttributeFormatInterface
{
    /**
     * @return array{id: mixed, value: string, display_value: string}
     */
    public static function formatItem(array $row): array;
}
