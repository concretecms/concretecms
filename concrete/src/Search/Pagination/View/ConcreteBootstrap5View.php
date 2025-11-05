<?php
namespace Concrete\Core\Search\Pagination\View;

use Pagerfanta\View\TwitterBootstrap5View;

class ConcreteBootstrap5View extends TwitterBootstrap5View implements ViewInterface
{
    protected function createDefaultTemplate()
    {
        return new ConcreteBootstrap5Template();
    }

    public function getArguments()
    {
        return array(
            'prev_message' => tc('Pagination', '<'),
            'next_message' => tc('Pagination', '>'),
            'proximity' => 1,
            'active_suffix' => '<span class="visually-hidden">' . tc('Pagination', '(current)') . '</span>',
        );
    }
}
