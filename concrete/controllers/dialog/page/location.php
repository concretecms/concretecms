<?php
namespace Concrete\Controller\Dialog\Page;

class Location extends \Concrete\Controller\Panel\Detail\Page\Location
{
    protected $viewPath = '/dialogs/page/location';
    public function view()
    {
        $this->set('sitemap', true);
        parent::view();
    }
}
