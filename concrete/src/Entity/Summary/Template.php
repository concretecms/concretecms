<?php
namespace Concrete\Core\Entity\Summary;

use Concrete\Core\Entity\PackageTrait;
use HtmlObject\Image;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="TemplateRepository")
 * @ORM\Table(
 *     name="SummaryTemplates"
 * )
 */
class Template
{
    
    use PackageTrait;
    
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    protected $id;
    
    /**
     * @ORM\Column(type="string")
     */
    protected $icon = '';

    /**
     * @ORM\Column(type="string")
     */
    protected $name = '';
    
    /**
     * @ORM\Column(type="string")
     */
    protected $handle;

    /**
     * @ORM\ManyToMany(targetEntity="Category", inversedBy="templates")
     * @ORM\JoinTable(name="SummaryTemplateCategories"
     * )
     */
    protected $categories;

    /**
     * @ORM\OneToMany(targetEntity="TemplateField", mappedBy="template", cascade={"persist", "remove"}, mappedBy="template")
     **/
    protected $fields;

    /**
     * Template constructor.
     */
    public function __construct()
    {
        $this->categories = new ArrayCollection();
        $this->fields = new ArrayCollection();
    }
    
    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }
    
    /**
     * @return mixed
     */
    public function getHandle()
    {
        return $this->handle;
    }

    /**
     * @param mixed $handle
     */
    public function setHandle($handle): void
    {
        $this->handle = $handle;
    }
    
    /**
     * @return mixed
     */
    public function getIcon()
    {
        return $this->icon;
    }

    /**
     * @param mixed $icon
     */
    public function setIcon($icon): void
    {
        $this->icon = $icon;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name): void
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getCategories()
    {
        return $this->categories;
    }

    /**
     * @param mixed $categories
     */
    public function setCategories($categories): void
    {
        $this->categories = $categories;
    }

    /**
     * @return mixed
     */
    public function getFields()
    {
        return $this->fields;
    }

    /**
     * @param mixed $fields
     */
    public function setFields($fields): void
    {
        $this->fields = $fields;
    }
    
    public function getTemplateIconImage($asTag = true)
    {
        if ($this->getIcon()) {
            $image = ASSETS_URL_IMAGES . '/icons/summary_templates/' . $this->getIcon();
            if ($asTag) {
                $image = new Image($image);
            }
            return $image;
        }
    }

    public function export(\SimpleXMLElement $node)
    {
        $template = $node->addChild('template');
        $template->addAttribute('handle', $this->getHandle());
        $template->addAttribute('name', h($this->getName()));
        $template->addAttribute('icon', h($this->getIcon()));
        $template->addAttribute('package', $this->getPackageHandle());
        
        $categories = $template->addChild('categories');
        foreach($this->getCategories() as $category) {
            $categoryNode = $categories->addChild('category');
            $categoryNode->addAttribute('handle', $category->getHandle());
        }
        $fields = $template->addChild('fields');
        foreach($this->getFields() as $field) {
            $fieldNode = $fields->addChild('field', $field->getField()->getHandle());
            if ($field->isRequired()) {
                $fieldNode->addAttribute('required', '1');
            }
        }
    }
}
