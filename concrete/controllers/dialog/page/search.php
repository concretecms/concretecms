<?php
namespace Concrete\Controller\Dialog\Page;

use Concrete\Controller\Backend\UserInterface as BackendInterfaceController;
use Concrete\Controller\Element\Search\Pages\Header;
use Loader;

class Search extends BackendInterfaceController
{
    protected $viewPath = '/dialogs/page/search';

    protected function canAccess()
    {
        $sh = Loader::helper('concrete/dashboard/sitemap');

        return $sh->canRead();
    }

    public function view()
    {
        $search = $this->app->make('Concrete\Controller\Search\Pages');
        $result = $search->getCurrentSearchObject();

        if (is_object($result)) {
            $this->set('result', $result);
        }

        $header = new Header();
        $this->set('header', $header);
        $this->requireAsset('core/sitemap');
    }

}
