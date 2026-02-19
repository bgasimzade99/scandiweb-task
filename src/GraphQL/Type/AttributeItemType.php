<?php

declare(strict_types=1);

namespace App\GraphQL\Type;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;

class AttributeItemType extends ObjectType
{
    public function __construct()
    {
        parent::__construct([
            'name' => 'AttributeItem',
            'fields' => [
                'id' => ['type' => Type::string()],
                'value' => ['type' => Type::string()],
                'display_value' => ['type' => Type::string()],
            ],
        ]);
    }
}
