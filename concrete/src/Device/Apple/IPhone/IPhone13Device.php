<?php
namespace Concrete\Core\Device\Apple\IPhone;

use Concrete\Core\Device\Apple\AppleDevice;

class IPhone13Device extends AppleDevice
{
    public function getViewportHTML()
    {
        return '<div class="ccm-device-iphone13"><iframe class="ccm-display-frame"></iframe></div>';
    }
}
