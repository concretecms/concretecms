<?php
namespace Concrete\Core\Summary\Data\Extractor\Driver;

use Concrete\Core\Calendar\Event\Formatter\LinkFormatter;
use Concrete\Core\Entity\Calendar\CalendarEvent;
use Concrete\Core\Summary\Category\CategoryMemberInterface;
use Concrete\Core\Summary\Data\Collection;
use Concrete\Core\Summary\Data\Field\DataField;
use Concrete\Core\Summary\Data\Field\FieldInterface;
use Concrete\Core\Summary\Data\Field\ImageDataFieldData;

class CalendarEventThumbnailDriver implements DriverInterface
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
        $thumbnail = $mixed->getAttribute('event_thumbnail');
        if ($thumbnail) {
            $collection->addField(
                new DataField(
                    FieldInterface::FIELD_THUMBNAIL,
                    new ImageDataFieldData($thumbnail)
                )
            );
        }
        return $collection;
    }

}
