<?php

namespace Concrete\Core\Updater;

use Concrete\Core\Config\Renderer;
use Concrete\Core\Foundation\Environment;
use Concrete\Core\Marketplace\Marketplace;
use Concrete\Core\Package\Package;
use Concrete\Core\Updater\ApplicationUpdate\DiagnosticFactory;
use Zend\Http\Client;
use \Config;
use Zend\Http\Request;
use Concrete\Core\Cache\OpCache;

class ApplicationUpdate
{
    /**
     * Code of the error that occurs when we weren't able to update the configuration file during the update process.
     *
     * @var int
     */
    const E_UPDATE_WRITE_CONFIG = 10;

    /**
     * The version string.
     *
     * @var string
     */
    protected $version;
    /**
     * The version identifier (equals to the name of the directory under the updates directory).
     *
     * @var string
     */
    protected $identifier;

    /**
     * Returns the version string.
     *
     * @return string
     */
    public function getUpdateVersion()
    {
        return $this->version;
    }
    /**
     * Returns the version identifier (equals to the name of the directory under the updates directory).
     *
     * @return string
     */
    public function getUpdateIdentifier()
    {
        return $this->identifier;
    }

    /**
     * Returns an ApplicationUpdate instance given its version string.
     *
     * @param string $version
     *
     * @return ApplicationUpdate|null Returns null if there's no update with $version, or an ApplicationUpdate instance if $version is ok.
     */
    public static function getByVersionNumber($version)
    {
        $updates = id(new Update())->getLocalAvailableUpdates();
        foreach ($updates as $up) {
            if ($up->getUpdateVersion() == $version) {
                return $up;
            }
        }
    }

    /**
     * Writes the core pointer into config/update.php.
     * 
     * @return true|int Returns true if the configuration file was updated, otherwise it returns the error code (one of the ApplicationUpdate::E_... constants)
     */
    public function apply()
    {
        $updates = array();
        $update_file = DIR_CONFIG_SITE . '/update.php';
        if (file_exists($update_file)) {
            if (!is_writable($update_file)) {
                return self::E_UPDATE_WRITE_CONFIG;
            }
            $updates = (array) include $update_file;
        }

        $updates['core'] = $this->getUpdateIdentifier();
        \Config::clear('concrete.version');

        $renderer = new Renderer($updates);
        file_put_contents($update_file, $renderer->render());
        OpCache::clear($update_file);

        return true;
    }

    /**
     * Parse an update dir and returns an ApplicationUpdate instance. 
     * 
     * @param $dir The base name of the directory under the updates directory.
     *
     * @return ApplicationUpdate|null Returns null if there's no update in the $dir directory, or an ApplicationUpdate instance if $dir is ok.
     */
    public static function get($dir)
    {
        $version_file = DIR_CORE_UPDATES . "/{$dir}/" . DIRNAME_CORE . '/config/concrete.php';

        $concrete = @include $version_file;
        if ($concrete['version'] != false) {
            $obj = new ApplicationUpdate();
            $obj->version = $concrete['version'];
            $obj->identifier = $dir;
            return $obj;
        }
    }

    public function getVersion()
    {
        return $this->version;
    }

    public function getIdentifier()
    {
        return $this->identifier;
    }

    /**
     * Given the current update object, sends information to concrete5.org to determine updatability
     * @return \Concrete\Core\Updater\ApplicationUpdateDiagnostic
     */
    public function getDiagnosticObject()
    {

        $request = new Request();
        $request->setUri(Config::get('concrete.updates.services.inspect_update'));
        $request->setMethod('POST');
        $request->getPost()->set('current_version', Config::get('concrete.version_installed'));
        $request->getPost()->set('requested_version', $this->getVersion());
        $request->getPost()->set('site_url', (string) \Core::getApplicationURL());

        $mi = Marketplace::getInstance();
        if ($mi->isConnected() && !$mi->hasConnectionError()) {
            $config = \Core::make('config/database');
            $request->getPost()->set('marketplace_token', $config->get('concrete.marketplace.token'));
            $list = Package::getInstalledList();
            $packages = array();
            foreach($list as $pkg) {
                $packages[] = array('version' => $pkg->getPackageVersion(), 'handle' => $pkg->getPackageHandle());
            }
            $request->getPost()->set('packages', $packages);
        }
        $overrides = id(Environment::get())->getOverrideList();
        $request->getPost()->set('overrides', $overrides);

        $client = new Client();
        $client->setMethod('POST');
        $response = $client->send($request);
        $body = $response->getBody();

        $diagnostic = DiagnosticFactory::getFromJSON($body);
        return $diagnostic;
    }

}
