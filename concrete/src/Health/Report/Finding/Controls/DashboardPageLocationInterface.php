<?php
namespace Concrete\Core\Health\Report\Finding\Controls;

interface DashboardPageLocationInterface extends LocationInterface
{

    /**
     * @return string
     */
    public function getPagePath(): string;


}
