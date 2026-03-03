<?php

declare(strict_types=1);

namespace App\Service;

use GraphQL\Error\ClientAware;

class OrderValidationException extends \RuntimeException implements ClientAware
{
    public function isClientSafe(): bool
    {
        return true;
    }

    public function getCategory(): string
    {
        return 'OrderValidation';
    }
}
