<?php
namespace Concrete\Controller\SinglePage\Dashboard\System\Basics;

use Concrete\Core\Page\Controller\DashboardPageController;
use Request;
use Config;

class Accessibility extends DashboardPageController
{

    public function view()
    {
        $this->set('show_titles', Config::get('concrete.accessibility.toolbar_titles'));
        $this->set('increase_font_size', Config::get('concrete.accessibility.toolbar_large_font'));
        $this->set('display_help', Config::get('concrete.accessibility.display_help_system'));
    }

    public function saved()
    {
        $this->set('message', t('Successfully saved accessibility settings.'));
        $this->view();
    }

    public function save()
    {
        Config::save('concrete.accessibility.toolbar_titles', !!Request::post('show_titles', false));
        Config::save('concrete.accessibility.toolbar_large_font', !!Request::post('increase_font_size', false));
        Config::save('concrete.accessibility.display_help_system', !!Request::post('display_help', false));
        $this->redirect('/dashboard/system/basics/accessibility', 'saved');
    }

}
