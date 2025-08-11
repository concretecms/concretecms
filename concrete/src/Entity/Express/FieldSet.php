<?php

namespace Concrete\Core\Entity\Express;

use Concrete\Core\Entity\Express\Control\Control;
use Concrete\Core\Support\Facade\Application;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gettext\Translations;

/**
 * @ORM\Entity
 * @ORM\Table(name="ExpressFormFieldSets")
 */
class FieldSet implements \JsonSerializable
{
    /**
     * @ORM\Id @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="integer")
     */
    protected $position = 0;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $title;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $description;

    /**
     * @ORM\ManyToOne(targetEntity="Form", inversedBy="field_sets")
     **/
    protected $form;

    /**
     * @ORM\OneToMany(targetEntity="\Concrete\Core\Entity\Express\Control\Control", mappedBy="field_set", cascade={"persist", "remove"})
     * @ORM\OrderBy({"position" = "ASC"})
     **/
    protected $controls;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param mixed $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param mixed $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return Form
     */
    public function getForm()
    {
        return $this->form;
    }

    /**
     * @param mixed $form
     */
    public function setForm($form)
    {
        $this->form = $form;
    }

    /**
     * @return Collection<int, Control>|Control[]
     */
    public function getControls()
    {
        return $this->controls;
    }

    /**
     * @param mixed $controls
     */
    public function setControls($controls)
    {
        $this->controls = $controls;
    }

    public function __construct()
    {
        $this->controls = new ArrayCollection();
    }

    /**
     * @return mixed
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * @param mixed $position
     */
    public function setPosition($position)
    {
        $this->position = $position;
    }

    /**
     * Export all the translations associates to every fielset.
     *
     * @return Translations
     */
    public static function exportTranslations()
    {
        $translations = new Translations();
        $app = Application::getFacadeApplication();
        $db = $app->make('database')->connection();

        $r = $db->executeQuery('SELECT title FROM ExpressFormFieldSets');
        while ($row = $r->fetch()) {
            $fieldSetTitle = $row['title'];
            if (is_string($fieldSetTitle) && ($fieldSetTitle !== '')) {
                $translations->insert('FieldSetTitle', $fieldSetTitle);
            }
        }

        return $translations;
    }

    public function __clone()
    {
        $this->id = null;
        $this->controls = new ArrayCollection();
        $this->form = null;
    }

    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        return [
            'id' => $this->getId(),
            'title' => $this->getTitle(),
            'position' => $this->getPosition()
        ];
    }
}
