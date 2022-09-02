<?php
namespace Concrete\Core\Health\Report\Finding\Controls\Location;

use Concrete\Core\Health\Report\Finding\Controls\DashboardPageLocation;

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
