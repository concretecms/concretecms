<?php

namespace Concrete\Block\CoreStackDisplay;

use Concrete\Core\Block\BlockController;
use Concrete\Core\Multilingual\Page\Section\Section;
use Concrete\Core\Page\Page;
use Concrete\Core\Page\Stack\Stack;
use Concrete\Core\Permission\Checker;
use Concrete\Core\Statistics\UsageTracker\TrackableInterface;

/**
 * The controller for the stack display block. This is an internal proxy block that is inserted when a stack's contents are displayed in a page.
 *
 * @package Blocks
 * @subpackage Core Stack Display
 *
 * @author Andrew Embler <andrew@concretecms.org>
 * @copyright  Copyright (c) 2003-2022 concretecms. (http://www.concretecms.org)
 * @license    http://www.concretecms.org/license/     MIT License
 */
class Controller extends BlockController implements TrackableInterface
{
    /**
     * @var int|null
     */
    public $stID;

    /**
     * @var bool
     */
    protected $btCacheBlockRecord = true;

    /**
     * @var string
     */
    protected $btTable = 'btCoreStackDisplay';

    /**
     * @var bool
     */
    protected $btIsInternal = true;

    /**
     * @var bool
     */
    protected $btCacheSettingsInitialized = false;

    /**
     * @var int|null
     */
    protected $stIDNeutral;

    /**
     * @var int|null
     */
    protected $bOriginalID;

    /**
     * @return string
     */
    public function getBlockTypeDescription()
    {
        return t('Proxy block for stacks added through the UI.');
    }

    /**
     * @return string
     */
    public function getBlockTypeName()
    {
        return t('Stack Display');
    }

    /**
     * @return int
     */
    public function getOriginalBlockID()
    {
        return $this->bOriginalID;
    }

    /**
     * @return string
     */
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

    /**
     * @param \SimpleXMLElement $blockNode
     * @param Page $page
     *
     * @return array<string, mixed>
     */
    public function getImportData($blockNode, $page)
    {
        $args = [];
        $content = (string) $blockNode->stack;
        $stack = Stack::getByName($content);
        $args['stID'] = 0;
        if (is_object($stack)) {
            $args['stID'] = $stack->getCollectionID();
        }

        return $args;
    }

    /**
     * @param string $method
     * @param mixed[] $parameters
     *
     * @return bool
     */
    public function isValidControllerTask($method, $parameters = [])
    {
        $b = $this->findBlockForAction($method, $parameters);

        return !empty($b);
    }

    /**
     * @param string $action
     * @param mixed[] $parameters
     *
     * @return mixed|void
     */
    public function runAction($action, $parameters = [])
    {
        parent::runAction($action, $parameters); // handles on_page_view

        $b = $this->findBlockForAction($action, $parameters);
        if (empty($b)) {
            return;
        }

        $controller = $b->getController();

        return $controller->runAction($action, $parameters);
    }

    /**
     * @param string $outputContent
     *
     * @return void
     */
    public function registerViewAssets($outputContent = '')
    {
        $stack = $this->getStack(true);
        if ($stack === null) {
            return;
        }

        $blocks = $stack->getBlocks();
        foreach ($blocks as $b) {
            /** @var BlockController|null $controller */
            $controller = $b->getController();
            if ($controller) {
                /** this always returns void */
                $controller->registerViewAssets($outputContent);
            }
        }
    }

