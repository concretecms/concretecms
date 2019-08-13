<?php
namespace Concrete\Core\Device\Apple\IPad;

use Concrete\Core\Device\Apple\AppleDevice;

/**
 * @since 5.7.5
 */
class IPadDevice extends AppleDevice
{
    public function getViewportHTML()
    {
        return '<div class="ccm-device-ipad"><iframe class="ccm-display-frame"></iframe></div>';
    }
}
