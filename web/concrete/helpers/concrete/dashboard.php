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
	
	public function enableDashboardBackNavigation($pagePath = false, $title = false) {
		if ($pagePath) {
			$page = Page::getByPath($pagePath, 'ACTIVE');
		} else {
			$c = Page::getCurrentPage();
			$page = Page::getByID($c->getCollectionParentID(), 'ACTIVE');
		}
		
		if (!$title) {
			$title = t($page->getCollectionName());
		}
		
		$this->backNavigationPage = $page;
		$this->backNavigationTitle = $title;		
	}
	
	public function getDashboardPaneHeader($title = false, $help = false) {
		$c = Page::getCurrentPage();
		$vt = Loader::helper('validation/token');
		$token = $vt->generate('access_quick_nav');
		$html = '<div class="ccm-pane-header">';
		if (isset($this->backNavigationPage)) { 
			$html .= '<div class="ccm-dashboard-pane-header-up"><a href="' . Loader::helper('navigation')->getLinkToCollection($this->backNavigationPage) . '">&lt; to ' .$this->backNavigationTitle . '</a></div>';
		}
		
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
		
		if ($help) {
			$html .= '<li><a href="javascript:void(0)" onclick="ccm_togglePageHelp(event, this)" class="ccm-icon-help" title="' . t('Help') . '" id="ccm-page-help" data-content="' . $help . '">' . t('Help') . '</a></li>';
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
	
	public function getDashboardBackgroundImageSRC() {
		$feed = array();
		// this feed is an array of standard PHP objects with a SRC, a caption, and a URL
		// allow for a custom white-label feed
		$filename = date('Ymd') . '.jpg';
		
		if (defined('WHITE_LABEL_DASHBOARD_BACKGROUND_FEED') && WHITE_LABEL_DASHBOARD_BACKGROUND_FEED != '') {
			$image = WHITE_LABEL_DASHBOARD_BACKGROUND_FEED . '/' . $filename;
		} else if (defined('WHITE_LABEL_DASHBOARD_BACKGROUND_SRC') && WHITE_LABEL_DASHBOARD_BACKGROUND_SRC != '') {
			$image = WHITE_LABEL_DASHBOARD_BACKGROUND_SRC;
		} else {
			$image = DASHBOARD_BACKGROUND_FEED . '/' . $filename;
		}
		return $image;
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
					<li><a href="<?=Loader::helper('navigation')->getLinkToCollection($subpage)?>"><?=$subpage->getCollectionName()?></a></li>
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
			<li><a href="<?=View::url('/dashboard')?>"><strong><?=t('News')?></strong></a><?=t(' – Learn about your site and concrete5')?></li>
			<li><a href="<?=View::url('/dashboard/system')?>"><strong><?=t('System &amp; Settings')?></strong></a><?=t(' – Secure and setup your site.')?></li>
			<li><a href="<?=View::url('/dashboard/extend')?>"><strong><?=t('Extend concrete5')?></strong></a> – <a href="<?=View::url('/dashboard/extend/install')?>"><?=t('Install')?></a>, <a href="<?=View::url('/dashboard/extend/update')?>"><?=t('update')?></a> <?=t('or download more')?> <a href="<?=View::url('/dashboard/extend/themes')?>"><?=t('themes')?></a> <?=t('and')?> <a href="<?=View::url('/dashboard/extend/add-ons')?>"><?=t('add-ons')?></a>.</li>
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
					<li><a href="<?=Loader::helper('navigation')->getLinkToCollection($subpage)?>"><?=$subpage->getCollectionName()?></a></li>
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