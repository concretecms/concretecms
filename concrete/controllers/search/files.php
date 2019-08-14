<?php
namespace Concrete\Controller\Search;

use Concrete\Controller\Dialog\Search\AdvancedSearch;
use Concrete\Core\Entity\Search\Query;
use Concrete\Core\Entity\Search\SavedSearch;
use Concrete\Core\File\Filesystem;
use Concrete\Core\File\Search\ColumnSet\DefaultSet;
use Concrete\Core\Search\Field\Field\KeywordsField;
use Concrete\Core\Search\Field\ManagerFactory;
use Concrete\Core\Search\Result\Result;
use Concrete\Core\Tree\Node\Type\SearchPreset;
use Symfony\Component\HttpFoundation\JsonResponse;

class Files extends Standard
{

    /**
     * @since 8.0.0
     */
    protected function getAdvancedSearchDialogController()
    {
        return $this->app->make('\Concrete\Controller\Dialog\File\AdvancedSearch');
    }

    /**
     * @since 8.0.0
     */
    protected function getSavedSearchPreset($presetID)
    {
        $em = \Database::connection()->getEntityManager();
        $preset = $em->find('Concrete\Core\Entity\Search\SavedFileSearch', $presetID);
        return $preset;
    }

    /**
     * @since 8.0.0
     */
    protected function getDefaultResetSearchResultObject()
    {
        $search = $this->app->build(FileFolder::class);
        $search->search();
        $result = $search->getSearchResultObject();
        return $result;
    }

    /**
     * @since 8.0.0
     */
    protected function onAfterSearchPreset(Result $result, SavedSearch $preset)
    {
        $filesystem = new Filesystem();
        $root = $filesystem->getRootFolder();
        $breadcrumb = [];
        $breadcrumb[] = [
            'active' => false,
            'name' => $root->getTreeNodeDisplayName(),
            'folder' => $root->getTreeNodeID(),
            'menu' => $root->getTreeNodeMenu(),
            'url' => (string) \URL::to('/ccm/system/file/folder/contents'),
        ];

        $node = SearchPreset::getNodeBySavedSearchID($preset->getID());

        $breadcrumb[] = [
            'active' => true,
            'name' => $node->getTreeNodeDisplayName(),
            'folder' => $node->getTreeNodeID(),
            'menu' => $node->getTreeNodeMenu(),
            'url' => false
        ];

        $result->setBreadcrumb($breadcrumb);
        return $result;
    }

    /**
     * @since 8.0.0
     */
    protected function getBasicSearchFieldsFromRequest()
    {
        $fields = parent::getBasicSearchFieldsFromRequest();
        $keywords = htmlentities($this->request->get('fKeywords'), ENT_QUOTES, APP_CHARSET);
        if ($keywords) {
            $fields[] = new KeywordsField($keywords);
        }
        return $fields;
    }

    /**
     * @since 8.0.0
     */
    protected function canAccess()
    {
        $cp = \FilePermissions::getGlobal();
        if ($cp->canSearchFiles()) {
            return true;
        }
        return false;
    }


}
