<?php
namespace Concrete\Core\Express\Generator;

use Concrete\Core\Entity\Express\Entity;
use Doctrine\ORM\EntityManagerInterface;

class EntityHandleGenerator
{
    protected $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    protected function handleIsAvailable($handleToTest, Entity $existingEntity)
    {
        $r = $this->entityManager->getRepository('Concrete\Core\Entity\Express\Entity');
        $entity = $r->findOneByHandle($handleToTest);
        if (is_object($entity)) {
            if ($entity->getID() != $existingEntity->getID()) {
                return false;
            } else {
                return true;
            }
        } else {
            return true;
        }
    }

    public function generate(Entity $entity)
    {
        $text = \Core::make('helper/text');
        $baseHandle = trim(substr($text->handle($entity->getName()), 0, 32), '_');

        if ($this->handleIsAvailable($baseHandle, $entity)) {
            return $baseHandle;
        }
        $suffix = 2;
        $handle = $baseHandle . '_' . $suffix;
        while (!$this->handleIsAvailable($handle, $entity)) {
            $suffix++;
            $handle = $baseHandle . '_' . $suffix;
        }

        return $handle;
    }
}
