<?php

namespace Concrete\Block\CoreStackDisplay;

use Stack;
use Permissions;
use Page;
use Concrete\Core\Block\BlockController;

/**
 * The controller for the stack display block. This is an internal proxy block that is inserted when a stack's contents are displayed in a page.
 *
 * @package Blocks
 * @subpackage Core Stack Display
 *
 * @author Andrew Embler <andrew@concrete5.org>
 * @copyright  Copyright (c) 2003-2012 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 */
class Controller extends BlockController
{
    protected $btCacheBlockRecord = true;
    protected $btTable = 'btCoreStackDisplay';
    protected $btIsInternal = true;
    protected $btCacheSettingsInitialized = false;

    public function getBlockTypeDescription()
    {
        return t("Proxy block for stacks added through the UI.");
    }

    public function getBlockTypeName()
    {
        return t("Stack Display");
    }

    public function getOriginalBlockID()
    {
        return $this->bOriginalID;
    }

    public function getSearchableContent() 
    {
        $searchableContent = '';
        $stack = Stack::getByID($this->stID);
        if (is_object($stack)) {
            $blocks = $stack->getBlocks();
            if (!empty($blocks)) {
                foreach ($blocks as $block) {
                    if (method_exists($block->instance, 'getSearchableContent')) {
                        $searchableContent .= $block->instance->getSearchableContent();
                    }
                }
            }
        }
        return $searchableContent;
    }

    public function getImportData($blockNode, $page)
    {
        $args = array();
        $content = (string) $blockNode->stack;
        $stack = Stack::getByName($content);
        $args['stID'] = 0;
        if (is_object($stack)) {
            $args['stID'] = $stack->getCollectionID();
        }

        return $args;
    }

    public function isValidControllerTask($method, $parameters = array())
    {
        $b = $this->findBlockForAction($method, $parameters);

        return !empty($b);
    }

    public function runAction($action, $parameters = array())
    {
        $b = $this->findBlockForAction($action, $parameters);
        if (empty($b)) {
            return;
        }

        $controller = $b->getController();

        return $controller->runAction($action, $parameters);
    }

    public function findBlockForAction($method, $parameters)
    {
        $stack = Stack::getByID($this->stID);
        if (!is_object($stack)) {
            return null;
        }
        $blocks = $stack->getBlocks();
        foreach ($blocks as $b) {
            $controller = $b->getController();
            if ($controller->isValidControllerTask($method, $parameters)) {
                return $b;
            }
        }

        return null;
    }

    public function export(\SimpleXMLElement $blockNode)
    {
        $stack = Stack::getByID($this->stID);
        if (is_object($stack)) {
            $cnode = $blockNode->addChild('stack');
            $node = dom_import_simplexml($cnode);
            $no = $node->ownerDocument;
            $node->appendChild($no->createCDataSection($stack->getCollectionName()));
        }
    }

    public function on_page_view($page)
    {
        $stack = Stack::getByID($this->stID);
        if (!is_object($stack)) {
            return false;
        }
        $p = new Permissions($stack);
        if ($p->canViewPage()) {
            $blocks = $stack->getBlocks();
            foreach ($blocks as $b) {
                $bp = new Permissions($b);
                if ($bp->canViewBlock()) {
                    $btc = $b->getInstance();
                    if ('Controller' != get_class($btc)) {
                        $btc->outputAutoHeaderItems();
                    }
                    $csr = $b->getCustomStyle();
                    if (is_object($csr)) {
                        $css = $csr->getCSS();
                        if ($css !== '') {
                            $styleHeader = $csr->getStyleWrapper($css);
                            $btc->addHeaderItem($styleHeader);
                        }
                    }
                    $btc->runTask('on_page_view', array($page));
                }
            }
        }
    }

    protected function setupCacheSettings()
    {
        if ($this->btCacheSettingsInitialized || Page::getCurrentPage()->isEditMode()) {
            return;
        }

        $this->btCacheSettingsInitialized = true;

        //Block cache settings are only as good as the weakest cached item inside. So loop through and check.
        $btCacheBlockOutput = true;
        $btCacheBlockOutputOnPost = true;
        $btCacheBlockOutputLifetime = 0;

        $stack = Stack::getByID($this->stID);
        if (!is_object($stack)) {
            return false;
        }

        $p = new Permissions($stack);
        if ($p->canViewPage()) {
            $blocks = $stack->getBlocks();
            foreach ($blocks as $b) {
                $btCacheBlockOutput = $btCacheBlockOutput && $b->cacheBlockOutput();
                $btCacheBlockOutputOnPost = $btCacheBlockOutputOnPost && $b->cacheBlockOutputOnPost();

                //As soon as we find something which cannot be cached, entire block cannot be cached, so stop checking.
                if (!$btCacheBlockOutput) {
                    return;
                }

                if ($expires = $b->getBlockOutputCacheLifetime()) {
                    if ($expires && $btCacheBlockOutputLifetime < $expires) {
                        $btCacheBlockOutputLifetime = $expires;
                    }
                }
            }
        }

        $this->btCacheBlockOutput = $btCacheBlockOutput;
        $this->btCacheBlockOutputOnPost = $btCacheBlockOutputOnPost;
        $this->btCacheBlockOutputLifetime = $btCacheBlockOutputLifetime;
    }

    public function cacheBlockOutput()
    {
        $this->setupCacheSettings();

        return $this->btCacheBlockOutput;
    }

    public function cacheBlockOutputOnPost()
    {
        $this->setupCacheSettings();

        return $this->btCacheBlockOutputOnPost;
    }

    public function getBlockTypeCacheOutputLifetime()
    {
        $this->setupCacheSettings();

        return $this->btCacheBlockOutputLifetime;
    }
}
