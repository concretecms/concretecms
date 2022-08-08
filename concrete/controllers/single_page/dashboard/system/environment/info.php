<?php

namespace Concrete\Controller\SinglePage\Dashboard\System\Environment;

use Concrete\Core\Http\Response;
use Concrete\Core\Page\Controller\DashboardPageController;
use Concrete\Core\System\Info as SystemInfo;

class Info extends DashboardPageController
{
    public function get_environment_info()
    {
        $info = $this->app->make(SystemInfo::class);

        $hostname = $info->getHostName();
        $environment = $info->getEnvironment();

        $dbInfos = '';
        if ($info->isInstalled()) {
            $dbInfos = "\n# Database Information\nVersion: {$info->getDBMSVersion()}\nSQL Mode: {$info->getDBMSSqlMode()}\n";
        }

        $packages = $info->getPackages() ?: 'None';
        $overrides = $info->getOverrides() ?: 'None';
        $phpExtensions = ($info->getPhpExtensions() === false) ? 'Unable to determine' : $info->getPhpExtensions();

        $content = <<<EOL
# Concrete Version
{$info->getCoreVersions()}

# Hostname
{$hostname}

# Environment
{$environment}
{$dbInfos}
# Concrete Packages
{$packages}

# Concrete Overrides
{$overrides}

# Concrete Cache Settings
{$info->getCache()}

# Server Software
{$info->getServerSoftware()}

# Server API
{$info->getServerAPI()}

# PHP Version
{$info->getPhpVersion()}

# PHP Extensions
{$phpExtensions}

# PHP Settings
{$info->getPhpSettings()}
EOL;

        return new Response($content);
    }
}
