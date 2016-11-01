<?php
namespace Concrete\Controller\SinglePage\Dashboard\System\Environment;

use Concrete\Core\Page\Controller\DashboardPageController;

class Info extends DashboardPageController
{
    public function get_environment_info()
    {
        $info = $this->app->make(\Concrete\Core\System\Info::class);
        /* @var \Concrete\Core\System\Info $info */

        echo "# concrete5 Version\n".$info->getCoreVersions()."\n\n";

        echo "# concrete5 Packages\n".($info->getPackages() ?: 'None')."\n\n";

        echo "# concrete5 Overrides\n".($info->getOverrides() ?: 'None')."\n\n";

        echo "# concrete5 Cache Settings\n".$info->getCache()."\n\n";

        echo "# Server Software\n".$info->getServerSoftware()."\n\n";

        echo "# Server API\n".$info->getServerAPI()."\n\n";

        echo "# PHP Version\n".$info->getPhpVersion()."\n\n";

        echo "# PHP Extensions\n".($info->getPhpExtensions() === false ? 'Unable to determine' : $info->getPhpExtensions())."\n\n";

        echo "# PHP Settings\n".$info->getPhpSettings();

        exit;
    }
}
