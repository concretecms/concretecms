<?php
namespace Concrete\Core\File\Search\Field\Field;

use Concrete\Core\File\FileList;
use Concrete\Core\File\Type\Type;
use Concrete\Core\Search\Field\AbstractField;
use Concrete\Core\Search\Field\FieldInterface;
use Concrete\Core\Search\ItemList\ItemList;

class TypeField extends AbstractField
{

    protected $requestVariables = ['fileType'];

    public function getKey()
    {
        return 'fileType';
    }

    public function getDisplayName()
    {
        return t('File Type');
    }

    /**
     * @param FileList $list
     * @param $request
     */
    public function filterList(ItemList $list)
    {
        $type = $this->getData('fileType');
        $list->filterByType($type);
    }

    public function renderSearchField()
    {
        $form = \Core::make('helper/form');
        $t1 = Type::getTypeList();
        $html = '';
        $types = array();
        foreach ($t1 as $value) {
            $types[$value] = Type::getGenericTypeText($value);
        }
        $html .= $form->select('fileType', $types, $this->getData('fileType'));
        return $html;
    }


}
