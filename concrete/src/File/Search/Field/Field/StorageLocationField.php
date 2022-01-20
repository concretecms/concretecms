<?php

namespace Concrete\Core\File\Search\Field\Field;

use Concrete\Core\Support\Facade\Application;
use Concrete\Core\File\FileList;
use Concrete\Core\File\StorageLocation\StorageLocationFactory;
use Concrete\Core\Search\Field\AbstractField;
use Concrete\Core\Search\ItemList\ItemList;

class StorageLocationField extends AbstractField
{
    /**
     * @var array
     */
    protected $requestVariables = [
        'fslID',
    ];

    /**
     * @return string
     */
    public function getKey()
    {
        return 'fslID';
    }

    /**
     * @return string
     */
    public function getDisplayName()
    {
        return t('Storage Location');
    }

    /**
     * @param FileList $list
     * @param $request
     */
    public function filterList(ItemList $list)
    {
        $storageLocation = $this->getData('fslID');
        $list->filterByStorageLocationID($storageLocation);
    }

    /**
     * @return string
     */
    public function renderSearchField()
    {
        $app = Application::getFacadeApplication();
        $form = $app->make('helper/form');
        $locations = $app->make(StorageLocationFactory::class)->fetchList();
        $storageLocations = [];
        foreach ($locations as $location) {
            $locationID = $location->getID();
            $locationName = $location->getDisplayName('text');
            if (!empty($locationID) && !empty($locationName)) {
                $storageLocations[$locationID] = $locationName;
            }
        }

        return $form->select('fslID', $storageLocations, $this->getData('fslID'));
    }
}
