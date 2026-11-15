<?php

declare(strict_types=1);

namespace Concrete\Core\File\Search\Field\Field;

use Concrete\Core\File\FileList;
use Concrete\Core\File\FolderItemList;
use Concrete\Core\Search\Field\AbstractField;
use Concrete\Core\Search\ItemList\ItemList;

defined('C5_EXECUTE') or die('Access Denied.');

class NoFileSetField extends AbstractField
{
    public function getKey()
    {
        return 'no_file_set';
    }

    public function getDisplayName()
    {
        return t('Files in No Sets');
    }

    /**
     * @param FileList|FolderItemList $list
     */
    public function filterList(ItemList $list)
    {
        $list->filterByNoSet();
    }
}
