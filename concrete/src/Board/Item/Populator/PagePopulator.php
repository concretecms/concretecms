<?php
namespace Concrete\Core\Board\Item\Populator;

use Concrete\Core\Entity\Board\Board;
use Concrete\Core\Entity\Board\DataSource\Configuration\Configuration;
use Concrete\Core\Page\Page;
use Concrete\Core\Page\PageList;

defined('C5_EXECUTE') or die("Access Denied.");

class PagePopulator extends AbstractPopulator
{
    
    public function getDataObjects(Board $board, Configuration $configuration) : array 
    {
        $list = new PageList();
        $query = $configuration->getQuery();
        $list->ignorePermissions();
        if ($query) {
            foreach($query->getFields() as $field) {
                $field->filterList($list);
            }
        }
        if ($board->getDateLastRefreshed()) {
            $filterDate = date('Y-m-d H:i:s', $board->getDateLastRefreshed());
            $list->filterByPublicDate($filterDate, '>');
        }

        $list->setItemsPerPage(100);
        return $list->getResults();
    }
    
    /**
     * @param Page $mixed
     * @return int
     */
    public function getObjectRelevantDate($mixed): int
    {
        return $mixed->getCollectionDatePublicObject()->getTimestamp();
    }

    /**
     * @param Page $mixed
     * @return null|string
     */
    public function getObjectName($mixed): ?string
    {
        return $mixed->getCollectionName();
    }

    /**
     * @param Page $mixed
     * @return array
     */
    public function getObjectCategories($mixed): array
    {
        $categories = [];
        $attributes = $mixed->getSetCollectionAttributes();
        foreach($attributes as $key) {
            if ($key->getAttributeType()->getAttributeTypeHandle() == 'topics') {
                $topics = $mixed->getAttribute($key);
                foreach($topics as $topic) {
                    $categories[] = $topic;
                }

            }
        }
        return $categories;
    }
    
    public function getObjectTags($mixed): array
    {
        return [];
    }
}
