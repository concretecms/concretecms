<?php
namespace Concrete\Core\Search\Pagination\View;

/*
 * This is the pagination view used by the CMS domain, including within our in-page overlays as well as in
 * the Dashboard.
 */
class ConcreteCMSView extends ConcreteBootstrap4View
{
    protected function createDefaultTemplate()
    {
        return new ConcreteCMSTemplate();
    }

}
