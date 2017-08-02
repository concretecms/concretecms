<?php
namespace Concrete\Core\Search\ItemList\Pager;

use Concrete\Core\Search\ItemList\Column;
use Concrete\Core\Search\ItemList\Pager\Manager\PagerManagerInterface;
use Concrete\Core\Search\ItemList\Pager\QueryString\VariableFactory;
use Concrete\Core\Search\PermissionableListItemInterface;

interface PagerProviderInterface extends PermissionableListItemInterface
{

    /**
     * @return PagerManagerInterface
     */
    function getPagerManager();

    /**
     * @return VariableFactory
     */
    function getPagerVariableFactory();

}