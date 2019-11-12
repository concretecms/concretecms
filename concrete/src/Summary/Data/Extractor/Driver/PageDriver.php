<?php
namespace Concrete\Core\Summary\Data\Extractor\Driver;

use Concrete\Core\Page\Page;
use Concrete\Core\Summary\Data\Collection;
use Concrete\Core\Summary\Data\Field\DataField;
use Concrete\Core\Summary\Data\Field\FieldInterface;

class PageDriver implements DriverInterface
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
    public function extractData($mixed): Collection
    {
        $collection = new Collection();
        $collection->addField(new DataField(FieldInterface::FIELD_TITLE, $mixed->getCollectionName()));
        $collection->addField(new DataField(FieldInterface::FIELD_LINK, $mixed->getCollectionLink()));
        $description = $mixed->getCollectionDescription();
        if ($description) {
            $collection->addField(new DataField(FieldInterface::FIELD_DESCRIPTION, $description));
        }
        return $collection;
    }

}
