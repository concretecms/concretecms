<?php
namespace Concrete\Core\Entity\StyleCustomizer\Inline;

use Concrete\Core\Backup\ContentExporter;
use Concrete\Core\Page\Theme\GridFramework\GridFramework;
use Database;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="StyleCustomizerInlineStyleSets")
 */
class StyleSet
{
    /**
     * @ORM\Id @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    protected $issID;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $customClass;

    /**
     * @param mixed $customClass
     */
    public function setCustomClass($customClass)
    {
        $this->customClass = $customClass;
    }

    /**
     * @return mixed
     */
    public function getCustomClass()
    {
        return $this->customClass;
    }

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $customID;

    /**
     * @param mixed $customID
     */
    public function setCustomID($customID)
    {
        $this->customID = $customID;
    }

    /**
     * @return mixed
     */
    public function getCustomID()
    {
        return $this->customID;
    }

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $customElementAttribute;

    /**
     * @param mixed $customElementAttribute
     */
    public function setCustomElementAttribute($customElementAttribute)
    {
        $this->customElementAttribute = $customElementAttribute;
    }

    /**
     * @return mixed
     */
    public function getCustomElementAttribute()
    {
        return $this->customElementAttribute;
    }

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $backgroundColor;

    /**
     * @ORM\Column(type="integer")
     */
    protected $backgroundImageFileID = 0;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $backgroundRepeat = 'no-repeat';

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $backgroundSize = 'auto';

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $backgroundPosition = '0% 0%';

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $borderColor;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $borderStyle;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $borderWidth;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $borderRadius;

    /**
     * @param mixed $borderWidth
     */
    public function setBorderWidth($borderWidth)
    {
        $this->borderWidth = $borderWidth;
    }

    /**
     * @return mixed
     */
    public function getBorderWidth()
    {
        return $this->borderWidth;
    }

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $baseFontSize;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $alignment;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $textColor;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $linkColor;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $marginTop;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $marginBottom;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $marginLeft;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $marginRight;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $paddingTop;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $paddingBottom;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $paddingLeft;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $paddingRight;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $rotate;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $boxShadowHorizontal;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $boxShadowVertical;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $boxShadowBlur;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $boxShadowSpread;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $boxShadowColor;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    protected $hideOnExtraSmallDevice = false;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    protected $hideOnSmallDevice = false;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    protected $hideOnMediumDevice = false;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    protected $hideOnLargeDevice = false;

    /**
     * @param mixed $alignment
     */
    public function setAlignment($alignment)
    {
        $this->alignment = $alignment;
    }

    /**
     * @return mixed
     */
    public function getAlignment()
    {
        return $this->alignment;
    }

    /**
     * @param mixed $backgroundRepeat
     */
    public function setBackgroundRepeat($backgroundRepeat)
    {
        $this->backgroundRepeat = $backgroundRepeat;
    }

    /**
     * @return mixed
     */
    public function getBackgroundRepeat()
    {
        return $this->backgroundRepeat;
    }

    /**
     * @param mixed $backgroundSize
     */
    public function setBackgroundSize($backgroundSize)
    {
        $this->backgroundSize = $backgroundSize;
    }

    /**
     * @return mixed
     */
    public function getBackgroundSize()
    {
        return $this->backgroundSize;
    }

    /**
     * @param mixed $backgroundPosition
     */
    public function setBackgroundPosition($backgroundPosition)
    {
        $this->backgroundPosition = $backgroundPosition;
    }

    /**
     * @return mixed
     */
    public function getBackgroundPosition()
    {
        return $this->backgroundPosition;
    }

    /**
     * @param mixed $baseFontSize
     */
    public function setBaseFontSize($baseFontSize)
    {
        $this->baseFontSize = $baseFontSize;
    }

    /**
     * @return mixed
     */
    public function getBaseFontSize()
    {
        return $this->baseFontSize;
    }

    /**
     * @param mixed $borderColor
     */
    public function setBorderColor($borderColor)
    {
        $this->borderColor = $borderColor;
    }

    /**
     * @return mixed
     */
    public function getBorderColor()
    {
        return $this->borderColor;
    }

    /**
     * @param mixed $borderStyle
     */
    public function setBorderStyle($borderStyle)
    {
        $this->borderStyle = $borderStyle;
    }

    /**
     * @return mixed
     */
    public function getBorderStyle()
    {
        return $this->borderStyle;
    }

    /**
     * @param mixed $borderStyle
     */
    public function setBorderRadius($borderRadius)
    {
        $this->borderRadius = $borderRadius;
    }

    /**
     * @return mixed
     */
    public function getBorderRadius()
    {
        return $this->borderRadius;
    }

    /**
     * @param mixed $boxShadowBlur
     */
    public function setBoxShadowBlur($boxShadowBlur)
    {
        $this->boxShadowBlur = $boxShadowBlur;
    }

