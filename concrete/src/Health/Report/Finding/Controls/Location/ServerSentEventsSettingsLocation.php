<?php
namespace Concrete\Core\Health\Report\Finding\Controls\Location;

use Concrete\Core\Health\Report\Finding\Controls\DashboardPageLocation;

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
