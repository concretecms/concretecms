<?php
namespace Concrete\Controller\SinglePage\Dashboard\System\Environment;

use Concrete\Core\Foundation\Environment;
use Concrete\Core\Page\Controller\DashboardPageController;
use Concrete\Core\Package\PackageList;
use Config;
use Core;
use Localization;

class Info extends DashboardPageController
{
    public function get_environment_info()
    {
        $activeLocale = Localization::activeLocale();
        if ($activeLocale != 'en_US') {
            Localization::changeLocale('en_US');
        }
        $maxExecutionTime = ini_get('max_execution_time');
        set_time_limit(5);

        $environmentMessage = "# concrete5 Version\n";
        $environmentMessage .= "Core Version - " . \Config::get('concrete.version') . "\n";
        $environmentMessage .= "Version Installed - " . \Config::get('concrete.version_installed') . "\n";
        $environmentMessage .= "Database Version - " . \Config::get('concrete.version_db') . "\n\n";

        $environmentMessage .= "# concrete5 Packages\n";
        $pla = PackageList::get();
        $pl = $pla->getPackages();
        $packages = array();
        foreach ($pl as $p) {
            if ($p->isPackageInstalled()) {
                $packages[] = $p->getPackageName() . ' (' . $p->getPackageVersion() . ')';
            }
        }
        if (count($packages) > 0) {
            natcasesort($packages);
            $environmentMessage .= implode(', ', $packages);
            $environmentMessage .= ".\n";
        } else {
            $environmentMessage .= "None\n";
        }
        $environmentMessage .= "\n";

        // overrides
        $environmentMessage .= "# concrete5 Overrides\n";
        $env = Environment::get();
        $overrides = $env->getOverrideList();

        if (count($overrides) > 0) {
            $environmentMessage .= implode(', ', $overrides);
            $environmentMessage .= "\n";
        } else {
            $environmentMessage .= "None\n";
        }
        $environmentMessage .= "\n";

        print $environmentMessage;

        // cache
        $environmentMessage = "# concrete5 Cache Settings\n";
        $environmentMessage .= sprintf("Block Cache - %s\n", Config::get('concrete.cache.blocks') ? 'On' : 'Off');
        $environmentMessage .= sprintf("Overrides Cache - %s\n", Config::get('concrete.cache.overrides') ? 'On' : 'Off');
        $environmentMessage .= sprintf("Full Page Caching - %s\n", (Config::get('concrete.cache.pages') == 'blocks' ? 'On - If blocks on the particular page allow it.' : (Config::get('concrete.cache.pages') == 'all' ? 'On - In all cases.' : 'Off')));
        if (Config::get('concrete.cache.full_page_lifetime')) {
            $environmentMessage .= sprintf("Full Page Cache Lifetime - %s\n", (Config::get('concrete.cache.full_page_lifetime') == 'default' ? sprintf('Every %s (default setting).', Core::make('helper/date')->describeInterval(Config::get('concrete.cache.lifetime'))) : (Config::get('concrete.cache.full_page_lifetime') == 'forever' ? 'Only when manually removed or the cache is cleared.' : sprintf('Every %s minutes.', Config::get('concrete.cache.full_page_lifetime_value')))));
        }
        $environmentMessage .= "\n";
        print $environmentMessage;

        $environmentMessage = "# Server Software\n" . $_SERVER['SERVER_SOFTWARE'] . "\n\n";
        $environmentMessage .= "# Server API\n" . php_sapi_name() . "\n\n";
        $environmentMessage .= "# PHP Version\n" . PHP_VERSION . "\n\n";
        $environmentMessage .= "# PHP Extensions\n";
        if (function_exists('get_loaded_extensions')) {
            $gle = @get_loaded_extensions();
            natcasesort($gle);
            $environmentMessage .= implode(', ', $gle);
            $environmentMessage .= ".\n";
        } else {
            $environmentMessage .= "Unable to determine\n";
        }

        print $environmentMessage;

        ob_start();
        phpinfo();
        $section = 'phpinfo';
        $phpinfo = array($section => array());
        if (preg_match_all('#(?:<h2>(?:<a name=".*?">)?(.*?)(?:</a>)?</h2>)|(?:<tr(?: class=".*?")?><t[hd](?: class=".*?")?>(.*?)\s*</t[hd]>(?:<t[hd](?: class=".*?")?>(.*?)\s*</t[hd]>(?:<t[hd](?: class=".*?")?>(.*?)\s*</t[hd]>)?)?</tr>)#s', ob_get_clean(), $matches, PREG_SET_ORDER)) {
            foreach ($matches as $match) {
                if (strlen($match[1])) {
                    $section = $match[1];
                    $phpinfo[$section] = array();
                } elseif (isset($match[3])) {
                    $phpinfo[$section][$match[2]] = isset($match[4]) ? array($match[3], $match[4]) : $match[3];
                } else {
                    $phpinfo[$section][] = $match[2];
                }
            }
        }
        $environmentMessage = "\n# PHP Settings\n";
        $environmentMessage .= "max_execution_time - $maxExecutionTime\n";
        foreach ($phpinfo as $name => $section) {
            foreach ($section as $key => $val) {
                if (preg_match('/.*max_execution_time*/', $key)) {
                    continue;
                }
                if (!preg_match('/.*limit.*/', $key) && !preg_match('/.*safe.*/', $key) && !preg_match('/.*max.*/', $key)) {
                    continue;
                }
                if (is_array($val)) {
                    $environmentMessage .= "$key - $val[0]\n";
                } elseif (is_string($key)) {
                    $environmentMessage .= "$key - $val\n";
                } else {
                    $environmentMessage .= "$val\n";
                }
            }
        }

        print $environmentMessage;
        exit;
    }
}
