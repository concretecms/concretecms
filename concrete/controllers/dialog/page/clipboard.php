<?php

namespace Concrete\Controller\Dialog\Page;

use Concrete\Controller\Panel\Add as AddPanelController;

class Clipboard extends AddPanelController
{
    protected $viewPath = '/dialogs/page/clipboard';

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Controller\Panel\Add::getSelectedTab()
     * @since 8.4.1
     */
    protected function getSelectedTab()
    {
        return 'clipboard';
    }
}
