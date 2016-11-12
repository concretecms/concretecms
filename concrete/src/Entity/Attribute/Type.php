<?php
namespace Concrete\Core\Entity\Attribute;

use Concrete\Core\Attribute\View;
use Concrete\Core\Entity\PackageTrait;
use Concrete\Core\Export\ExportableInterface;
use Concrete\Core\Export\Item\AttributeType;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="AttributeTypes", indexes={@ORM\Index(name="pkgID", columns={"pkgID", "atID"})})
 */
class Type implements ExportableInterface
{
    use PackageTrait;

    protected $controller;

    public function __construct()
    {
        $this->categories = new ArrayCollection();
    }

    /**
     * @ORM\Id @ORM\Column(type="integer", options={"unsigned":true})
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $atID;

    /**
     * @ORM\Column(type="string", unique=true)
     */
    protected $atHandle;

    /**
     * @ORM\ManyToMany(targetEntity="Category", mappedBy="types")
     */
    protected $categories;

    /**
     * @ORM\Column(type="string")
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

    public function getExporter()
    {
        return new AttributeType();
    }

    public function getController()
    {
        // Note, you can't cache this against the type class itself –it needs to return
        // a new instance every time because sometimes attribute specific keys get
        // bound to the controller and then if it caches against type the wrong controller
        // key combination gets returned with this call.
        //if (!isset($this->controller)) {
        $env = \Environment::get();
        $r = $env->getRecord(DIRNAME_ATTRIBUTES . '/' . $this->atHandle . '/' . FILENAME_CONTROLLER);
        $prefix = $r->override ? true : $this->getPackageHandle();
        $atHandle = \Core::make('helper/text')->camelcase($this->atHandle);
        $class = core_class('Attribute\\' . $atHandle . '\\Controller', $prefix);
        $controller = \Core::make($class);
        $controller->setAttributeType($this);
        return $controller;
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
            echo $contents;
        }
    }

    /**
     * @deprecated
     * You shouldnt have to call this, the package uninstaller itself will take care of this
     */
    public function delete()
    {
        $em = \Database::connection()->getEntityManager();
        $em->remove($this);
        $em->flush();
    }

}
