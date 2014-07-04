<?php
namespace Concrete\Core\Marketplace;

use Cache;
use Loader;
use Config;
use \Concrete\Core\Legacy\ItemList;

class RemoteItemList extends ItemList
{
    protected $includeInstalledItems = true;
    protected $params = array();
    protected $type = 'themes';
    protected $itemsPerPage = 20;

    public static function getItemSets($type)
    {
        $url = MARKETPLACE_REMOTE_ITEM_LIST_WS;
        $url .= $type . '/-/get_remote_item_sets';
        $contents = Loader::helper("file")->getContents($url);
        $sets = array();
        if ($contents != '') {
            $objects = @Loader::helper('json')->decode($contents);
            if (is_array($objects)) {
                foreach ($objects as $obj) {
                    $mr = new RemoteItemSet();
                    $mr->id = $obj->marketplaceItemSetID;
                    $mr->name = $obj->marketplaceItemSetName;
                    $sets[] = $mr;
                }
            }
        }

        return $sets;
    }

    public function setIncludeInstalledItems($pp)
    {
        $this->includeInstalledItems = $pp;
    }

    public function setType($type)
    {
        $this->type = $type;
    }

    public function filterByKeywords($keywords)
    {
        $this->params['keywords'] = $keywords;
    }

    public function filterByMarketplaceItemID($mpID)
    {
        $this->params['mpID'] = $mpID;
    }

    public function sortBy($sortBy)
    {
        $this->params['sort'] = $sortBy;
    }

    public function filterBySet($set)
    {
        $this->params['set'] = $set;
    }

    public function filterByIsFeaturedRemotely($r)
    {
        $this->params['is_featured_remotely'] = $r;
    }

    public function filterByCompatibility($r)
    {
        $this->params['is_compatible'] = $r;
    }

    public function execute()
    {
        $params = $this->params;
        $params['version'] = APP_VERSION;
        $params['itemsPerPage'] = $this->itemsPerPage;
        $mi = Marketplace::getInstance();
        $params['csToken'] = $mi->getSiteToken();

        if ($this->includeInstalledItems) {
            $params['includeInstalledItems'] = 1;
        } else {
            $params['includeInstalledItems'] = 0;
            $list = Package::getInstalledList();
            foreach ($list as $pkg) {
                $params['installedPackages'][] = $pkg->getPackageHandle();
            }
        }

        if (isset($_REQUEST[$this->queryStringPagingVariable])) {
            $params[$this->queryStringPagingVariable] = $_REQUEST[$this->queryStringPagingVariable];
        }

        $uh = Loader::helper('url');

        $url = $uh->buildQuery(MARKETPLACE_REMOTE_ITEM_LIST_WS . $this->type . '/-/get_remote_list', $params);
        $r = Loader::helper('file')->getContents($url);
        $r2 = @Loader::helper('json')->decode($r);

        $total = 0;
        $items = array();

        if (is_object($r2)) {
            $items = $r2->items;
            $total = $r2->total;
        }

        $this->total = $total;
        $this->setItems($items);
    }

    public function get($itemsToGet = 0, $offset = 0)
    {
        $this->start = $offset;
        $items = $this->items;
        $marketplaceItems = array();
        foreach ($items as $stdObj) {
            $mi = new RemoteItem();
            $mi->setPropertiesFromJSONObject($stdObj);
            $marketplaceItems[] = $mi;
        }

        return $marketplaceItems;
    }

}
