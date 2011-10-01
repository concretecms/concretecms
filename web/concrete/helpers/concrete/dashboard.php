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

	public function inDashboard() {
		$c = Page::getCurrentPage();
		return strpos($c->getCollectionPath(), '/dashboard') === 0;
	}


	public function getDashboardAndSearchMenus() {
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
				
				
				$ch2 = $page->getCollectionChildrenArray(true);
				?>
				
				<div class="ccm-intelligent-search-results-module ccm-intelligent-search-results-module-onsite">
				
				<h1><?=$page->getCollectionName()?></h1>
				
				
				<ul class="ccm-intelligent-search-results-list">
				<? if (count($ch2) == 0) { ?>
					<li><a href="<?=Loader::helper('navigation')->getLinkTocollection($page)?>"><?=$page->getCollectionName()?></a><span><?=$page->getCollectionName()?></span></li>
				<? } ?>
				
				<?
				foreach($ch2 as $chi) {
					$subpage = Page::getByID($chi); 
					if ($subpage->getAttribute('exclude_search_index')) {
						continue;
					}
			
					?>
					<li><a href="<?=Loader::helper('navigation')->getLinkTocollection($subpage)?>"><?=$subpage->getCollectionName()?></a><span><?=$page->getCollectionName()?> <?=$subpage->getCollectionName()?></span></li>
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
				
				
				<div class="ccm-intelligent-search-results-module ccm-intelligent-search-results-module-offsite ccm-intelligent-search-results-module-loading">
				<h1><?=t('Help')?></h1>
				<ul class="ccm-intelligent-search-results-list" id="ccm-intelligent-search-results-list-help">
				</ul>
				
				</div>
			
				<div class="ccm-intelligent-search-results-module ccm-intelligent-search-results-module-offsite ccm-intelligent-search-results-module-loading">
				<h1><?=t('Add-Ons &amp; Themes')?></h1>
				<ul class="ccm-intelligent-search-results-list" id="ccm-intelligent-search-results-list-marketplace">
				</ul>
				
				</div>
				
			</div>
			
			<div id="ccm-dashboard-overlay">
			<div id="ccm-dashboard-overlay-core">
			<div class="ccm-dashboard-overlay-inner" id="ccm-dashboard-overlay-main">
			
				<div class="ccm-dashboard-overlay-module">
				
				<h1><a href="<?=View::url('/dashboard')?>"><?=t('Dashboard Home')?></a></h1>
				
				
				<ul>
					<li><a href="<?=View::url('/dashboard/home')?>"><?=t('Customize')?></a></li>
				</ul>
				
				</div>
			
			
			<?php
			
			foreach($corepages as $page) {
				?>
				
				<div class="ccm-dashboard-overlay-module">
				
				<h1><a href="<?=Loader::helper('navigation')->getLinkToCollection($page)?>"><?=$page->getCollectionName()?></a></h1>
				
				
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
			<? if (count($packagepages) > 0) { ?>
			<div id="ccm-dashboard-overlay-footer">
			<div class="ccm-dashboard-overlay-inner" id="ccm-dashboard-overlay-packages">
			<?php
			
			
			foreach($packagepages as $page) {
				?>
				
				<div class="ccm-dashboard-overlay-module">
				
				<h1><a href="<?=Loader::helper('navigation')->getLinkToCollection($page)?>"><?=$page->getCollectionName()?></a></h1>
				
				
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