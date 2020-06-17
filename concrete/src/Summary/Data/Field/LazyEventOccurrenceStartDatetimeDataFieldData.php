<?php
namespace Concrete\Core\Summary\Data\Field;

use Carbon\Carbon;
use Concrete\Core\Entity\Calendar\CalendarEventVersionOccurrence;
use Concrete\Core\Summary\Category\CategoryMemberInterface;

class LazyEventOccurrenceStartDatetimeDataFieldData extends AbstractLazyDataFieldData
{

    /**
     * @param CalendarEventVersionOccurrence $categoryMember
     * @return DataFieldDataInterface
     */
    public function loadDataFieldDataFromCategoryMember(CategoryMemberInterface $categoryMember): DataFieldDataInterface
    {
        $occurrence = $categoryMember->getOccurrence();
        $start = Carbon::createFromTimestamp($occurrence->getStart(), $categoryMember->getEvent()->getCalendar()->getTimezone());
        return new DatetimeDataFieldData($start);
    }


}
