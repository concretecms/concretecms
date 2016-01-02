<?php

namespace Concrete\Core\Entity\Attribute;

use Concrete\Core\Attribute\View;
use Concrete\Core\Entity\PackageTrait;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @Entity
 * @Table(name="AttributeTypes", indexes={@Index(name="pkgID", columns={"pkgID", "atID"})})
 */
class Type
{

    use PackageTrait;

    protected $controller;

    public function __construct()
    {
        $this->categories = new ArrayCollection();
    }

    /**
     * @Id @Column(type="integer", options={"unsigned":true})
     * @GeneratedValue(strategy="AUTO")
     */
    protected $atID;

    /**
     * @Column(type="string", unique=true)
     */
    protected $atHandle;

    /**
     * @ManyToMany(targetEntity="Category", mappedBy="types")
     */
    protected $categories;

    /**
     * @Column(type="string")
     */
    protected $atName;

    /**
     * @return mixed
     */
    public function getAttributeTypeID()
    {
        return $this->atID;
    }

    /**
     * @return mixed
     */
    public function getAttributeTypeHandle()
    {
        return $this->atHandle;
    }

    /**
     * @param mixed $atHandle
     */
    public function setAttributeTypeHandle($atHandle)
    {
        $this->atHandle = $atHandle;
    }

    /**
     * @return mixed
     */
    public function getAttributeTypeName()
    {
        return $this->atName;
    }

    /**
     * @param mixed $atName
     */
    public function setAttributeTypeName($atName)
    {
        $this->atName = $atName;
    }

    /**
     * @return mixed
     */
    public function getAttributeCategories()
    {
        return $this->categories;
    }

    /**
     * @param mixed $categories
     */
    public function setAttributeCategories($categories)
    {
        $this->categories = $categories;
    }

    public function isAssociatedWithCategory(Category $category)
    {
        return $this->categories->contains($category);
    }

    public function getAttributeTypeDisplayName($format = 'html')
    {
        $value = tc('AttributeTypeName', $this->getAttributeTypeName());
        switch ($format) {
            case 'html':
                return h($value);
            case 'text':
            default:
                return $value;
        }
    }

    public function getController()
    {
        if (!isset($this->controller)) {
            $env = \Environment::get();
            $r = $env->getRecord(DIRNAME_ATTRIBUTES . '/' . $this->atHandle . '/' . FILENAME_CONTROLLER);
            $prefix = $r->override ? true : $this->getPackageHandle();
            $atHandle = \Core::make('helper/text')->camelcase($this->atHandle);
            $class = core_class('Attribute\\' . $atHandle . '\\Controller', $prefix);
            $this->controller = \Core::make($class, array($this));
        }
        return $this->controller;
    }

    /**
     * @deprecated
     */
    public function render($view, $ak = false, $value = false, $return = false)
    {
        // local scope
        if ($value) {
            $av = new View($value);
        } else {
            if ($ak) {
                $av = new View($ak);
            } else {
                $av = new View($this);
            }
        }
        ob_start();
        $av->render($view);
        $contents = ob_get_contents();
        ob_end_clean();
        if ($return) {
            return $contents;
        } else {
            print $contents;
        }
    }




}

