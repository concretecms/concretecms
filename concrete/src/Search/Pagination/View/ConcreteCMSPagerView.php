<?php
namespace Concrete\Core\Search\Pagination\View;

class ConcreteCMSPagerView extends ConcreteBootstrap4View
{
    protected function createDefaultTemplate()
    {
        return new ConcreteCMSPagerTemplate();
    }


}
