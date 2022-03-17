<?php

namespace Concrete\Block\CoreScrapbookDisplay;

use Concrete\Core\Block\Block;
use Concrete\Core\Block\BlockController;
use Concrete\Core\Block\View\BlockViewTemplate;

/**
 * The controller for the core scrapbook display block. This block is automatically used when a block is copied into a
 * page from a clipboard. It is a proxy block.
 *
 * @package    Blocks
 * @subpackage Core Scrapbook/Clipboard Display
 *
 * @author     Andrew Embler <andrew@concretecms.org>
 * @copyright  Copyright (c) 2003-2022 concreteCMS. (http://www.concretecms.org)
 * @license    http://www.concretecms.org/license/     MIT License
 */
class Controller extends BlockController
{
    /**
     * @var string
     */
    protected $btTable = 'btCoreScrapbookDisplay';

    /**
     * @var bool
     */
    protected $btIsInternal = true;

    /**
     * @var BlockController|null
     */
    protected $passthruController;

    /**
     * @var int Original Block ID
     */
    protected $bOriginalID;

    /**
     * {@inheritdoc}
     *
     * @return bool
     */
    public function ignorePageThemeGridFrameworkContainer()
    {
        $bc = $this->getScrapbookBlockController();
        if (is_object($bc)) {
            return $bc->ignorePageThemeGridFrameworkContainer();
        }

        return false;
    }

    /**
     * @return string
     */
    public function getBlockTypeDescription()
    {
        return t('Proxy block for blocks pasted through the scrapbook.');
    }

    /**
     * @return string
     */
    public function getBlockTypeName()
    {
        return t('Scrapbook Display');
    }

    /**
     * @return int
     */
    public function getOriginalBlockID()
    {
        return $this->bOriginalID;
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     *
     * @return BlockController|false|null
     */
    public function getScrapbookBlockController()
    {
        if (!isset($this->passthruController)) {
            $b = Block::getByID($this->bOriginalID);
            $bc = ($b) ? $b->getInstance() : false;
            $this->passthruController = $bc;
        }

        return $this->passthruController;
    }

    /**
     * @param \SimpleXMLElement $blockNode
     *
     * @throws \Doctrine\DBAL\Exception
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     *
     * @return void
     */
    public function export(\SimpleXMLElement $blockNode)
    {
        $b = Block::getByID($this->bOriginalID);
        /** @var BlockController|null $bc */
        $bc = $b->getInstance();
        if ($bc) {
            $blockNode->addAttribute('type', $b->getBlockTypeHandle());
            $blockNode->addAttribute('name', $b->getBlockName());
            if ($b->getBlockFilename() != '') {
                $blockNode->addAttribute('custom-template', $b->getBlockFilename());
            }
            $bc->export($blockNode);
        }
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     *
     * @return string
     */
    public function getSearchableContent()
    {
        $bc = $this->getScrapbookBlockController();

        if ($bc && method_exists($bc, 'getSearchableContent')) {
            return $bc->getSearchableContent();
        }

        return  '';
    }

    /**
     * @param string $method
     * @param array<string,mixed> $parameters
     *
     * @return mixed[]
     */
    public function getPassThruActionAndParameters($method, $parameters = [])
    {
        /** @phpstan-ignore-next-line */
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

    /**
     * @param string $method
     * @param mixed[] $parameters
     *
     * @throws \Doctrine\DBAL\Exception
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     *
     * @return bool
     */
    public function isValidControllerTask($method, $parameters = [])
    {
        $bc = $this->getScrapbookBlockController();

        if (is_object($bc)) {
            return $bc->isValidControllerTask($method, $parameters);
        }

        return false;
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     *
     * @return void|mixed
     */
    public function on_start()
    {
        $bc = $this->getScrapbookBlockController();

        if (is_object($bc)) {
            return $bc->on_start();
        }
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     *
     *  @return void|mixed
     */
    public function on_before_render()
    {
        $bc = $this->getScrapbookBlockController();

        if (is_object($bc)) {
            return $bc->on_before_render();
        }
    }

    /**
     * @param string $action
     * @param mixed[] $parameters
     *
     * @throws \Doctrine\DBAL\Exception
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     *
     * @return mixed
     */
    public function runAction($action, $parameters = [])
    {
        $bc = $this->getScrapbookBlockController();

        if (is_object($bc)) {
            return $bc->runAction($action, $parameters);
        }

        return null;
    }

    /**
     * @param string $outputContent
     *
     * @throws \Doctrine\DBAL\Exception
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     *
     * @return void
     */
    public function registerViewAssets($outputContent = '')
    {
        $bc = $this->getScrapbookBlockController();

        if (is_object($bc) && is_callable([$bc, 'registerViewAssets'])) {
            $bc->registerViewAssets($outputContent);
        }
    }

    /**
     * @param \Concrete\Core\Page\Page $page
     *
     * @throws \Doctrine\DBAL\Exception
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     *
     * @return mixed|void
     */
    public function on_page_view($page)
    {
        $bc = $this->getScrapbookBlockController();

        if ($bc && method_exists($bc, 'on_page_view')) {
            return $bc->on_page_view($page);
        }
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     *
     * @return void
     */
    public function outputAutoHeaderItems()
    {
        $b = Block::getByID($this->bOriginalID);
        if ($b) {
            $b = $this->getBlockObject();
            $bvt = new BlockViewTemplate($b);
            $bvt->registerTemplateAssets();
        }
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     *
     * @return bool
     */
    public function cacheBlockOutput()
    {
        $bc = $this->getScrapbookBlockController();

        if ($bc) {
            return $bc->cacheBlockOutput();
        }

        return false;
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     *
     * @return bool
     */
    public function cacheBlockOutputForRegisteredUsers()
    {
        $bc = $this->getScrapbookBlockController();

        if ($bc) {
            return $bc->cacheBlockOutputForRegisteredUsers();
        }

        return false;
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     *
     * @return bool
     */
    public function cacheBlockOutputOnPost()
    {
        $bc = $this->getScrapbookBlockController();

        if ($bc) {
            return $bc->cacheBlockOutputOnPost();
        }

        return false;
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     *
     * @return int
     */
    public function getBlockTypeCacheOutputLifetime()
    {
        $bc = $this->getScrapbookBlockController();

        if ($bc) {
            return $bc->getBlockTypeCacheOutputLifetime();
        }

        return $this->btCacheBlockOutputLifetime;
    }
}
