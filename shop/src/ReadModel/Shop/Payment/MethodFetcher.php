<?php

namespace App\ReadModel\Shop\Payment;

use App\Model\Shop\Entity\Payment\Method;
use App\ReadModel\Shared\FetcherMetaHelper;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Driver\ResultStatement;
use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\DBAL\Statement;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;

class MethodFetcher
{
    private Connection $connection;
    private ClassMetadata $meta;
    /** @var \Doctrine\ORM\EntityRepository */
    private $repository;

    public function __construct(
        Connection $connection,
        EntityManagerInterface $em
    )
    {
        $this->connection = $connection;
        $this->meta = $em->getClassMetadata(Method\Method::class);
        $this->repository = $em->getRepository(Method\Method::class);
    }

    public function defaultMethodId()
    {
        $helper = new FetcherMetaHelper($this->meta, 'pm');
        $priority = $helper->select('priority');
        $qb = $helper->from($this->connection->createQueryBuilder());

        return $qb
            ->select('id')
            ->setMaxResults(1)
            ->orderBy($priority)
            ->execute()
            ->fetchOne();
    }

    public function allList(): array
    {
        $helper = new FetcherMetaHelper($this->meta, 'pm');
        $conn = $this->connection;
        $qb = $helper->from($this->connection->createQueryBuilder());
        $priority = $helper->select('priority');
        $qb->select([
            $helper->select('id', $conn->quoteIdentifier('id')),
            $helper->select('title', $conn->quoteIdentifier('title')),
        ])
            ->orderBy($priority);

        $stmt = $this->getResult($qb);
        $list = $stmt->fetchAllAssociative();

        $result = [];
        foreach ($list as $item) {
            $result[$item['id']] = $item['title'];
        }

        return $result;
    }

    /**
     * @return Statement|ResultStatement
     */
    private function getResult(QueryBuilder $qb)
    {
        return $qb->execute();
    }
}
