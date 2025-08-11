<?php
namespace Concrete\Controller\SinglePage\Dashboard\Extend;

use Concrete\Core\Page\Controller\DashboardPageController;

/**
 * @deprecated This will be removed in version 10
 */
class Connect extends DashboardPageController
{
    public function view()
    {
        throw new \RuntimeException('Please migrate to the new marketplace.');
    }
    public function do_connect()
    {
        throw new \RuntimeException('Please migrate to the new marketplace.');
    }

}