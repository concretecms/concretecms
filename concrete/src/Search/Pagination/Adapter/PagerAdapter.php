<?php
namespace Concrete\Core\Search\Pagination\Adapter;

use Concrete\Core\Search\ItemList\Database\ItemList;
use Pagerfanta\Adapter\AdapterInterface;
use Pagerfanta\Adapter\ArrayAdapter;
use Pagerfanta\Pagerfanta;

class PagerAdapter implements AdapterInterface
{

    protected $itemList;

    public function __construct(ItemList $itemList)
    {
        $this->itemList = $itemList;
    }

    public function getNbResults()
    {
        return -1; // Unknown
    }

    protected function checkPermissions($checker, $object)
    {
        if ($checker instanceof \Closure) {
            return $checker($object);
        } else {

            $this->itemList->enablePermissions();
            $valid = $this->itemList->checkPermissions($object);
            $this->itemList->setPermissionsChecker($checker);
            return $valid;

        }
    }

    public function getSlice($offset, $length)
    {
        $checker = $this->itemList->getPermissionsChecker();
        $manager = $this->itemList->getPagerManager();

        $this->itemList->ignorePermissions();
        $this->itemList
            ->getQueryObject()
            ->setMaxResults($length);

        $currentResults = array();
        $results = $this->itemList->getResults();

        while (count($results) != 0 && count($currentResults) < $length) {

            foreach($results as $result) {

                if ($this->checkPermissions($checker, $result)) {

                    if (!isset($this->firstResult)) {
                        $this->firstResult = $result;
                    }

                    $currentResults[] = $result;
                }

            }

            $manager->displaySegmentAtCursor($result, $this->itemList);
            $this->itemList->ignorePermissions();
            $results = $this->itemList->getResults();
        }

        $this->itemList->setPermissionsChecker($checker);
        return $currentResults;
    }


}
