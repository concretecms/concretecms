<?php
namespace Concrete\Core\Express\Controller;

use Concrete\Core\Application\Application;
use Concrete\Core\Support\Manager as CoreManager;

class Manager extends CoreManager
{

    protected $standardController = StandardController::class;

    /**
     * @param mixed $standardController
     */
    public function setStandardController($standardController)
    {
        $this->standardController = $standardController;
    }


    protected function getStandardController()
    {
        return $this->app->make($this->standardController);
    }

    public function driver($driver = null)
    {
        if (!isset($this->customCreators[$driver]) && !isset($this->drivers[$driver])) {
            return $this->getStandardController();
        }

        return parent::driver($driver);
    }

    public function __construct(Application $application)
    {
        parent::__construct($application);
    }

}
