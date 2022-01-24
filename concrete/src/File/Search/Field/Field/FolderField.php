<?php
namespace Concrete\Core\File\Search\Field\Field;

use Concrete\Core\File\FileList;
use Concrete\Core\File\FolderItemList;
use Concrete\Core\File\Set\Set;
use Concrete\Core\File\Type\Type;
use Concrete\Core\Search\Field\AbstractField;
use Concrete\Core\Search\Field\FieldInterface;
use Concrete\Core\Search\ItemList\ItemList;
use Concrete\Core\Support\Facade\Application;
use Concrete\Core\Tree\Node\Node;
use Concrete\Core\Tree\Node\Type\FileFolder;
use Concrete\Core\User\User;

class FolderField extends AbstractField
{

    public function __construct(FileFolder $folder = null, $searchSubFolders = false)
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
     * @param FolderItemList $list
     * @param $request
     */
    public function filterList(ItemList $list)
    {
        $folderID = $this->getData('folderID');
        if ($folderID) {
            $folder = Node::getByID($folderID);
            if ($folder && $folder instanceof FileFolder) {
                $list->filterByParentFolder($folder);
                if ($this->getData('searchSubFolder')) {
                    $list->enableSubFolderSearch();
                }
            }
        }
    }

    public function renderSearchField()
    {
        $selector = new \Concrete\Core\Form\Service\Widget\FileFolderSelector();
        return $selector->selectFileFolder('folderID', $this->getData('folderID'), false);
    }


}
