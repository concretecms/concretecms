<?php
namespace Concrete\Core\Api\Fractal\Transformer;

use Concrete\Core\Api\Resources;
use Concrete\Core\Entity\Calendar\Calendar;
use Concrete\Core\Entity\Calendar\CalendarEvent;
use League\Fractal\TransformerAbstract;

class CalendarEventTransformer extends TransformerAbstract
{

    protected $availableIncludes = [
        'custom_attributes',
    ];

    protected $defaultIncludes = [
        'version',
    ];

    /**
     * @param Calendar $calendar
     * @return array
     */
    public function transform(CalendarEvent $event)
    {
        $data['id'] = $event->getID();
        $data['name'] = $event->getName();
        return $data;
    }

    public function includeCustomAttributes(CalendarEvent $event)
    {
        $values = $event->getObjectAttributeCategory()->getAttributeValues($event->getSelectedVersion());
        return $this->collection($values, new AttributeValueTransformer(), Resources::RESOURCE_CUSTOM_ATTRIBUTES);
    }

    public function includeVersion(CalendarEvent $event)
    {
        $version = $event->getSelectedVersion();
        return $this->item($version, new CalendarEventVersionTransformer(), Resources::RESOURCE_CALENDAR_EVENT_VERSIONS);
    }



}
