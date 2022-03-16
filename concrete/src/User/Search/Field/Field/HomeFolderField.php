<?php
namespace Concrete\Core\User\Search\Field\Field;

use Concrete\Core\Form\Service\Widget\FileFolderSelector;
use Concrete\Core\Search\Field\AbstractField;
use Concrete\Core\Search\ItemList\ItemList;
use Concrete\Core\Support\Facade\Application;
use Concrete\Core\User\UserList;

class HomeFolderField extends AbstractField
{

    protected $requestVariables = [
        'home_folder',
    ];

    public function getKey()
    {
        return 'home_folder';
    }

    public function getDisplayName()
    {
        return t('Home Folder');
    }

    public function renderSearchField()
    {
        $app = Application::getFacadeApplication();
        /** @var FileFolderSelector $fileFolderSelector */
        $fileFolderSelector = $app->make(FileFolderSelector::class);
        return $fileFolderSelector->selectFileFolder('home_folder', $this->getData('home_folder'));
    }

    /**
     * @param UserList $list
     * @param $request
     */
    public function filterList(ItemList $list)
    {
        $list->filterByHomeFolderID($this->getData('home_folder'));
    }



}
