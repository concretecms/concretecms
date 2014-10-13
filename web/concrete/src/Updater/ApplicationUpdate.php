<?php
namespace Concrete\Core\Updater;
use Concrete\Core\Config\Renderer;
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
	 * Writes the core pointer into config/update.php
	 */
	public function apply() {

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

		return true;
	}

    /**
     * @param $dir
     * @return static
     */
    public static function get($dir) {
        $version_file = DIR_CORE_UPDATES . "/{$dir}/" . DIRNAME_CORE . '/config/concrete.php';

        $concrete = @include($version_file);
        if ($concrete['version'] != false) {
            $obj = new ApplicationUpdate();
            $obj->version = $concrete['version'];
            $obj->identifier = $dir;
            return $obj;
        }
	}

}
