<?php
namespace Concrete\Core\Search\ItemList\Pager\QueryString;

use Concrete\Core\Search\ItemList\Pager\PagerProviderInterface;
use Concrete\Core\Search\StickyRequest;

interface VariableFactoryInterface
{

    function getRequestedVariables();

}