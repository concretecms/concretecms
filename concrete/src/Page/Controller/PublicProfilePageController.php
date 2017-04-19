<?php
namespace Concrete\Core\Page\Controller;

use PageController as CorePageController;
use Config;

class PublicProfilePageController extends CorePageController
{
    public function on_start()
    {
        parent::on_start();

        $site = \Core::make('site')->getSite();
        $config = $site->getConfigRepository();

        if (!$config->get('user.profiles_enabled')) {
            return $this->replace('/page_not_found');
        }
    }
}
