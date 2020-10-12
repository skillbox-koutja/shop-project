<?php

namespace App\ReadModel\Shop\Category;

use App\Model\Shop\Entity\Category;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Statement;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;

class CategoryFetcher
{
    private Connection $connection;
    private ClassMetadata $meta;
    /** @var \Doctrine\ORM\EntityRepository */
    private $repository;

    public function __construct(
        Connection $connection,
        EntityManagerInterface $em
    ) {
        $this->connection = $connection;
        $this->meta = $em->getClassMetadata(Category\Category::class);
        $this->repository = $em->getRepository(Category\Category::class);
    }

    public function find(string $id): ?Category\Category
    {
        return $this->repository->find($id);
    }

    public function findBySlug(string $slug): ?Category\Category
    {
        return $this->repository->findOneBy(['slug' => $slug]);
    }

    public function allList(): array
    {
        $meta = $this->meta;
        $slugColumn = $meta->getColumnName('slug');
        $qb = $this->connection->createQueryBuilder();
        $expr = $qb->expr();
        $qb->select(
                $meta->getColumnName('slug') . ' slug',
                $meta->getColumnName('title') . ' title'
            )
            ->from($meta->getTableName())
            ->where($expr->notIn($slugColumn, ':types'))
            ->setParameter('types', Category\Type::existed(), Connection::PARAM_STR_ARRAY)
            ->orderBy('title');
        /** @var Statement $stmt */
        $stmt = $qb->execute();
        $list = $stmt->fetchAllAssociative();

        $result = [];
        foreach ($list as $item) {
            $result[$item['slug']] = $item['title'];
        }

        return $result;
    }
}
