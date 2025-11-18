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
            'prev_message' => tc('Pagination', '<'),
            'next_message' => tc('Pagination', '>'),
            'proximity' => 1,
            'active_suffix' => '<span class="sr-only">' . tc('Pagination', '(current)') . '</span>',
        );

        return $arguments;
    }

}
