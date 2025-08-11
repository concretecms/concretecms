<?php

namespace Concrete\Block\DesktopLatestHealthResult;

use Concrete\Core\Block\BlockController;
use Concrete\Core\Health\Report\Result\ResultList;

class Controller extends BlockController
{

    /**
     * @return string
     */
    public function getBlockTypeDescription()
    {
        return t('Displays the latest Health Report result on your Dashboard desktop.');
    }

    /**
     * @return string
     */
    public function getBlockTypeName()
    {
        return t('Desktop Latest Health Result');
    }

    public function view()
    {
        $this->set('latestResult', ResultList::getLatestResult());
    }
}
