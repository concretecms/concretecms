<?php

namespace Concrete\Core\Area;

use Concrete\Core\Entity\Page\Container\InstanceArea;
use Concrete\Core\Page\Container\ContainerBlockInstance;
use Concrete\Core\Page\Page;
use Concrete\Core\Support\Facade\Facade;
use Doctrine\ORM\EntityManager;

class ContainerArea
{

    /**
     * @var bool
     */
    protected $gridContainerEnabled = false;

    /**
     * @var int|null
     */
    protected $gridMaximumColumns;

    /**
     * @var ContainerBlockInstance
     */
    protected $instance;

    /**
     * @var string
     */
    protected $areaDisplayName;

    public function __construct(ContainerBlockInstance $instance, string $areaDisplayName)
    {
        $this->instance = $instance;
        $this->areaDisplayName = $areaDisplayName;
    }

    public function getAreaBlocksArray(Page $page) : array
    {
        $subArea = $this->getSubAreaObject($page);
        if ($subArea) {
            return $subArea->getAreaBlocksArray($page);
        }
        return [];
    }

    /**
     * Enable Grid containers.
     */
    final public function enableGridContainer()
    {
        $this->gridContainerEnabled = true;
    }

    /**
     * @param int $columns
     */
    final public function setAreaGridMaximumColumns(int $columns)
    {
        $this->gridMaximumColumns = $columns;
    }

    public function getSubAreaObject(Page $page): ?SubArea
    {
        $block = $this->instance->getBlock();
        $area = $block->getBlockAreaObject();
        if ($area) {
            $arHandle = $this->instance->getInstance()->getContainerInstanceID() . SubArea::AREA_SUB_DELIMITER .
                $this->areaDisplayName;
            $subArea = new SubArea(
                $arHandle,
                $area->getAreaHandle(),
                $area->getAreaID()
            );
            $subArea->setAreaDisplayName($this->areaDisplayName);
            $page = $area->getAreaCollectionObject();
            $subArea->load($page);
            $subArea->setSubAreaBlockObject($block);
            $this->refreshInstanceAreas($subArea);

            return $subArea;
        }
        return null;
    }

    protected function refreshInstanceAreas(SubArea $subArea): void
    {
        $app = Facade::getFacadeApplication();
        /** @var EntityManager $em */
        $em = $app->make(EntityManager::class);

        $instance = $this->instance->getInstance();

        // Lightweight existence check: select only the PK, max 1 row.
        $exists = $em->getRepository(InstanceArea::class)
            ->createQueryBuilder('i')
            ->select('i')
            ->where('i.instance = :instance')
            ->andWhere('i.containerAreaName = :handle')
            ->setParameters([
                'instance' => $instance,
                'handle'   => $this->areaDisplayName,
            ])
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();

        if ($exists) {
            // Already present; nothing to do.
            return;
        }

        // Insert (first time only).
        $instanceArea = new InstanceArea();
        $instanceArea->setContainerAreaName($this->areaDisplayName);
        $instanceArea->setAreaID($subArea->getAreaID());
        $instanceArea->setInstance($instance);

        $em->persist($instanceArea);
        // Keep inverse side up to date if you maintain the collection.
        $instance->getInstanceAreas()->add($instanceArea);

        // In case a concurrent request inserts the same row between the check and flush,
        // ignore a unique-key violation (see note below about adding a unique index).
        try {
            // Optionally: $em->flush($instanceArea); to scope the flush.
            $em->flush();
        } catch (UniqueConstraintViolationException $e) {
            // Another request inserted it; safe to ignore.
            $em->clear($instanceArea); // optional: detach the transient entity
        }
    }

    public function getTotalBlocksInArea(Page $page): int
    {
        $blocks = $this->getAreaBlocksArray($page);
        return count($blocks);
    }

    public function display(Page $page)
    {
        $subArea = $this->getSubAreaObject($page);
        if ($subArea) {
            if ($this->gridContainerEnabled) {
                $subArea->enableGridContainer();
            }
            if (isset($this->gridMaximumColumns)) {
                $subArea->setAreaGridMaximumColumns($this->gridMaximumColumns);
            }

            $subArea->display($page);
        }
    }

}
