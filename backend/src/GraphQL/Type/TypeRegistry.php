<?php

declare(strict_types=1);

namespace App\GraphQL\Type;

use GraphQL\Type\Definition\Type;

class TypeRegistry
{
    private static array $types = [];

    public static function get(string $name): Type
    {
        if (!isset(self::$types[$name])) {
            $className = "App\\GraphQL\\Type\\{$name}Type";
            if (!class_exists($className)) {
                throw new \RuntimeException("GraphQL type {$name} not found");
            }
            self::$types[$name] = new $className();
        }
        return self::$types[$name];
    }
}
