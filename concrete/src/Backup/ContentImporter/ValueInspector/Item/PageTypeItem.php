<?php
namespace Concrete\Core\Backup\ContentImporter\ValueInspector\Item;

use Concrete\Core\Page\Type\Type;

/**
 * @since 5.7.5.3
 */
class PageTypeItem extends AbstractItem
{
    public function getDisplayName()
    {
        return t('Page Type');
    }

    public function getContentObject()
    {
        return Type::getByHandle($this->getReference());
    }

    public function getFieldValue()
    {
        if ($o = $this->getContentObject()) {
            return $o->getPageTypeID();
        }
    }
}
