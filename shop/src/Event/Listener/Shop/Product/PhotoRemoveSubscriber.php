<?php

declare(strict_types=1);

namespace App\Event\Listener\Shop\Product;

use App\Model\Shop\Entity\Product\Event\ProductPhotoRemoved;
use App\Service\Uploader\FileUploader;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class PhotoRemoveSubscriber implements EventSubscriberInterface
{
    private FileUploader $uploader;

    public function __construct(FileUploader $uploader)
    {
        $this->uploader = $uploader;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            ProductPhotoRemoved::class => 'onProductPhotoRemoved',
        ];
    }

    public function onProductPhotoRemoved(ProductPhotoRemoved $event): void
    {
        $this->uploader->remove($event->info->getPath(), $event->info->getTitle());
    }
}
