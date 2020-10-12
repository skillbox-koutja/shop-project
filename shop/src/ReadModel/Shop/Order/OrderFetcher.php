<?php

namespace App\ReadModel\Shop\Order;

use App\Model\Shop\Entity\Order;
use App\Model\Shop\Entity\Delivery;
use App\Model\Shop\Entity\Payment;
use App\ReadModel\Shared\FetcherMetaHelper;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

class OrderFetcher
{
    private Connection $connection;
    private PaginatorInterface $paginator;
    private ClassMetadata $orderMeta;
    private ClassMetadata $deliveryMethodMeta;
    private ClassMetadata $paymentMethodMeta;

    public function __construct(
        Connection $connection,
        EntityManagerInterface $em,
        PaginatorInterface $paginator
    )
    {
        $this->connection = $connection;
        $this->paginator = $paginator;
        $this->orderMeta = $em->getClassMetadata(Order\Order::class);
        $this->deliveryMethodMeta = $em->getClassMetadata(Delivery\Method\Method::class);
        $this->paymentMethodMeta = $em->getClassMetadata(Payment\Method\Method::class);

    }

    /**
     * @param int $page
     * @param int $size
     * @return PaginationInterface
     */
    public function feed(
        int $page,
        int $size
    ): PaginationInterface
    {
        $conn = $this->connection;
        $helper = new FetcherMetaHelper($this->orderMeta, 'o');
        $paymentHelper = new FetcherMetaHelper($this->paymentMethodMeta, 'pm');
        $deliveryHelper = new FetcherMetaHelper($this->deliveryMethodMeta, 'dm');
        $qb = $helper->from($conn->createQueryBuilder());
        $expr = $qb->expr();
        $qb->select([
            $helper->select('id', $conn->quoteIdentifier('id')),
            $paymentHelper->select('title', $conn->quoteIdentifier('payment_method')),
            $deliveryHelper->select('title', $conn->quoteIdentifier('delivery_method')),
            $deliveryHelper->select('type', $conn->quoteIdentifier('delivery_method_type')),
            $helper->select('status', $conn->quoteIdentifier('status')),
            $helper->select('cost', $conn->quoteIdentifier('cost')),
            $helper->select('note', $conn->quoteIdentifier('note')),
            $helper->select('customer_name', $conn->quoteIdentifier('customer_name')),
            $helper->select('customer_phone', $conn->quoteIdentifier('customer_phone')),
            $helper->select('customer_city', $conn->quoteIdentifier('customer_city')),
            $helper->select('customer_street', $conn->quoteIdentifier('customer_street')),
            $helper->select('customer_house', $conn->quoteIdentifier('customer_house')),
            $helper->select('customer_apartment', $conn->quoteIdentifier('customer_apartment')),
        ]);

        $qb->innerJoin('o',
            $this->paymentMethodMeta->getTableName(), $paymentHelper->getRootAlias(),
            $expr->eq(
                $helper->select('payment_method_id'),
                $helper->selectColumn($paymentHelper->getRootAlias(), 'id'),
            )
        );
        $qb->innerJoin('o',
            $this->deliveryMethodMeta->getTableName(), $deliveryHelper->getRootAlias(),
            $expr->eq(
                $helper->select('delivery_method_id'),
                $helper->selectColumn($deliveryHelper->getRootAlias(), 'id'),
            )
        );

        $progress = $helper->select('progress');
        $created = $helper->select('created');
        $qb->addOrderBy($progress, 'asc');
        $qb->addOrderBy($created, 'desc');

        return $this->paginator->paginate($qb, $page, $size);
    }
}
