<?php

namespace Concrete\Core\Express\Form\Control;

class TextEntityPropertyControlRenderer extends EntityPropertyControlRenderer
{

    public function getControlHandle()
    {
        return 'text';
    }

    public function getControlLabel()
    {
        return null;
    }


}