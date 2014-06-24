<?
namespace Concrete\Core\Search;
interface ListItemInterface
{
    public function createQuery();
    public function getQueryObject();
    public function getPagination();
    public function getTotalResults();
    public function getResult($mixed);
    public function getResults();
    public function sortBy($column, $direction = false);
    public function isActiveSortColumn($column);
    public function getQuerySortColumnParameter();
    public function getQuerySortDirectionParameter();
    public function getQueryPaginationPageParameter();

}