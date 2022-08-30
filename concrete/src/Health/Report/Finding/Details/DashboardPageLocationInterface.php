<?php
namespace Concrete\Core\Health\Report\Finding\Details;

interface DashboardPageLocationInterface extends LocationInterface
{

    /**
     * @return string
     */
    public function getPagePath(): string;


}
