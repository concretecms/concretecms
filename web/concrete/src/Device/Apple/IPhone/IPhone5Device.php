<?php
namespace Concrete\Core\Device\Apple\IPhone;

use Concrete\Core\Device\Device;

class IPhone5Device extends Device
{

    public function getViewportHTML()
    {
        return '<div class="ccm-device-iphone5"><iframe class="ccm-display-frame"></iframe></div>';
    }

}