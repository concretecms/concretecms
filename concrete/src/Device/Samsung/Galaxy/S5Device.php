<?php
namespace Concrete\Core\Device\Samsung\Galaxy;

use Concrete\Core\Device\Device;

/**
 * @since 5.7.5
 */
class S5Device extends Device
{
    public function getViewportHTML()
    {
        return '<div class="ccm-device-galaxys5"><iframe class="ccm-display-frame"></iframe></div>';
    }
}
