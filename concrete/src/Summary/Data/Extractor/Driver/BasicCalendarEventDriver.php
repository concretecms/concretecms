<?php
namespace Concrete\Core\Summary\Data\Extractor\Driver;

use Carbon\Carbon;
use Concrete\Core\Calendar\Event\Formatter\LinkFormatterInterface;
use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Entity\Calendar\CalendarEvent;
use Concrete\Core\Entity\Calendar\CalendarEventVersionOccurrence;
use Concrete\Core\Summary\Category\CategoryMemberInterface;
use Concrete\Core\Summary\Data\Collection;
use Concrete\Core\Summary\Data\Extractor\Driver\Traits\GetCategoriesTrait;
use Concrete\Core\Summary\Data\Extractor\Driver\Traits\GetThumbnailTrait;
use Concrete\Core\Summary\Data\Field\AuthorDataFieldData;
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
        return $mixed instanceof CalendarEvent || $mixed instanceof CalendarEventVersionOccurrence;
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
     * @param $mixed CalendarEvent|CalendarEventVersionOccurrence
     * @return Collection
     */
    public function extractData(CategoryMemberInterface $mixed): Collection
    {
        $collection = new Collection();
        if ($mixed instanceof CalendarEvent) {
            $version = $mixed->getApprovedVersion();
            $event = $mixed;
        } else {
            $version = $mixed->getVersion();
            $event = $version->getEvent();
        }
        if ($version) {
            $this->entityManager->refresh($version);
            $collection->addField(new DataField(FieldInterface::FIELD_TITLE, $version->getName()));
            $link = $this->linkFormatter->getEventFrontendViewLink($event);
            if ($link) {
                $collection->addField(new DataField(FieldInterface::FIELD_LINK, $link));
            }
            $description = $version->getDescription();
            if ($description) {
                $collection->addField(new DataField(FieldInterface::FIELD_DESCRIPTION, $description));
            }

            $author = $version->getAuthor();
            if ($author) {
                $collection->addField(new DataField(FieldInterface::FIELD_AUTHOR, new AuthorDataFieldData($author->getUserInfoObject())));
            }

            $collection->addField(new DataField(FieldInterface::FIELD_LINK, new LazyEventOccurrenceLinkDataFieldData()));
            $collection->addField(new DataField(FieldInterface::FIELD_DATE, new LazyEventOccurrenceStartDatetimeDataFieldData()));
            $collection->addField(new DataField(FieldInterface::FIELD_DATE_START, new LazyEventOccurrenceStartDatetimeDataFieldData()));
            $collection->addField(new DataField(FieldInterface::FIELD_DATE_END, new LazyEventOccurrenceEndDatetimeDataFieldData()));

            $thumbnail = $this->getThumbnailDataField($event);
            if ($thumbnail) {
                $collection->addField($thumbnail);
            }
            $categories = $this->getCategoriesDataField($event);
            if ($categories) {
                $collection->addField($categories);
            }
        }
        return $collection;
    }

}
