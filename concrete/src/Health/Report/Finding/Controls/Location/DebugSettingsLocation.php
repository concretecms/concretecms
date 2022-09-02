<?php
namespace Concrete\Core\Health\Report\Finding\Controls\Location;

use Concrete\Core\Health\Report\Finding\Controls\DashboardPageLocation;

class DebugSettingsLocation extends DashboardPageLocation
{

    public function getPagePath(): string
    {
        return '/dashboard/system/environment/debug';
    }

    public function getName(): string
    {
        return 'Debug Settings';
    }
}
