<?php
namespace Concrete\Core\Health\Report\Finding\SettingsLocation\Location;

use Concrete\Core\Health\Report\Finding\SettingsLocation\DashboardPageSettingsLocation;

class AutomationSettingsLocation extends DashboardPageSettingsLocation
{

    public function getPagePath(): string
    {
        return '/dashboard/system/automation/settings';
    }

    public function getPageName(): string
    {
        return 'Automation Settings';
    }
}
