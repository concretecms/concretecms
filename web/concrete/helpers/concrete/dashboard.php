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
		return $cp->canViewPage();
	}
	
	
	public function canAccessComposer() {
		$c = Page::getByPath('/dashboard/composer', 'ACTIVE');
		$cp = new Permissions($c);
		return $cp->canViewPage();
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
	
	public function getDashboardPaneHeaderWrapper($title = false, $help = false, $span = 'span16', $includeDefaultBody = true, $navigatePages = array(), $upToPage = false) {
		if (!$span) {
			$span = 'span16';
		}
		$html = '<div class="ccm-ui"><div class="row"><div class="' . $span . '"><div class="ccm-pane">';
		$html .= self::getDashboardPaneHeader($title, $help, $navigatePages, $upToPage);
		if ($includeDefaultBody) {
			$html .= '<div class="ccm-pane-body ccm-pane-body-footer">';
		}
		return $html;
	}
	
	public function getDashboardPaneHeader($title = false, $help = false, $navigatePages = array(), $upToPage = false) {
		$c = Page::getCurrentPage();
		$vt = Loader::helper('validation/token');
		$token = $vt->generate('access_quick_nav');

		$currentMenu = array();
		$nh = Loader::helper('navigation');
		$trail = $nh->getTrailToCollection($c);
		if (count($trail) > 1 || count($navigatePages) > 1 || is_object($upToPage)) { 
			$parent = Page::getByID($c->getCollectionParentID());
			if (count($trail) > 1 && (!is_object($upToPage))) {
				$upToPage = Page::getByID($parent->getCollectionParentID());
			}
			Loader::block('autonav');
			$subpages = array();
			if ($navigatePages !== -1) { 
				if (count($navigatePages) > 0) { 
					$subpages = $navigatePages;
				} else { 
					$subpages = AutonavBlockController::getChildPages($parent);
				}
			}
			
			$subpagesP = array();
			foreach($subpages as $sc) {
				$cp = new Permissions($sc);
				if ($cp->canViewPage()) { 
					$subpagesP[] = $sc;
				}
			}
			
			if (count($subpagesP) > 0 || is_object($upToPage)) { 
				$relatedPages = '<div id="ccm-page-navigate-pages-content" style="display: none">';
				$relatedPages .= '<ul class="ccm-navigate-page-menu">';
		
				foreach($subpagesP as $sc) { 
		
					if ($c->getCollectionPath() == $sc->getCollectionPath() || (strpos($c->getCollectionPath(), $sc->getCollectionPath()) == 0) && strpos($c->getCollectionPath(), $sc->getCollectionPath()) !== false) {
						$class= 'nav-selected';
					} else {
						$class = '';
					}
					
					$relatedPages .= '<li class="' . $class . '"><a href="' . $nh->getLinkToCollection($sc, false, true) . '">' . $sc->getCollectionName() . '</a></li>';
				}
		
				if ($upToPage) { 
					$relatedPages .= '<li class="ccm-menu-separator"></li>';
					$relatedPages .= '<li><a href="' . $nh->getLinkToCollection($upToPage, false, true) . '">' . t('&lt; Back to %s', $upToPage->getCollectionName()) . '</a></li>';
				}
				$relatedPages .= '</ul>';
				$relatedPages .= '</div>';
				$navigateTitle = $parent->getCollectionName();
			}
		}
		

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
		
		if (is_array($help)) {
			$help = $help[0] . '<br/><br/><a href="' . $help[1] . '" class="btn small" target="_blank">' . t('Learn More') . '</a>';
		}
		
		if (isset($relatedPages)) { 
			$html .= '<li><a href="javascript:void(0)" onmouseover="ccm_togglePopover(event, this)" class="ccm-icon-navigate-pages" title="' . $navigateTitle . '" id="ccm-page-navigate-pages">' . t('Help') . '</a></li>';
		}
		
		if ($help) {
			$html .= '<li><span style="display: none" id="ccm-page-help-content">' . $help . '</span><a href="javascript:void(0)" onclick="ccm_togglePopover(event, this)" class="ccm-icon-help" title="' . t('Help') . '" id="ccm-page-help">' . t('Help') . '</a></li>';
		}
		if (Config::get('TOOLBAR_QUICK_NAV_BEHAVIOR') != 'disabled') {
			$html .= '<li><a href="javascript:void(0)" id="ccm-add-to-quick-nav" onclick="ccm_toggleQuickNav(' . $c->getCollectionID() . ',\'' . $token . '\')" class="' . $class . '">' . t('Add to Favorites') . '</a></li>';
		}
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
		$obj->displayCaption = false;
		
		if (defined('WHITE_LABEL_DASHBOARD_BACKGROUND_FEED') && WHITE_LABEL_DASHBOARD_BACKGROUND_FEED != '') {
			$image = WHITE_LABEL_DASHBOARD_BACKGROUND_FEED . '/' . $filename;
		} else if (defined('WHITE_LABEL_DASHBOARD_BACKGROUND_SRC') && WHITE_LABEL_DASHBOARD_BACKGROUND_SRC != '') {
			$image = WHITE_LABEL_DASHBOARD_BACKGROUND_SRC;
			if ($image == 'none') {
				$image = '';
			}
		} else {
			$obj->checkData = true;
			$imageSetting = Config::get('DASHBOARD_BACKGROUND_IMAGE');
			if ($imageSetting == 'custom') {
				$fo = File::getByID(Config::get('DASHBOARD_BACKGROUND_IMAGE_CUSTOM_FILE_ID'));
				if (is_object($fo)) {
					$image = $fo->getRelativePath();
				}
			} else if ($imageSetting == 'none') {
				$image = '';
			} else { 
				$image = DASHBOARD_BACKGROUND_FEED . '/' . $filename;
				$obj->displayCaption = true;
			}
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
				$pageP = new Permissions($page);
				if ($pageP->canRead()) { 
					if (!$page->getAttribute("exclude_nav")) {
						if ($page->getPackageID() > 0) {
							$packagepages[] = $page;
						} else {
							$corepages[] = $page;
						}
					}
				} else {
					continue;
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
					<li><a href="<?=Loader::helper('navigation')->getLinkTocollection($page, false, true)?>"><?=t($page->getCollectionName())?></a><span><?=t($page->getCollectionName())?> <?=$page->getAttribute('meta_keywords')?></span></li>
				<? } ?>
				
				<?
				if ($page->getCollectionPath() == '/dashboard/system') { ?>
					<li><a href="<?=Loader::helper('navigation')->getLinkTocollection($page, false, true)?>"><?=t('View All')?><span><?=t($page->getCollectionName())?> <?=$page->getAttribute('meta_keywords')?></span></li>
				<?				
				}
				
				foreach($ch2 as $chi) {
					$subpage = Page::getByID($chi); 
					$subpageP = new Permissions($subpage);
					if (!$subpageP->canRead()) {
						continue;
					}

					if ($subpage->getAttribute('exclude_search_index')) {
						continue;
					}
			
					?>
					<li><a href="<?=Loader::helper('navigation')->getLinkTocollection($subpage, false, true)?>"><?=$subpage->getCollectionName()?></a><span><? if ($page->getCollectionPath() != '/dashboard/system') { ?><?=t($page->getCollectionName())?> <?=$page->getAttribute('meta_keywords')?> <? } ?><?=$subpage->getCollectionName()?> <?=$subpage->getAttribute('meta_keywords')?></span></li>
					<? 
				}
				?>
				</ul>
				
				</div>
				<? }
				
				$custHome = Page::getByPath('/dashboard/home');
				$custHomeP = new Permissions($custHome);
				if ($custHomeP->canRead()) {
				?>
				
				<div class="ccm-intelligent-search-results-module ccm-intelligent-search-results-module-onsite">
				
				<h1><?=t('Dashboard Home')?></h1>
				
				
				<ul class="ccm-intelligent-search-results-list">
					<li><a href="<?=View::url('/dashboard/home')?>"><?=t('Customize')?> <span><?=('Customize Dashboard Home')?></span></a></li>
				</ul>
				
				</div>
				
				<? } ?>
				
				<div class="ccm-intelligent-search-results-module ccm-intelligent-search-results-module-loading">
				<h1><?=t('Your Site')?></h1>
				<ul class="ccm-intelligent-search-results-list" id="ccm-intelligent-search-results-list-your-site">
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
				<h1><?=t('Add-Ons')?></h1>
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
				
				<h1><a href="<?=Loader::helper('navigation')->getLinkToCollection($page, false, true)?>"><?=t($page->getCollectionName())?></a></h1>
				
				
				<ul>
				
				<?
				$ch2 = $page->getCollectionChildrenArray(true);
				foreach($ch2 as $chi) {
					$subpage = Page::getByID($chi); 
					$subpageP = new Permissions($subpage);
					if (!$subpageP->canRead()) {
						continue;
					}

					if ($subpage->getAttribute('exclude_nav')) {
						continue;
					}
			
					?>
					<li><a href="<?=Loader::helper('navigation')->getLinkToCollection($subpage, false, true)?>"><?=t($subpage->getCollectionName())?></a></li>
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
			<li><a href="<?=View::url('/dashboard/news')?>"><strong><?=t('News')?></strong></a> – <?=t('Learn about your site and concrete5.')?></li>
			<?
			$systemSettings = Page::getByPath('/dashboard/system');
			$systemSettingsP = new Permissions($systemSettings);
			if ($systemSettingsP->canRead()) { ?>
				<li><a href="<?=View::url('/dashboard/system')?>"><strong><?=t('System &amp; Settings')?></strong></a> – <?=t('Secure and setup your site.')?></li>
			<? } ?>
			<?
			$tpa = new TaskPermission();
			if ($tpa->canInstallPackages()) { ?>
				<li><a href="<?php echo View::url('/dashboard/extend') ?>"><strong><?php echo t("Extend concrete5") ?></strong></a> – 
				<? if (ENABLE_MARKETPLACE_SUPPORT) { ?>
				<?php echo sprintf(t('<a href="%s">Install</a>, <a href="%s">update</a> or download more <a href="%s">themes</a> and <a href="%s">add-ons</a>.'),
					View::url('/dashboard/extend/install'),
					View::url('/dashboard/extend/update'),
					View::url('/dashboard/extend/themes'),
					View::url('/dashboard/extend/add-ons')); ?>
				<? } else { ?>
				<?php echo sprintf(t('<a href="%s">Install</a> or <a href="%s">update</a> packages.'),
					View::url('/dashboard/extend/install'),
					View::url('/dashboard/extend/update'))?>
					
				<? } ?>
			</li>
			<? } ?>
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
				
				<h1><a href="<?=Loader::helper('navigation')->getLinkToCollection($page, false, true)?>"><?=t($page->getCollectionName())?></a></h1>
				
				
				<ul>
				
				<?
				$ch2 = $page->getCollectionChildrenArray(true);
				foreach($ch2 as $chi) {
					$subpage = Page::getByID($chi); 
					$subpageP = new Permissions($subpage);
					if (!$subpageP->canRead()) {
						continue;
					}
					if ($subpage->getAttribute('exclude_nav')) {
						continue;
					}
					
					?>
					<li><a href="<?=Loader::helper('navigation')->getLinkToCollection($subpage, false, true)?>"><?=t($subpage->getCollectionName())?></a></li>
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