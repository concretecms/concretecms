<?php
namespace Concrete\Core\Application\UserInterface\OptionsForm;

class OptionsForm
{

    protected $provider;
    protected $environment;

    public function __construct(OptionsFormProviderInterface $provider)
    {
        $this->provider = $provider;
    }

    protected function getController()
    {
        return core_class($this->provider->getElementController(), $this->provider->getPackageHandle());
    }

    public function formExists()
    {
        return class_exists($this->getController());
    }

    public function renderForm()
    {
        /**
         * @var $elementController OptionsFormControllerInterface
         */
        $elementController = \Core::make($this->getController());
        $elementController->setupController($this->provider);
        $elementController->setPackageHandle($this->provider->getPackageHandle());
        $elementController->render();
    }
}
