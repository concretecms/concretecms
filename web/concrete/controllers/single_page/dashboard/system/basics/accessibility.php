<?php
namespace Concrete\Controller\SinglePage\Dashboard\System\Basics;

use Concrete\Core\Page\Controller\DashboardPageController;
use Request;
use Config;

class Accessibility extends DashboardPageController {

    public function view() {
        $this->set('show_titles', Config::get('ACCESSIBILITY_SHOW_TOOLBAR_TITLES'));
        $this->set('increase_font_size', Config::get('ACCESSIBILITY_INCREASE_TOOLBAR_FONT_SIZE'));
    }

    public function save() {
        Config::save('ACCESSIBILITY_SHOW_TOOLBAR_TITLES', !!Request::post('show_titles', false));
        Config::save('ACCESSIBILITY_INCREASE_TOOLBAR_FONT_SIZE', !!Request::post('increase_font_size', false));

        $this->set('message', t('Successfully saved accessibility settings.'));
        $this->view();
    }

}
