<?php
namespace Concrete\Core\Health\Report\Finding\SettingsLocation\Location;

use Concrete\Core\Health\Report\Finding\SettingsLocation\DashboardPageSettingsLocation;

class UrlSettingsLocation extends DashboardPageSettingsLocation
{

    public function getPagePath(): string
    {
        return '/dashboard/system/seo/urls';
    }

    public function getPageName(): string
    {
        return 'URL Settings';
    }
}
