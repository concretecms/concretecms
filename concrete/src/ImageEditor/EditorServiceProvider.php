<?php

namespace Concrete\Core\ImageEditor;

use Concrete\Core\Foundation\Service\Provider;

class EditorServiceProvider extends Provider
{

    public function register()
    {
        $this->app->singleton(ImageEditorService::class);
        /** @var ImageEditorService $imageEditorService */
        $imageEditorService = $this->app->make(ImageEditorService::class);
        $imageEditorService->checkDefaultEditor();

        $this->app->bind('editor/image', function () use ($imageEditorService) {
            return $imageEditorService->getActiveEditor();
        });
    }
}
