<?php
namespace Concrete\Controller\SinglePage\Dashboard\System\Basics;

use Concrete\Core\Page\Controller\DashboardPageController;

class Accessibility extends DashboardPageController
{
    public function view()
    {
        $config = $this->app->make('config');
        $this->set('show_titles', $config->get('concrete.accessibility.toolbar_titles'));
        $this->set('show_tooltips', $config->get('concrete.accessibility.toolbar_tooltips'));
        $this->set('increase_font_size', $config->get('concrete.accessibility.toolbar_large_font'));
        $this->set('full_lisiting_thumbnails', ($config->get('concrete.file_manager.images.preview_image_size')=='full')?true:false);
        $this->set('preview_popover', $config->get('concrete.file_manager.images.preview_image_popover'));
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
            $config->save('concrete.file_manager.images.preview_image_size', ((bool)$post->get('full_lisiting_thumbnails', false))?'full':'small');
            $config->save('concrete.file_manager.images.preview_image_popover', (bool) $post->get('preview_popover', false));
            $this->flash('success', t('Successfully saved accessibility settings.'));
            $this->redirect('/dashboard/system/basics/accessibility');
        }
    }
}
