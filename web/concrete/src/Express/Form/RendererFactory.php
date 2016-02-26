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
    protected $viewControlRenderer;
    protected $formRenderer;
    protected $formControlRenderer;

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

    public function __construct($formRenderer, Control $control, Application $application, EntityManagerInterface $entityManager)
    {
        $this->control = $control;
        $this->application = $application;
        $this->entityManager = $entityManager;
        $this->formRenderer = $formRenderer;
    }

    /**
     * @return mixed
     */
    public function getFormRenderer()
    {
        return $this->formRenderer;
    }

    public function getViewControlRenderer(Entry $entry)
    {
        if (!isset($this->viewRenderer)) {
            $this->viewControlRenderer = $this->control->getViewControlRenderer($entry);
            if (is_object($this->viewControlRenderer)) {
                $this->viewControlRenderer->build($this);
            }
        }

        return $this->viewControlRenderer;
    }

    public function getFormControlRenderer()
    {
        if (!isset($this->formControlRenderer)) {
            $this->formControlRenderer = $this->control->getFormControlRenderer();
            if (is_object($this->formControlRenderer)) {
                $this->formControlRenderer->build($this);
            }
        }

        return $this->formControlRenderer;
    }
}
