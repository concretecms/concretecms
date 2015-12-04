<?php

namespace Concrete\Core\Express\Form\Control;

class NameEntityPropertyControlRenderer extends EntityPropertyControlRenderer
{

    public function getControlHandle()
    {
        return 'name';
    }

    public function getControlLabel()
    {
        return t('Name');
    }


}