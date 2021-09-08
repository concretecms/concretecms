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

    public function createBlogStripeDriver()
    {
        return $this->app->make(BlogStripeDriver::class);
    }

    public function createBlogCardDriver()
    {
        return $this->app->make(BlogCardDriver::class);
    }


}
