<?
namespace Concrete\Block\PageList;
use Loader;
use PageList;
use Page;
use CollectionAttributeKey;
use BlockType;

use \Concrete\Core\Block\BlockController;
class Controller extends BlockController {
	protected $btTable = 'btPageList';
	protected $btInterfaceWidth = "500";
	protected $btInterfaceHeight = "350";
	protected $btExportPageColumns = array('cParentID');
	protected $btExportPageTypeColumns = array('ptID');
	protected $btCacheBlockRecord = true;

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

	protected function getPageList() {
		$db = Loader::db();
		$bID = $this->bID;
		if ($this->bID) {
			$q = "select num, cParentID, cThis, orderBy, ptID, displayAliases, rss from btPageList where bID = '$bID'";
			$r = $db->query($q);
			if ($r) {
				$row = $r->fetchRow();
			}
		} else {
			$row['num'] = $this->num;
			$row['cParentID'] = $this->cParentID;
			$row['cThis'] = $this->cThis;
			$row['orderBy'] = $this->orderBy;
			$row['ptID'] = $this->ptID;
			$row['rss'] = $this->rss;
			$row['displayAliases'] = $this->displayAliases;
		}


		$pl = new PageList();
		//$pl->setNameSpace('b' . $this->bID);

		$cArray = array();

		switch($row['orderBy']) {
			case 'display_asc':
				$pl->sortByDisplayOrder();
				break;
			case 'display_desc':
				$pl->sortByDisplayOrderDescending();
				break;
			case 'chrono_asc':
				$pl->sortByPublicDate();
				break;
			case 'alpha_asc':
				$pl->sortByName();
				break;
			case 'alpha_desc':
				$pl->sortByNameDescending();
				break;
			default:
				$pl->sortByPublicDateDescending();
				break;
		}

		$c = Page::getCurrentPage();
		if (is_object($c)) {
			$this->cID = $c->getCollectionID();
		}

		if ($this->displayFeaturedOnly == 1) {
			$cak = CollectionAttributeKey::getByHandle('is_featured');
			if (is_object($cak)) {
				$pl->filterByIsFeatured(1);
			}
		}
		if ($row['displayAliases']) {
			$pl->includeAliases();
		}
		$pl->filter('cvName', '', '!=');

		if ($row['ptID']) {
			$pl->filterByPageTypeID($row['ptID']);
		}

		$columns = $db->MetaColumnNames(CollectionAttributeKey::getIndexedSearchTable());
		if (isset($columns['ak_exclude_page_list'])) {
			$pl->filter(false, '(ak_exclude_page_list = 0 or ak_exclude_page_list is null)');
		}

		if ( intval($row['cParentID']) != 0) {
			$cParentID = ($row['cThis']) ? $this->cID : $row['cParentID'];
			if ($this->includeAllDescendents) {
				$pl->filterByPath(Page::getByID($cParentID)->getCollectionPath());
			} else {
				$pl->filterByParentID($cParentID);
			}
		}
		return $pl;
	}

	public function view() {
		$list = $this->getPageList();
		$nh = Loader::helper('navigation');
		$this->set('nh', $nh);

		//RSS...
		$showRss = false;
		$rssIconSrc = '';
		$rssInvisibleLink = '';
		if ($this->rss) {
			$showRss = true;
			$rssIconSrc = Loader::helper('concrete/urls')->getBlockTypeAssetsURL(BlockType::getByID($this->getBlockObject()->getBlockTypeID()), 'rss.png');
			//DEV NOTE: Ideally we'd set rssUrl here, but we can't because for some reason calling $this->getBlockObject() here doesn't load all info properly, and then the call to $this->getRssUrl() fails when it tries to get the area handle of the block.
		}
		$this->set('showRss', $showRss);
		$this->set('rssIconSrc', $rssIconSrc);

		//Pagination...
		$showPagination = false;
		if ($this->num > 0) {
            $list->setItemsPerPage($this->num);
            $pagination = $list->getPagination();
            $pages = $pagination->getCurrentPageResults();
            if ($pagination->getTotalPages() > 1 && $this->paginate) {
                $showPagination = true;
                $view = $pagination->getView();
                $c = Page::getCurrentPage();
                $url = $c->getCollectionLink();
                $pagination = $view->render($pagination, function($page) use ($list, $url, $result) {
                    return $url . '?' . $list->getQueryPaginationPageParameter() . '=' . $page;
                });
                $this->set('pagination', $pagination);
			}
		} else {
            $pages = $list->getResults();
        }

		if ($showPagination) {
			$this->requireAsset('css', 'core/frontend/pagination');
		}
        $this->set('pages', $pages);
        $this->set('list', $list);
		$this->set('showPagination', $showPagination);
	}

