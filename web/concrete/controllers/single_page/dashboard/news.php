<?php
namespace Concrete\Controller\SinglePage\Dashboard;

use Concrete\Core\Page\Controller\DashboardPageController;

class News extends DashboardPageController
{
    protected $viewPath = '/dashboard/home';

    public $helpers = array('form');
}
