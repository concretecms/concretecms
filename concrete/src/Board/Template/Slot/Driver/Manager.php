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

    public function createStripeDriver()
    {
        return $this->app->make(StripeDriver::class);
    }

    public function createCardDriver()
    {
        return $this->app->make(CardDriver::class);
    }

    public function createTwoColumnDriver()
    {
        return $this->app->make(TwoColumnDriver::class);
    }
    

}
