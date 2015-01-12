<?php
namespace Concrete\Core\Calendar\Event;

class EditResponse extends \Concrete\Core\Application\EditResponse
{

    protected $occurrences = array();

    public function setOccurrences($occurrences)
    {
        $this->occurrences = $occurrences;
    }

    public function getJSONObject()
    {
        $o = parent::getBaseJSONObject();
        foreach ($this->occurrences as $occurrence) {
            $o->occurrences[] = $occurrence->getJSONObject();
        }
        return $o;
    }

}
