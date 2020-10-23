<?php
namespace Concrete\Core\User\Search\Field\Field;

use Concrete\Core\Database\Connection\Connection;
use Concrete\Core\Form\Service\Form;
use Concrete\Core\Search\Field\AbstractField;
use Concrete\Core\Search\ItemList\ItemList;
use Concrete\Core\Support\Facade\Application;
use Concrete\Core\User\UserList;

class HomeFolderField extends AbstractField
{

    protected $requestVariables = [
        'homeFolder',
    ];

    public function getKey()
    {
        return 'home_folder';
    }

    public function getDisplayName()
    {
        return t('Home Folder');
    }

    private function getFolderList()
    {
        $folderList = [];

        $app = Application::getFacadeApplication();
        /** @var Connection $db */
        $db = $app->make(Connection::class);

        // fetch all folders from database
        $rows = $db->fetchAll("SELECT tn.treeNodeId, tn.treeNodeName FROM TreeNodes AS tn LEFT JOIN TreeNodeTypes AS tnt ON (tn.treeNodeTypeID = tnt.treeNodeTypeID) WHERE tnt.treeNodeTypeHandle = 'file_folder' AND tn.treeNodeName != ''");

        foreach ($rows as $row) {
            $folderList[$row["treeNodeId"]] = $row["treeNodeName"];
        }

        return $folderList;
    }

    public function renderSearchField()
    {
        $app = Application::getFacadeApplication();
        /** @var Form $form */
        $form = $app->make(Form::class);
        $folderList = ['' => t("** None") ] + $this->getFolderList();
        return $form->select('home_folder', $folderList, ['style' => 'vertical-align: middle']);
    }

    /**
     * @param UserList $list
     * @param $request
     */
    public function filterList(ItemList $list)
    {
        $list->filterByHomeFolderID($this->data['home_folder']);
    }



}
