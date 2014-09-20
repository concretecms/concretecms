<?php
namespace Concrete\Core\Activity;

use Config;
use Package;
use Environment;
use Marketplace;
use Concrete\Core\File\Service\File;
use Concrete\Core\Activity\NewsflowItem;
use Concrete\Core\Activity\NewsflowSlotItem;

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

    public function __construct()
    {
        if (!Config::get('concrete.external.news')) {
            $this->connectionError = Newsflow::E_NEWSFLOW_SUPPORT_MANUALLY_DISABLED;
            return;
        }
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
            $path = Config::get('concrete.urls.newsflow') . '/' . DISPATCHER_FILENAME . '/?_ccm_view_external=1&cID=' . rawurlencode($cID) . '&cfToken=' . rawurlencode($cfToken);
            $response = $fileService->getContents($path);
            $ni = new NewsflowItem();
            $obj = $ni->parseResponse($response);
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
        $cPath = trim($cPath, '/');
        if (!$this->hasConnectionError()) {
            $fileService = new File();
            $cfToken = Marketplace::getSiteToken();
            $path = Config::get('concrete.urls.newsflow') . '/' . DISPATCHER_FILENAME . '/' . $cPath . '/-/view_external?cfToken=' . rawurlencode($cfToken);
            $response = $fileService->getContents($path);
            $ni = new NewsflowItem();
            $obj = $ni->parseResponse($response);
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
            $url = Config::get('concrete.urls.newsflow') . Config::get('concrete.urls.paths.newsflow_slot_content');
            $path = $url . '?cfToken=' . rawurlencode($cfToken);
            $response = $fileService->getContents($path);
            $nsi = new NewsflowSlotItem();
            $this->slots = $nsi->parseResponse($response);
        }
        return $this->slots;
    }
}
