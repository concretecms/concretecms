<?php

namespace Concrete\Core\Page;

use League\Fractal\TransformerAbstract;

class PageTransformer extends TransformerAbstract
{
    /**
     * Basic transforming of a page object into an array
     *
     * @param Page $page
     * @return array
     */
    public function transform(Page $page)
    {
        return (array) $page->getJSONObject();
    }

}
