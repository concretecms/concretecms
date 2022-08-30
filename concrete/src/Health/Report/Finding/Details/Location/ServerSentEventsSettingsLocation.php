<?php
namespace Concrete\Core\Health\Report\Finding\Details\Location;

use Concrete\Core\Health\Report\Finding\Details\DashboardPageLocation;

class ServerSentEventsSettingsLocation extends DashboardPageLocation
{

    public function getPagePath(): string
    {
        return '/dashboard/system/notification/events';
    }

    public function getName(): string
    {
        return 'SSE Settings';
    }
}
