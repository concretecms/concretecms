<?php

namespace Concrete\Core\Search\Pagination\View;
use \Pagerfanta\View\Template\TwitterBootstrap3Template;

class ConcreteBootstrap3Template extends TwitterBootstrap3Template
{

    public function container()
    {
        return sprintf('<div class="ccm-pagination-wrapper"><ul class="%s">%%pages%%</ul></div>',
            $this->option('css_container_class')
        );
    }

}