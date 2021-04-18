<?php

namespace Concrete\Core\Express\Command;

use Concrete\Core\Attribute\Category\CategoryInterface;
use Concrete\Core\Attribute\Command\RebuildIndexCommandHandler;
use Concrete\Core\Express\ObjectManager;
use Concrete\Core\Page\Command\AbstractRebuildIndexCommand;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class RebuildEntityIndexCommand extends AbstractRebuildIndexCommand
{

    /**
     * @var string
     */
    protected $entityId;

    /**
     * @param string $entityId
     */
    public function __construct(string $entityId = null)
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

    public function normalize(NormalizerInterface $normalizer, string $format = null, array $context = [])
    {
        return [
            'entityId' => $this->entityId
        ];
    }

    public function denormalize(DenormalizerInterface $denormalizer, $data, string $format = null, array $context = [])
    {
        $this->entityId = $data['entityId'];
    }


}
