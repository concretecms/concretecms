<?php
namespace Concrete\Core\Feature\Detail;

use Concrete\Core\Foundation\ConcreteObject;
use Concrete\Core\Feature\Assignment\Assignment as FeatureAssignment;

class Detail extends ConcreteObject
{
    protected $item;

    public function __construct($mixed)
    {
        $this->item = $mixed;
    }

    public function getValue()
    {
        return $this->item;
    }

    public function __toString()
    {
        $r = $this->getValue();
        if ($r) {
            return $r;
        } else {
            return '';
        }
    }

    public function getGatheringItemSuggestedSlotWidth()
    {
        return 0;
    }

    public function getGatheringItemSuggestedSlotHeight()
    {
        return 0;
    }

    public function handleFeatureAssignment(FeatureAssignment $fa)
    {
        return false;
    }

    public function deleteFeatureAssignment(FeatureAssignment $fa)
    {
        return false;
    }
}
