<?php
namespace Concrete\Core\Page\Controller;

use Loader;
use Page;

class PublicProfilePageController extends PageController {

    public function on_start(){
        parent::on_start();

        if (!defined('ENABLE_USER_PROFILES') || !ENABLE_USER_PROFILES) {
            $this->render('/page_not_found');
        }
    }


}
