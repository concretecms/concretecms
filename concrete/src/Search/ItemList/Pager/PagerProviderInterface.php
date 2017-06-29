<?php
namespace Concrete\Core\Search\ItemList\Pager;

use Concrete\Core\Search\ItemList\Column;
use Concrete\Core\Search\ItemList\Pager\Manager\PagerManagerInterface;
use Concrete\Core\Search\ItemList\Pager\QueryString\VariableFactory;

interface PagerProviderInterface
{

    /**
     * @return PagerManagerInterface
     */
    function getPagerManager();

    /**
     * @return VariableFactory
     */
    function getPagerVariableFactory();


    function getPermissionsChecker();
    function ignorePermissions();
    function enablePermissions();

}