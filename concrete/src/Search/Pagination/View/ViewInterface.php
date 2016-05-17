<?php
namespace Concrete\Core\Search\Pagination\View;

interface ViewInterface extends \Pagerfanta\View\ViewInterface
{
    /**
     * @return array
     */
    public function getArguments();
}
