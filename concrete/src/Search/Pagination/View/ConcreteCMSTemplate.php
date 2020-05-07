<?php
namespace Concrete\Core\Search\Pagination\View;

class ConcreteCMSTemplate extends ConcreteBootstrap4Template
{

    public function container()
    {
        $container = parent::container();
        $container = '<div class="ccm-search-results-pagination">' . $container . '</div>';
        return $container;
    }

}
