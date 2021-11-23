<?php
namespace Concrete\Core\Summary\Data\Field;

use Concrete\Core\Calendar\Event\Formatter\LinkFormatterInterface;
use Concrete\Core\Page\Page;
use Concrete\Core\Summary\Category\CategoryMemberInterface;

class LazyPageLinkDataFieldData extends AbstractLazyDataFieldData
{

    /**
     * @param Page $categoryMember
     * @return DataFieldDataInterface
     */
    public function loadDataFieldDataFromCategoryMember(CategoryMemberInterface $categoryMember): DataFieldDataInterface
    {
        $link = $categoryMember->getCollectionLink();
        return new DataFieldData($link);
    }


}
