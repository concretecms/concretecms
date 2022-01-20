<?php

namespace Concrete\Core\User\Group\Search\Field\Field;

use Concrete\Core\Search\Field\AbstractField;
use Concrete\Core\Search\ItemList\ItemList;
use Concrete\Core\Tree\Node\Node;
use Concrete\Core\Tree\Node\Type\Group;
use Concrete\Core\Tree\Node\Type\GroupFolder;
use Concrete\Core\View\View;

class FolderField extends AbstractField
{

    /**
     * FolderField constructor.
     * @param GroupFolder|Node $folder
     * @param bool $searchSubFolders
     */
    public function __construct(Node $folder = null, $searchSubFolders = false)
    {
        if ($folder) {
            $this->data['folderID'] = $folder->getTreeNodeID();
            $this->data['searchSubFolder'] = $searchSubFolders;
            $this->isLoaded = true;
        }
    }

    protected $requestVariables = [
        'folderID',
    ];

    public function getKey()
    {
        return 'folder';
    }

    public function getDisplayName()
    {
        return t('Folder');
    }

    /**
     * @param \Concrete\Core\User\Group\FolderItemList $list
     * @param $request
     */
    public function filterList(ItemList $list)
    {
        $folderID = $this->getData('folderID');
        if ($folderID) {
            $folder = Node::getByID($folderID);
            if ($folder && $folder instanceof GroupFolder) {
                $list->filterByParentFolder($folder);
                if ($this->getData('searchSubFolder')) {
                    $list->enableSubFolderSearch();
                }
            } elseif ($folder && $folder instanceof Group) {
                // Add support for legacy folders
                $group = $folder->getTreeNodeGroupObject();
                if (count($group->getChildGroups()) > 0) {
                    $list->filterByParentFolder($folder);
                    if ($this->getData('searchSubFolder')) {
                        $list->enableSubFolderSearch();
                    }
                }
            }
        }
    }

    public function renderSearchField()
    {
        ob_start();
        /** @noinspection PhpUnhandledExceptionInspection */
        View::element('groups/folder_selector', ['rootTreeNodeID' => $this->getData('folderID'), 'inputName' => 'folderID']);
        $html = ob_get_contents();
        ob_end_clean();
        return $html;
    }


}
