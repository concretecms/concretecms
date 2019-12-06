<?php
namespace Concrete\Core\Summary\Data\Extractor\Driver;

use Concrete\Core\Page\Page;
use Concrete\Core\Summary\Category\CategoryMemberInterface;
use Concrete\Core\Summary\Data\Collection;
use Concrete\Core\Summary\Data\Field\DataField;
use Concrete\Core\Summary\Data\Field\FieldInterface;

class BasicPageDriver implements DriverInterface
{
    public function getCategory()
    {
        return 'page';
    }
    
    public function isValidForObject($mixed): bool
    {
        return $mixed instanceof Page;
    }

    /**
     * @param $mixed Page
     * @return Collection
     */
    public function extractData(CategoryMemberInterface $mixed): Collection
    {
        $collection = new Collection();
        $collection->addField(new DataField(FieldInterface::FIELD_TITLE, $mixed->getCollectionName()));
        $collection->addField(new DataField(FieldInterface::FIELD_LINK, $mixed->getCollectionLink()));
        $collection->addField(new DataField(FieldInterface::FIELD_DATE, $mixed->getCollectionDatePublicObject()->getTimestamp()));
        $description = $mixed->getCollectionDescription();
        if ($description) {
            $collection->addField(new DataField(FieldInterface::FIELD_DESCRIPTION, $description));
        }
        return $collection;
    }

}
