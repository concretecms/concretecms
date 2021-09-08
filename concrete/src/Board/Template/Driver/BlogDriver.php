<?php
namespace Concrete\Core\Board\Template\Driver;

use Concrete\Core\Board\Layout\PlannerInterface;

defined('C5_EXECUTE') or die("Access Denied.");

class BlogDriver implements DriverInterface
{
    
    public function getFormFactor()
    {
        return [
            1 => 'stripe',
            2 => 'card',
            3 => 'card',
            4 => 'stripe',
            5 => 'stripe',
            6 => 'card',
            7 => 'card',
        ];
    }

    public function getTotalSlots(): int
    {
        return 7;
    }

    public function getLayoutPlanner(): ?PlannerInterface
    {
        return null;
    }


}
