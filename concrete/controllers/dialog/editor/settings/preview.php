<?php

namespace Concrete\Controller\Dialog\Editor\Settings;

use Concrete\Controller\Backend\UserInterface as BackendUserInterface;
use Concrete\Core\Editor\EditorInterface;
use Concrete\Core\Error\UserMessageException;
use Concrete\Core\File\Filesystem;
use Concrete\Core\Page\Page;
use Concrete\Core\Permission\Checker;

class Preview extends BackendUserInterface
{
    protected $viewPath = '/dialogs/editor/settings/preview';

    public function view()
    {
        $token = $this->app->make('token');
        if (!$token->validate('ccm-editor-preview')) {
            throw new UserMessageException($token->getErrorMessage());
        }
        $post = $this->request->request;
        $editor = $this->app->make(EditorInterface::class);
        $editor->setAllowFileManager(false);
        if ($post->get('enable_filemanager')) {
            $filesystem = new Filesystem();
            $rootFolder = $filesystem->getRootFolder();
            if ($rootFolder !== null) {
                $checker = new Checker($rootFolder);
                if ($checker->canAccessFileManager()) {
                    $editor->setAllowFileManager(true);
                }
            }
        }
        $editor->setAllowSitemap(true);
        if ($post->get('enable_sitemap')) {
            $checker = new Checker();
            if ($checker->canAccessSitemap()) {
                $editor->setAllowSitemap(true);
            }
        }
        $manager = $editor->getPluginManager();
        $manager->deselect($manager->getSelectedPluginObjects());
        $config = $this->app->make('site')->getDefault()->getConfigRepository();
        $manager->select($config->get('editor.ckeditor4.plugins.selected_hidden'));
        $plugins = $post->get('plugin');
        if (is_array($plugins)) {
            $manager->select($plugins);
        }
        $this->set('editor', $editor);
        $previewHtml = $post->get('previewHtml');
        $this->set('previewHtml', is_string($previewHtml) ? $previewHtml : '');
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Controller\Backend\UserInterface::canAccess()
     */
    protected function canAccess()
    {
        $result = false;
        $page = Page::getByPath('/dashboard/system/basics/editor');
        if ($page && !$page->isError()) {
            $checker = new Checker($page);
            $result = $checker->canViewPage();
        }

        return $result;
    }
}
