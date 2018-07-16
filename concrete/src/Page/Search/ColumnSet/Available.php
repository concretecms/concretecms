<?php
namespace Concrete\Core\Page\Search\ColumnSet;

use Concrete\Core\Page\Search\ColumnSet\Column\PageIDColumn;
use Concrete\Core\Page\Search\ColumnSet\Column\SitemapDisplayOrderColumn;
use Concrete\Core\Search\Column\Column;

class Available extends DefaultSet
{
    protected $attributeClass = 'CollectionAttributeKey';

    public function __construct()
    {
        $this->addColumn(new SitemapDisplayOrderColumn());
        $this->addColumn(new PageIDColumn());
        parent::__construct();
    }
}
