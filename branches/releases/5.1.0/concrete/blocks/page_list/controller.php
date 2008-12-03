<?php 

	defined('C5_EXECUTE') or die(_("Access Denied."));
	class PageListBlockController extends BlockController {

		protected $btTable = 'btPageList';
		protected $btInterfaceWidth = "430";
		protected $btInterfaceHeight = "300";
		
		/** 
		 * Used for localization. If we want to localize the name/description we have to include this
		 */
		public function getBlockTypeDescription() {
			return t("List pages based on type, area.");
		}
		
		public function getBlockTypeName() {
			return t("Page List");
		}
		
		public function getJavaScriptStrings() {
			return array(
				'feed-name' => t('Please give your RSS Feed a name.')
			);
		}
		
		function getPages($query = null) {
			$db = Loader::db();
			$bID = $this->bID;
			if ($this->bID) {
				$q = "select num, cParentID, cThis, orderBy, ctID, rss from btPageList where bID = '$bID'";
				$r = $db->query($q);
				if ($r) {
					$row = $r->fetchRow();
				}
			} else {
				$row['num'] = $this->num;
				$row['cParentID'] = $this->cParentID;
				$row['cThis'] = $this->cThis;
				$row['orderBy'] = $this->orderBy;
				$row['ctID'] = $this->ctID;
				$row['rss'] = $this->rss;
			}
			

			$cArray = array();

			switch($row['orderBy']) {
				case 'display_asc':
					$orderBy = "order by Pages.cDisplayOrder asc";
					break;
				case 'display_desc':
					$orderBy = "order by Pages.cDisplayOrder desc";
					break;
				case 'chrono_asc':
					$orderBy = "order by cvDatePublic asc";
					break;
				case 'alpha_asc':
					$orderBy = "order by cvName asc";
					break;
				case 'alpha_desc':
					$orderBy = "order by cvName desc";
					break;
				default:
					$orderBy = "order by cvDatePublic desc";
					break;
			}

			$num = (int) $row['num'];

			$cParentID = ($row['cThis']) ? $this->cID : $row['cParentID'];
			
			$filter = "where Pages.cPointerExternalLink is null and Pages.cIsTemplate = 0 ";
			$filter .= " AND  CollectionVersions.cvName!='' ";
			
			if ($row['ctID']) {
				$filter .= "and Pages.ctID = '{$row['ctID']}' ";
			}

			if ($row['cParentID'] != 0) {
				$filter .= "and Pages.cParentID = '{$cParentID}' and Pages.cIsTemplate = 0 ";
			}
			
			if (($query != null) || ($query != "")) {
				$filter .= "and ((";
				$filter .= "CollectionVersions.cvName like '%{$query}%'";
				$filter .= ") or (";
				$filter .= "CollectionVersions.cvDescription like '%{$query}%'";
				$filter .= "))";
			}

			$q = "select DISTINCT Pages.cID from Pages
			left join PagePaths on (Pages.cID = PagePaths.cID)
			left join PagePermissions on (Pages.cID = PagePermissions.cID)
			left join CollectionVersions on (CollectionVersions.cID = Pages.cID and CollectionVersions.cvIsApproved = 1)
			{$filter} {$orderBy} ";

			//echo $q;
			
			$r2 = $db->query($q);
			
			if ($r2) {
				while ($row = $r2->fetchRow()) {
					$nc = Page::getByID($row['cID']);
					$nc->loadVersionObject();
					if ($nc->isSystemPage()) {
						continue;
					}
					$cArray[] = $nc;
					if (count($cArray) == $num) {
						break;
					}
				}
				$r2->free();
				return $cArray;
			}
			$r->free();
		}
		
		public function view() {
			$cArray = $this->getPages();
			$nh = Loader::helper('navigation');
			$this->set('nh', $nh);
			$this->set('cArray', $cArray);
		}
		
		function save($args) {
			// If we've gotten to the process() function for this class, we assume that we're in
			// the clear, as far as permissions are concerned (since we check permissions at several
			// points within the dispatcher)
			$db = Loader::db();

			$bID = $this->bID;
			
			$args['num'] = ($args['num'] > 0) ? $args['num'] : 0;
			$args['cThis'] = ($args['cParentID'] == $this->cID) ? '1' : '0';
			$args['cParentID'] = ($args['cParentID'] == 'OTHER') ? $args['cParentIDValue'] : $args['cParentID'];
			$args['truncateSummaries'] = ($args['truncateSummaries']) ? '1' : '0';
			$args['truncateChars'] = intval($args['truncateChars']); 

			parent::save($args);
		
		}

		public function getRssUrl($b){
			$uh = Loader::helper('concrete/urls');
			if(!$b) return '';
			$btID = $b->getBlockTypeID();
			$bt = BlockType::getByID($btID);
			$rssUrl = $uh->getBlockTypeToolsURL($bt)."/rss?bID=".$b->getBlockID()."&cID=".$b->getBlockCollectionID()."&arHandle=".$b->getAreaHandle();
			return $rssUrl;
		}
	}

?>