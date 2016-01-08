<?php

namespace Concrete\Controller\SinglePage\Dashboard\System\Basics;
use Concrete\Core\Editor\RedactorEditor;
use \Concrete\Core\Page\Controller\DashboardPageController;

class Editor extends DashboardPageController
{

    public function view()
    {
        $manager = \Core::make("editor")->getPluginManager();
        $plugins = $manager->getAvailablePlugins();
        $this->set('plugins', $plugins);
        $this->set('manager', $manager);

        $this->set('filemanager', \Config::get('concrete.editor.concrete.enable_filemanager'));
        $this->set('sitemap', \Config::get('concrete.editor.concrete.enable_sitemap'));
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
