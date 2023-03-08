<?php

namespace Concrete\Controller\Element\Files\Edit\ImageEditor;

use Concrete\Core\Controller\ElementController;

class ToastThumbnail extends ElementController
{
    public function getElement()
    {
        return 'files/edit/image_editor/toast_thumbnail';
    }

    public function view()
    {
        $this->requireAsset('tui-image-editor');
    }
}