<?php
namespace Concrete\Core\Health\Report\Finding\Control;

interface DashboardPageLocationInterface extends LocationInterface
{

    /**
     * @return string
     */
    public function getPagePath(): string;


}
