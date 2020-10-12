<?php

declare(strict_types=1);

namespace App\Model\Shop\UseCase\Product\Create;

use App\Model\Shop\UseCase\Product\PhotoInfo;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

class Command
{
    /**
     * @Assert\NotBlank()
     * @Assert\Length(max="255")
     */
    public string $title;
    /**
     * @Assert\NotBlank()
     * @Assert\GreaterThan(value="0")
     */
    public int $price;

    public bool $sale = false;

    public bool $new = false;

    /**
     * @Assert\NotBlank()
     * @var PhotoInfo|UploadedFile
     */
    public $photo;

    /**
     * @var string[]
     * @Assert\NotBlank()
     */
    public array $categories;
}
