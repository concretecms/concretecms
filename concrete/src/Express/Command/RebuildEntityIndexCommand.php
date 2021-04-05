<?php

namespace Concrete\Core\Express\Command;

use Concrete\Core\Attribute\Category\CategoryInterface;
use Concrete\Core\Attribute\Command\RebuildIndexCommandHandler;
use Concrete\Core\Attribute\Command\RebuildIndexCommandInterface;
use Concrete\Core\Express\ObjectManager;
use Concrete\Core\Foundation\Command\HandlerAwareCommandInterface;

class RebuildEntityIndexCommand implements RebuildIndexCommandInterface, HandlerAwareCommandInterface
{

    /**
     * @var string
     */
    protected $entityId;

    /**
     * @param string $entityId
     */
    public function __construct(string $entityId)
    {
        $this->entityId = $entityId;
    }

    /**
     * @return string
     */
    public function getEntityId(): string
    {
        return $this->entityId;
    }

    public function getAttributeKeyCategory(): CategoryInterface
    {
        $entity = app(ObjectManager::class)->getObjectByID($this->getEntityId());
        if ($entity) {
            return $entity->getAttributeKeyCategory();
        }
    }

    public static function getHandler(): string
    {
        return RebuildIndexCommandHandler::class;
    }

    public function getIndexName()
    {
        $entity = app(ObjectManager::class)->getObjectByID($this->getEntityId());
        if ($entity) {
            return $entity->getName();
        }
    }


}
