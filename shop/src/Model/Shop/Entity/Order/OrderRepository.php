<?php

namespace App\Model\Shop\Entity\Order;

use App\Model\EntityNotFoundException;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;

class OrderRepository
{
    /** @var \Doctrine\ORM\EntityRepository */
    private $repo;
    private Connection $connection;
    private EntityManagerInterface $em;

    public function __construct(
        EntityManagerInterface $em
    )
    {
        $this->repo = $em->getRepository(Order::class);
        $this->connection = $em->getConnection();
        $this->em = $em;
    }

    public function nextId(): Id
    {
        $conn = $this->connection;
        $nextVal = $conn->getDatabasePlatform()->getSequenceNextValSQL(Id::SEQUENCE);
        return new Id((int) $conn->executeQuery($nextVal)->fetchOne());
    }

    public function get(Id $id): Order
    {
        /** @var Order $order */
        if (!$order = $this->repo->find($id->getValue())) {
            throw new EntityNotFoundException('Order is not found.');
        }
        return $order;
    }

    public function add(Order $order): void
    {
        $this->em->persist($order);
    }

    public function remove(Order $order): void
    {
        $this->em->remove($order);
    }
}
