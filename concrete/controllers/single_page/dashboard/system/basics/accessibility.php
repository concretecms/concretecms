<?php
namespace Concrete\Controller\SinglePage\Dashboard\System\Basics;

use Concrete\Core\Page\Controller\DashboardPageController;
use Request;
use Config;

class Accessibility extends DashboardPageController
{
    public function view()
    {
        $config = $this->app->make('config');
        $this->set('show_titles', $config->get('concrete.accessibility.toolbar_titles'));
        $this->set('show_tooltips', $config->get('concrete.accessibility.toolbar_tooltips'));
        $this->set('increase_font_size', $config->get('concrete.accessibility.toolbar_large_font'));
        $this->set('display_help', $config->get('concrete.accessibility.display_help_system'));
    }

    public function save()
    {
        if (!$this->token->validate('accessibility')) {
            $this->error->add(t('Invalid CSRF token. Please refresh and try again.'));
            $this->view();
        } else {
            $config = $this->app->make('config');
            $post = $this->request->request;
            $config->save('concrete.accessibility.toolbar_titles', (bool) $post->get('show_titles', false));
            $config->save('concrete.accessibility.toolbar_tooltips', (bool) $post->get('show_tooltips', false));
            $config->save('concrete.accessibility.toolbar_large_font', (bool) $post->get('increase_font_size', false));
            $config->save('concrete.accessibility.display_help_system', (bool) $post->get('display_help', false));
            $this->flash('message', t('Successfully saved accessibility settings.'));
            $this->redirect('/dashboard/system/basics/accessibility');
        }
    }
}
