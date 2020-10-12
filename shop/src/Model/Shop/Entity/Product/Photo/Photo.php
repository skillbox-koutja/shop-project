<?php

declare(strict_types=1);

namespace App\Model\Shop\Entity\Product\Photo;

use App\Model\Shop\Entity\Product\Product;

class Photo
{
    /**
     * @var Product
     * ORM\ManyToOne(targetEntity="App\Model\Work\Entity\Projects\Task\Task", inversedBy="files")
     * ORM\JoinColumn(name="task_id", referencedColumnName="id", nullable=false)
     */
    private Product $product;
    /**
     * @var Id
     * ORM\Column(type="work_projects_task_file_id")
     * ORM\Id
     */
    private Id $id;

    private Info $info;

    public function __construct(Product $product, Id $id, Info $info)
    {
        $this->product = $product;
        $this->id = $id;
        $this->info = $info;
    }

    public function getProduct(): Product
    {
        return $this->product;
    }

    public function getId(): Id
    {
        return $this->id;
    }

    public function getInfo(): Info
    {
        return $this->info;
    }
}
