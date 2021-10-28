<?php
namespace Concrete\Core\Board\Template\Driver;

use Concrete\Core\Application\Application;
use Concrete\Core\Support\Manager as CoreManager;

defined('C5_EXECUTE') or die("Access Denied.");

class Manager extends CoreManager
{
    
    public function __construct(Application $app)
    {
        parent::__construct($app);
    }

    public function createThreeByThreeDriver()
    {
        return $this->app->make(ThreeByThreeDriver::class);
    }

    public function createBlogDriver()
    {
        return $this->app->make(BlogDriver::class);
    }



}
