<?php
namespace Concrete\Core\Page\Search\Field\Field;

use Concrete\Core\Block\BlockType\BlockType;
use Concrete\Core\Block\BlockType\BlockTypeList;
use Concrete\Core\File\FileList;
use Concrete\Core\Search\Field\AbstractField;
use Concrete\Core\Search\ItemList\ItemList;

class ContainsBlockTypeField extends AbstractField
{

    protected $requestVariables = [
        'btID'
    ];

    public function getKey()
    {
        return 'contains_block_type';
    }

    public function getDisplayName()
    {
        return t('Contains Block Type');
    }

    /**
     * @param FileList $list
     * @param $request
     */
    public function filterList(ItemList $list)
    {
        $bt = BlockType::getByID($this->data['btID']);
        $list->filterByBlockType($bt);
    }

    public function renderSearchField()
    {
        $form = \Core::make('helper/form');
        $list = new BlockTypeList();
        $html = $form->select('btID', array_reduce(
            $list->get(), function ($types, $type) {
            $types[$type->getBlockTypeID()] = $type->getBlockTypeName();

            return $types;
        }
        ), $this->data['btID']);
        return $html;
    }


}
