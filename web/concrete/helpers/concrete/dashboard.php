<?
/**
 * @package Helpers
 * @category Concrete
 * @author Andrew Embler <andrew@concrete5.org>
 * @copyright  Copyright (c) 2003-2008 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 */

/**
 * Functions for use with the C5 dashboard.
 * @package Helpers
 * @category Concrete
 * @author Andrew Embler <andrew@concrete5.org>
 * @copyright  Copyright (c) 2003-2008 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 */

defined('C5_EXECUTE') or die("Access Denied.");
class ConcreteDashboardHelper {

	/** 
	 * Checks to see if a user has access to the C5 dashboard.
	 */
	public function canRead() {
		$c = Page::getByPath('/dashboard', 'ACTIVE');
		$cp = new Permissions($c);
		return $cp->canRead();
	}
	
	
	public function canAccessComposer() {
		$c = Page::getByPath('/dashboard/composer', 'ACTIVE');
		$cp = new Permissions($c);
		return $cp->canRead();
	}

	public function inDashboard($page = false) {
		if (!$page) {
			$page = Page::getCurrentPage();
		}
		return strpos($page->getCollectionPath(), '/dashboard') === 0;
	}
	
	public function getDashboardPaneFooterWrapper($includeDefaultBody = true) {
		$html = '</div></div></div></div>';
		if ($includeDefaultBody) {
			$html .= '</div>';
		}
		return $html;
	}
	
	public function getDashboardPaneHeaderWrapper($title = false, $help = false, $span = 'span16', $includeDefaultBody = true) {
		if (!$span) {
			$span = 'span16';
		}
		$html = '<div class="ccm-ui"><div class="row"><div class="' . $span . '"><div class="ccm-pane">';
		$html .= self::getDashboardPaneHeader($title, $help);
		if ($includeDefaultBody) {
			$html .= '<div class="ccm-pane-body ccm-pane-body-footer">';
		}
		return $html;
	}
	
	public function getDashboardPaneHeader($title = false, $help = false) {
		$c = Page::getCurrentPage();
		$vt = Loader::helper('validation/token');
		$token = $vt->generate('access_quick_nav');

		$currentMenu = array();
		$backTo = false;
		
		$relatedPages = '<div id="ccm-page-navigate-pages-content" style="display: none">';
		$relatedPages .= '<ul class="ccm-navigate-page-menu">';
		$relatedPages .= '<li><a href="">Page 1</a></li>';
		$relatedPages .= '<li><a href="">Page 2</a></li>';
		$relatedPages .= '<li><a href="">Page 3</a></li>';
		$relatedPages .= '<li><a href="">Page 4</a></li>';
		$relatedPages .= '<li><a href="">Page 5</a></li>';
		$relatedPages .= '<li><a href="">Page 6</a></li>';
		$relatedPages .= '<li><a href="">Page 7</a></li>';
		$relatedPages .= '<li><a href="">Page 8</a></li>';
		$relatedPages .= '<li><a href="">Page 8</a></li>';
		$relatedPages .= '<li><a href="">Page 9</a></li>';
		$relatedPages .= '<li class="ccm-menu-separator"></li>';
		if ($backTo) { 
		
		} else { 
			$parent = Page::getByID($c->getCollectionParentID());
			$relatedPages .= '<li><a href="' . Loader::helper('navigation')->getLinkToCollection($parent) . '">' . t('&lt; Back to %s', $parent->getCollectionName()) . '</a></li>';
		}
		$relatedPages .= '</ul>';
		$relatedPages .= '</div>';				
		

		$html = '<div class="ccm-pane-header">';
		
		$html .= $relatedPages;
		
		$class = 'ccm-icon-favorite';
		$u = new User();
		$quicknav = unserialize($u->config('QUICK_NAV_BOOKMARKS'));
		if (is_array($quicknav)) {
			if (in_array($c->getCollectionID(), $quicknav)) {
				$class = 'ccm-icon-favorite-selected';	
			}
		}
		$html .= '<ul class="ccm-pane-header-icons">';
		if (!$help) {
			$ih = Loader::helper('concrete/interface/help');
			$pageHelp = $ih->getPages();
			if (isset($pageHelp[$c->getCollectionPath()])) {
				$help = $pageHelp[$c->getCollectionPath()];
			}
		}
		
		$html .= '<li><a href="javascript:void(0)" onclick="ccm_togglePopover(event, this)" class="ccm-icon-navigate-pages" title="' . t('Navigate') . '" id="ccm-page-navigate-pages">' . t('Help') . '</a></li>';
		
		if ($help) {
			$html .= '<li><span style="display: none" id="ccm-page-help-content">' . $help . '</span><a href="javascript:void(0)" onclick="ccm_togglePopover(event, this)" class="ccm-icon-help" title="' . t('Help') . '" id="ccm-page-help">' . t('Help') . '</a></li>';
		}
		$html .= '<li><a href="javascript:void(0)" id="ccm-add-to-quick-nav" onclick="ccm_toggleQuickNav(' . $c->getCollectionID() . ',\'' . $token . '\')" class="' . $class . '">' . t('Add to Favorites') . '</a></li>';
		$html .= '<li><a href="javascript:void(0)" onclick="ccm_closeDashboardPane(this)" class="ccm-icon-close">' . t('Close') . '</a></li>';
		$html .= '</ul>';
		if (!$title) {
			$title = $c->getCollectionName();
		}
		$html .= '<h3>' . $title . '</h3>';
		$html .= '</div>';
		return $html;
	}
	
