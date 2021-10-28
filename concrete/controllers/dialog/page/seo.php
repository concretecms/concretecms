<?php
namespace Concrete\Controller\Dialog\Page;

class Seo extends \Concrete\Controller\Panel\Detail\Page\Seo
{
    protected $viewPath = '/dialogs/page/seo';
    public function view()
    {
        $this->set('sitemap', true);
        parent::view();
    }
}
