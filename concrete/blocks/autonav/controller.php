<?php

namespace Concrete\Block\Autonav;

use Concrete\Core\Block\Block;
use Concrete\Core\Block\BlockController;
use Concrete\Core\Block\View\BlockView;
use Concrete\Core\Database\Connection\Connection;
use Concrete\Core\Entity\Block\BlockType\BlockType;
use Concrete\Core\Error\UserMessageException;
use Concrete\Core\Feature\Features;
use Concrete\Core\Feature\UsesFeatureInterface;
use Concrete\Core\Http\ResponseFactoryInterface;
use Concrete\Core\Page\Page;
use Concrete\Core\Permission\Checker;
use Doctrine\ORM\EntityManagerInterface;

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * The controller for the Auto-Nav block.
 *
 * @package    Blocks
 * @subpackage Auto-Nav
 *
 * @author     Andrew Embler <andrew@concrete5.org>
 * @author     Jordan Lev
 * @copyright  Copyright (c) 2003-2022 ConcreteCMS. (http://www.concretecms.org)
 * @license    http://www.concretecms.org/license/     MIT License
 */

class Controller extends BlockController implements UsesFeatureInterface
{
    /**
     * @var Page|null
     */
    public $collection;

    /**
     * @var NavItem[]
     */
    public $navArray = [];

    /**
     * @var int[]
     */
    public $cParentIDArray = [];

    /**
     * @var string
     */
    public $displayPages;

    /**
     * @var int|null
     */
    public $displayPagesCID;

    /**
     * @var int
     */
    public $displayPagesIncludeSelf;

    /**
     * @var string
     */
    public $displaySubPages;

    /**
     * @var string
     */
    public $displaySubPageLevels;

    /**
     * @var int
     */
    public $displaySubPageLevelsNum = 0;

    /**
     * @var string
     */
    public $orderBy;

    /**
     * @var int
     */
    public $displayUnavailablePages;

    /**
     * @var bool
     */
    public $haveRetrievedSelf = false;

    /**
     * @var bool
     */
    public $haveRetrievedSelfPlus1 = false;

    /**
     * @var bool
     */
    public $displayUnapproved = false;

    /**
     * @var bool
     */
    public $ignoreExcludeNav = false;

    /**
     * @var int|null
     */
    public $cParentID;

    /**
     * @var string[]
     */
    protected $helpers = ['form', 'validation/token', 'form/page_selector', 'concrete/ui'];

    /**
     * @var int|null
     */
    protected $homePageID;

    /**
     * @var string
     */
    protected $btTable = 'btNavigation';

    /**
     * @var int
     */
    protected $btInterfaceWidth = 700;

    /**
     * @var int
     */
    protected $btInterfaceHeight = 525;

    /**
     * @var bool
     */
    protected $btCacheBlockRecord = true;

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
    protected $btCacheBlockOutputForRegisteredUsers = false;

    /**
     * @var int
     */
    protected $btCacheBlockOutputLifetime = 300;

    /**
     * @var string
     */
    protected $btWrapperClass = 'ccm-ui';

    /**
     * @var string[]
     */
    protected $btExportPageColumns = ['displayPagesCID'];

    /**
     * @var bool
     */
    protected $includeParentItem;

    /**
     * @var int|null
     */
    public $cID;

    /**
     * Instantiates the block controller.
     *
     * @param BlockType $obj |Block $obj
     */
    public function __construct($obj = null)
    {
        if (is_object($obj)) {
            if ($obj instanceof Block) {
                $this->bID = $obj->bID;
            } else {
                $this->bID = null;
            }
        }

        $c = Page::getCurrentPage();
        if (is_object($c)) {
            if ($c->getCollectionPointerOriginalID() > 0) {
                $this->cID = $c->getCollectionPointerOriginalID();
            } else {
                $this->cID = $c->getCollectionID();
            }
            $this->cParentID = $c->getCollectionParentID();
            $this->homePageID = $c->getSiteHomePageID();
        } else {
            $this->homePageID = Page::getHomePageID();
        }
        parent::__construct($obj);
    }

    /**
     * @return string[]
     */
    public function getRequiredFeatures(): array
    {
        return [
            Features::NAVIGATION,
        ];
    }

    /**
     * @return string
     */
    public function getBlockTypeDescription()
    {
        return t('Creates navigation trees and sitemaps.');
    }

    // haveRetrievedSelf is a variable that stores whether or not a particular tree walking operation has retrieved the current page. We use this
    // with subpage modes like enough and enough_plus1

    // displayUnavailablePages allows us to decide whether this autonav block filters out unavailable pages (pages we can't see, are restricted, etc...)
    // or whether they display them, but then restrict them when the page is actually visited
    // TODO - Implement displayUnavailablePages in the btNavigation table, and in the frontend of the autonav block

    /**
     * @return string
     */
    public function getBlockTypeName()
    {
        return t('Auto-Nav');
    }

    /**
     * @param array<string,mixed> $args
     */
    public function save($args)
    {
        $args['displayPagesIncludeSelf'] = isset($args['displayPagesIncludeSelf']) && $args['displayPagesIncludeSelf'] ? 1 : 0;
        $args['displayPagesCID'] = isset($args['displayPagesCID']) && $args['displayPagesCID'] ? $args['displayPagesCID'] : 0;
        $args['displaySubPageLevelsNum'] = isset($args['displaySubPageLevelsNum']) && $args['displaySubPageLevelsNum'] > 0 ? $args['displaySubPageLevelsNum'] : 0;
        $args['displayUnavailablePages'] = isset($args['displayUnavailablePages']) && $args['displayUnavailablePages'] ? 1 : 0;
        parent::save($args);
    }

    /**
     * @return array<string, mixed>
     */
    public function getContent(): array
    {
        // our templates expect a variable not an object
        $con = [];
        foreach (get_object_vars($this) as $key => $value) {
            $con[$key] = $value;
        }

        return $con;
    }

    /**
     * @param Page $c
     *
     * @return Page[]
     */
    public function getChildPages($c): array
    {
        /** @var Connection $db */
        $db = $this->app->make(Connection::class);
        $r = $db->executeQuery(
            'select cID from Pages where cParentID = ? order by cDisplayOrder asc',
            [$c->getCollectionID()]
        );
        $pages = [];
        $rows = $r->fetchAllAssociative();
        foreach ($rows as $row) {
            $pages[] = Page::getByID($row['cID'], 'ACTIVE');
        }

        return $pages;
    }

    /**
     * New and improved version of "generateNav()" function.
     * Use this unless you need to maintain backwards compatibility with older custom templates.
     *
     * Pass in TRUE for the $ignore_exclude_nav arg if you don't want to exclude any pages
     *  (for both the "exclude_nav" and "exclude_subpages_from_nav" attribute).
     * This is useful for breadcrumb nav menus, for example.
     *
     * Historical note: this must stay a function that gets called by the view templates
     * (as opposed to just having the view() method set the variables)
     * because we need to maintain the generateNav() function for backwards compatibility with
     * older custom templates... and that function unfortunately has side-effects so it cannot
     * be called more than once per request (otherwise there will be duplicate items in the nav menu).
     *
     * @param bool $ignore_exclude_nav
     *
     * @return \stdClass[]
     */
    public function getNavItems($ignore_exclude_nav = false)
    {
        if (!is_object($this->collection)) {
            $c = Page::getCurrentPage();
            if (!is_object($c)) {
                return [];
            }
        } else {
            $c = $this->collection;

            // let's use the ID of the collection passed in $this->collection
            if ($this->collection instanceof \Concrete\Core\Page\Page) {
                $this->cID = $this->collection->getCollectionID();
            }
        }
        //Create an array of parent cIDs so we can determine the "nav path" of the current page
        $inspectC = $c;
        $selectedPathCIDs = [$inspectC->getCollectionID()];
        $parentCIDnotZero = true;

        while ($parentCIDnotZero) {
            $cParentID = $inspectC->getCollectionParentID();
            if (!(int) $cParentID) {
                $parentCIDnotZero = false;
            } else {
                if ($cParentID != $this->homePageID) {
                    $selectedPathCIDs[] = $cParentID; //Don't want home page in nav-path-selected
                }
                $inspectC = Page::getByID($cParentID, 'ACTIVE');
            }
        }

        $this->ignoreExcludeNav = $ignore_exclude_nav;

        //Retrieve the raw "pre-processed" list of all nav items (before any custom attributes are considered)
        $allNavItems = $this->generateNav();

        //Remove excluded pages from the list (do this first because some of the data prep code needs to "look ahead" in the list)
        $includedNavItems = [];
        $excluded_parent_level = 9999; //Arbitrarily high number denotes that we're NOT currently excluding a parent (because all actual page levels will be lower than this)
        $exclude_children_below_level = 9999; //Same deal as above. Note that in this case "below" means a HIGHER number (because a lower number indicates higher placement in the sitemp -- e.g. 0 is top-level)
        foreach ($allNavItems as $ni) {
            $_c = $ni->getCollectionObject();
            $current_level = $ni->getLevel();

            if ($this->excludeFromNavViaAttribute($_c) && ($current_level <= $excluded_parent_level)) {
                $excluded_parent_level = $current_level;
                $exclude_page = true;
            } else {
                if (($current_level > $excluded_parent_level) || ($current_level > $exclude_children_below_level)) {
                    $exclude_page = true;
                } else {
                    $excluded_parent_level = 9999; //Reset to arbitrarily high number to denote that we're no longer excluding a parent
                    $exclude_children_below_level = $_c->getAttribute(
                        'exclude_subpages_from_nav'
                    ) ? $current_level : 9999;
                    $exclude_page = false;
                }
            }

            if (!$exclude_page || $this->ignoreExcludeNav) {
                $includedNavItems[] = $ni;
            }
        }

        //Prep all data and put it into a clean structure so markup output is as simple as possible
        $navItems = [];
        $navItemCount = count($includedNavItems);
        for ($i = 0; $i < $navItemCount; $i++) {
            $ni = $includedNavItems[$i];
            $_c = $ni->getCollectionObject();
            $current_level = $ni->getLevel();

            //Link target (e.g. open in new window)
            $target = $ni->getTarget();
            $target = empty($target) ? '_self' : $target;

            //Link URL
            $pageLink = false;
            if ($_c->getAttribute('replace_link_with_first_in_nav')) {
                $subPage = $_c->getFirstChild(); //Note: could be a rare bug here if first child was excluded, but this is so unlikely (and can be solved by moving it in the sitemap) that it's not worth the trouble to check
                if ($subPage instanceof Page) {
                    $pageLink = $this->app->make('helper/navigation')->getLinkToCollection(
                        $subPage
                    ); //We could optimize by instantiating the navigation helper outside the loop, but this is such an infrequent attribute that I prefer code clarity over performance in this case
                }
            }
            if (!$pageLink) {
                $pageLink = $ni->getURL();
            }

            //Current/ancestor page
            $selected = false;
            $path_selected = false;
            if ($c->getCollectionID() == $_c->getCollectionID()) {
                $selected = true; //Current item is the page being viewed
                $path_selected = true;
            } elseif (in_array($_c->getCollectionID(), $selectedPathCIDs)) {
                $path_selected = true; //Current item is an ancestor of the page being viewed
            }

            //Calculate difference between this item's level and next item's level so we know how many closing tags to output in the markup
            $next_level = isset($includedNavItems[$i + 1]) ? $includedNavItems[$i + 1]->getLevel() : 0;
            $levels_between_this_and_next = $current_level - $next_level;

            //Determine if this item has children (can't rely on $ni->hasChildren() because it doesn't ignore excluded items!)
            $has_children = $next_level > $current_level;

            //Calculate if this is the first item in its level (useful for CSS classes)
            $prev_level = isset($includedNavItems[$i - 1]) ? $includedNavItems[$i - 1]->getLevel() : -1;
            $is_first_in_level = $current_level > $prev_level;

            //Calculate if this is the last item in its level (useful for CSS classes)
            $is_last_in_level = true;
            for ($j = $i + 1; $j < $navItemCount; $j++) {
                if ($includedNavItems[$j]->getLevel() == $current_level) {
                    //we found a subsequent item at this level (before this level "ended"), so this is NOT the last in its level
                    $is_last_in_level = false;
                    break;
                }
                if ($includedNavItems[$j]->getLevel() < $current_level) {
                    //we found a previous level before any other items in this level, so this IS the last in its level
                    $is_last_in_level = true;
                    break;
                }
            } //If loop ends before one of the "if" conditions is hit, then this is the last in its level (and $is_last_in_level stays true)

            //Custom CSS class
            $attribute_class = $_c->getAttribute('nav_item_class');
            $attribute_class = empty($attribute_class) ? '' : $attribute_class;

            //Page ID stuff
            $item_cid = $_c->getCollectionID();
            $is_home_page = $item_cid && $item_cid == Page::getHomePageID($item_cid);

            //Package up all the data
            $navItem = new \stdClass();
            $navItem->url = $pageLink;
            $translate = $this->get('translate');
            $name = (isset($translate) && $translate == true) ? t($ni->getName()) : $ni->getName();
            $text = $this->app->make('helper/text');
            $navItem->name = $text->entities($name);
            $navItem->target = $target;
            $navItem->level = $current_level + 1; //make this 1-based instead of 0-based (more human-friendly)
            $navItem->subDepth = $levels_between_this_and_next;
            $navItem->hasSubmenu = $has_children;
            $navItem->isFirst = $is_first_in_level;
            $navItem->isLast = $is_last_in_level;
            $navItem->isCurrent = $selected;
            $navItem->inPath = $path_selected;
            $navItem->attrClass = $attribute_class;
            $navItem->isHome = $is_home_page;
            $navItem->cID = $item_cid;
            $navItem->cObj = $_c;
            $navItems[] = $navItem;
        }

        return $navItems;
    }

    /**
     * This function is used by the getNavItems() method to generate the raw "pre-processed" nav items array.
     * It also must exist as a separate function to preserve backwards-compatibility with older autonav templates.
     * Warning: this function has side-effects -- if this gets called twice, items will be duplicated in the nav structure!
     *
     * @throws \Doctrine\DBAL\Driver\Exception
     * @throws \Doctrine\DBAL\Exception
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     *
     * @return NavItem[]
     */
    public function generateNav(): array
    {
        // Initialize Nav Array
        $this->navArray = [];

        if (isset($this->displayPagesCID) && !$this->app->make('helper/validation/numbers')->integer($this->displayPagesCID)) {
            $this->displayPagesCID = 0;
        }

        switch ($this->orderBy) {
            case 'display_asc':
                $orderBy = 'order by Pages.cDisplayOrder asc';
                break;
            case 'display_desc':
                $orderBy = 'order by Pages.cDisplayOrder desc';
                break;
            default:
                $orderBy = '';
                break;
        }
        $level = 0;
        switch ($this->displayPages) {
            case 'current':
                $cParentID = $this->cParentID;
                if ($cParentID < 1) {
                    $cParentID = 1;
                }
                break;
            case 'top':
                // top level actually has ID 1 as its parent, since the home page is effectively alone at the top
                $cParentID = $this->homePageID;
                break;
            case 'above':
                $cParentID = $this->getParentParentID();
                break;
            case 'below':
                $cParentID = $this->cID;
                break;
            case 'second_level':
                $cParentID = $this->getParentAtLevel(2);
                break;
            case 'third_level':
                $cParentID = $this->getParentAtLevel(3);
                break;
            case 'custom':
                $cParentID = $this->displayPagesCID;
                break;
            default:
                $cParentID = 1;
                break;
        }

        if ($cParentID != null) {
            if ($this->displaySubPages == 'relevant' || $this->displaySubPages == 'relevant_breadcrumb') {
                $this->populateParentIDArray($this->cID);
            }

            $this->getNavigationArray($cParentID, $orderBy, $level);

            $shouldIncludeParentItem = $this->shouldIncludeParentItem();
            if ($shouldIncludeParentItem === null) {
                // if we're at the top level we add home to the beginning
                $shouldIncludeParentItem = ($cParentID == Page::getHomePageID($cParentID));
            }
            if ($shouldIncludeParentItem) {
                if ($this->displayUnapproved) {
                    $tc1 = Page::getByID($cParentID);
                } else {
                    $tc1 = Page::getByID($cParentID, 'ACTIVE');
                }
                $niRow = [];
                $niRow['cvName'] = $tc1->getCollectionName();
                $niRow['cID'] = $cParentID;
                $niRow['cvDescription'] = $tc1->getCollectionDescription();
                $niRow['cPath'] = $this->app->make('helper/navigation')->getLinkToCollection($tc1);

                $ni = new NavItem($niRow, 0);
                $ni->setCollectionObject($tc1);

                array_unshift($this->navArray, $ni);
            }
        }

        return $this->navArray;
    }

    /**
     * heh. probably should've gone the simpler route and named this getGrandparentID().
     *
     * @return int
     */
    public function getParentParentID()
    {
        // this has to be the stupidest name of a function I've ever created. sigh
        $cParentID = Page::getCollectionParentIDFromChildID($this->cParentID);

        return $cParentID ?: 0;
    }

    /**
     * @param int $level
     *
     * @return int|null
     */
    public function getParentAtLevel($level)
    {
        // this function works in the following way
        // we go from the current collection up to the top level. Then we find the parent Id at the particular level specified, and begin our
        // autonav from that point

        $this->populateParentIDArray($this->cID);

        $idArray = array_reverse($this->cParentIDArray);
        $this->cParentIDArray = [];
        if ($level - count($idArray) == 0) {
            // This means that the parent ID array is one less than the item
            // we're trying to grab - so we return our CURRENT page as the item to get
            // things under
            return $this->cID;
        }

        if (isset($idArray[$level])) {
            return $idArray[$level];
        }

            return null;
    }

    /** Pupulates the $cParentIDArray instance property.
     * @param int $cID the collection id
     *
     * @return void
     */
    public function populateParentIDArray($cID)
    {
        // returns an array of collection IDs going from the top level to the current item
        $cParentID = Page::getCollectionParentIDFromChildID($cID);
        if (is_numeric($cParentID)) {
            if (!in_array($cParentID, $this->cParentIDArray)) {
                $this->cParentIDArray[] = $cParentID;
            }
            if ($cParentID > 0) {
                $this->populateParentIDArray($cParentID);
            }
        }
    }

    /**
     * @param int $cParentID
     * @param string $orderBy
     * @param int $currentLevel
     *
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     * @throws \Doctrine\DBAL\Exception
     * @throws \Doctrine\DBAL\Driver\Exception
     *
     * @return void
     */
    public function getNavigationArray($cParentID, $orderBy, $currentLevel)
    {
        // Check if the parent page is excluded or if it has been set to exclude child pages
        foreach ($this->navArray as $ni) {
            if ($ni->getCollectionID() == $cParentID && $this->ignoreExcludeNav === false) {
                if ($ni->getCollectionObject()->getAttribute('exclude_nav') == 1 || $ni->getCollectionObject()->getAttribute('exclude_subpages_from_nav') == 1) {
                    return;
                }
            }
        }

        // increment all items in the nav array with a greater $currentLevel
        foreach ($this->navArray as $ni) {
            if ($ni->getLevel() + 1 < $currentLevel) {
                $ni->hasChildren = true;
            }
        }

        /** @var Connection $db */
        $db = $this->app->make(Connection::class);
        $navSort = [];
        $sorted_array = [];
        $navObjectNames = [];

        $q = "select Pages.cID from Pages where cIsTemplate = 0 and cIsActive = 1 and cParentID = '{$cParentID}' {$orderBy}";
        $rows = $db->executeQuery($q)->fetchAllAssociative();
        if (count($rows) > 0) {
            foreach ($rows as $row) {
                if ($this->displaySubPages != 'relevant_breadcrumb' || (in_array(
                    $row['cID'],
                    $this->cParentIDArray
                ) || $row['cID'] == $this->cID)
                ) {
                    /*
                    if ($this->haveRetrievedSelf) {
                        // since we've already retrieved self, and we're going through again, we set plus 1
                        $this->haveRetrievedSelfPlus1 = true;
                    } else
                     */

                    if ($this->haveRetrievedSelf && $cParentID == $this->cID) {
                        $this->haveRetrievedSelfPlus1 = true;
                    } else {
                        if ($row['cID'] == $this->cID) {
                            $this->haveRetrievedSelf = true;
                        }
                    }
                    if ($this->displayUnapproved) {
                        $tc = Page::getByID($row['cID']);
                    } else {
                        $tc = Page::getByID($row['cID'], 'ACTIVE');
                    }

                    $displayPage = $this->displayPage($tc);

                    if ($displayPage) {
                        $niRow = [];
                        $niRow['cvName'] = $tc->getCollectionName();
                        $niRow['cID'] = $row['cID'];
                        $niRow['cvDescription'] = $tc->getCollectionDescription();
                        $niRow['cPath'] = $this->app->make('helper/navigation')->getLinkToCollection($tc);
                        $niRow['cPointerExternalLink'] = $tc->getCollectionPointerExternalLink();
                        $niRow['cPointerExternalLinkNewWindow'] = $tc->openCollectionPointerExternalLinkInNewWindow();
                        $dateKey = strtotime($tc->getCollectionDatePublic());

                        $ni = new NavItem($niRow, (int) $currentLevel);
                        $ni->setCollectionObject($tc);
                        // $this->navArray[] = $ni;
                        $navSort[$niRow['cID']] = $dateKey;
                        $sorted_array[$niRow['cID']] = $ni;

                        $_c = $ni->getCollectionObject();
                        $object_name = $_c->getCollectionName();
                        $navObjectNames[$niRow['cID']] = $object_name;
                    }
                }
            }
            // end while -- sort navSort

            // Joshua's Huge Sorting Crap
            if ($navSort) {
                $sortit = 0;
                if ($this->orderBy == 'alpha_desc') {
                    $navObjectNames = array_map('strtolower', $navObjectNames);
                    arsort($navObjectNames);
                    $sortit = 1;
                }

                if ($this->orderBy == 'alpha_asc') {
                    $navObjectNames = array_map('strtolower', $navObjectNames);
                    asort($navObjectNames);
                    $sortit = 1;
                }
                $sortArray = $navObjectNames;
                if ($this->orderBy == 'chrono_asc') {
                    asort($navSort);
                    $sortit = 1;
                    $sortArray = $navSort;
                }
                if ($this->orderBy == 'chrono_desc') {
                    arsort($navSort);
                    $sortit = 1;
                    $sortArray = $navSort;
                }
                if ($this->orderBy == 'display_desc' || $this->orderBy == 'display_asc') {
                    $sortit = 1;
                }

                if ($sortit) {
                    foreach ($sortArray as $sortCID => $sortnameaction) {
                        // create sorted_array
                        $this->navArray[] = $sorted_array[$sortCID];

                        //############start_recursive_crap
                        $retrieveMore = false;
                        if ($this->displaySubPages == 'all') {
                            if ($this->displaySubPageLevels == 'all' || ($this->displaySubPageLevels == 'custom' && $this->displaySubPageLevelsNum > $currentLevel)) {
                                $retrieveMore = true;
                            }
                        } else {
                            if (($this->displaySubPages == 'relevant' || $this->displaySubPages == 'relevant_breadcrumb') && (in_array(
                                $sortCID,
                                $this->cParentIDArray
                            ) || $sortCID == $this->cID)
                            ) {
                                if ($this->displaySubPageLevels == 'enough' && $this->haveRetrievedSelf == false) {
                                    $retrieveMore = true;
                                } else {
                                    if ($this->displaySubPageLevels == 'enough_plus1' && $this->haveRetrievedSelfPlus1 == false) {
                                        $retrieveMore = true;
                                    } else {
                                        if ($this->displaySubPageLevels == 'all' || ($this->displaySubPageLevels == 'custom' && $this->displaySubPageLevelsNum > $currentLevel)) {
                                            $retrieveMore = true;
                                        }
                                    }
                                }
                            }
                        }
                        if ($retrieveMore) {
                            $this->getNavigationArray($sortCID, $orderBy, $currentLevel + 1);
                        }
                        //############end_recursive_crap
                    }
                }
            }
            // End Joshua's Huge Sorting Crap
        }
    }

    /**
     * @param Page $c
     *
     * @return mixed
     */
    public function excludeFromNavViaAttribute($c)
    {
        return $c->getAttribute('exclude_nav');
    }

    /**
     * @throws UserMessageException
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     *
     * @return \Concrete\Core\Http\Response
     */
    public function action_preview_pane()
    {
        $token = $this->app->make('token');
        if (!$token->validate('ccm-autonav-preview')) {
            throw new UserMessageException($token->getErrorMessage());
        }
        $bt = $this->app->make(EntityManagerInterface::class)->find(BlockType::class, $this->getBlockTypeID());
        /** @var Controller $btc */
        $btc = $bt->getController();
        $post = $this->request->request;
        $btc->collection = $this->getCollectionObject();
        $btc->orderBy = $post->get('orderBy');
        $btc->cID = $post->get('cID');
        $btc->displayPages = $post->get('displayPages');
        $btc->displaySubPages = $post->get('displaySubPages');
        $btc->displaySubPageLevels = $post->get('displaySubPageLevels');
        $btc->displaySubPageLevelsNum = $post->get('displaySubPageLevelsNum');
        $btc->displayUnavailablePages = $post->get('displayUnavailablePages');
        if ($btc->displayPages === 'custom') {
            $btc->displayPagesCID = $post->get('displayPagesCID') ? $post->get('displayPagesCID') : Page::getHomePageID();
            $btc->displayPagesIncludeSelf = $post->get('displayPagesIncludeSelf');
        }
        $bv = new BlockView($bt);
        ob_start();
        $bv->render('view');
        $content = ob_get_contents();
        ob_end_clean();

        return $this->app->make(ResponseFactoryInterface::class)->create($content);
    }

    /**
     * @return bool|null
     */
    public function shouldIncludeParentItem()
    {
        return $this->includeParentItem;
    }

    /**
     * @param bool $includeParentItem
     *
     * @return void
     */
    public function setIncludeParentItem($includeParentItem)
    {
        $this->includeParentItem = $includeParentItem;
    }

    /**
     * @param Page $tc
     *
     * @return bool
     */
    protected function displayPage($tc)
    {
        $tcv = $tc->getVersionObject();
        if ((!is_object($tcv)) || (!$tcv->isApprovedNow() && !$this->displayUnapproved)) {
            return false;
        }

        if ($this->displayUnavailablePages == false) {
            $tcp = new Checker($tc);
            /** @phpstan-ignore-next-line */
            if (!$tcp->canRead()) {
                return false;
            }
        }

        return true;
    }
}
