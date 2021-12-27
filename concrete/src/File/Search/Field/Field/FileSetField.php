<?php

namespace Concrete\Core\File\Search\Field\Field;

use Concrete\Core\File\FileList;
use Concrete\Core\File\Set\Set;
use Concrete\Core\Search\Field\AbstractField;
use Concrete\Core\Search\ItemList\ItemList;
use Concrete\Core\Support\Facade\Application;
use Concrete\Core\User\User;

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
        $ids = $this->getData('fsID');
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
        $app = Application::getFacadeApplication();
        $form = $app->make('helper/form');
        $sets = [];
        $u = $app->make(User::class);
        $fileSets = Set::getMySets($u);
        foreach($fileSets as $set) {
            $sets[$set->getFileSetID()] = $set->getFileSetName();
        }

        return $form->selectMultiple('fsID', $sets, $this->getData('fsID'), ['class' => 'ccm-enhanced-select']);
    }
}
