<?php

namespace Concrete\Block\TopNavigationBar;

use ClassesWithParents\E;
use Concrete\Core\Application\Service\FileManager;
use Concrete\Core\Block\BlockController;
use Concrete\Core\Editor\LinkAbstractor;
use Concrete\Core\Feature\Features;
use Concrete\Core\Feature\UsesFeatureInterface;
use Concrete\Core\File\File;
use Concrete\Core\File\Tracker\FileTrackableInterface;
use Concrete\Core\Html\Service\Navigation as NavigationService;
use Concrete\Core\Multilingual\Page\Section\Section;
use Concrete\Core\Navigation\Breadcrumb\PageBreadcrumbFactory;
use Concrete\Core\Navigation\Item\PageItem;
use Concrete\Core\Navigation\Navigation;
use Concrete\Core\Page\Page;
use Concrete\Core\Permission\Checker;
use HtmlObject\Image;

defined('C5_EXECUTE') or die('Access Denied.');

class Controller extends BlockController implements UsesFeatureInterface, FileTrackableInterface
{
    public $helpers = ['form'];

    public $brandingLogo = 0;
    public $brandingTransparentLogo = 0;
    public $includeBrandLogo = false;
    public $includeBrandText = false;
    public $includeStickyNav = false;
    public $includeNavigationDropdowns = false;
    public $includeSearchInput;

    protected $btInterfaceWidth = 640;
    protected $btInterfaceHeight = 500;
    protected $btTable = 'btTopNavigationBar';
    protected $btCacheBlockRecord = true;
    protected $btCacheBlockOutput = true;
    protected $btCacheBlockOutputOnPost = true;
    protected $btCacheBlockOutputForRegisteredUsers = true;
    protected $btCacheBlockOutputLifetime = 300;
    protected $btExportFileColumns = ['brandingLogo', 'brandingTransparentLogo'];

