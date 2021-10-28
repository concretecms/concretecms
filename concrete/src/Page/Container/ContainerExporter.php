<?php

namespace Concrete\Core\Page\Container;

use Concrete\Core\Area\Area;
use Concrete\Core\Entity\Page\Container\Instance;
use Concrete\Core\Export\Item\ItemInterface;
use Concrete\Core\Page\Page;

class ContainerExporter implements ItemInterface
{

    /**
     * @var Page 
     */
    protected $page;
    
    public function __construct(Page $page)
    {
        $this->page = $page;
    }

    /**
     * @param Instance $instance
     * @param \SimpleXMLElement $element
     */
    public function export($instance, \SimpleXMLElement $element)
    {
        $container = $instance->getContainer();
        if ($container) {
            $containerNode = $element->addChild('container');
            $containerNode->addAttribute('handle', $container->getContainerHandle());
            
            // Retrieve all the areas within this container.
            $instanceAreas = $instance->getInstanceAreas();
            foreach($instanceAreas as $instanceArea) {
                $arHandle = Area::getAreaHandleFromID($instanceArea->getAreaID());
                if ($arHandle) {
                    $containerAreaNode = $containerNode->addChild('containerarea');
                    $containerAreaNode->addAttribute('name', $instanceArea->getContainerAreaName());
                    $area = Area::get($this->page, $arHandle);
                    if ($area) {
                        $area->export($containerAreaNode, $this->page);
                    }
                }
            }
        }
        
        
    }
}
