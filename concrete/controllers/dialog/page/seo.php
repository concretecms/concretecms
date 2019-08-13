<?php
namespace Concrete\Controller\Dialog\Page;

/**
 * @since 5.7.2.1
 */
class Seo extends \Concrete\Controller\Panel\Detail\Page\Seo
{
    public function view()
    {
        $this->set('sitemap', true);
        parent::view();
    }
}
