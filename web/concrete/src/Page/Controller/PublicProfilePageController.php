<?php
namespace Concrete\Core\Page\Controller;

use \PageController as CorePageController;
use Loader;
use Page;
use Config;

class PublicProfilePageController extends CorePageController
{

    public function on_start()
    {
        parent::on_start();

        if (!Config::get('concrete.user.profiles_enabled')) {
            $this->replace('/page_not_found');
        }
    }


}
