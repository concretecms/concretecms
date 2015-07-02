<?php
namespace Concrete\Core\Device\Apple\IPad;

use Concrete\Core\Device\Device;

class IPadDevice extends Device
{

    public function getViewportHTML()
    {
        return '<div class="ccm-device-ipad"><iframe class="ccm-display-frame"></iframe></div>';
    }

}