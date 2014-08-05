<?
namespace Concrete\Core\Updater;
use Loader;
use Marketplace;
class ApplicationUpdate {

	protected $version;
	protected $identifier;

	const E_UPDATE_WRITE_CONFIG = 10;

	public function getUpdateVersion() {return $this->version;}
	public function getUpdateIdentifier() {return $this->identifier;}

	public static function getByVersionNumber($version) {
		$updates = id(new Update())->getLocalAvailableUpdates();
		foreach($updates as $up) {
			if ($up->getUpdateVersion() == $version) {
				return $up;
			}
		}
	}

	/**
	 * Writes the core pointer into config/site.php
	 */
	public function apply() {
		if (!is_writable(CONFIG_FILE)) {
			return self::E_UPDATE_WRITE_CONFIG;
		}

		$configFile = CONFIG_FILE;
		$contents = Loader::helper('file')->getContents($configFile);
		$contents = trim($contents);
		// remove any instances of app pointer

		$contents = preg_replace("/define\('DIRNAME_CORE_UPDATED', '(.+)'\);/i", "", $contents);

		file_put_contents($configFile, $contents);

		if (substr($contents, -2) == '?>') {
			file_put_contents($configFile, "<" . "?" . "p" . "hp define('DIRNAME_CORE_UPDATED', '" . $this->getUpdateIdentifier() . "');?>", FILE_APPEND);
		} else {
			file_put_contents($configFile, "?><" . "?" . "p" . "hp define('DIRNAME_CORE_UPDATED', '" . $this->getUpdateIdentifier() . "');?>", FILE_APPEND);
		}

		return true;
	}

    /**
     * @param $dir
     * @return static
     */
    public static function get($dir) {
		$APP_VERSION = false;
		// given a directory, we figure out what version of the system this is
		$version = DIR_CORE_UPDATES . '/' . $dir . '/' . DIRNAME_CORE . '/config/version.php';
		@include($version);
		if ($APP_VERSION != false) {
			$obj = new ApplicationUpdate();
			$obj->version = $APP_VERSION;
			$obj->identifier = $dir;
			return $obj;
		}
	}

}
