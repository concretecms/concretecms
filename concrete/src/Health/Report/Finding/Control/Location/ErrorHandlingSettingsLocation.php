<?php
namespace Concrete\Core\Health\Report\Finding\Control\Location;

use Concrete\Core\Health\Report\Finding\Control\DashboardPageLocation;

class ErrorHandlingSettingsLocation extends DashboardPageLocation
{

    public function getPagePath(): string
    {
        return '/dashboard/system/environment/errors';
    }

    public function getName(): string
    {
        return 'Error Handling';
    }
}
