<?php
namespace Concrete\Core\Health\Report\Finding\Details\Location;

use Concrete\Core\Health\Report\Finding\Details\DashboardPageLocation;

class UrlSettingsLocation extends DashboardPageLocation
{

    public function getPagePath(): string
    {
        return '/dashboard/system/seo/urls';
    }

    public function getName(): string
    {
        return 'URL Settings';
    }
}
