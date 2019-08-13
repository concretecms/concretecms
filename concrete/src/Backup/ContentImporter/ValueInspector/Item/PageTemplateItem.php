<?php
namespace Concrete\Core\Backup\ContentImporter\ValueInspector\Item;

use Concrete\Core\Page\Template;

/**
 * @since 5.7.5.3
 */
class PageTemplateItem extends AbstractItem
{
    public function getDisplayName()
    {
        return t('Page Template');
    }

    public function getContentObject()
    {
        return Template::getByHandle($this->getReference());
    }

    public function getFieldValue()
    {
        if ($o = $this->getContentObject()) {
            return $o->getPageTemplateID();
        }
    }
}
