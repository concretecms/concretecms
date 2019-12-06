<?php
namespace Concrete\Core\Board\Instance\Slot\Content\Filterer;

use Concrete\Core\Board\Instance\Slot\Content\SummaryObject;

class SummaryObjectFilterer implements FiltererInterface
{
    
    protected $slots = [];
    
    public function registerSlot(int $slot, array $templateHandles)
    {
        $this->slots[$slot] = $templateHandles;
    }

    /**
     * @inheritdoc
     */
    public function findContentObjectsForSlot(array $objects, int $slot): array
    {
        $return = [];
        foreach($objects as $object) {
            if ($object instanceof SummaryObject) {
                $summaryObject = $object->getSummaryObject();
                $template = $summaryObject->getTemplate();
                $templateHandles = $this->slots[$slot];
                if (in_array($template->getHandle(), $templateHandles)) {
                    $return[] = $object;
                }
            }
        }
        return $return;
    }


}
