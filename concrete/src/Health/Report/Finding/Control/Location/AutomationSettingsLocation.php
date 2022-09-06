<?php
namespace Concrete\Core\Health\Report\Finding\Control\Location;

use Concrete\Core\Health\Report\Finding\Control\DashboardPageLocation;

class AutomationSettingsLocation extends DashboardPageLocation
{

    public function getPagePath(): string
    {
        return '/dashboard/system/automation/settings';
    }

    public function getName(): string
    {
        return 'Automation Settings';
    }
}
