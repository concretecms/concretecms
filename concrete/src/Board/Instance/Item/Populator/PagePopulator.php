<?php
namespace Concrete\Core\Board\Instance\Item\Populator;

use Concrete\Core\Board\Instance\Item\Data\DataInterface;
use Concrete\Core\Board\Instance\Item\Data\PageData;
use Concrete\Core\Entity\Board\DataSource\Configuration\Configuration;
use Concrete\Core\Entity\Board\Instance;
use Concrete\Core\Page\Page;
use Concrete\Core\Page\PageList;
use Concrete\Core\Page\Search\Field\Field\SiteField;

defined('C5_EXECUTE') or die("Access Denied.");

class PagePopulator extends AbstractPopulator
{

    public function getDataObjects(Instance $instance, Configuration $configuration) : array
    {
        $board = $instance->getBoard();
        $list = new PageList();
        $query = $configuration->getQuery();
        $list->ignorePermissions();
        $containsSitefield = false;
        if ($query) {
            foreach($query->getFields() as $field) {
                if ($field instanceof SiteField) {
                    // If we have a site field we handle it manually here, because we have to use the instance's
                    // site.
                    $containsSitefield = true;
                    if ($field->isSetToCurrent()) {
                        // we filter by the instance's site
                        $list->setSiteTreeObject($instance->getSite()->getSiteTreeObject());
                    } else if ($field->isSetToAll()) {
                        $list->setSiteTreeToAll();
                    }
                } else {
                    $field->filterList($list);
                }
            }
        }

        if (!$containsSitefield) {
            if ($board->getSite()) {
                // The board is not a shared board, so we filter by the instance's current site.
                $list->setSiteTreeObject($instance->getSite()->getSiteTreeObject());
            } else {
                $list->setSiteTreeToAll();
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
     * @return PageData
     */
    public function getObjectData($mixed): DataInterface
    {
        return new PageData($mixed);
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
