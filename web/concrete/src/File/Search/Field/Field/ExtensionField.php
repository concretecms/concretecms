<?php
namespace Concrete\Core\File\Search\Field\Field;

use Concrete\Core\File\FileList;
use Concrete\Core\File\Type\Type;
use Concrete\Core\Search\Field\AbstractField;
use Concrete\Core\Search\Field\FieldInterface;
use Concrete\Core\Search\ItemList\ItemList;

class ExtensionField extends AbstractField
{

    public function getKey()
    {
        return 'extension';
    }

    public function getDisplayName()
    {
        return t('Extension');
    }

    /**
     * @param FileList $list
     * @param $request
     */
    public function filterList(ItemList $list, $request)
    {
        $extension = $request['extension'];
        $list->filterByExtension($extension);
    }

    public function renderSearchField()
    {
        $form = \Core::make('helper/form');
        $ext1 = Type::getUsedExtensionList();
        $extensions = array();
        foreach ($ext1 as $value) {
            $extensions[$value] = $value;
        }
        return $form->select('extension', $extensions, $searchRequest['extensions']);
    }


}
