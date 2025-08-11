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
class Template implements \JsonSerializable
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
    protected $handle = '';

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
     * @ORM\ManyToMany(targetEntity="Concrete\Core\Entity\Design\DesignTag")
     * @ORM\JoinTable(name="SummaryTemplateTags")
     */
    protected $tags;

    /**
     * Template constructor.
     */
    public function __construct()
    {
        $this->categories = new ArrayCollection();
        $this->fields = new ArrayCollection();
        $this->tags = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getHandle(): string
    {
        return $this->handle;
    }

    /**
     * @return $this
     */
    public function setHandle(string $handle): self
    {
        $this->handle = $handle;

        return $this;
    }

    public function getIcon(): string
    {
        return $this->icon;
    }

    /**
     * @return $this
     */
    public function setIcon(string $icon): self
    {
        $this->icon = $icon;

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDisplayName(): string
    {
        return tc('SummaryTemplateName', $this->getName());
    }

    /**
     * @return $this
     */
    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
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
     * @return TemplateField[]
     */
    public function getFields()
    {
        return $this->fields;
    }

    /**
     * Goes through all TemplateField objects for this template, checks to see if they're required
     * and if they are returns the Field object.
     *
     * @return Field[]
     */
    public function getRequiredFields()
    {
        $fields = [];
        foreach($this->getFields() as $templateField) {
            if ($templateField->isRequired()) {
                $fields[] = $templateField->getField();
            }
        }
        return $fields;
    }

    /**
     * @param mixed $fields
     */
    public function setFields($fields): void
    {
        $this->fields = $fields;
    }

    /**
     * @return ArrayCollection
     */
    public function getTags()
    {
        return $this->tags;
    }

    /**
     * @return \HtmlObject\Image|string|null
     */
    public function getTemplateIconImage(bool $asTag = true)
    {
        if ($this->getIcon()) {
            $image = ASSETS_URL_IMAGES . '/icons/summary_templates/' . $this->getIcon();
            if ($asTag) {
                $image = new Image($image);
            }
            return $image;
        }
    }

    public function export(\SimpleXMLElement $node): void
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

    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        $data = [
            'id' => $this->getId(),
            'handle' => $this->getHandle(),
            'name' => $this->getName(),
            'icon' => $this->getIcon(),
            'iconImage', $this->getTemplateIconImage()
        ];
        return $data;
    }
}
