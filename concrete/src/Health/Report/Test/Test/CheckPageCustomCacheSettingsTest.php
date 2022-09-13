<?php
namespace Concrete\Core\Health\Report\Test\Test;

use Concrete\Core\Health\Report\Runner;
use Concrete\Core\Page\Page;

class CheckPageCustomCacheSettingsTest extends AbstractPageTest
{

    public function run(Runner $report): void
    {
        $page = Page::getByID($this->getPageId());
        if ($page->getCollectionFullPageCaching() !== -1) {
            $enabled = $page->getCollectionFullPageCaching() == 1 ? t('enabled') : 'disabled';
            $lifetime = $page->getCollectionFullPageCachingLifetime();
            if ($lifetime === '0' || $lifetime === 0) {
                $lifetime = t('default');
            }
            $lifetimeValue = $page->getCollectionFullPageCachingLifetimeValue();
            $report->info(t('Custom settings detected for page: %s (ID: %s). Caching %s - lifetime %s (value: %s)',
                $page->getCollectionPath(), $page->getCollectionID(), $enabled, $lifetime, $lifetimeValue
            ));
        }
    }

}