    /**
     * {@inheritdoc}
     */
    public function getBlockTypeName()
    {
        return t('Top Navigation Bar');
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockTypeDescription()
    {
        return t('Adds a responsive navigation bar with a logo, menu and search.');
    }

    /**
     * {@inheritdoc}
     */
    public function getRequiredFeatures(): array
    {
        return [
            Features::NAVIGATION,
        ];
    }

    public function add()
    {
        $site = $this->app->make('site')->getSite();
        $brandingText = $site->getSiteName();

        $this->set('includeTransparency', false);
        $this->set('includeStickyNav', false);
        $this->set('includeNavigation', true);
        $this->set('includeNavigationDropdowns', false);
        $this->set('includeSearchInput', false);
        $this->set('includeBrandText', true);
        $this->set('includeBrandLogo', false);
        $this->set('brandingLogo', null);
        $this->set('brandingTransparentLogo', null);
        $this->set('searchInputFormActionPageID', null);
        $this->set('brandingText', $brandingText);
        $this->edit();
    }

    public function edit()
    {
        $this->set('fileManager', new FileManager());
        $this->set('editor', $this->app->make('editor'));
    }

    protected function includePageInNavigation(Page $page)
    {
        $checker = new Checker($page);
        if ($checker->canViewPage() && !$page->getAttribute('exclude_nav')) {
            return true;
        }
        return false;
    }

    protected function includeSubPagesInNavigation(Page $page)
    {
        if ($this->includeNavigationDropdowns) {
            $excludeSubPages = $page->getAttribute('exclude_subpages_from_nav');
            if ($excludeSubPages) {
                return false;
            } else {
                return true;
            }
        }
        return false;
    }

    protected function getParentIDsToCurrent(): array
    {
        $current = Page::getCurrentPage();
        $navigationService = $this->app->make(NavigationService::class);
        $trail = $navigationService->getTrailToCollection($current);
        $ids = [];
        foreach ($trail as $ancestor) {
            $ids[] = (int) $ancestor->getCollectionID();
        }

        return $ids;
    }

    protected function getNavigation(): Navigation
    {
        $section = Section::getByLocale(\Localization::getInstance()->getLocale());
        if ($section instanceof Section) {
            $home = \Page::getByID($section->getSiteHomePageID());
        } else {
            $site = $this->app->make('site')->getSite();
            $home = $site->getSiteHomePageObject();
        }
        $children = $home->getCollectionChildren();
        $navigation = new Navigation();

        $current = Page::getCurrentPage();
        $parentIDs = $this->getParentIDsToCurrent();

        foreach ($children as $child) {
            if ($this->includePageInNavigation($child)) {
                $item = new PageItem($child);
                if ($home->getCollectionID() !== $current->getCollectionID() && in_array($child->getCollectionID(), $parentIDs)) {
                    $item->setIsActiveParent(true);
                }
                $item->setIsActive($current->getCollectionID() === $child->getCollectionID());
                if ($this->includeSubPagesInNavigation($child)) {
                    $dropdownChildren = $child->getCollectionChildren();
                    foreach ($dropdownChildren as $dropdownChild) {
                        if ($this->includePageInNavigation($dropdownChild)) {
                            $dropdownChildItem = new PageItem($dropdownChild);
                            if (in_array($dropdownChild->getCollectionID(), $parentIDs)) {
                                $dropdownChildItem->setIsActiveParent(true);
                            }
                            $dropdownChildItem->setIsActive($current->getCollectionID() === $dropdownChild->getCollectionID());
                            $item->addChild($dropdownChildItem);
                        }
                    }
                }
                $navigation->add($item);
            }
        }
        return $navigation;
    }

    public function view()
    {
        $site = $this->app->make('site')->getSite();
        $home = $site->getSiteHomePageObject();

        if ($this->brandingLogo) {
            $logo = File::getByID($this->brandingLogo);
            if ($logo) {
                $this->set('logo', $logo);
            }
        }
        if (!isset($logo)) {
            $this->set('logo', null);
        }
        if ($this->brandingTransparentLogo) {
            $transparentLogo = File::getByID($this->brandingTransparentLogo);
            if ($transparentLogo) {
                $this->set('transparentLogo', $transparentLogo);
            }
        }
        $this->set('navigation', $this->getNavigation());
        $this->set('home', $home);
        if ($this->includeSearchInput) {
            $searchPage = Page::getByID($this->searchInputFormActionPageID);
            if ($searchPage && !$searchPage->isError()) {
                $searchAction = (string) $searchPage->getCollectionLink();
            } else if ($home) {
                $searchAction = (string) $home->getCollectionLink();
            }
            $this->set('searchAction', $searchAction);
        }
    }

    public function save($args)
    {
        $data = [];
        $data['includeNavigation'] = !empty($args['includeNavigation']) ? 1 : 0;
        $data['includeNavigationDropdowns'] = !empty($args['includeNavigationDropdowns']) ? 1 : 0;
        $data['includeTransparency'] = !empty($args['includeTransparency']) ? 1 : 0;
        $data['includeSearchInput'] = !empty($args['includeSearchInput']) ? 1 : 0;
        $data['includeStickyNav'] = !empty($args['includeStickyNav']) ? 1 : 0;

        $data['includeBrandLogo'] = 0;
        $data['includeBrandText'] = 0;

        // This line is for import purposes – in import this is already set this way
        if (isset($args['includeBrandText']) || isset($args['includeBrandLogo'])) {
            $data['includeBrandLogo'] = $args['includeBrandLogo'];
            $data['includeBrandText'] = $args['includeBrandText'];
        } else {
            switch ($args['brandingMode']) {
                case 'logoText':
                    $data['includeBrandLogo'] = 1;
                    $data['includeBrandText'] = 1;
                    break;
                case 'logo':
                    $data['includeBrandLogo'] = 1;
                    break;
                case 'text':
                    $data['includeBrandText'] = 1;
                    break;
            }
        }

        $brandingLogo = 0;
        if (!empty($args['brandingLogo'])) {
            $file = File::getByID($args['brandingLogo']);
            if ($file) {
                $checker = new Checker($file);
                if ($checker->canViewFileInFileManager()) {
                    $brandingLogo = $file->getFileID();
                }
            }
        }
        $brandingTransparentLogo = 0;
        if (!empty($args['brandingTransparentLogo'])) {
            $file = File::getByID($args['brandingTransparentLogo']);
            if ($file) {
                $checker = new Checker($file);
                if ($checker->canViewFileInFileManager()) {
                    $brandingTransparentLogo = $file->getFileID();
                }
            }
        }
        $searchInputFormActionPageID = 0;
        if (!empty($args['searchInputFormActionPageID'])) {
            $searchPage = Page::getByID($args['searchInputFormActionPageID']);
            if ($searchPage && !$searchPage->isError()) {
                $checker = new Checker($searchPage);
                if ($checker->canViewPage()) {
                    $searchInputFormActionPageID = $searchPage->getCollectionID();
                }
            }
        }

        if ($data['includeBrandText']) {
            $data['brandingText'] = $args['brandingText'];
        }
        if ($data['includeBrandLogo']) {
            $data['brandingLogo'] = $brandingLogo;
            $data['brandingTransparentLogo'] = $brandingTransparentLogo;
        }
        if ($data['includeSearchInput']) {
            $data['searchInputFormActionPageID'] = $searchInputFormActionPageID;
        }
        parent::save($data);
    }

    public function getUsedFiles()
    {
        $files = [];
        if (isset($this->brandingLogo) && $this->brandingLogo) {
            $files[] = $this->brandingLogo;
        }
        if (isset($this->brandingTransparentLogo) && $this->brandingTransparentLogo) {
            $files[] = $this->brandingTransparentLogo;
        }
        return $files;
    }
    
    public function getPageItemNavTarget($pageItem) // Respect nav_target Page Attribute & External Link targets
    {
	if (!is_object($pageItem) || !$pageItem instanceof \Concrete\Core\Navigation\Item\PageItem) {
	    return '';
	}
	$page = Page::getByID($pageItem->getPageID());
	
	if ($page->getCollectionPointerExternalLink() != '' && $page->openCollectionPointerExternalLinkInNewWindow()) {
	    $target = '_blank';
	} else {
	    $target = $page->getAttribute('nav_target');
	}
	$target = empty($target) ? '_self' : $target;
	
	return $target;
    }

}
