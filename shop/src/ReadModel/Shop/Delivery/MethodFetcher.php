<?php

namespace App\ReadModel\Shop\Delivery;

use App\Model\Shop\Entity\Delivery\Method;
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
    private Method\Type $defaultMethodType;
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
        $this->defaultMethodType = new Method\Type(Method\Type::DEFAULT);
    }

    public function getDefaultMethodType()
    {
        return $this->defaultMethodType;
    }

    public function defaultMethodId()
    {
        return $this->getId($this->defaultMethodType);
    }

    /**
     * @param Method\Type|string $type
     * @return false|string
     * @throws \Doctrine\DBAL\Driver\Exception
     */
    public function getId($type)
    {
        $helper = new FetcherMetaHelper($this->meta, 'dm');
        $qb = $helper->from($this->connection->createQueryBuilder());
        $qb->select('id')
            ->where($qb->expr()->eq($helper->select('type'), ':type'))
            ->setParameter(':type', $type);
        $stmt = $this->getResult($qb);

        return $stmt->fetchOne();
    }

    /**
     * @return Statement|ResultStatement
     */
    private function getResult(QueryBuilder $qb)
    {
        return $qb->execute();
    }
}
