<?php

namespace Concrete\Core\Search\Pagination\View;
use \Pagerfanta\View\TwitterBootstrap3View;

class ConcreteBootstrap3View extends TwitterBootstrap3View
{

    protected function createDefaultTemplate()
    {
        return new ConcreteBootstrap3Template();
    }

}