<?php

namespace App\ReadModel\Shared;

use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\ORM\Mapping\ClassMetadata;

class FetcherMetaHelper
{
    private string $rootAlias;
    private ClassMetadata $meta;

    public function __construct(ClassMetadata $meta, string $rootAlias)
    {
        $this->rootAlias = $rootAlias;
        $this->meta = $meta;
    }

    public function getRootAlias(): string
    {
        return $this->rootAlias;
    }

    public function select(string $field, string $alias = null): string
    {
        $column = $this->meta->getColumnName($field);

        return $this->selectColumn($this->rootAlias, $column, $alias);
    }

    public function selectColumn(string $rootAlias, string $column, string $alias = null): string
    {
        if ($alias) {
            return sprintf('%s.%s %s', $rootAlias, $column, $alias);
        }

        return sprintf('%s.%s', $rootAlias, $column);
    }

    public function from(QueryBuilder $qb)
    {
        return $qb->from($this->meta->getTableName(), $this->rootAlias);
    }

    public function fromManyToMany(QueryBuilder $qb, string $field, string $rootAlias)
    {
        $joinTable = $this->tableManyToMany($field);

        return $qb->from($joinTable, $rootAlias);
    }

    public function tableManyToMany(string $field)
    {
        $mapping = $this->meta->getAssociationMapping($field);

        return $mapping['joinTable']['name'];
    }

    public function addOrderBy(QueryBuilder $qb, Sorter $sorter)
    {
        $sort = $this->select($sorter->field);

        return $qb->addOrderBy(
            $sort,
            $sorter->order
        );
    }
}