    /**
     * @return mixed
     */
    public function getBoxShadowBlur()
    {
        return $this->boxShadowBlur;
    }

    /**
     * @param mixed $boxShadowColor
     */
    public function setBoxShadowColor($boxShadowColor)
    {
        $this->boxShadowColor = $boxShadowColor;
    }

    /**
     * @return mixed
     */
    public function getBoxShadowColor()
    {
        return $this->boxShadowColor;
    }

    /**
     * @param mixed $boxShadowHorizontal
     */
    public function setBoxShadowHorizontal($boxShadowHorizontal)
    {
        $this->boxShadowHorizontal = $boxShadowHorizontal;
    }

    /**
     * @return mixed
     */
    public function getBoxShadowHorizontal()
    {
        return $this->boxShadowHorizontal;
    }

    /**
     * @param mixed $boxShadowSpread
     */
    public function setBoxShadowSpread($boxShadowSpread)
    {
        $this->boxShadowSpread = $boxShadowSpread;
    }

    /**
     * @return mixed
     */
    public function getBoxShadowSpread()
    {
        return $this->boxShadowSpread;
    }

    /**
     * @param mixed $boxShadowVertical
     */
    public function setBoxShadowVertical($boxShadowVertical)
    {
        $this->boxShadowVertical = $boxShadowVertical;
    }

    /**
     * @return mixed
     */
    public function getBoxShadowVertical()
    {
        return $this->boxShadowVertical;
    }

    /**
     * @param mixed $linkColor
     */
    public function setLinkColor($linkColor)
    {
        $this->linkColor = $linkColor;
    }

    /**
     * @return mixed
     */
    public function getLinkColor()
    {
        return $this->linkColor;
    }

    /**
     * @param mixed $textColor
     */
    public function setTextColor($textColor)
    {
        $this->textColor = $textColor;
    }

    /**
     * @return mixed
     */
    public function getTextColor()
    {
        return $this->textColor;
    }

    /**
     * @param mixed $rotate
     */
    public function setRotate($rotate)
    {
        $this->rotate = $rotate;
    }

    /**
     * @return mixed
     */
    public function getRotate()
    {
        return $this->rotate;
    }

    public function getID()
    {
        return $this->issID;
    }

    /**
     * @param mixed $marginBottom
     */
    public function setMarginBottom($marginBottom)
    {
        $this->marginBottom = $marginBottom;
    }

    /**
     * @return mixed
     */
    public function getMarginBottom()
    {
        return $this->marginBottom;
    }

    /**
     * @param mixed $marginLeft
     */
    public function setMarginLeft($marginLeft)
    {
        $this->marginLeft = $marginLeft;
    }

    /**
     * @return mixed
     */
    public function getMarginLeft()
    {
        return $this->marginLeft;
    }

    /**
     * @param mixed $marginRight
     */
    public function setMarginRight($marginRight)
    {
        $this->marginRight = $marginRight;
    }

    /**
     * @return mixed
     */
    public function getMarginRight()
    {
        return $this->marginRight;
    }

    /**
     * @param mixed $marginTop
     */
    public function setMarginTop($marginTop)
    {
        $this->marginTop = $marginTop;
    }

    /**
     * @return mixed
     */
    public function getMarginTop()
    {
        return $this->marginTop;
    }

    /**
     * @param mixed $paddingBottom
     */
    public function setPaddingBottom($paddingBottom)
    {
        $this->paddingBottom = $paddingBottom;
    }

    /**
     * @return mixed
     */
    public function getPaddingBottom()
    {
        return $this->paddingBottom;
    }

    /**
     * @param mixed $paddingLeft
     */
    public function setPaddingLeft($paddingLeft)
    {
        $this->paddingLeft = $paddingLeft;
    }

    /**
     * @return mixed
     */
    public function getPaddingLeft()
    {
        return $this->paddingLeft;
    }

    /**
     * @param mixed $paddingRight
     */
    public function setPaddingRight($paddingRight)
    {
        $this->paddingRight = $paddingRight;
    }

    /**
     * @return mixed
     */
    public function getPaddingRight()
    {
        return $this->paddingRight;
    }

    /**
     * @param mixed $paddingTop
     */
    public function setPaddingTop($paddingTop)
    {
        $this->paddingTop = $paddingTop;
    }

    /**
     * @return mixed
     */
    public function getPaddingTop()
    {
        return $this->paddingTop;
    }

    public function setBackgroundColor($backgroundColor)
    {
        $this->backgroundColor = $backgroundColor;
    }

    public function setBackgroundImageFileID($backgroundImageFileID)
    {
        $this->backgroundImageFileID = $backgroundImageFileID;
    }

    public function getBackgroundColor()
    {
        return $this->backgroundColor;
    }

    public function getBackgroundImageFileID()
    {
        return $this->backgroundImageFileID;
    }

    public function getBackgroundImageFileObject()
    {
        if ($this->backgroundImageFileID) {
            $f = \File::getByID($this->backgroundImageFileID);

            return $f;
        }
    }

