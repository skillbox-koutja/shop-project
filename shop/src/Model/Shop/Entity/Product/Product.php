<?php

declare(strict_types=1);

namespace App\Model\Shop\Entity\Product;

use App\Model\AggregateRoot;
use App\Model\EventsTrait;
use App\Model\Shop\Entity\Category;
use App\Model\Shop\Entity\Product\Photo\PhotoRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Webmozart\Assert\Assert;

class Product implements AggregateRoot
{
    use EventsTrait;
    private Id $id;
    private string $title;
    private int $price;
    private ?Photo\Photo $photo;
    /** @var Category\Category[]|ArrayCollection */
    private $categories;
    private int $version;

    public function __construct(Id $id, string $title, int $price)
    {
        Assert::notEmpty($title, 0);
        Assert::greaterThan($price, 0);
        $this->id = $id;
        $this->title = $title;
        $this->price = $price;
        $this->categories = new ArrayCollection();
    }

    public function getId(): Id
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getPrice(): int
    {
        return $this->price;
    }

    public function getPhoto(): ?Photo\Photo
    {
        return $this->photo;
    }

    public function changeTitle(string $title): void
    {
        Assert::notEmpty($title, 0);
        $this->title = $title;
    }

    public function changePrice(int $price): void
    {
        Assert::greaterThan($price, 0);
        $this->price = $price;
    }

    public function getCategories(): array
    {
        $categories = [];
        foreach ($this->categories as $category) {
            if ($category->isNew()) {
                continue;
            }
            if ($category->isSale()) {
                continue;
            }
            $categories[] = $category;
        }
        return $categories;
    }

    public function assignCategory(Category\Category $category): void
    {
        if ($this->categories->contains($category)) {
            throw new \DomainException('Category is already assigned.');
        }
        $this->categories->add($category);
    }

    public function removeCategory(Category\Category $category): void
    {
        if ($this->categories->contains($category)) {
            $this->categories->removeElement($category);
            return;
        }
        throw new \DomainException('Category is not assigned.');
    }

    /**
     * @param Category\Category[] $categories
     */
    public function updateCategories(iterable $categories): void
    {
        $assign = [];
        $index = [];
        foreach ($categories as $category) {
            $id = $category->getId()->getValue();
            if (isset($index[$id])) {
                continue;  // exclude duplicate
            }
            $index[$id] = 1;
            if (!$this->categories->contains($category)) {
                $assign[] = $category;
            }
        }
        /** @var Category\Category $item */
        foreach ($this->categories->toArray() as $item) {
            $id = $item->getId()->getValue();
            if (!isset($index[$id])) {
                $this->removeCategory($item);
                $index[$id] = 1; // exclude duplicate
            }
        }
        foreach ($assign as $item) {
            $this->assignCategory($item);
        }
    }

    public function changePhoto(Photo\Photo $photo): void
    {
        $this->photo = $photo;
    }

    public function removePhoto(PhotoRepository $photos): void
    {
        if (isset($this->photo)) {
            $photos->remove($this->photo);
            $this->recordEvent(
                new Event\ProductPhotoRemoved(
                    $this->id,
                    $this->photo->getInfo()
                )
            );
            $this->photo = null;
            return;
        }
        throw new \DomainException('Photo is not existed');
    }

    public function isNew(): bool
    {
        foreach ($this->categories as $category) {
            if ($category->isNew()) {
                return true;
            }
        }

        return false;
    }

    public function isSale(): bool
    {
        foreach ($this->categories as $category) {
            if ($category->isSale()) {
                return true;
            }
        }

        return false;
    }
}
