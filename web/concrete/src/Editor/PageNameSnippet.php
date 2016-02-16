<?php
namespace Concrete\Core\Editor;

use Page;

class PageNameSnippet extends Snippet
{
    public function replace()
    {
        $c = Page::getCurrentPage();
        if (is_object($c)) {
            return $c->getCollectionName();
        }
    }
}
