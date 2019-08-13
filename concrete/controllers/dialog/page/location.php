<?php
namespace Concrete\Controller\Dialog\Page;

/**
 * @since 5.7.2.1
 */
class Location extends \Concrete\Controller\Panel\Detail\Page\Location
{
    public function view()
    {
        $this->set('sitemap', true);
        parent::view();
    }
}
