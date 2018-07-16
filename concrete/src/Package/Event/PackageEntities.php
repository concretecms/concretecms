<?php
namespace Concrete\Core\Package\Event;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\Event;

class PackageEntities extends Event
{
    /**
     * @var EntityManagerInterface[]
     */
    protected $entityManagers = [];

    /**
     * Add an EntityManagerInterface instance to the list.
     *
     * @param EntityManagerInterface $em
     *
     * @return $this
     */
    public function addEntityManager(EntityManagerInterface $em)
    {
        if (!in_array($em, $this->entityManagers, true)) {
            $this->entityManagers[] = $em;
        }

        return $this;
    }

    /**
     * Get the EntityManagerInterface instances.
     *
     * @return EntityManagerInterface[]
     */
    public function getEntityManagers()
    {
        return $this->entityManagers;
    }
}
