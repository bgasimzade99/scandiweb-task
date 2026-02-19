<?php

declare(strict_types=1);

namespace App\Controller;

use App\GraphQL\SchemaBuilder;
use GraphQL\GraphQL as GraphQLBase;
use GraphQL\Type\Schema;
use RuntimeException;
use Throwable;

class GraphQL
{
    /**
     * @param array<string, mixed> $routeVars
     */
    public static function handle(array $routeVars = []): string
    {
        try {
            $schema = SchemaBuilder::build();

            $rawInput = file_get_contents('php://input');
            if ($rawInput === false) {
                throw new RuntimeException('Failed to get php://input');
            }

            $input = json_decode($rawInput, true);
            $query = $input['query'] ?? null;
            $variableValues = $input['variables'] ?? null;

            if (!$query) {
                throw new RuntimeException('Query is required');
            }

            $result = GraphQLBase::executeQuery($schema, $query, null, null, $variableValues);
            $output = $result->toArray();
        } catch (Throwable $e) {
            $output = [
                'errors' => [
                    [
                        'message' => $e->getMessage(),
                    ],
                ],
            ];
        }

        header('Content-Type: application/json; charset=UTF-8');
        return json_encode($output);
    }
}
