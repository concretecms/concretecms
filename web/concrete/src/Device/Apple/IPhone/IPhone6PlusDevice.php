<?php
namespace Concrete\Core\Device\Apple\IPhone;

use Concrete\Core\Device\Apple\AppleDevice;

class IPhone6PlusDevice extends AppleDevice
{

    public function getViewportHTML()
    {
        return '<div class="ccm-device-iphone6-plus"><iframe class="ccm-display-frame"></iframe></div>';
    }

}