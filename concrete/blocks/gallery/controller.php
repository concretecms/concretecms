<?php

namespace Concrete\Block\Gallery;

use Concrete\Core\Block\BlockController;

class Controller extends BlockController
{
    protected $btTable = 'btGallery';
    protected $btInterfaceWidth = '800';
    protected $btInterfaceHeight = '500';

    protected $btWrapperClass = 'ccm-ui';

    public function getBlockTypeName()
    {
        return t('Gallery');
    }

    public function getBlockTypeDescription()
    {
        return t('Creates an Image Gallery in your web page.');
    }



}
