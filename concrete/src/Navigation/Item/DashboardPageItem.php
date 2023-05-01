<?php

namespace Concrete\Core\Navigation\Item;

use HtmlObject\Element;
use HtmlObject\Link;
use HtmlObject\Traits\Tag;

class DashboardPageItem extends PageItem
{

    public function getKeywords(): ?string
    {
        return $this->page->getAttribute('meta_keywords');
    }

    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        $data = parent::jsonSerialize();
        $data['keywords'] = $this->getKeywords();
        return $data;
    }


}

