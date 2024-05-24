<?php

use Monarch\Database\QueryBuilder;

describe('QueryBuilder', function () {
    it('should concatenate SQL lines', function () {
        $queryBuilder = new QueryBuilder();

        $queryBuilder->sql('SELECT * FROM users')
            ->concat('WHERE id = ?', [1]);

        expect($queryBuilder->toSql())->toBe('SELECT * FROM users WHERE id = ?');
        expect($queryBuilder->bindings())->toBe([1]);
    });

    it('should conditionally add SQL lines', function () {
        $queryBuilder = new QueryBuilder();
        $id = 1;

        $queryBuilder->sql('SELECT * FROM users')
            ->when($id, 'WHERE id = ?', [$id]);

        expect($queryBuilder->toSql())->toBe('SELECT * FROM users WHERE id = ?');
        expect($queryBuilder->bindings())->toBe([$id]);

        $queryBuilder->reset();

        $queryBuilder->sql('SELECT * FROM users')
            ->whenNot($id, 'WHERE id = ?', [$id]);

        expect($queryBuilder->toSql())->toBe('SELECT * FROM users');
        expect($queryBuilder->bindings())->toBe([]);
    });

    it('should apply callback to each item', function () {
        $queryBuilder = new QueryBuilder();
        $ids = [1, 2, 3];

        $queryBuilder->sql('SELECT * FROM users')
            ->each($ids, function ($id, $query, $index) {
                $query->concat(($index === 0 ? 'WHERE' : 'AND') .' id = ?', [$id]);
            });

        expect($queryBuilder->toSql())->toBe('SELECT * FROM users WHERE id = ? AND id = ? AND id = ?');
        expect($queryBuilder->bindings())->toBe([1, 2, 3]);
    });
});
