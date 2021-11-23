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

            $instanceAreas = $this->instance->getInstance()->getInstanceAreas();
            if (!count($instanceAreas)) {
                $this->refreshInstanceAreas($subArea);
            }

            return $subArea;
        }
        return null;
    }

    protected function refreshInstanceAreas(SubArea $subArea)
    {
        $app = Facade::getFacadeApplication();
        $entityManager = $app->make(EntityManager::class);

        $query = $entityManager->createQueryBuilder()
            ->delete(InstanceArea::class, 'i')
            ->where('i.instance = :instanceID')
            ->andWhere('i.containerAreaName = :areaHandle');
        $query->setParameter('instanceID', $this->instance->getInstance()->getContainerInstanceID());
        $query->setParameter('areaHandle', $this->areaDisplayName);
        $query->getQuery()->execute();

        $instanceArea = new InstanceArea();
        $instanceArea->setContainerAreaName($this->areaDisplayName);
        $instanceArea->setAreaID($subArea->getAreaID());
        $instanceArea->setInstance($this->instance->getInstance());
        $entityManager->persist($instanceArea);
        $entityManager->flush();
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
