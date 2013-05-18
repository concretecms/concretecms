<?
/**
 * @access private
 * @package Helpers
 * @category Concrete
 * @author Andrew Embler <andrew@concrete5.org>
 * @copyright  Copyright (c) 2003-2008 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 */

/**
 * @access private
 * @package Helpers
 * @category Concrete
 * @author Andrew Embler <andrew@concrete5.org>
 * @copyright  Copyright (c) 2003-2008 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 */
 
defined('C5_EXECUTE') or die("Access Denied.");
Loader::model('page_list');
Loader::model('collection_types');
class ConcreteDashboardSitemapHelper {

	protected $autoOpenNodes = true;

	function showSystemPages() {
		return $_SESSION['dsbSitemapShowSystem'] == 1;
	}

	public function setAutoOpenNodes($autoOpen) {
		$this->autoOpenNodes = $autoOpen;
	}

	function getSubNodes($cID) {
		$db = Loader::db();
		
		$obj = new stdClass;
		$pl = new PageList();
		$pl->sortByDisplayOrder();
		if (ConcreteDashboardSitemapHelper::showSystemPages()) {
			$pl->includeSystemPages();
			$pl->includeInactivePages();
		}
		$pl->filterByParentID($cID);
		$pl->displayUnapprovedPages();
		$total = $pl->getTotal();
		if ($cID == 1) {
			$results = $pl->get();			
		} else {
			$pl->setItemsPerPage(SITEMAP_PAGES_LIMIT);
			$results = $pl->getPage();
		}
		
		$nodes = array();
		foreach($results as $c) {
			$n = ConcreteDashboardSitemapHelper::getNode($c);
			if ($n != false) {
				$nodes[] = $n;
			}
		}

		return $nodes;
	}

