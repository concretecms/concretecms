<?php
namespace Concrete\Core\Health\Report\Finding\SettingsLocation\Location;

use Concrete\Core\Health\Report\Finding\SettingsLocation\DashboardPageSettingsLocation;

class CacheSettingsLocation extends DashboardPageSettingsLocation
{

    public function getPagePath(): string
    {
        return '/dashboard/system/optimization/cache';
    }

    public function getPageName(): string
    {
        return 'Cache Settings';
    }
}
