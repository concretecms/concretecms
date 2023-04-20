<?php
namespace Concrete\Core\ImageEditor\Controller;

class DefaultEditorController implements EditorControllerInterface
{

    public function getImageEditorHandle()
    {
        return 'toast';
    }

    public function getThumbnailEditorHandle()
    {
        return 'concrete_thumbnail_editor';
    }

}