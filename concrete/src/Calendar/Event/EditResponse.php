<?php
namespace Concrete\Core\Calendar\Event;

class EditResponse extends \Concrete\Core\Application\EditResponse
{
    protected $occurrences = array();
    protected $eventVersion;

    /**
     * @return mixed
     */
    public function getEventVersion()
    {
        return $this->eventVersion;
    }

    /**
     * @param mixed $eventVersion
     */
    public function setEventVersion($eventVersion)
    {
        $this->eventVersion = $eventVersion;
    }

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
