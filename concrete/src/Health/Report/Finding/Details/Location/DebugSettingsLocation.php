<?php
namespace Concrete\Core\Health\Report\Finding\Details\Location;

use Concrete\Core\Health\Report\Finding\Details\DashboardPageLocation;

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
