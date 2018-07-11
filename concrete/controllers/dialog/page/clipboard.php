<?php

namespace Concrete\Controller\Dialog\Page;

use Concrete\Controller\Panel\Add;

class Clipboard extends Add
{
    protected $viewPath = '/dialogs/page/clipboard';

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Controller\Panel\Add::getSelectedTab()
     */
    protected function getSelectedTab()
    {
        return 'clipboard';
    }
}
