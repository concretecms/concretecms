<?php

namespace Concrete\Core\Board\Instance\Item\Populator;

use Concrete\Core\Board\Instance\Item\Data\DataInterface;
use Concrete\Core\Board\Instance\Item\Data\PageData;
use Concrete\Core\Entity\Board\DataSource\ConfiguredDataSource;
use Concrete\Core\Entity\Board\Instance;
use Concrete\Core\Entity\File\File;
use Concrete\Core\Page\Page;
use Concrete\Core\Page\PageList;
use Concrete\Core\Page\Search\Field\Field\SiteField;

defined('C5_EXECUTE') or die("Access Denied.");

class PagePopulator extends AbstractPopulator
{

    public function getDataObjects(Instance $instance, ConfiguredDataSource $dataSource): array
    {
        $configuration = $dataSource->getConfiguration();
        $list = new PageList();
        $query = $configuration->getQuery();
        $list->ignorePermissions();
        $containsSitefield = false;
        if ($query) {
            foreach ($query->getFields() as $field) {
                if ($field instanceof SiteField) {
                    // If we have a site field we handle it manually here, because we have to use the instance's
                    // site.
                    $containsSitefield = true;
                    if ($field->isSetToCurrent()) {
                        // we filter by the instance's site
                        $list->setSiteTreeObject($instance->getSite()->getSiteTreeObject());
                    } else {
                        if ($field->isSetToAll()) {
                            $list->setSiteTreeToAll();
                        }
                    }
                } else {
                    $field->filterList($list);
                }
            }
        }

        if (!$containsSitefield) {
            // We filter by the instance's current site.
            $list->setSiteTreeObject($instance->getSite()->getSiteTreeObject());
        }

        $future = $this->getPopulationDayIntervalFutureDatetime($dataSource, $instance);
        $past = $this->getPopulationDayIntervalPastDatetime($dataSource, $instance);
        $list->filterByPublicDate($future->format('Y-m-d- H:i:s'), '<=');
        $list->filterByPublicDate($past->format('Y-m-d- H:i:s'), '>=');

        $pagination = $list->getPagination();
        $pagination->setMaxPerPage(1000);
        return $pagination->getCurrentPageResults();
        /* this is old logic, remove once we're sure this works
        if ($mode == PopulatorInterface::RETRIEVE_FIRST_RUN) {
            // the first time we run we start today and go into the past.
            $list->sortByPublicDateDescending();
        } else {
            $list->sortByPublicDate();
            $list->filterByPublicDate(date('Y-m-d H:i:s', $instance->getDateDataPoolLastUpdated()), '>');
        }

        $pagination = $list->getPagination();
        $pagination->setMaxPerPage(100);
        return $pagination->getCurrentPageResults();
        */
    }

    /**
     * @param Page $mixed
     * @return int
     */
    public function getObjectRelevantDate($mixed): int
    {
        return $mixed->getCollectionDatePublicObject()->getTimestamp();
    }

    public function getObjectUniqueItemId($mixed): ?string
    {
        return $mixed->getCollectionID();
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

    public function getObjectRelevantThumbnail($mixed): ?File
    {
        $thumbnail = $mixed->getAttribute('thumbnail');
        return $thumbnail;
    }

    /**
     * @param Page $mixed
     * @return array
     */
    public function getObjectCategories($mixed): array
    {
        $categories = [];
        $attributes = $mixed->getSetCollectionAttributes();
        foreach ($attributes as $key) {
            if ($key->getAttributeType()->getAttributeTypeHandle() == 'topics') {
                $topics = $mixed->getAttribute($key);
                if ($topics) {
                    foreach ($topics as $topic) {
                        $categories[] = $topic;
                    }
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
