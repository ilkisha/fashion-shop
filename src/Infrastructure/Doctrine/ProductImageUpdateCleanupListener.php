<?php

namespace App\Infrastructure\Doctrine;

use App\Entity\Product;
use App\Infrastructure\Storage\ProductImageStorage;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use Doctrine\ORM\Event\PostUpdateEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Events;

#[AsDoctrineListener(event: Events::preUpdate)]
#[AsDoctrineListener(event: Events::postUpdate)]
final class ProductImageUpdateCleanupListener
{
    /**
     * @var array<int, string> pending deletes keyed by spl_object_id(product)
     */
    private array $pendingDeletes = [];

    public function __construct(private readonly ProductImageStorage $storage) {}

    public function preUpdate(PreUpdateEventArgs $args): void
    {
        $entity = $args->getObject();

        if (!$entity instanceof Product) {
            return;
        }

        if (!$args->hasChangedField('imagePath')) {
            return;
        }

        $old = $args->getOldValue('imagePath');
        $new = $args->getNewValue('imagePath');

        if (!$old) {
            return;
        }

        if ($old === $new) {
            return;
        }

        $this->pendingDeletes[spl_object_id($entity)] = $old;
    }

    public function postUpdate(PostUpdateEventArgs $args): void
    {
        $entity = $args->getObject();

        if (!$entity instanceof Product) {
            return;
        }

        $key = spl_object_id($entity);
        if (!isset($this->pendingDeletes[$key])) {
            return;
        }

        $old = $this->pendingDeletes[$key];
        unset($this->pendingDeletes[$key]);

        $this->storage->delete($old);
    }
}
