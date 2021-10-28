<?php

namespace Concrete\Controller\Dialog\Page;

class Versions extends \Concrete\Controller\Panel\Page\Versions
{
    public function view()
    {
        $this->set('isDialogMode', true);
        parent::view();
    }
}
