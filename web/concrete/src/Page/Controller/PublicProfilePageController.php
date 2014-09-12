<?php
namespace Concrete\Core\Page\Controller;

use Loader;
use Page;
use Config;

class PublicProfilePageController extends PageController {

    public function on_start(){
        parent::on_start();

        if (!Config::get('concrete.user.profiles_enabled')) {
            $this->render('/page_not_found');
        }
    }


}
