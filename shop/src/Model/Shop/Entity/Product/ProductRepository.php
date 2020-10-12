<?php

namespace App\Model\Shop\Entity\Product;

use App\Model\EntityNotFoundException;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;

class ProductRepository
{
    /** @var \Doctrine\ORM\EntityRepository */
    private $repo;
    private Connection $connection;
    private EntityManagerInterface $em;

    public function __construct(
        EntityManagerInterface $em,
        Photo\PhotoRepository $photos
    )
    {
        $this->repo = $em->getRepository(Product::class);
        $this->connection = $em->getConnection();
        $this->em = $em;
        $this->photos = $photos;
    }

    public function nextId(): Id
    {
        $conn = $this->connection;
        $nextVal = $conn->getDatabasePlatform()->getSequenceNextValSQL(Id::SEQUENCE);
        return new Id((int) $conn->executeQuery($nextVal)->fetchOne());
    }

    public function get(Id $id): Product
    {
        /** @var Product $product */
        if (!$product = $this->repo->find($id->getValue())) {
            throw new EntityNotFoundException('Product is not found.');
        }
        return $product;
    }

    public function add(Product $product): void
    {
        $this->em->persist($product);
    }

    public function remove(Product $product): void
    {
        $product->removePhoto($this->photos);

        $this->em->remove($product);
    }
}
