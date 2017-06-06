<?php
namespace Concrete\Core\Search\Column;

use Concrete\Core\Search\ItemList\Pager\PagerProviderInterface;
use Concrete\Core\Search\Result\Result;

interface ColumnInterface
{

    function getColumnValue($mixed);

    function getColumnKey();

    function getColumnName();

    function getColumnSortDirection();

    function isColumnSortable();

    function getColumnCallback();

    function setColumnDefaultSortDirection($dir);

    function getSortClassName(Result $result);

    function getSortURL(Result $result);


}