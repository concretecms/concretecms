<?php
namespace Concrete\Core\Health\Report\Finding\Controls\Location;

use Concrete\Core\Health\Report\Finding\Controls\DashboardPageLocation;

class CacheSettingsLocation extends DashboardPageLocation
{

    public function getPagePath(): string
    {
        return '/dashboard/system/optimization/cache';
    }

    public function getName(): string
    {
        return 'Cache Settings';
    }
}
