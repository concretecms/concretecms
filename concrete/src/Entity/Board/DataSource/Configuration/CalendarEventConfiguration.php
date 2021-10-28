<?php

namespace Concrete\Core\Entity\Board\DataSource\Configuration;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="BoardConfiguredDataSourceConfigurationCalendarEvent")
 */
class CalendarEventConfiguration extends Configuration
{

    /**
     * @ORM\ManyToOne(targetEntity="\Concrete\Core\Entity\Calendar\Calendar")
     * @ORM\JoinColumn(name="caID", referencedColumnName="caID")
     */
    protected $calendar;

    /** @ORM\Embedded(class = "\Concrete\Core\Entity\Search\Query") */
    protected $query;

    /**
     * @ORM\Column(type="integer", options={"unsigned": true})
     */
    protected $maxOccurrencesOfSameEvent = 0;

    /**
     * @return \Concrete\Core\Entity\Search\Query
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
     * @param mixed $query
     */
    public function setQuery($query): void
    {
        $this->query = $query;
    }

    /**
     * @return mixed
     */
    public function getCalendar()
    {
        return $this->calendar;
    }

    /**
     * @param mixed $calendar
     */
    public function setCalendar($calendar): void
    {
        $this->calendar = $calendar;
    }

    /**
     * @return int
     */
    public function getMaxOccurrencesOfSameEvent(): int
    {
        return $this->maxOccurrencesOfSameEvent;
    }

    /**
     * @param int $maxOccurrencesOfSameEvent
     */
    public function setMaxOccurrencesOfSameEvent(int $maxOccurrencesOfSameEvent): void
    {
        $this->maxOccurrencesOfSameEvent = $maxOccurrencesOfSameEvent;
    }

    public function export(\SimpleXMLElement $element)
    {
        $element->addAttribute('max-occurrences-of-event', $this->getMaxOccurrencesOfSameEvent());
        $element->addAttribute('calendar', $this->getCalendar()->getName());

        if ($this->query) {
            $fields = $this->query->getFields();
            if (count($fields)) {
                $fieldsNode = $element->addChild('fields');
                foreach ($fields as $field) {
                    $field->export($fieldsNode);
                }
            }
        }

    }



}
