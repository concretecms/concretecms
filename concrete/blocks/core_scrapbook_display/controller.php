<?php
namespace Concrete\Block\CoreScrapbookDisplay;

use Concrete\Core\Block\BlockController;
use Block;
use Concrete\Core\Block\View\BlockViewTemplate;

/**
 * The controller for the core scrapbook display block. This block is automatically used when a block is copied into a
 * page from a clipboard. It is a proxy block.
 *
 * @package    Blocks
 * @subpackage Core Scrapbook/Clipboard Display
 *
 * @author     Andrew Embler <andrew@concrete5.org>
 * @copyright  Copyright (c) 2003-2012 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 */
class Controller extends BlockController
{
    protected $btCacheBlockRecord = true;
    protected $btTable = 'btCoreScrapbookDisplay';
    protected $btIsInternal = true;
    protected $passthruController;

    public function ignorePageThemeGridFrameworkContainer()
    {
        $bc = $this->getScrapbookBlockController();
        if (is_object($bc)) {
            return $bc->ignorePageThemeGridFrameworkContainer();
        }

        return false;
    }

    /**
     * @var int Original Block ID
     */
    protected $bOriginalID;

    public function getBlockTypeDescription()
    {
        return t("Proxy block for blocks pasted through the scrapbook.");
    }

    public function getBlockTypeName()
    {
        return t("Scrapbook Display");
    }

    public function getOriginalBlockID()
    {
        return $this->bOriginalID;
    }

    public function getScrapbookBlockController()
    {
        if (!isset($this->passthruController)) {
            $b = Block::getByID($this->bOriginalID);
            $bc = ($b) ? $b->getInstance() : false;
            $this->passthruController = $bc;
        }

        return $this->passthruController;
    }

    public function export(\SimpleXMLElement $blockNode)
    {
        $b = Block::getByID($this->bOriginalID);
        $bc = $b->getInstance();
        if ($bc) {
            $blockNode['type'] = $b->getBlockTypeHandle();
            $blockNode['name'] = $b->getBlockName();
            if ($b->getBlockFilename() != '') {
                $blockNode['custom-template'] = $b->getBlockFilename();
            }
            return $bc->export($blockNode);
        }
    }

    public function getSearchableContent()
    {
        $bc = $this->getScrapbookBlockController();

        if ($bc && method_exists($bc, 'getSearchableContent')) {
            return $bc->getSearchableContent();
        }
    }

    public function getPassThruActionAndParameters($method, $parameters = array())
    {
        $return = parent::getPassThruActionAndParameters($method, $parameters);

        $parameters = $return[1];

        // pop the last element off the array and get it
        $bID = array_pop($parameters);
        if ($bID == $this->bID) {
            // this is the proxy block. So we pop off the block ID and replace it with the original ID
            $parameters[] = $this->bOriginalID;
            $return[1] = $parameters;
        }

        return $return;
    }

    public function isValidControllerTask($method, $parameters = array())
    {
        $bc = $this->getScrapbookBlockController();

        if (is_object($bc)) {
            return $bc->isValidControllerTask($method, $parameters);
        }

        return false;
    }

    public function on_start()
    {
        $bc = $this->getScrapbookBlockController();

        if (is_object($bc)) {
            return $bc->on_start();
        }
    }

    public function on_before_render()
    {
        $bc = $this->getScrapbookBlockController();

        if (is_object($bc)) {
            return $bc->on_before_render();
        }
    }

    public function runAction($action, $parameters = array())
    {
        $bc = $this->getScrapbookBlockController();

        if (is_object($bc)) {
            return $bc->runAction($action, $parameters);
        }
    }

    public function registerViewAssets($outputContent = '')
    {
        $bc = $this->getScrapbookBlockController();

        if (is_object($bc) && is_callable(array($bc, 'registerViewAssets'))) {
            $bc->registerViewAssets($outputContent);
        }
    }

    public function on_page_view($page)
    {
        $bc = $this->getScrapbookBlockController();

        if ($bc && method_exists($bc, 'on_page_view')) {
            return $bc->on_page_view($page);
        }
    }

    public function outputAutoHeaderItems()
    {
        $b = Block::getByID($this->bOriginalID);
        if ($b) {
            $b = $this->getBlockObject();
            $bvt = new BlockViewTemplate($b);
            $bvt->registerTemplateAssets();
        }
    }

    public function cacheBlockOutput()
    {
        $bc = $this->getScrapbookBlockController();

        if ($bc) {
            return $bc->cacheBlockOutput();
        }
    }

    public function cacheBlockOutputForRegisteredUsers()
    {
        $bc = $this->getScrapbookBlockController();

        if ($bc) {
            return $bc->cacheBlockOutputForRegisteredUsers();
        }
    }

    public function cacheBlockOutputOnPost()
    {
        $bc = $this->getScrapbookBlockController();

        if ($bc) {
            return $bc->cacheBlockOutputOnPost();
        }
    }

    public function getBlockTypeCacheOutputLifetime()
    {
        $bc = $this->getScrapbookBlockController();

        if ($bc) {
            return $bc->getBlockTypeCacheOutputLifetime();
        }
    }
}
