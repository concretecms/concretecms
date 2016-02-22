<?php
namespace Concrete\Core\Express\Form;

use Concrete\Core\Application\Application;
use Concrete\Core\Entity\Express\Control\Control;
use Concrete\Core\Entity\Express\Entity;
use Concrete\Core\Entity\Express\Entry;
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

    public function getViewRenderer(Entry $entry)
    {
        if (!isset($this->viewRenderer)) {
            $this->viewRenderer = $this->control->getViewRenderer($entry);
            if (is_object($this->viewRenderer)) {
                $this->viewRenderer->build($this);
            }
        }

        return $this->viewRenderer;
    }

    public function getFormRenderer()
    {
        if (!isset($this->formRenderer)) {
            $this->formRenderer = $this->control->getFormRenderer();
            if (is_object($this->formRenderer)) {
                $this->formRenderer->build($this);
            }
        }

        return $this->formRenderer;
    }
}
