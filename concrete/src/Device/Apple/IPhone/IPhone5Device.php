<?php
namespace Concrete\Core\Device\Apple\IPhone;

use Concrete\Core\Device\Apple\AppleDevice;

/**
 * @since 5.7.5
 */
class IPhone5Device extends AppleDevice
{
    public function getViewportHTML()
    {
        return '<div class="ccm-device-iphone5"><iframe class="ccm-display-frame"></iframe></div>';
    }
}