	public function getDashboardBackgroundImage() {
		$feed = array();
		// this feed is an array of standard PHP objects with a SRC, a caption, and a URL
		// allow for a custom white-label feed
		$filename = date('Ymd') . '.jpg';
		$obj = new stdClass;
		$obj->checkData = false;
		
		if (defined('WHITE_LABEL_DASHBOARD_BACKGROUND_FEED') && WHITE_LABEL_DASHBOARD_BACKGROUND_FEED != '') {
			$image = WHITE_LABEL_DASHBOARD_BACKGROUND_FEED . '/' . $filename;
		} else if (defined('WHITE_LABEL_DASHBOARD_BACKGROUND_SRC') && WHITE_LABEL_DASHBOARD_BACKGROUND_SRC != '') {
			$image = WHITE_LABEL_DASHBOARD_BACKGROUND_SRC;
		} else {
			$image = DASHBOARD_BACKGROUND_FEED . '/' . $filename;
			$obj->checkData = true;
		}
		$obj->filename = $filename;
		$obj->image = $image;
		return $obj;
	}

	public function getDashboardAndSearchMenus() {
		if (isset($_SESSION['dashboardMenus'])) {
			
			return $_SESSION['dashboardMenus'];
		}
		
		ob_start(); ?>
			<div id="ccm-intelligent-search-results">
			<?
			$page = Page::getByPath('/dashboard');
			$children = $page->getCollectionChildrenArray(true);
			
			$packagepages = array();
			$corepages = array();
			foreach($children as $ch) {
				$page = Page::getByID($ch);
				if (!$page->getAttribute("exclude_nav")) {
					if ($page->getPackageID() > 0) {
						$packagepages[] = $page;
					} else {
						$corepages[] = $page;
					}
				}
			
				if ($page->getAttribute('exclude_search_index')) {
					continue;
				}
				
				
				if ($page->getCollectionPath() == '/dashboard/system') {
					$ch2 = $page->getCollectionChildrenArray();
				} else {
					$ch2 = $page->getCollectionChildrenArray(true);
				}
				?>
				
				<div class="ccm-intelligent-search-results-module ccm-intelligent-search-results-module-onsite">
				
				<h1><?=t($page->getCollectionName())?></h1>
				
				
				<ul class="ccm-intelligent-search-results-list">
				<? if (count($ch2) == 0) { ?>
					<li><a href="<?=Loader::helper('navigation')->getLinkTocollection($page)?>"><?=t($page->getCollectionName())?></a><span><?=t($page->getCollectionName())?> <?=$page->getAttribute('meta_keywords')?></span></li>
				<? } ?>
				
				<?
				if ($page->getCollectionPath() == '/dashboard/system') { ?>
					<li><a href="<?=Loader::helper('navigation')->getLinkTocollection($page)?>"><?=t('View All')?><span><?=t($page->getCollectionName())?> <?=$page->getAttribute('meta_keywords')?></span></li>
				<?				
				}
				
				foreach($ch2 as $chi) {
					$subpage = Page::getByID($chi); 
					if ($subpage->getAttribute('exclude_search_index')) {
						continue;
					}
			
					?>
					<li><a href="<?=Loader::helper('navigation')->getLinkTocollection($subpage)?>"><?=$subpage->getCollectionName()?></a><span><? if ($page->getCollectionPath() != '/dashboard/system') { ?><?=t($page->getCollectionName())?> <?=$page->getAttribute('meta_keywords')?> <? } ?><?=$subpage->getCollectionName()?> <?=$subpage->getAttribute('meta_keywords')?></span></li>
					<? 
				}
				?>
				</ul>
				
				</div>
				<? } ?>
				
				<div class="ccm-intelligent-search-results-module ccm-intelligent-search-results-module-onsite">
				
				<h1><?=t('Dashboard Home')?></h1>
				
				
				<ul class="ccm-intelligent-search-results-list">
					<li><a href="<?=View::url('/dashboard/home')?>"><?=t('Customize')?> <span><?=('Customize Dashboard Home')?></span></a></li>
				</ul>
				
				</div>
				
				<? if (ENABLE_INTELLIGENT_SEARCH_HELP) { ?>
				<div class="ccm-intelligent-search-results-module ccm-intelligent-search-results-module-offsite ccm-intelligent-search-results-module-loading">
				<h1><?=t('Help')?></h1>
				<ul class="ccm-intelligent-search-results-list" id="ccm-intelligent-search-results-list-help">
				</ul>
				</div>
				<? } ?>
				
				<? if (ENABLE_INTELLIGENT_SEARCH_MARKETPLACE) { ?>
				<div class="ccm-intelligent-search-results-module ccm-intelligent-search-results-module-offsite ccm-intelligent-search-results-module-loading">
				<h1><?=t('Add-Ons &amp; Themes')?></h1>
				<ul class="ccm-intelligent-search-results-list" id="ccm-intelligent-search-results-list-marketplace">
				</ul>
				</div>
				<? } ?>				
			</div>
			
			<div id="ccm-dashboard-overlay">
			<div id="ccm-dashboard-overlay-core">
			<div class="ccm-dashboard-overlay-inner" id="ccm-dashboard-overlay-main">
			
			<?php
			
			foreach($corepages as $page) {
				?>
				
				<div class="ccm-dashboard-overlay-module">
				
				<h1><a href="<?=Loader::helper('navigation')->getLinkToCollection($page)?>"><?=t($page->getCollectionName())?></a></h1>
				
				
				<ul>
				
				<?
				$ch2 = $page->getCollectionChildrenArray(true);
				foreach($ch2 as $chi) {
					$subpage = Page::getByID($chi); 
					if ($subpage->getAttribute('exclude_nav')) {
						continue;
					}
			
					?>
					<li><a href="<?=Loader::helper('navigation')->getLinkToCollection($subpage)?>"><?=t($subpage->getCollectionName())?></a></li>
					<? 
				}
				?>
				</ul>
				
				</div>
				
				<?
			}
				
			?>
			
			
			</div>
			</div>
			<div id="ccm-dashboard-overlay-misc" <? if (count($packagepages) == 0)  { ?>class="ccm-dashboard-overlay-misc-rounded" <? } ?>>
			<div class="ccm-dashboard-overlay-inner">
			<ul>
			<li><a href="<?=View::url('/dashboard')?>"><strong><?=t('News')?></strong></a> – <?=t('Learn about your site and concrete5')?></li>
			<li><a href="<?=View::url('/dashboard/system')?>"><strong><?=t('System &amp; Settings')?></strong></a> – <?=t('Secure and setup your site.')?></li>
			<li><a href="<?php echo View::url('/dashboard/extend') ?>"><strong><?php echo t("Extend concrete5") ?></strong></a> – 
			<?php echo sprintf(t('<a href="%s">Install</a>, <a href="%s">update</a> or download more <a href="%s">themes</a> and <a href="%s">add-ons</a>.'),
				View::url('/dashboard/extend/install'),
				View::url('/dashboard/extend/update'),
				View::url('/dashboard/extend/themes'),
				View::url('/dashboard/extend/add-ons')); ?>
			</li>
			</ul>
			</div>
			</div>
			<? if (count($packagepages) > 0) { ?>
			<div id="ccm-dashboard-overlay-footer">
			<div class="ccm-dashboard-overlay-inner" id="ccm-dashboard-overlay-packages">
			<?php
			
			
			foreach($packagepages as $page) {
				?>
				
				<div class="ccm-dashboard-overlay-module">
				
				<h1><a href="<?=Loader::helper('navigation')->getLinkToCollection($page)?>"><?=t($page->getCollectionName())?></a></h1>
				
				
				<ul>
				
				<?
				$ch2 = $page->getCollectionChildrenArray(true);
				foreach($ch2 as $chi) {
					$subpage = Page::getByID($chi); 
					if ($subpage->getAttribute('exclude_nav')) {
						continue;
					}
					
					?>
					<li><a href="<?=Loader::helper('navigation')->getLinkToCollection($subpage)?>"><?=t($subpage->getCollectionName())?></a></li>
					<? 
				}
				?>
				</ul>
				
				</div>
				
				<?
			}
				
			?>
			</div>
			</div>
			<? } ?>
			</div>
		<?
			$contents = ob_get_contents();
			ob_end_clean();
			return str_replace(array("\n", "\r", "\t"), "", $contents);
	
	}
}