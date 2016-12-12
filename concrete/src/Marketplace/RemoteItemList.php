<?php
namespace Concrete\Core\Marketplace;

use Core;
use Config;
use Log;
use Concrete\Core\Legacy\ItemList;
use Concrete\Core\Package\Package;

class RemoteItemList extends ItemList
{
    protected $includeInstalledItems = true;
    protected $params = array();
    protected $type = 'themes';
    protected $itemsPerPage = 20;

    public static function getItemSets($type)
    {
        $cache = Core::make('cache/expensive');
        $r = $cache->getItem('concrete.marketplace.remote_item_sets.' . $type);
        if (!$r->isMiss()) {
            $sets = $r->get();
        } else {
            $r->lock();
            $url = Config::get('concrete.urls.concrete5') . Config::get('concrete.urls.paths.marketplace.remote_item_list');
            $url .= $type . '/-/get_remote_item_sets';
            if (Config::get('concrete.marketplace.log_requests')) {
                Log::info($url);
            }
            $contents = Core::make('helper/file')->getContents($url);
            $sets = array();
            if ($contents != '') {
                $objects = @Core::make('helper/json')->decode($contents);
                if (is_array($objects)) {
                    foreach ($objects as $obj) {
                        $mr = new RemoteItemSet();
                        $mr->id = $obj->marketplaceItemSetID;
                        $mr->name = $obj->marketplaceItemSetName;
                        $sets[] = $mr;
                    }
                }
            }
            $cache->save($r->set($sets));
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

    public function sortBy($column, $direction = 'asc')
    {
        $this->params['sort'] = $column;
        $direction = strtolower($direction);
        //$this->params['sortDirection'] = in_array($direction, array('asc', 'desc')) ? $direction : 'asc';
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
        $this->loadQueryStringPagingVariable();
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

        $uh = Core::make('helper/url');

        $url = Config::get('concrete.urls.concrete5') . Config::get('concrete.urls.paths.marketplace.remote_item_list');
        $url = $uh->buildQuery($url . $this->type . '/-/get_remote_list', $params);
        if (Config::get('concrete.marketplace.log_requests')) {
            Log::info($url);
        }
        $r = Core::make('helper/file')->getContents($url);
        $r2 = @Core::make('helper/json')->decode($r);

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
