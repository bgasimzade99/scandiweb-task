<?php

declare(strict_types=1);

namespace App\Service;

/**
 * Formats raw attribute row into the standard item structure.
 * Implements AttributeFormatInterface for both text and swatch types (same format).
 */
class AttributeFormatter implements AttributeFormatInterface
{
    public static function formatItem(array $row): array
    {
        return [
            'id' => $row['value_id'],
            'value' => $row['value'],
            'display_value' => $row['display_value'],
        ];
    }
}
