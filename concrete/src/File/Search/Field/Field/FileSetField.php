<?php
namespace Concrete\Core\File\Search\Field\Field;

use Concrete\Core\File\FileList;
use Concrete\Core\File\Set\Set;
use Concrete\Core\File\Type\Type;
use Concrete\Core\Search\Field\AbstractField;
use Concrete\Core\Search\Field\FieldInterface;
use Concrete\Core\Search\ItemList\ItemList;

class FileSetField extends AbstractField
{

    protected $requestVariables = [
        'fsID',
    ];

    public function getKey()
    {
        return 'file_set';
    }

    public function getDisplayName()
    {
        return t('File Set');
    }

    /**
     * @param FileList $list
     * @param $request
     */
    public function filterList(ItemList $list)
    {
        $ids = $this->data['fsID'];
        if (is_array($ids)) {
            foreach($ids as $fsID) {
                $set = Set::getByID($fsID);
                if (is_object($set)) {
                    $list->filterBySet($set);
                }
            }
        }
    }

    public function renderSearchField()
    {
        $form = \Core::make('helper/form');
        $sets = array();
        $u = new \User();
        $fileSets = Set::getMySets($u);
        foreach($fileSets as $set) {
            $sets[$set->getFileSetID()] = $set->getFileSetName();
        }

        return $form->selectMultiple('fsID', $sets, $this->data['fsID'], ['class' => 'selectize-select']);
    }


}
