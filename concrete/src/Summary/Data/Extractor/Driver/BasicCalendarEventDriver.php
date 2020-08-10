<?php
namespace Concrete\Core\Summary\Data\Extractor\Driver;

use Carbon\Carbon;
use Concrete\Core\Calendar\Event\Formatter\LinkFormatterInterface;
use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Entity\Calendar\CalendarEvent;
use Concrete\Core\Summary\Category\CategoryMemberInterface;
use Concrete\Core\Summary\Data\Collection;
use Concrete\Core\Summary\Data\Extractor\Driver\Traits\GetCategoriesTrait;
use Concrete\Core\Summary\Data\Extractor\Driver\Traits\GetThumbnailTrait;
use Concrete\Core\Summary\Data\Field\DataField;
use Concrete\Core\Summary\Data\Field\DatetimeDataFieldData;
use Concrete\Core\Summary\Data\Field\FieldInterface;
use Concrete\Core\Summary\Data\Field\LazyEventOccurrenceEndDatetimeDataFieldData;
use Concrete\Core\Summary\Data\Field\LazyEventOccurrenceLinkDataFieldData;
use Concrete\Core\Summary\Data\Field\LazyEventOccurrenceStartDatetimeDataFieldData;
use Doctrine\ORM\EntityManager;

class BasicCalendarEventDriver implements DriverInterface
{

    use GetThumbnailTrait;
    use GetCategoriesTrait;

    /**
     * @var LinkFormatterInterface
     */
    protected $linkFormatter;

    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @var Repository
     */
    protected $repository;

    public function __construct(EntityManager $entityManager, LinkFormatterInterface $linkFormatter, Repository $repository)
    {
        $this->entityManager = $entityManager;
        $this->linkFormatter = $linkFormatter;
        $this->repository = $repository;
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
        $handle = $this->repository->get('concrete.calendar.summary_thumbnail_attribute');
        if (!$handle) {
            $handle = 'event_thumbnail';
        }
        return $handle;
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

            $collection->addField(new DataField(FieldInterface::FIELD_LINK, new LazyEventOccurrenceLinkDataFieldData()));
            $collection->addField(new DataField(FieldInterface::FIELD_DATE, new LazyEventOccurrenceStartDatetimeDataFieldData()));
            $collection->addField(new DataField(FieldInterface::FIELD_DATE_START, new LazyEventOccurrenceStartDatetimeDataFieldData()));
            $collection->addField(new DataField(FieldInterface::FIELD_DATE_END, new LazyEventOccurrenceEndDatetimeDataFieldData()));

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
