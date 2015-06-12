<?php
namespace Concrete\Core\Multilingual\Page;

use Concrete\Core\Page\Page;

class LinkScanner
{
    protected $page;

    public function __construct(Page $page)
    {
        $this->page = $page;
    }


}
