<?php
namespace Concrete\Core\Health\Report\Finding\SettingsLocation\Location;

use Concrete\Core\Health\Report\Finding\SettingsLocation\DashboardPageSettingsLocation;

class ServerSentEventsSettingsLocation extends DashboardPageSettingsLocation
{

    public function getPagePath(): string
    {
        return '/dashboard/system/notification/events';
    }

    public function getPageName(): string
    {
        return 'SSE Settings';
    }
}