	// this doesn't work yet
	/*
	public function on_page_view() {
		if ($this->rss) {
			$b = $this->getBlockObject();
			$this->addHeaderItem('<link href="' . $this->getRssUrl($b) . '"  rel="alternate" type="application/rss+xml" title="' . $this->rssTitle . '" />');
		}
	}
	*/

	public function add() {

		$c = Page::getCurrentPage();
		$uh = Loader::helper('concrete/urls');
		//	echo $rssUrl;
		$this->set('c', $c);
		$this->set('uh', $uh);
		$this->set('bt', BlockType::getByHandle('page_list'));
		$this->set('displayAliases', true);
	}

	public function edit() {
		$b = $this->getBlockObject();
		$bCID = $b->getBlockCollectionID();
		$bID=$b->getBlockID();
		$this->set('bID', $bID);
		$c = Page::getCurrentPage();
		if ($c->getCollectionID() != $this->cParentID && (!$this->cThis) && ($this->cParentID != 0)) {
			$isOtherPage = true;
			$this->set('isOtherPage', true);
		}
		$uh = Loader::helper('concrete/urls');
		$this->set('uh', $uh);
		$this->set('bt', BlockType::getByHandle('page_list'));
	}

	function save($args) {
		// If we've gotten to the process() function for this class, we assume that we're in
		// the clear, as far as permissions are concerned (since we check permissions at several
		// points within the dispatcher)
		$db = Loader::db();

		$bID = $this->bID;
		$c = $this->getCollectionObject();
		if (is_object($c)) {
			$this->cID = $c->getCollectionID();
		}

		$args['num'] = ($args['num'] > 0) ? $args['num'] : 0;
		$args['cThis'] = ($args['cParentID'] == $this->cID) ? '1' : '0';
		$args['cParentID'] = ($args['cParentID'] == 'OTHER') ? $args['cParentIDValue'] : $args['cParentID'];
		if (!$args['cParentID']) {
			$args['cParentID'] = 0;
		}
		$args['includeAllDescendents'] = ($args['includeAllDescendents']) ? '1' : '0';
		$args['truncateSummaries'] = ($args['truncateSummaries']) ? '1' : '0';
		$args['displayFeaturedOnly'] = ($args['displayFeaturedOnly']) ? '1' : '0';
		$args['displayAliases'] = ($args['displayAliases']) ? '1' : '0';
		$args['truncateChars'] = intval($args['truncateChars']);
		$args['paginate'] = intval($args['paginate']);
		$args['rss'] = intval($args['rss']);
		$args['ptID'] = intval($args['ptID']);

		parent::save($args);

	}

	public function getRssUrl($b, $tool = 'rss'){
		$uh = Loader::helper('concrete/urls');
		if(!$b) return '';

		$pb = $b->getProxyBlock();
		$rssb = $b;
		if (is_object($pb)) {
			$rssb = $pb;
		}

		$btID = $b->getBlockTypeID();
		$bt = BlockType::getByID($btID);
		$c = $rssb->getBlockCollectionObject();
		$a = $rssb->getBlockAreaObject();
		if (is_object($a)) {
			$rssUrl = $uh->getBlockTypeToolsURL($bt)."/" . $tool . "?bID=".$rssb->getBlockID()."&amp;cID=".$c->getCollectionID()."&amp;arHandle=" . $a->getAreaHandle();
			return $rssUrl;
		}
	}
}

?>
