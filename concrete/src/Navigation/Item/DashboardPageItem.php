<?php

namespace Concrete\Core\Navigation\Item;

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

