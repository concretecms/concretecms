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
            $message = t(/* i18n: %1$s is a path, %2$s is a number */'Custom settings detected for page: %1$s (ID: %2$s).', $page->getCollectionPath(), $page->getCollectionID());
            $cachingDescription = $page->getCollectionFullPageCaching() == 1 ? t('Caching enabled') : t('Caching disabled');
            $lifetime = $page->getCollectionFullPageCachingLifetime();
            $lifetimeValue = $page->getCollectionFullPageCachingLifetimeValue();
            switch ($lifetime) {
                case 'default':
                    $lifetimeDescription = t('Default lifetime (value: %s)', $lifetimeValue);
                    break;
                case 'forever':
                    $lifetimeDescription = t('Unlimited lifetime (value: %s)', $lifetimeValue);
                    break;
                case 'custom':
                    $lifetimeDescription = t('Custom lifetime (value: %s)', $lifetimeValue);
                    break;
                default: // '0', 0, other...
                    $lifetimeDescription = t('Default lifetime (value: %s)', $lifetimeValue);
                    break;
            }
            $message .= " {$cachingDescription} - {$lifetimeDescription}";
            $report->info($message);
        }
    }

}
