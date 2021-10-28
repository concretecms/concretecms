<?php
namespace Concrete\Core\Search\Pagination\View;

use Pagerfanta\View\TwitterBootstrap4View;

class ConcreteBootstrap4View extends TwitterBootstrap4View implements ViewInterface
{
    protected function createDefaultTemplate()
    {
        return new ConcreteBootstrap4Template();
    }

    public function getArguments()
    {
        $arguments = array(
            'prev_message' => tc('Pagination', '&larr; Previous'),
            'next_message' => tc('Pagination', 'Next &rarr;'),
            'active_suffix' => '<span class="sr-only">' . tc('Pagination', '(current)') . '</span>',
        );

        return $arguments;
    }

}
