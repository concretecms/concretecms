<?php

declare(strict_types=1);

namespace Concrete\Core\User\Search\Result;

use Concrete\Core\Search\Result\Result as SearchResult;
use Concrete\Core\User\Search\ColumnSet\Column\TotalGroupsColumn;
use Concrete\Core\User\Search\ColumnSet\Column\UserGroupListColumn;
use Concrete\Core\User\Search\MembershipsProvider;

defined('C5_EXECUTE') or die('Access Denied.');

class Result extends SearchResult
{
    public function getItems()
    {
        if (!isset($this->items)) {
            $this->items = [];
            $items = $this->pagination->getCurrentPageResults();
            $includeMemberships = false;
            $includeGroupPaths = false;
            foreach ($this->listColumns->getColumns() as $column) {
                if ($column instanceof TotalGroupsColumn) {
                    $includeMemberships = true;
                } elseif ($column instanceof UserGroupListColumn) {
                    $includeMemberships = true;
                    $includeGroupPaths = true;
                }
            }
            if ($includeMemberships) {
                $userIDs = array_map(static function ($item): int {
                    return (int) $item->getUserID();
                }, $items);
                $membershipsProvider = app(MembershipsProvider::class);
                $membershipsProvider->clear();
                $membershipsProvider->preload($userIDs, $includeGroupPaths);
            }
            foreach ($items as $item) {
                $this->items[] = $this->getItemDetails($item);
            }
        }

        return $this->items;
    }

    public function getItemDetails($item)
    {
        return new Item($this, $this->listColumns, $item);
    }

    public function getColumnDetails($column)
    {
        return new Column($this, $column);
    }
}