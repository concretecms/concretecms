<?php

namespace Concrete\Controller\Dialog\Page;

use Concrete\Controller\Panel\Add as AddPanelController;

class AddBlockList extends AddPanelController
{
    protected $viewPath = '/dialogs/page/add_block_list';

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Controller\Panel\Add::getSelectedTab()
     */
    protected function getSelectedTab()
    {
        return 'blocks';
    }
}
