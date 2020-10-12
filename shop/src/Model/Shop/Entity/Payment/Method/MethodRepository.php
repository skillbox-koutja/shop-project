<?php

namespace App\Model\Shop\Entity\Payment\Method;

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

    public function get(Id $id): Method
    {
        /** @var Method $method */
        if (!$method = $this->repo->find($id->getValue())) {
            throw new EntityNotFoundException('Payment\Method is not found.');
        }
        return $method;
    }
}
