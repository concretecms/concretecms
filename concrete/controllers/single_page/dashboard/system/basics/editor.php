<?php
namespace Concrete\Controller\SinglePage\Dashboard\System\Basics;

use Concrete\Core\Page\Controller\DashboardPageController;
use Concrete\Core\Page\Controller\DashboardSitePageController;

class Editor extends DashboardSitePageController
{
    public function view()
    {
        $manager = \Core::make("editor")->getPluginManager();
        $plugins = $manager->getAvailablePlugins();
        $this->set('plugins', $plugins);
        $this->set('manager', $manager);

        $this->set('filemanager', \Config::get('site.sites.default.editor.concrete.enable_filemanager'));
        $this->set('sitemap', \Config::get('site.sites.default.editor.concrete.enable_sitemap'));

        $this->set('selected_hidden', \Config::get('site.sites.default.editor.ckeditor4.plugins.selected_hidden'));
    }

    public function saved()
    {
        $this->set('success', t('Options saved successfully.'));
        $this->view();
    }

    public function submit()
    {
        if ($this->token->validate('submit')) {
            $editor = \Core::make('editor');
            $editor->saveOptionsForm($this->request);
            $this->redirect('/dashboard/system/basics/editor', 'saved');
        } else {
            $this->error->add($this->token->getErrorMessage());
        }
        $this->view();
    }
}