    /**
     * @return mixed
     */
    public function getHideOnExtraSmallDevice()
    {
        return $this->hideOnExtraSmallDevice;
    }

    /**
     * @param mixed $hideOnExtraSmallDevice
     */
    public function setHideOnExtraSmallDevice($hideOnExtraSmallDevice)
    {
        $this->hideOnExtraSmallDevice = $hideOnExtraSmallDevice;
    }

    /**
     * @return mixed
     */
    public function getHideOnSmallDevice()
    {
        return $this->hideOnSmallDevice;
    }

    /**
     * @param mixed $hideOnSmallDevice
     */
    public function setHideOnSmallDevice($hideOnSmallDevice)
    {
        $this->hideOnSmallDevice = $hideOnSmallDevice;
    }

    /**
     * @return mixed
     */
    public function getHideOnMediumDevice()
    {
        return $this->hideOnMediumDevice;
    }

    /**
     * @param mixed $hideOnMediumDevice
     */
    public function setHideOnMediumDevice($hideOnMediumDevice)
    {
        $this->hideOnMediumDevice = $hideOnMediumDevice;
    }

    /**
     * @return mixed
     */
    public function getHideOnLargeDevice()
    {
        return $this->hideOnLargeDevice;
    }

    /**
     * @param mixed $hideOnLargeDevice
     */
    public function setHideOnLargeDevice($hideOnLargeDevice)
    {
        $this->hideOnLargeDevice = $hideOnLargeDevice;
    }

    public function save()
    {
        $em = \ORM::entityManager();
        $em->persist($this);
        $em->flush();
    }

    public function export(\SimpleXMLElement $node)
    {
        $node = $node->addChild('style');
        $node->addChild('backgroundColor', $this->getBackgroundColor());
        $fID = $this->backgroundImageFileID;
        if ($fID) {
            $node->addChild('backgroundImage', ContentExporter::replaceFileWithPlaceHolder($fID));
        }
        $node->addChild('backgroundRepeat', $this->getBackgroundRepeat());
        $node->addChild('backgroundSize', $this->getBackgroundSize());
        $node->addChild('backgroundPosition', $this->getBackgroundPosition());
        $node->addChild('borderWidth', $this->getBorderWidth());
        $node->addChild('borderColor', $this->getBorderColor());
        $node->addChild('borderStyle', $this->getBorderStyle());
        $node->addChild('borderRadius', $this->getBorderRadius());
        $node->addChild('baseFontSize', $this->getBaseFontSize());
        $node->addChild('alignment', $this->getAlignment());
        $node->addChild('textColor', $this->getTextColor());
        $node->addChild('linkColor', $this->getLinkColor());
        $node->addChild('paddingTop', $this->getPaddingTop());
        $node->addChild('paddingBottom', $this->getPaddingBottom());
        $node->addChild('paddingLeft', $this->getPaddingLeft());
        $node->addChild('paddingRight', $this->getPaddingRight());
        $node->addChild('marginTop', $this->getMarginTop());
        $node->addChild('marginBottom', $this->getMarginBottom());
        $node->addChild('marginLeft', $this->getMarginLeft());
        $node->addChild('marginRight', $this->getMarginRight());
        $node->addChild('rotate', $this->getRotate());
        $node->addChild('boxShadowHorizontal', $this->getBoxShadowHorizontal());
        $node->addChild('boxShadowVertical', $this->getBoxShadowVertical());
        $node->addChild('boxShadowBlur', $this->getBoxShadowBlur());
        $node->addChild('boxShadowSpread', $this->getBoxShadowSpread());
        $node->addChild('boxShadowColor', $this->getBoxShadowColor());
        $node->addChild('customClass', $this->getCustomClass());
        $node->addChild('customID', $this->getCustomID());
        $node->addChild('customElementAttribute', $this->getCustomElementAttribute());
        $node->addChild('hideOnExtraSmallDevice', $this->getHideOnExtraSmallDevice());
        $node->addChild('hideOnSmallDevice', $this->getHideOnSmallDevice());
        $node->addChild('hideOnMediumDevice', $this->getHideOnMediumDevice());
        $node->addChild('hideOnLargeDevice', $this->getHideOnLargeDevice());
    }

    public function isHiddenOnDevice($class)
    {
        switch ($class) {
            case GridFramework::DEVICE_CLASSES_HIDE_ON_EXTRA_SMALL:
                return $this->getHideOnExtraSmallDevice();
            case GridFramework::DEVICE_CLASSES_HIDE_ON_SMALL:
                return $this->getHideOnSmallDevice();
            case GridFramework::DEVICE_CLASSES_HIDE_ON_MEDIUM:
                return $this->getHideOnMediumDevice();
            case GridFramework::DEVICE_CLASSES_HIDE_ON_LARGE:
                return $this->getHideOnLargeDevice();
        }
    }
}
