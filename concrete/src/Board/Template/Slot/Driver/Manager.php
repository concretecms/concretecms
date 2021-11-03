<?php
namespace Concrete\Core\Board\Template\Slot\Driver;

use Concrete\Core\Application\Application;
use Concrete\Core\Support\Manager as CoreManager;

defined('C5_EXECUTE') or die("Access Denied.");

class Manager extends CoreManager
{
    
    public function __construct(Application $app)
    {
        parent::__construct($app);
    }

    public function createBlogImageLeftDriver()
    {
        return $this->app->make(BlogImageLeftDriver::class);
    }

    public function createBlogImageRightDriver()
    {
        return $this->app->make(BlogImageRightDriver::class);
    }

    public function createBlogCardDriver()
    {
        return $this->app->make(BlogCardDriver::class);
    }

    public function createBlogTwoUpDriver()
    {
        return $this->app->make(BlogTwoUpDriver::class);
    }

    public function createBlogThreeUpDriver()
    {
        return $this->app->make(BlogThreeUpDriver::class);
    }



}
