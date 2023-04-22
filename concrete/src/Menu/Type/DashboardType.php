<?php

namespace Concrete\Core\Menu\Type;

class DashboardType implements TypeInterface
{

    public function getDriverHandle(): string
    {
        return 'dashboard';
    }

    public function getName(): string
    {
        return t('Dashboard Menu');
    }

    public function getTreeTypeHandle()
    {
        return 'dashboard_menu';
    }

}
