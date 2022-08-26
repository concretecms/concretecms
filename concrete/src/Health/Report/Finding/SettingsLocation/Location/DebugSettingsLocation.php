<?php
namespace Concrete\Core\Health\Report\Finding\SettingsLocation\Location;

use Concrete\Core\Health\Report\Finding\SettingsLocation\DashboardPageSettingsLocation;

class DebugSettingsLocation extends DashboardPageSettingsLocation
{

    public function getPagePath(): string
    {
        return '/dashboard/system/environment/debug';
    }

    public function getPageName(): string
    {
        return 'Debug Settings';
    }
}
