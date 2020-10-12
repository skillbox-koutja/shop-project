<?php

namespace App\Model\Shop\Entity\Delivery\Method;

use App\Model\EntityNotFoundException;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\Persistence\ObjectRepository;

class MethodRepository
{
    private EntityManagerInterface $em;
    /** @var EntityRepository|ObjectRepository */
    private ObjectRepository $repo;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
        $class = Method::class;
        $this->repo = $em->getRepository($class);
    }

    public function findByType(Type $type): Method
    {
        /** @var Method $method */
        if (!$method = $this->repo->findOneBy(['type' => $type])) {
            throw new EntityNotFoundException('Delivery\Method is not found.');
        }
        return $method;
    }
}
