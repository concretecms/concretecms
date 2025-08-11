<?php

namespace Concrete\Controller\Dialog\Page\Bulk;

use Concrete\Controller\Backend\UserInterface as BackendInterfaceController;
use Concrete\Core\Application\EditResponse;
use Concrete\Core\Controller\Traits\DashboardBulkUpdaterTrait;
use Concrete\Core\Page\Page;
use Concrete\Core\Permission\Checker;
use Permissions;
use Symfony\Component\HttpFoundation\JsonResponse;

class Cache extends BackendInterfaceController
{
    use DashboardBulkUpdaterTrait;
    protected $viewPath = '/dialogs/page/bulk/cache';

    protected function getObjectFromRequestId(string $id)
    {
        $page = Page::getByID($id, 'RECENT');
        if ($page && !$page->isError()) {
            return $page;
        }
        return null;
    }

    public function canPerformOperationOnObject($object): bool
    {
        $checker = new Checker($object);
        return $checker->canEditPageSpeedSettings();
    }

    public function view()
    {
        $config = $this->app->make('config');
        $fullPageCaching = -3;
        $cCacheFullPageContentOverrideLifetime = -2;
        $cCacheFullPageContentOverrideLifetimeCustomValue = -1;
        foreach ($this->items as $c) {
            $cp = new Permissions($c);
            if ($cp->canEditPageSpeedSettings()) {
                if ($c->getCollectionFullPageCaching() != $fullPageCaching && $fullPageCaching != -3) {
                    $fullPageCaching = -2;
                } else {
                    $fullPageCaching = $c->getCollectionFullPageCaching();
                }
                if ($c->getCollectionFullPageCachingLifetime(
                    ) != $cCacheFullPageContentOverrideLifetime && $cCacheFullPageContentOverrideLifetime != -2) {
                    $cCacheFullPageContentOverrideLifetime = -1;
                } else {
                    $cCacheFullPageContentOverrideLifetime = $c->getCollectionFullPageCachingLifetime();
                }
                if ($c->getCollectionFullPageCachingLifetimeCustomValue(
                    ) != $cCacheFullPageContentOverrideLifetimeCustomValue && $cCacheFullPageContentOverrideLifetimeCustomValue != -1) {
                    $cCacheFullPageContentOverrideLifetimeCustomValue = 0;
                } else {
                    $cCacheFullPageContentOverrideLifetimeCustomValue = $c->getCollectionFullPageCachingLifetimeCustomValue(
                    );
                }
            }
        }
        switch ($config->get('concrete.cache.pages')) {
            case 'blocks':
                $globalSetting = t('cache page if all blocks support it.');
                $enableCache = 1;
                break;
            case 'all':
                $globalSetting = t('enable full page cache.');
                $enableCache = 1;
                break;
            default: // false
                $globalSetting = t('disable full page cache.');
                $enableCache = 0;
                break;
        }
        switch ($this->app->make('config')->get('concrete.cache.full_page_lifetime')) {
            case 'custom':
                $custom = $this->app->make('date')->describeInterval(
                    $config->get('concrete.cache.full_page_lifetime_value') * 60
                );
                $globalSettingLifetime = t('%s minutes', $custom);
                break;
            case 'forever':
                $globalSettingLifetime = t('Until manually cleared');
                break;
            default: // "default"
                $globalSettingLifetime = $this->app->make('date')->describeInterval(
                    $config->get('concrete.cache.lifetime')
                );
                break;
        }
        $this->set('pages', $this->items);
        $this->set('fullPageCaching', $fullPageCaching);
        $this->set('cCacheFullPageContentOverrideLifetime', $cCacheFullPageContentOverrideLifetime);
        $this->set(
            'cCacheFullPageContentOverrideLifetimeCustomValue',
            $cCacheFullPageContentOverrideLifetimeCustomValue
        );
        $this->set('globalSetting', $globalSetting);
        $this->set('enableCache', $enableCache);
        $this->set('globalSettingLifetime', $globalSettingLifetime);
    }

    public function submit()
    {
        if ($this->canAccess()) {
            foreach ($this->items as $page) {
                $data = array();
                if (($cCacheFullPageContent = $this->request->request->getInt('cCacheFullPageContent')) > -2) {
                    $data['cCacheFullPageContent'] = $cCacheFullPageContent;
                }
                if ($cCacheFullPageContent === 1) {
                    $data['cCacheFullPageContentOverrideLifetime'] = $this->request->request->get(
                        'cCacheFullPageContentOverrideLifetime'
                    );
                    if ($data['cCacheFullPageContentOverrideLifetime'] === 'custom') {
                        $data['cCacheFullPageContentLifetimeCustom'] = $this->request->request->getInt(
                            'cCacheFullPageContentLifetimeCustom'
                        );
                    }
                }
                if (count($data) > 0) {
                    $page->update($data);
                }
            }
            $response = new EditResponse();
            $response->setMessage(t('Cache settings updated successfully.'));
            return new JsonResponse($response);
        } else {
            throw new \RuntimeException(t('Access Denied'));
        }
    }


}
