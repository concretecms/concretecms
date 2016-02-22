<?php
namespace Concrete\Core\Page\Type;

use Concrete\Core\Page\Event as PageEvent;

class Event extends PageEvent
{
    protected $type;

    public function setPageType(Type $type)
    {
        $this->type = $type;
    }

    public function getPageTypeObject()
    {
        return $this->type;
    }
}
