<?php
namespace Concrete\Core\Entity\Page\Theme;

use Concrete\Core\Page\Theme\Theme;
use Concrete\Core\StyleCustomizer\Skin\SkinInterface;
use Doctrine\ORM\Mapping as ORM;
use HtmlObject\Element;

/**
 * @ORM\Entity
 * @ORM\Table(
 *     name="PageThemeCustomSkins"
 * )
 */
class CustomSkin implements \JsonSerializable, SkinInterface
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    protected $skinID;

    /**
     * @ORM\Column(type="string")
     */
    protected $skinIdentifier = '';

    /**
     * @ORM\Column(type="integer", options={"unsigned": true})
     */
    protected $pThemeID;

    /**
     * @ORM\Column(type="string")
     * @ORM\OrderBy({"skinName" = "ASC"})
     */
    protected $skinName = '';

    /**
     * @ORM\Column(type="string")
     */
    protected $presetStartingPoint = '';

    /**
     * @ORM\ManyToOne(targetEntity="\Concrete\Core\Entity\User\User")
     * @ORM\JoinColumn(name="uID", referencedColumnName="uID")
     */
    protected $author;

    /**
     * @ORM\Column(type="integer", options={"unsigned": true})
     */
    protected $dateCreated;

    /**
     * @ORM\Column(type="integer", options={"unsigned": true})
     */
    protected $dateUpdated;

    /**
     * @ORM\Column(type="json")
     */
    protected $variableCollection;

    /**
     * @ORM\Column(type="text")
     */
    protected $customCss;

    /**
     * @return mixed
     */
    public function getSkinID()
    {
        return $this->skinID;
    }

    /**
     * @param mixed $skinID
     */
    public function setSkinID($skinID): void
    {
        $this->skinID = $skinID;
    }

    /**
     * @return string
     */
    public function getSkinIdentifier(): string
    {
        return $this->skinIdentifier;
    }

    /**
     * @param string $skinIdentifier
     */
    public function setSkinIdentifier(string $skinIdentifier): void
    {
        $this->skinIdentifier = $skinIdentifier;
    }

    /**
     * @return string
     */
    public function getSkinName(): string
    {
        return $this->skinName;
    }

    /**
     * @param string $skinName
     */
    public function setSkinName(string $skinName): void
    {
        $this->skinName = $skinName;
    }

    /**
     * @return mixed
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * @param mixed $author
     */
    public function setAuthor($author): void
    {
        $this->author = $author;
    }

    /**
     * @return mixed
     */
    public function getDateCreated()
    {
        return $this->dateCreated;
    }

    /**
     * @param mixed $dateCreated
     */
    public function setDateCreated($dateCreated): void
    {
        $this->dateCreated = $dateCreated;
    }

    /**
     * @return mixed
     */
    public function getDateUpdated()
    {
        return $this->dateUpdated;
    }

    /**
     * @param mixed $dateUpdated
     */
    public function setDateUpdated($dateUpdated): void
    {
        $this->dateUpdated = $dateUpdated;
    }

    /**
     * @return mixed
     */
    public function getVariableCollection()
    {
        return $this->variableCollection;
    }

    /**
     * @param mixed $variableCollection
     */
    public function setVariableCollection($variableCollection): void
    {
        $this->variableCollection = $variableCollection;
    }

    /**
     * @return mixed
     */
    public function getThemeID()
    {
        return $this->pThemeID;
    }

    /**
     * @param mixed $pThemeID
     */
    public function setThemeID($pThemeID): void
    {
        $this->pThemeID = $pThemeID;
    }

    /**
     * @return mixed
     */
    public function getCustomCss()
    {
        return $this->customCss;
    }

    /**
     * @param mixed $customCss
     */
    public function setCustomCss($customCss): void
    {
        $this->customCss = $customCss;
    }

    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        return [
            'skinID' => $this->getSkinID(),
            'skinIdentifier' => $this->getSkinIdentifier(),
            'skinName' => $this->getSkinName(),
            'name' => $this->getSkinName(),
            'identifier' => $this->getSkinIdentifier(),
        ];
    }

    public function getIdentifier(): string
    {
        return $this->getSkinIdentifier();
    }

    public function getName(): string
    {
        return $this->getSkinName();
    }

    /**
     * @return string
     */
    public function getPresetStartingPoint(): string
    {
        return $this->presetStartingPoint;
    }

    /**
     * @param string $presetStartingPoint
     */
    public function setPresetStartingPoint(string $presetStartingPoint): void
    {
        $this->presetStartingPoint = $presetStartingPoint;
    }

    public function getTheme(): Theme
    {
        return Theme::getByID($this->getThemeID());
    }

    public function getStylesheet(): Element
    {
        $stylesheet = REL_DIR_FILES_UPLOADED_STANDARD . '/' . DIRNAME_STYLE_CUSTOMIZER_PRESETS . '/' . $this->getIdentifier() . '.css';
        $element = new Element('link', null);
        $element->setIsSelfClosing(true);
        $element->rel('stylesheet')->type('text/css')->href($stylesheet);
        return $element;
    }

}
