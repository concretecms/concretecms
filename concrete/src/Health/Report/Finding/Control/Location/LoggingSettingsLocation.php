<?php
namespace Concrete\Core\Health\Report\Finding\Control\Location;

use Concrete\Core\Health\Report\Finding\Control\DashboardPageLocation;

class LoggingSettingsLocation extends DashboardPageLocation
{

    public function getPagePath(): string
    {
        return '/dashboard/system/environment/logging';
    }

    public function getName(): string
    {
        return 'Logging Settings';
    }
}
