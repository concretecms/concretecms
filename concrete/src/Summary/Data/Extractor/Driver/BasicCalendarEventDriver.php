<?php
namespace Concrete\Core\Summary\Data\Extractor\Driver;

use Concrete\Core\Calendar\Event\Formatter\LinkFormatter;
use Concrete\Core\Entity\Calendar\CalendarEvent;
use Concrete\Core\Summary\Category\CategoryMemberInterface;
use Concrete\Core\Summary\Data\Collection;
use Concrete\Core\Summary\Data\Field\DataField;
use Concrete\Core\Summary\Data\Field\FieldInterface;

class BasicCalendarEventDriver implements DriverInterface
{

    /**
     * @var LinkFormatter 
     */
    protected $linkFormatter;
    
    public function __construct(LinkFormatter $linkFormatter)
    {
        $this->linkFormatter = $linkFormatter;
    }

    public function getCategory()
    {
        return 'calendar_event';
    }

    public function isValidForObject($mixed): bool
    {
        return $mixed instanceof CalendarEvent;
    }

    /**
     * @param $mixed CalendarEvent
     * @return Collection
     */
    public function extractData(CategoryMemberInterface $mixed): Collection
    {
        $collection = new Collection();
        $version = $mixed->getApprovedVersion();
        if ($version) {
            $collection->addField(new DataField(FieldInterface::FIELD_TITLE, $version->getName()));
            $link = $this->linkFormatter->getEventFrontendViewLink($mixed);
            if ($link) {
                $collection->addField(new DataField(FieldInterface::FIELD_LINK, $link));
            }
            $description = $version->getDescription();
            if ($description) {
                $collection->addField(new DataField(FieldInterface::FIELD_DESCRIPTION, $description));
            }
            $occurrence = $version->getOccurrences()[0];
            if ($occurrence) {
                $collection->addField(new DataField(FieldInterface::FIELD_DATE, $occurrence->getStart()));
            }
        }
        return $collection;
    }

}
