<?php
namespace Concrete\Core\ImageEditor;

use Concrete\Core\Asset\AssetList;
use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Foundation\Service\Provider;

class EditorServiceProvider extends Provider
{

    public function register()
    {
        $obj = $this->app;
        $this->app->bindShared('editor/image/extension/factory', function () use ($obj) {
            return new ExtensionFactory(AssetList::getInstance());
        });

        $this->app->bind('editor/image', function () {
            $editor = new ImageEditor();

            return $editor;
        });

        $this->app->bindShared('editor/image/core', function () use ($obj) {
            /** @var ImageEditor $editor */
            $editor = $obj->make('editor/image');
            /** @var ExtensionFactory $factory */
            $factory = $obj->make('editor/image/extension/factory');

            /** @var Repository $config */
            $config = $obj['config'];
            $extension_config = $config->get('imageeditor.extensions');

            foreach ($extension_config as $config) {
                $extension = $factory->extensionFromConfig($config);

                $editor->addExtension(array_get($config, 'type'), $extension);
            }

            return $editor;
        });
    }

}