    /**
     * @param string $method
     * @param array<string, mixed> $parameters
     *
     * @return \Concrete\Core\Block\Block|null
     */
    public function findBlockForAction($method, $parameters)
    {
        $stack = $this->getStack(true);
        if ($stack === null) {
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

    /**
     * @param \SimpleXMLElement $blockNode
     *
     * @return void
     */
    public function export(\SimpleXMLElement $blockNode)
    {
        $stack = $this->getStack(false);
        if ($stack !== null) {
            $cnode = $blockNode->addChild('stack');
            $node = dom_import_simplexml($cnode);
            $no = $node->ownerDocument;
            $node->appendChild($no->createCDataSection($stack->getCollectionName()));
        }
    }

    /**
     * @param Page $page
     *
     * @return mixed|void
     */
    public function on_page_view($page)
    {
        $stack = $this->getStack(true);
        if ($stack === null) {
            return;
        }
        $p = new Checker($stack);
        /** @phpstan-ignore-next-line */
        if ($p->canViewPage()) {
            $blocks = $stack->getBlocks();
            foreach ($blocks as $b) {
                $bp = new Checker($b);
                /** @phpstan-ignore-next-line */
                if ($bp->canViewBlock()) {
                    $btc = $b->getController();
                    if (get_class($btc) !== 'Controller') {
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
                    $btc->runAction('on_page_view', [$page]);
                }
            }
        }
    }

    /**
     * @return bool
     */
    public function cacheBlockOutput()
    {
        $this->setupCacheSettings();

        return $this->btCacheBlockOutput;
    }

    /**
     * @return bool
     */
    public function cacheBlockOutputOnPost()
    {
        $this->setupCacheSettings();

        return $this->btCacheBlockOutputOnPost;
    }

    /**
     * @return int
     */
    public function getBlockTypeCacheOutputLifetime()
    {
        $this->setupCacheSettings();

        return $this->btCacheBlockOutputLifetime;
    }

    /**
     * @return int|null
     */
    public function getStackID()
    {
        return $this->stID;
    }

    /**
     * @param array<string,mixed> $args
     * @return void
     */
    public function save($args)
    {
        parent::save($args);
        $this->stID = $args['stID'];
    }

    /**
     * @throws \Illuminate\Contracts\Container\BindingResolutionException|\Exception
     *
     * @return void
     */
    protected function load()
    {
        parent::load();
        $this->set('stIDNeutral', null);
        /** @var Stack|false $stack */
        $stack = Stack::getByID($this->stID);
        if ($stack && $stack->isNeutralStack()) {
            $detector = app('multilingual/detector');
            // @var \Concrete\Core\Multilingual\Service\Detector $detector
            if ($detector->isEnabled()) {
                /** @var Section|false $section */
                $section = Section::getCurrentSection();
                if ($section) {
                    $localized = $stack->getLocalizedStack($section);
                    if ($localized) {
                        $this->stIDNeutral = $this->stID;
                        $this->stID = $localized->getCollectionID();
                        $this->set('stIDNeutral', $this->stIDNeutral);
                        $this->set('stID', $this->stID);
                    }
                }
            }
        }
    }

    /**
     * Returns the Stack instance (if found).
     *
     * @param bool $localized set to true to look for a localized version of the stack (if not found return the neutral version)
     *
     * @return Stack|null
     */
    protected function getStack($localized)
    {
        if ($this->stIDNeutral === null || $localized) {
            $result = Stack::getByID($this->stID);
        } else {
            $result = Stack::getByID($this->stIDNeutral);
        }
        /** @var Stack|null $result */

        return $result;
    }

    /**
     * @return void
     */
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

        $stack = $this->getStack(true);
        if ($stack === null) {
            return;
        }

        $p = new Checker($stack);
        /** @phpstan-ignore-next-line */
        if ($p->canViewPage()) {
            $blocks = $stack->getBlocks();
            foreach ($blocks as $b) {
                if ($b->overrideAreaPermissions()) {
                    $btCacheBlockOutput = false;
                    $btCacheBlockOutputOnPost = false;
                    $btCacheBlockOutputLifetime = 0;
                    break;
                }

                $btCacheBlockOutput = $b->cacheBlockOutput();

                $btCacheBlockOutputOnPost = $btCacheBlockOutputOnPost && $b->cacheBlockOutputOnPost();

                //As soon as we find something which cannot be cached, entire block cannot be cached, so stop checking.
                if (!$btCacheBlockOutput) {
                    return;
                }

                $expires = $b->getBlockOutputCacheLifetime();
                if ($expires && $btCacheBlockOutputLifetime < $expires) {
                    $btCacheBlockOutputLifetime = $expires;
                }
            }
        }

        $this->btCacheBlockOutput = $btCacheBlockOutput;
        $this->btCacheBlockOutputOnPost = $btCacheBlockOutputOnPost;
        $this->btCacheBlockOutputLifetime = $btCacheBlockOutputLifetime;
    }
}
