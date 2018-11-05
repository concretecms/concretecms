<?php

namespace Concrete\Controller\SinglePage\Dashboard\System\Basics;

use Concrete\Core\Editor\Plugin;
use Concrete\Core\Page\Controller\DashboardSitePageController;
use Punic\Comparer;

class Editor extends DashboardSitePageController
{
    public function view()
    {
        $manager = $this->app->make('editor')->getPluginManager();
        $config = $this->app->make('site')->getDefault()->getConfigRepository();
        $plugins = $manager->getAvailablePlugins();
        $cmp = new Comparer();
        uasort($plugins, function (Plugin $a, Plugin $b) use ($cmp) {
            return $cmp->compare($a->getName(), $b->getName());
        });

        $this->set('filemanager', (bool) $config->get('editor.concrete.enable_filemanager'));
        $this->set('sitemap', (bool) $config->get('editor.concrete.enable_sitemap'));

        $this->set('plugins', $plugins);
        $this->set('manager', $manager);
        $this->set('selected_hidden', $config->get('editor.ckeditor4.plugins.selected_hidden'));
    }

    public function submit()
    {
        if ($this->token->validate('submit')) {
            $editor = $this->app->make('editor');
            $editor->saveOptionsForm($this->request);
            $this->flash('success', t('Options saved successfully.'));
            $this->redirect('/dashboard/system/basics/editor');
        } else {
            $this->error->add($this->token->getErrorMessage());
        }
        $this->view();
    }
}