	function getNode($cItem) {
		if (!is_object($cItem)) {
			$cID = $cItem;
			$c = Page::getByID($cID, 'RECENT');
		} else {
			$cID = $cItem->getCollectionID();
			$c = $cItem;
		}
		
		$cp = new Permissions($c);
		$canEditPageProperties = $cp->canEditPageProperties();
		$canEditPageSpeedSettings = $cp->canEditPageSpeedSettings();
		$canEditPagePermissions = $cp->canEditPagePermissions();
		$canEditPageDesign = ($cp->canEditPageTheme() || $cp->canEditPageType());
		$canViewPageVersions = $cp->canViewPageVersions();
		$canDeletePage = $cp->canDeletePage();
		$canAddSubpages = $cp->canAddSubpage();
		$canAddExternalLinks = $cp->canAddExternalLink();
		
		$nodeOpen = false;
		$openNodeArray = explode(',', str_replace('_', '', $_COOKIE['ccmsitemap-expand']));
		if (is_array($openNodeArray)) {
			if (in_array($cID, $openNodeArray)) {
				$nodeOpen = true;
			}
		}
		
		$cls = ($c->getNumChildren() > 0) ? "folder" : "file";
		$leaf = ($c->getNumChildren() > 0) ? false : true;
		$numSubpages = ($c->getNumChildren()  > 0) ? $c->getNumChildren()  : '';
		
		$cvName = ($c->getCollectionName()) ? $c->getCollectionName() : '(No Title)';
		
		$ct = CollectionType::getByID($c->getCollectionTypeID());
		$isInTrash = $c->isInTrash();
		
		$isTrash = $c->getCollectionPath() == TRASH_PAGE_PATH;
		if ($isTrash || $isInTrash) { 
			$pk = PermissionKey::getByHandle('empty_trash');
			if (!$pk->validate()) {
				return false;
			}
		}
		
		$cIcon = $c->getCollectionIcon();
		if (!$cIcon) {
			if ($numSubpages > 0) {
				$cIcon = ASSETS_URL_IMAGES . '/dashboard/sitemap/folder.png';
			} else {
				$cIcon = ASSETS_URL_IMAGES . '/dashboard/sitemap/document.png';
			}
		}

		$cAlias = $c->isAlias();
		$cPointerID = $c->getCollectionPointerID();
		if ($cAlias) {
			if ($cPointerID > 0) {
				$cIcon = ASSETS_URL_IMAGES . '/icons/alias.png';
				$cAlias = 'POINTER';
				$cID = $c->getCollectionPointerOriginalID();
			} else {
				$cIcon = ASSETS_URL_IMAGES . '/icons/alias_external.png';
				$cAlias = 'LINK';
			}
		}

		/*
		$node = array(
			'cIcon' => $cIcon,
			'cAlias' => $cAlias,
			'numSubpages'=> $numSubpages,
		);

		*/

		$node = new stdClass;
		$node->title = $cvName;
		if ($numSubpages > 0) {
			$node->isLazy = true;
		}
		if ($cIcon) {
			$node->icon = $cIcon;
		}
		$node->cAlias = $cAlias;
		$node->isInTrash = $isInTrash;
		$node->numSubpages = $numSubpages;
		$node->isTrash = $isTrash;
		$node->cID = $cID;
		$node->key = $cID;
		$node->canEditPageProperties = $canEditPageProperties;
		$node->canEditPageSpeedSettings = $canEditPageSpeedSettings;
		$node->canEditPagePermissions = $canEditPagePermissions;
		$node->canEditPageDesign = $canEditPageDesign;
		$node->canViewPageVersions = $canViewPageVersions;
		$node->canDeletePage = $canDeletePage;
		$node->canAddSubpages = $canAddSubpages;
		$node->canAddExternalLinks = $canAddExternalLinks;

		if ($cID == 1 || ($nodeOpen && $this->autoOpenNodes)) {
			// We open another level
			$node->children = $this->getSubNodes($cID, $level, false, $autoOpenNodes);
		}
		
		return $node;
	}
	/*
	public function getPermissionsNodes($obj) {
		$str = '';
		if ($obj['canEditPageProperties']) {
			$str .= 'tree-node-can-edit-properties="true" ';
		}
		if ($obj['canEditPageSpeedSettings']) {
			$str .= 'tree-node-can-edit-speed-settings="true" ';
		}
		if ($obj['canEditPagePermissions']) {
			$str .= 'tree-node-can-edit-permissions="true" ';
		}
		if ($obj['canEditPageDesign']) {
			$str .= 'tree-node-can-edit-design="true" ';
		}
		if ($obj['canViewPageVersions']) {
			$str .= 'tree-node-can-view-versions="true" ';
		}
		if ($obj['canDeletePage']) {
			$str .= 'tree-node-can-delete="true" ';
		}
		if ($obj['canAddSubpages']) {
			$str .= 'tree-node-can-add-subpages="true" ';
		}
		if ($obj['canAddExternalLinks']) {
			$str .= 'tree-node-can-add-external-links="true" ';
		}
		return $str;
	}
	
	public function outputRequestHTML($instanceID, $display_mode, $select_mode, $req) {
		$nodeID = $req->nodeID;
		$spID = ($this->selectedPageID > 0) ? $this->selectedPageID : 'false';
		$c = Page::getByID($req->nodeID, 'ACTIVE');
		if ($display_mode == 'explore') {
			$nav = Loader::helper('navigation');
			$trail = $nav->getTrailToCollection($c);
			$trail = array_reverse($trail);
			$this->html .= '<div id="ccm-sitemap-bc"><ul>';
			foreach($trail as $t) {
				if ($select_mode == '') {
					$this->html .= '<li><a href="' . View::url('/dashboard/sitemap/explore', $t->getCollectionID()) . '"><span>' . $t->getCollectionName() . '</span></a></li>';
				} else {
					$this->html .= '<li><a href="javascript:void(0)" onclick="ccmSitemapExploreNode(\'' . $instanceID . '\', \''. $display_mode . '\', \'' . $select_mode . '\',' . $t->getCollectionID() . ',\'' . $spID . '\')">' . $t->getCollectionName() . '</a></li>';
				}
			}
			$cnode = $this->getNode($c);
			$this->html .= '<li class="ccm-sitemap-current-level-title">';
			$this->html .= '<div sitemap-display-mode="' . $display_mode . '" ' . $this->getPermissionsNodes($cnode) . ' sitemap-select-mode="' . $select_mode . '" sitemap-instance-id="' . $instanceID . '" class="tree-label" rel="' . DIR_REL . '/' . DISPATCHER_FILENAME . '?cID=' . $c->getCollectionID() . '" tree-node-alias="0" ';
			$this->html .= 'selected-page-id="' . $this->selectedPageID . '" tree-node-children="' . $c->getNumChildren() . '" ';
			$this->html .= 'tree-node-title="' . htmlspecialchars($c->getCollectionName()) . '" id="tree-label' . $c->getCollectionID() . '">';
			$this->html .= '<span>' . $c->getCollectionName() . '</span></div></li>';
			$this->html .= '</ul></div>';
		}
		if ($display_mode == 'full' || $display_mode == '') {
			$this->html .= '<div class="dropzone tree-dz' . $nodeID . '" tree-parent="' . $nodeID . '" id="tree-dz' . $nodeID . '-sub"></div>';
		}
		$moveableClass = '';
		for ($i = 0; $i < count($req->results); $i++) {
			$ri = $req->results[$i];
			$typeClass = 'tree-node-document';
			$treeNodeType = 'document';
			$labelClass = "tree-label";
			if ($ri['numSubpages'] > 0) {
				$treeNodeType = 'folder';
				if ($display_mode == 'full' || $display_mode == '') {
					$typeClass = 'tree-node-folder';
				} else {
					$typeClass = 'tree-node-folder-explore';
				}
			}
			$customIconSrc = "";
			if ($ri['cIcon']) {
				$customIconSrc = ' style="background-image: url(' . $ri['cIcon'] . ')"';
			}
			$cAlias = $ri['cAlias'];
			$canDrag = ($ri['id'] > 1) ? "true" : "false";
			
			$this->html .= '<li ' . $this->getPermissionsNodes($ri) . ' tree-node-intrash="' . $ri['isInTrash'] . '" tree-node-istrash="' . $ri['isTrash'] . '" tree-node-type="' . $treeNodeType . '" draggable="' . $canDrag . '" class="tree-node ' . $typeClass . ' tree-branch' . $nodeID . '" id="tree-node' . $ri['id'] . '"' . $customIconSrc . '>';
			
			if ($ri['numSubpages'] > 0) {
				$subPageStr = ($ri['id'] == 1) ? '' : ' <span class="ccm-sitemap-num-subpages">(' . $ri['numSubpages'] . ')</span>';

				$this->html .= '<img src="' . ASSETS_URL_IMAGES . '/spacer.gif" width="16" height="16" class="handle ' . $moveableClass . '" />';

				if ($display_mode == 'explore') {
					$this->html .= ($select_mode == 'move_copy_delete' || $select_mode == 'select_page') ? '<a href="javascript:void(0)" onclick="ccmSitemapExploreNode(\'' . $instanceID . '\', \'' . $display_mode . '\', \'' . $select_mode . '\', ' . $ri["id"] . ',\'' . $spID . '\')">' : '<a href="' . View::url('/dashboard/sitemap/explore', $ri['id']) . '">' ;
				} else {
					$this->html .= '<a href="javascript:toggleSub(\'' . $instanceID . '\',\'' . $ri['id'] . '\',\'' . $display_mode . '\',\'' . $select_mode . '\')">';
				}
				$this->html .= '<img src="' . ASSETS_URL_IMAGES . '/dashboard/plus.jpg" width="9" height="9" class="tree-plus" id="tree-collapse' . $ri['id'] . '" /></a>';
				$this->html .= '<div rel="' . DIR_REL . '/' . DISPATCHER_FILENAME . '?cID=' . $ri['id'] . '" class="' . $labelClass . '" tree-node-alias="' . $cAlias . '" ';
				$this->html .= 'selected-page-id="' . $this->selectedPageID . '" ' . $this->getPermissionsNodes($ri) . ' tree-node-intrash="' . $ri['isInTrash'] . '" tree-node-istrash="' . $ri['isTrash'] . '" sitemap-display-mode="' . $display_mode . '" sitemap-select-mode="' . $select_mode . '" sitemap-instance-id="' . $instanceID . '" tree-node-children="' . $ri['numSubpages'] . '" ';
				$this->html .= 'tree-node-title="' . htmlspecialchars($ri['cvName']) . '" id="tree-label' . $ri['id'] . '" ';
				if ($ri['selected']) {
					$this->html .= 'class="tree-label-selected-onload" ';
				}
				$this->html .= '>';
				$this->html .= '<span>' . $ri['cvName'] . $subPageStr . '</span>';


				$this->html .= '</div>';
				if ($display_mode == 'full' || $display_mode == '') {

					// we HAVE to add another <LI> because of jQuery UI's weird drag and drop behavior on Windows
					if (strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE') > -1) {
						$this->html .= '<li>';
					}
					$this->html .= '<ul tree-root-state="closed" tree-root-node-id="' . $ri['id'] . '" tree-root-num-subpages="' . $ri['numSubpages'] . '" id="tree-root' . $ri['id'] . '" selected-page-id="' . $this->selectedPageID . '" sitemap-instance-id="' . $instanceID . '" sitemap-display-mode="' . $display_mode . '" sitemap-select-mode="' . $select_mode . '">';
					if (is_object($ri['subnodes']) && count($ri['subnodes']->results) > 0) {
						$this->outputRequestHTML($instanceID, $display_mode, $select_mode, $ri['subnodes']);
					}
					$this->html .= '</ul>';
				}
			} else {
				$this->html .= '<div tree-node-title="' . htmlspecialchars($ri['cvName']) . '" tree-node-children="' . $ri['numSubpages'] . '" ';
				$this->html .= 'class="' . $labelClass . '" ' . $this->getPermissionsNodes($ri) . ' tree-node-intrash="' . $ri['isInTrash'] . '" tree-node-istrash="' . $ri['isTrash'] . '" tree-node-alias="' . $cAlias . '" ';
				$this->html .= 'selected-page-id="' . $this->selectedPageID . '" sitemap-display-mode="' . $display_mode . '" sitemap-select-mode="' . $select_mode . '" sitemap-instance-id="' . $instanceID . '" id="tree-label' . $ri['id'] . '" rel="' . DIR_REL . '/' . DISPATCHER_FILENAME . '?cID=' . $ri['id'] . '">';
				$this->html .= '<img src="' . ASSETS_URL_IMAGES . '/spacer.gif" width="16" height="16" class="handle ' . $moveableClass . '" /><span>' . $ri['cvName'] . '</span></div>';
			}
			
			$this->html .= '</li>';
			if ($display_mode == 'full' || $display_mode == '') {
				$this->html .= '<div class="dropzone tree-dz' . $nodeID . '" tree-parent="' . $nodeID . '" id="tree-dz' . $ri['id'] . '"></div>';
			}
		}
		
		if ($req->total > count($req->results) && $nodeID > 1) {
			if ($display_mode == 'explore' || $select_mode == 'move_copy_delete' || $select_mode == 'select_page') {
				if ($display_mode == 'explore') { 
					$this->html .= '<li class="ccm-sitemap-explore-paging">' . $req->pageList->displayPagingV2(false, true) . '</li>';
				} else {
					$this->html .= '<li class="ccm-sitemap-explore-paging">' . $req->pageList->displayPagingV2(REL_DIR_FILES_TOOLS_REQUIRED . '/dashboard/sitemap_data', true, array('node' => $nodeID)) . '</li>';
				}
			} else {
				$drillDownAction = ($req->keywords != null) ? View::url('/dashboard/sitemap/search?cvName=' . $req->keywords . '&selectedSearchField[]=parent&numResults=' . SITEMAP_PAGES_LIMIT . '&' . PAGING_STRING . '=2&cParentAll=1&ccm_order_by=cDisplayOrder&cParentIDSearchField=' . $nodeID) : View::url('/dashboard/sitemap/explore', $nodeID);
				$this->html .= '<li class="ccm-sitemap-more-results">' . t('%s more to display. <a href="%s">View All</a>',  $req->total - count($req->results), $drillDownAction) . '</a></li>';
			}
		}

		return $this->html;
	}
	
	public function setSelectedPageID($cID) {
		$this->selectedPageID = $cID;
	}
	
	function getDroppables($cID) {
		$db = Loader::db();
		$v = array($cID);
		$q = "select cID from Pages where cParentID = ? and cPointerID = 0 or cPointerID is null";
		$r = $db->query($q, $v);
		$drops = array();
		while ($row = $r->fetchRow()) {
			$drops[] = $row['cID'];
		}
		return $drops;
	}

	*/
	
	function canRead() {
		$tp = new TaskPermission();
		return $tp->canAccessSitemap();
	}


}