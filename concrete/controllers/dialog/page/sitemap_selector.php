<?php
namespace Concrete\Controller\Dialog\Page;

use Concrete\Controller\Backend\UserInterface as UserInterfaceController;
use Concrete\Core\Validation\SanitizeService;

class SitemapSelector extends UserInterfaceController
{
    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Controller\Controller::$viewPath
     */
    protected $viewPath = '/dialogs/page/sitemap_selector';

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Controller\Controller::$controllerActionPath
     */
    protected $controllerActionPath = '/ccm/system/dialogs/page/sitemap_selector';

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Controller\Backend\UserInterface::$validationToken
     */
    protected $validationToken = '/dialogs/page/sitemap_selector';

    protected $helpers = ['concrete/ui'];

    public function view()
    {
        /** @var $sanitizer SanitizeService */
        $sanitizer = $this->app->make(SanitizeService::class);
        $cID = (int) $sanitizer->sanitizeInt($this->request->query->get('cID'));
        $this->set('cID', $cID ?: null);
        $selectMode = (string) $sanitizer->sanitizeString($this->request->query->get('sitemap_select_mode'));
        $this->set('selectMode', $selectMode);
        $this->set('uniqid', uniqid());
        $this->set('includeSystemPages', $this->request->query->get('includeSystemPages') ? true : false);
        $this->set('askIncludeSystemPages', $this->request->query->get('askIncludeSystemPages') ? true : false);
    }

    protected function canAccess()
    {
        $siteHelper = $this->app->make('helper/concrete/dashboard/sitemap');

        return $siteHelper->canRead();
    }
}
