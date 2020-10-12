<?php

declare(strict_types=1);

namespace App\Model\Shop\Entity\Category;

use App\Model\EntityNotFoundException;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;

class CategoryRepository
{
    /**
     * @var \Doctrine\ORM\EntityRepository
     */
    private $repo;
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->repo = $em->getRepository(Category::class);
        $this->em = $em;
    }

    public function get(Id $id): Category
    {
        if (!$category = $this->repo->find($id->getValue())) {
            throw new EntityNotFoundException('Category is not found.');
        }
        return $category;
    }

    public function getByType(Type $type): Category
    {
        if (!$category = $this->repo->findOneBy(['slug' => (string)$type])) {
            throw new EntityNotFoundException('Category is not found.');
        }
        return $category;
    }

    /**
     * @param Type[]|string[] $types
     * @return Category[]|ArrayCollection
     */
    public function getByTypes(array $types)
    {
        return $this->repo->findBy(['slug' => array_map(function ($type) {
            return (string)$type;
        }, $types)]);
    }

    public function add(Category $category): void
    {
        $this->em->persist($category);
    }

    public function remove(Category $category): void
    {
        $this->em->remove($category);
    }
}
