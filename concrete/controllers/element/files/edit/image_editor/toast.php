<?php

namespace Concrete\Controller\Element\Files\Edit\ImageEditor;

use Concrete\Core\Controller\ElementController;

class Toast extends ElementController
{
    public function getElement()
    {
        return 'files/edit/image_editor/toast';
    }

    public function view()
    {
        $this->requireAsset('tui-image-editor');
    }
}