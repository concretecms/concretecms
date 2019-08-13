<?php
namespace Concrete\Core\Search\Pagination\View;

/**
 * @since 5.7.4
 */
interface ViewInterface extends \Pagerfanta\View\ViewInterface
{
    /**
     * @return array
     */
    public function getArguments();
}
