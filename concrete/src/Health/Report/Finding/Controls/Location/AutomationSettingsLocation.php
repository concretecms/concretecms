<?php
namespace Concrete\Core\Health\Report\Finding\Controls\Location;

use Concrete\Core\Health\Report\Finding\Controls\DashboardPageLocation;

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
