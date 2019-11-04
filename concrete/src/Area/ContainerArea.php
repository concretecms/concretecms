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
     * @var ContainerBlockInstance 
     */
    protected $instance;

    /**
     * @var string
     */
    protected $areaHandle;
    
    public function __construct(ContainerBlockInstance $instance, string $areaHandle)
    {
        $this->instance = $instance;
        $this->areaHandle = $areaHandle;
    }

    public function display(Page $page)
    {
        $block = $this->instance->getBlock();
        $area = $block->getBlockAreaObject();
        if ($area) {
            $subArea = new SubArea(
                $this->areaHandle,
                $area->getAreaHandle(),
                $area->getAreaID()
            );
            $subArea->setAreaDisplayName($this->areaHandle);
            $page = $area->getAreaCollectionObject();
            $subArea->load($page);
            $subArea->setSubAreaBlockObject($block);
            $subArea->display($page);
            
            if (!$this->instance->getInstance()->areaAreasComputed()) {
           
                $app = Facade::getFacadeApplication();
                $entityManager = $app->make(EntityManager::class);
                
                $query = $entityManager->createQueryBuilder()
                    ->delete(InstanceArea::class, 'i')
                    ->where('i.instance = :instanceID')
                    ->andWhere('i.containerAreaName = :areaHandle');
                $query->setParameter('instanceID', $this->instance->getInstance()->getContainerInstanceID());
                $query->setParameter('areaHandle', $this->areaHandle);
                $query->getQuery()->execute();
                
                $instanceArea = new InstanceArea();
                $instanceArea->setContainerAreaName($this->areaHandle);
                $instanceArea->setAreaID($subArea->getAreaID());
                $instanceArea->setInstance($this->instance->getInstance());
                $entityManager->persist($instanceArea);
                $entityManager->flush();                
            }
        }
    }

}
