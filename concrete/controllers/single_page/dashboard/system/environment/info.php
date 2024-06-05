<?php

namespace Concrete\Controller\SinglePage\Dashboard\System\Environment;

use Concrete\Core\Http\Response;
use Concrete\Core\Page\Controller\DashboardPageController;
use Concrete\Core\System\Info as SystemInfo;
use Concrete\Core\System\SystemUser;

class Info extends DashboardPageController
{
    public function get_environment_info()
    {
        $info = $this->app->make(SystemInfo::class);
        $systemUser = $this->app->make(SystemUser::class)->getCurrentUserName();
        if ($systemUser === '') {
            $systemUser = '*unknown*';
        }

        $hostname = $info->getHostName();
        $environment = $info->getEnvironment();

        $dbInfos = '';
        if ($info->isInstalled()) {
            $dbInfos = "\n# Database Information\nVersion: {$info->getDBMSVersion()}\nSQL Mode: {$info->getDBMSSqlMode()}\n".
                "Character Set: {$info->getDbCharset()}\nCollation: {$info->getDbCollation()}\n";
        }

        $packages = $info->getPackages() ?: 'None';
        $overrides = $info->getOverrides() ?: 'None';
        $phpExtensions = ($info->getPhpExtensions() === false) ? 'Unable to determine' : $info->getPhpExtensions();

        $content = <<<EOL
# Concrete Version
{$info->getCoreVersions()}

# Hostname
{$hostname}

# System User
{$systemUser}

# Environment
{$environment}
{$dbInfos}
# Concrete Packages
{$packages}

# Concrete Overrides
{$overrides}

# Concrete Cache Settings
{$info->getCache()}

# Database Entities Settings
{$info->getEntities()}

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
