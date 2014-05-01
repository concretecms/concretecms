<?
namespace Concrete\Core\Activity;

use Config;
use Package;
use Environment;
use Marketplace;
use Concrete\Core\File\Service\File;

/**
 * Class Newsflow
 *
 * A class used for retrieving the latest news and updates from Concrete5. This is a singleton class that should be
 * instantiated via Newsflow::getInstance(). This object is prevented from being created if the config file has the
 * ENABLE_APP_NEWS setting set to false.
 * @package Concrete\Core\Activity
 */
class Newsflow
{

    const E_NEWSFLOW_SUPPORT_MANUALLY_DISABLED = 21;

    protected $isConnected = false;
    protected $connectionError = false;
    protected $slots = null;

    /**
     * @return self Returns a singleton for this class
     */
    public static function getInstance()
    {
        static $instance = null;
        if ($instance === null) {
            $m = __CLASS__;
            $instance = new $m;
        }
        return $instance;
    }

    /**
     * protected constructor to prevent instances other than the singleton
     */
    protected function __construct()
    {
        if (defined('ENABLE_APP_NEWS') && ENABLE_APP_NEWS == false) {
            $this->connectionError = Newsflow::E_NEWSFLOW_SUPPORT_MANUALLY_DISABLED;
            return;
        }
    }

    /**
     * protected clone function to prevent instances other than the singleton
     */
    protected function __clone()
    {
        //we don't want clones of our singleton running around
    }

    /**
     * @return bool Returns true if there is a connection error, false if there is no error.
     */
    public function hasConnectionError()
    {
        return $this->connectionError !== false;
    }

    /**
     * @return bool|int Returns false if there are no errors, or an int corresponding to one of the E_* class constants
     */
    public function getConnectionError()
    {
        return $this->connectionError;
    }

    /**
     * Retrieves a NewsflowItem object for a given collection ID
     * @param int $cID
     * @return bool|NewsflowItem Returns a NewsflowItem object, false if there was an error or one could not be located.
     */
    public function getEditionByID($cID)
    {
        if (!$this->hasConnectionError()) {
            $fileService = new File();
            $cfToken = Marketplace::getSiteToken();
            $path = NEWSFLOW_URL . '/' . DISPATCHER_FILENAME . '/?_ccm_view_external=1&cID=' . rawurlencode($cID) . '&cfToken=' . rawurlencode($cfToken);
            $response = $fileService->getContents($path);
            $obj = NewsflowItem::parseResponse($response);
            return $obj;
        }
        return false;
    }

    /**
     * Retrieves a NewsflowItem object for a given collection path
     * @param $cPath
     * @return bool|NewsflowItem
     */
    public function getEditionByPath($cPath)
    {
        $ni = self::getInstance();
        $cPath = trim($cPath, '/');
        if (!$ni->hasConnectionError()) {
            $fileService = new File();
            $cfToken = Marketplace::getSiteToken();
            $path = NEWSFLOW_URL . '/' . DISPATCHER_FILENAME . '/' . $cPath . '/-/view_external?cfToken=' . rawurlencode($cfToken);
            $response = $fileService->getContents($path);
            $obj = NewsflowItem::parseResponse($response);
            return $obj;
        }
        return false;
    }

    /**
     * Retrieves an array of NewsflowSlotItems
     * @return NewsflowSlotItem[]|null
     */
    public function getSlotContents()
    {
        if ($this->slots === null) {
            $fileService = new File();
            $cfToken = Marketplace::getSiteToken();
            $path = NEWSFLOW_SLOT_CONTENT_URL . '?cfToken=' . rawurlencode($cfToken);
            $response = $fileService->getContents($path);
            $this->slots = NewsflowSlotItem::parseResponse($response);
        }
        return $this->slots;
    }
}
