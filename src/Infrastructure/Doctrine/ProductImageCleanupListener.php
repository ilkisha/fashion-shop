<?php

namespace App\Infrastructure\Doctrine;

use App\Entity\Product;
use App\Infrastructure\Storage\ProductImageStorage;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use Doctrine\ORM\Event\PostRemoveEventArgs;
use Doctrine\ORM\Events;

#[AsDoctrineListener(event: Events::postRemove)]
final readonly class ProductImageCleanupListener
{
    public function __construct(
        private ProductImageStorage $storage
    ) {}

    public function postRemove(PostRemoveEventArgs $args): void
    {
        $entity = $args->getObject();

        if (!$entity instanceof Product) {
            return;
        }

        $this->storage->delete($entity->getImagePath());
    }
}
