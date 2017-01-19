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
        $this->set('show_tooltips', Config::get('concrete.accessibility.toolbar_tooltips'));
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
        if (!$this->token->validate('accessibility')) {
            $this->error->add(t('Invalid CSRF token. Please refresh and try again.'));

            return $this->view();
        }
        Config::save('concrete.accessibility.toolbar_titles', (bool) Request::post('show_titles', false));
        Config::save('concrete.accessibility.toolbar_tooltips', (bool) Request::post('show_tooltips', false));
        Config::save('concrete.accessibility.toolbar_large_font', (bool) Request::post('increase_font_size', false));
        Config::save('concrete.accessibility.display_help_system', (bool) Request::post('display_help', false));
        $this->redirect('/dashboard/system/basics/accessibility', 'saved');
    }
}
