<?php

namespace App\Model\Shop\Entity\Product\Photo;

use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;

class PhotoRepository
{
    /** @var \Doctrine\ORM\EntityRepository */
    private $repo;
    private Connection $connection;
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->repo = $em->getRepository(Photo::class);
        $this->connection = $em->getConnection();
        $this->em = $em;
    }

    public function remove(Photo $photo): void
    {
        $this->em->remove($photo);
    }
}
