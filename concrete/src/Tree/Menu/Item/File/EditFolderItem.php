<?php

namespace Concrete\Core\Tree\Menu\Item\File;

use Concrete\Core\Tree\Menu\Item\Category\EditCategoryItem;

/**
 * @since 8.0.0
 */
class EditFolderItem extends EditCategoryItem
{

    public function getDialogTitle()
    {
        return t('Edit Folder');
    }

    public function getItemName()
    {
        return t('Edit Folder');
    }


}