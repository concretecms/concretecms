<?php

namespace Concrete\Core\Site\Type\Controller;

use Concrete\Core\Application\Application;
use Concrete\Core\Support\Manager as CoreManager;

class Manager extends CoreManager
{
    /**
     * @var string
     */
    protected $standardController = StandardController::class;

    public function __construct(Application $application)
    {
        parent::__construct($application);
    }

    /**
     * @return $this
     */
    public function setStandardController(string $vaiue): self
    {
        $this->standardController = $vaiue;

        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Illuminate\Support\Manager::driver()
     *
     * @throws \InvalidArgumentException
     *
     * @return \Concrete\Core\Site\Type\Controller\ControllerInterface
     */
    public function driver($driver = null)
    {
        if (!isset($this->customCreators[$driver]) && !isset($this->drivers[$driver])) {
            return $this->getStandardController();
        }

        return parent::driver($driver);
    }

    protected function getStandardController(): ControllerInterface
    {
        return $this->app->make($this->standardController);
    }
}
