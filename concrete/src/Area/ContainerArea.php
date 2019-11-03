<?php
namespace Concrete\Core\Area;

use Concrete\Core\Page\Container\ContainerBlockInstance;
use Concrete\Core\Page\Page;

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
        }
    }

}
