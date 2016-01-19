<?php
namespace Concrete\Core\Editor;

use Concrete\Core\Foundation\Service\Provider as ServiceProvider;

class EditorServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton('editor', function($app) {
            $config = $app->make('config');
            $styles = $config->get('concrete.editor.ckeditor4.styles', array());
            $pluginManager = new PluginManager();
            $pluginManager->selectMultiple(
                $config->get('concrete.editor.ckeditor4.plugins.selected', array())
            );
            $editor = new CkeditorEditor($config, $pluginManager, $styles);
            $editor->setToken($app->make('token')->generate('editor'));
            $editor->registerEditorPlugins();
            $editor->registerInternalPlugins();
            return $editor;
        });
    }
}
