<?php

declare(strict_types=1);

namespace App\Repository;

use PDO;

abstract class BaseRepository
{
    public function __construct(
        protected PDO $pdo,
    ) {
    }

    protected function fetchOne(string $sql, array $params = []): ?array
    {
        $stmt = $this->prepareAndExecute($sql, $params);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row !== false ? $row : null;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    protected function fetchAll(string $sql, array $params = []): array
    {
        $stmt = $this->prepareAndExecute($sql, $params);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $rows ?: [];
    }

    protected function execute(string $sql, array $params = []): void
    {
        $this->prepareAndExecute($sql, $params);
    }

    protected function lastInsertId(): int
    {
        return (int) $this->pdo->lastInsertId();
    }

    /**
     * @return mixed
     */
    protected function transaction(callable $fn)
    {
        $this->pdo->beginTransaction();
        try {
            $result = $fn();
            $this->pdo->commit();
            return $result;
        } catch (\Throwable $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    private function prepareAndExecute(string $sql, array $params): \PDOStatement
    {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }
}
