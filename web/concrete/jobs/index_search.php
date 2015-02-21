<?php
namespace Concrete\Job;

use Concrete\Core\Cache\Cache;
use \Job as AbstractJob;
use \Concrete\Core\Page\Search\IndexedSearch;

class IndexSearch extends AbstractJob
{

    public $jNotUninstallable = 1;

    public function getJobName()
    {
        return t("Index Search Engine - Updates");
    }

    public function getJobDescription()
    {
        return t(
            "Index the site to allow searching to work quickly and accurately. Only reindexes pages that have changed since last indexing."
        );
    }

    public function run()
    {
        Cache::disableAll();

        $is = new IndexedSearch();
        if ($_GET['force'] == 1) {
            $attributes = \CollectionAttributeKey::getList();
            $attributes = array_merge($attributes, \FileAttributeKey::getList());
            $attributes = array_merge($attributes, \UserAttributeKey::getList());
            foreach ($attributes as $ak) {
                $ak->updateSearchIndex();
            }

            $result = $is->reindexAll(true);
        } else {
            $result = $is->reindexAll();
        }

        if ($result->count == 0) {
            return t('Indexing complete. Index is up to date');
        } else if ($result->count == $is->searchBatchSize) {
            return t(
                'Index partially updated. %s pages indexed (maximum number.) Re-run this job to continue this process.',
                $result->count
            );
        } else {
            return t('Index updated.') . ' ' . t2(
                '%d page required reindexing.',
                '%d pages required reindexing.',
                $result->count,
                $result->count
            );
        }
    }
}