<?php
namespace Concrete\Core\Summary\Data\Field;

use Carbon\Carbon;
use Concrete\Core\Calendar\Event\Formatter\LinkFormatterInterface;
use Concrete\Core\Entity\Calendar\CalendarEventVersionOccurrence;
use Concrete\Core\Summary\Category\CategoryMemberInterface;

class LazyEventOccurrenceLinkDataFieldData extends AbstractLazyDataFieldData
{

    /**
     * @param CalendarEventVersionOccurrence $categoryMember
     * @return DataFieldDataInterface
     */
    public function loadDataFieldDataFromCategoryMember(CategoryMemberInterface $categoryMember): DataFieldDataInterface
    {
        $linkFormatter = app(LinkFormatterInterface::class);
        $link = $linkFormatter->getEventOccurrenceFrontendViewLink($categoryMember);
        return new DataFieldData($link);
    }


}
