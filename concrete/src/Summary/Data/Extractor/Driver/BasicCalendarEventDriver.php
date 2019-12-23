<?php
namespace Concrete\Core\Summary\Data\Extractor\Driver;

use Carbon\Carbon;
use Concrete\Core\Calendar\Event\Formatter\LinkFormatter;
use Concrete\Core\Entity\Calendar\CalendarEvent;
use Concrete\Core\Summary\Category\CategoryMemberInterface;
use Concrete\Core\Summary\Data\Collection;
use Concrete\Core\Summary\Data\Extractor\Driver\Traits\GetCategoriesTrait;
use Concrete\Core\Summary\Data\Extractor\Driver\Traits\GetThumbnailTrait;
use Concrete\Core\Summary\Data\Field\DataField;
use Concrete\Core\Summary\Data\Field\DatetimeDataFieldData;
use Concrete\Core\Summary\Data\Field\FieldInterface;
use Doctrine\ORM\EntityManager;

class BasicCalendarEventDriver implements DriverInterface
{

    use GetThumbnailTrait;
    use GetCategoriesTrait;
    
    /**
     * @var LinkFormatter 
     */
    protected $linkFormatter;

    /**
     * @var EntityManager
     */
    protected $entityManager;
    
    public function __construct(EntityManager $entityManager, LinkFormatter $linkFormatter)
    {
        $this->entityManager = $entityManager;
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

    public function getThumbnailAttributeKeyHandle()
    {
        return 'event_thumbnail';
    }

    public function getCategoriesAttributeKeyHandle()
    {
        return 'event_category';
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
            $this->entityManager->refresh($version);
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
                $start = Carbon::createFromTimestamp($occurrence->getStart(), $mixed->getCalendar()->getTimezone());
                $end = Carbon::createFromTimestamp($occurrence->getEnd(), $mixed->getCalendar()->getTimezone());
                $collection->addField(new DataField(FieldInterface::FIELD_DATE, new DatetimeDataFieldData($start)));
                $collection->addField(new DataField(FieldInterface::FIELD_DATE_START, new DatetimeDataFieldData($start)));
                $collection->addField(new DataField(FieldInterface::FIELD_DATE_END, new DatetimeDataFieldData($end)));
            }
            $thumbnail = $this->getThumbnailDataField($mixed);
            if ($thumbnail) {
                $collection->addField($thumbnail);
            }
            $categories = $this->getCategoriesDataField($mixed);
            if ($categories) {
                $collection->addField($categories);
            }
        }
        return $collection;
    }

}
