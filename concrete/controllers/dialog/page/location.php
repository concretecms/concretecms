<?php
namespace Concrete\Controller\Dialog\Page;

class Location extends \Concrete\Controller\Panel\Detail\Page\Location
{
    public function view()
    {
        $this->set('sitemap', true);
        parent::view();
    }
}
