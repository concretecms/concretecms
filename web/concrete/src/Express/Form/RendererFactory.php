<?php

namespace Concrete\Core\Express\Form;

use Concrete\Core\Application\Application;
use Concrete\Core\Entity\Express\Control\Control;
use Concrete\Core\Entity\Express\FieldSet;
use Concrete\Core\Entity\Express\Form;
use Concrete\Core\Foundation\Environment;
use Doctrine\ORM\EntityManagerInterface;

class RendererFactory
{

    protected $control;
    protected $application;
    protected $entityManager;
    protected $renderer;

    /**
     * @return Control
     */
    public function getControl()
    {
        return $this->control;
    }

    /**
     * @param Control $control
     */
    public function setControl($control)
    {
        $this->control = $control;
    }

    /**
     * @return Application
     */
    public function getApplication()
    {
        return $this->application;
    }

    /**
     * @param Application $application
     */
    public function setApplication($application)
    {
        $this->application = $application;
    }

    /**
     * @return mixed
     */
    public function getEntityManager()
    {
        return $this->entityManager;
    }

    /**
     * @param mixed $entityManager
     */
    public function setEntityManager($entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function __construct(Control $control, Application $application, EntityManagerInterface $entityManager)
    {
        $this->control = $control;
        $this->application = $application;
        $this->entityManager = $entityManager;
    }

    public function getRenderer()
    {
        if (!isset($this->renderer)) {
            $this->renderer = $this->control->getFormRenderer();
            if (is_object($this->renderer)) {
                $this->renderer->build($this);
            }
        }
        return $this->renderer;
    }

}