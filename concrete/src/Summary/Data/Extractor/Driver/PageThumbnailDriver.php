<?php
namespace Concrete\Core\Summary\Data\Extractor\Driver;

use Concrete\Core\Page\Page;
use Concrete\Core\Summary\Category\CategoryMemberInterface;
use Concrete\Core\Summary\Data\Collection;
use Concrete\Core\Summary\Data\Field\DataField;
use Concrete\Core\Summary\Data\Field\FieldInterface;
use Concrete\Core\Summary\Data\Field\ImageDataFieldData;

class PageThumbnailDriver implements DriverInterface
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
        $thumbnail = $mixed->getAttribute('thumbnail');
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
