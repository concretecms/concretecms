<?php

namespace Concrete\Block\DesktopFeaturedAddon;

use Concrete\Core\Block\BlockController;
use Concrete\Core\Marketplace\RemoteItemList as MarketplaceRemoteItemList;

    /**
     * The controller for the block that displays featured add-ons in the dashboard news overlay.
     *
     * @package Blocks
     * @subpackage Dashboard Featured Add-On
     *
     * @author Andrew Embler <andrew@concretecms.org>
     * @copyright  Copyright (c) 2003-2022 concreteCMS. (http://www.concretecms.org)
     * @license    http://www.concretecms.org/license/     MIT License
     */
    class Controller extends BlockController
    {
        /**
         * @var bool
         */
        protected $btCacheBlockOutput = true;

        /**
         * @var bool
         */
        protected $btCacheBlockOutputOnPost = true;

        /**
         * @var bool
         */
        protected $btCacheBlockOutputForRegisteredUsers = true;

        /**
         * @var int
         */
        protected $btCacheBlockOutputLifetime = 7200;

        /**
         * @var int
         */
        protected $btInterfaceWidth = 300;

        /**
         * @var int
         */
        protected $btInterfaceHeight = 100;

        /**
         * @return string
         */
        public function getBlockTypeDescription()
        {
            return t('Features an add-on from marketplace.concretecms.com.');
        }

        /**
         * @return string
         */
        public function getBlockTypeName()
        {
            return t('Dashboard Featured Add-On');
        }

        /**
         * @return void
         */
        public function view()
        {
            $mri = new MarketplaceRemoteItemList();
            $mri->sortBy('recommended');
            $mri->setItemsPerPage(1);
            $mri->filterByCompatibility(1);
            $mri->setType('addons');
            $mri->execute();
            $items = $mri->getPage();
            if (isset($items[0]) && is_object($items[0])) {
                $this->set('remoteItem', $items[0]);
            }
        }
    }
