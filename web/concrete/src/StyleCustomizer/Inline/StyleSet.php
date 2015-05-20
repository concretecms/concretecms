<?php

namespace Concrete\Core\StyleCustomizer\Inline;

use Concrete\Core\Backup\ContentExporter;
use Concrete\Core\Backup\ContentImporter;
use Database;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Entity
 * @Table(name="StyleCustomizerInlineStyleSets")
 */
class StyleSet
{
    /**
     * @Id @Column(type="integer")
     * @GeneratedValue
     */
    protected $issID;

    /**
     * @Column(type="string")
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
     * @Column(type="string")
     */
    protected $backgroundColor;

    /**
     * @Column(type="integer")
     */
    protected $backgroundImageFileID = 0;

    /**
     * @Column(type="string")
     */
    protected $backgroundRepeat = 'no-repeat';

    /**
     * @Column(type="string")
     */
    protected $borderColor;

    /**
     * @Column(type="string")
     */
    protected $borderStyle;

    /**
     * @Column(type="string")
     */
    protected $borderWidth;

    /**
     * @Column(type="string")
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
     * @Column(type="string")
     */
    protected $baseFontSize;

    /**
     * @Column(type="string")
     */
    protected $alignment;

    /**
     * @Column(type="string")
     */
    protected $textColor;

    /**
     * @Column(type="string")
     */
    protected $linkColor;

    /**
     * @Column(type="string")
     */
    protected $marginTop;

    /**
     * @Column(type="string")
     */
    protected $marginBottom;

    /**
     * @Column(type="string")
     */
    protected $marginLeft;

    /**
     * @Column(type="string")
     */
    protected $marginRight;

    /**
     * @Column(type="string")
     */
    protected $paddingTop;

    /**
     * @Column(type="string")
     */
    protected $paddingBottom;

    /**
     * @Column(type="string")
     */
    protected $paddingLeft;

    /**
     * @Column(type="string")
     */
    protected $paddingRight;

    /**
     * @Column(type="string")
     */
    protected $rotate;

    /**
     * @Column(type="string")
     */
    protected $boxShadowHorizontal;

    /**
     * @Column(type="string")
     */
    protected $boxShadowVertical;

    /**
     * @Column(type="string")
     */
    protected $boxShadowBlur;

    /**
     * @Column(type="string")
     */
    protected $boxShadowSpread;

    /**
     * @Column(type="string")
     */
    protected $boxShadowColor;

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
     * @param $issID
     *
     * @return \Concrete\Core\Page\Style\Set
     */
    public static function getByID($issID)
    {
        $db = Database::get();
        $em = $db->getEntityManager();

        return $em->find('\Concrete\Core\StyleCustomizer\Inline\StyleSet', $issID);
    }

    public function save()
    {
        $db = Database::get();
        $em = $db->getEntityManager();
        $em->persist($this);
        $em->flush();
    }

    public static function import(\SimpleXMLElement $node)
    {
        $o = new self();
        $o->setBackgroundColor((string) $node->backgroundColor);
        $filename = (string) $node['backgroundImage'];
        if ($filename) {
            $fID = ContentImporter::getValue($filename);
            if ($fID) {
                $o->setBackgroundImageFileID($fID);
            }
        }

        $o->setBackgroundRepeat((string) $node->backgroundRepeat);
        $o->setBorderWidth((string) $node->borderWidth);
        $o->setBorderColor((string) $node->borderColor);
        $o->setBorderStyle((string) $node->borderStyle);
        $o->setBorderRadius((string) $node->borderRadius);
        $o->setBaseFontSize((string) $node->baseFontSize);
        $o->setAlignment((string) $node->alignment);
        $o->setTextColor((string) $node->textColor);
        $o->setLinkColor((string) $node->linkColor);
        $o->setPaddingTop((string) $node->paddingTop);
        $o->setPaddingBottom((string) $node->paddingBottom);
        $o->setPaddingLeft((string) $node->paddingLeft);
        $o->setPaddingRight((string) $node->paddingRight);
        $o->setMarginTop((string) $node->marginTop);
        $o->setMarginBottom((string) $node->marginBottom);
        $o->setMarginLeft((string) $node->marginLeft);
        $o->setMarginRight((string) $node->marginRight);
        $o->setRotate((string) $node->rotate);
        $o->setBoxShadowHorizontal((string) $node->boxShadowHorizontal);
        $o->setBoxShadowVertical((string) $node->boxShadowVertical);
        $o->setBoxShadowSpread((string) $node->boxShadowSpread);
        $o->setBoxShadowBlur((string) $node->boxShadowBlur);
        $o->setBoxShadowColor((string) $node->boxShadowColor);
        $o->setCustomClass((string) $node->customClass);
        $o->save();

        return $o;
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
    }

    /**
     * If the request contains any fields that are valid to save as a style set, we return the style set object
     * pre-save. If it's not (e.g. there's a background repeat but no actual background image, empty strings, etc...)
     * then we return null.
     * return \Concrete\Core\StyleCustomizer\Inline\StyleSet|null
     * @param Request $request
     */
    public static function populateFromRequest(Request $request)
    {
        $r = $request->request->all();
        $set = new StyleSet();
        $return = false;
        if (trim($r['backgroundColor']) != '') {
            $set->setBackgroundColor($r['backgroundColor']);
            $set->setBackgroundRepeat($r['backgroundRepeat']);
            $return = true;
        }

        $fID = intval($r['backgroundImageFileID']);
        if ($fID > 0) {
            $set->setBackgroundImageFileID($fID);
            $set->setBackgroundRepeat($r['backgroundRepeat']);
            $return = true;
        }

        if (trim($r['linkColor']) != '') {
            $set->setLinkColor($r['linkColor']);
            $return = true;
        }

        if (trim($r['textColor']) != '') {
            $set->setTextColor($r['textColor']);
            $return = true;
        }

        if (trim($r['baseFontSize']) && trim($r['baseFontSize']) != '0px') {
            $set->setBaseFontSize($r['baseFontSize']);
            $return = true;
        }

        if (trim($r['marginTop']) && trim($r['marginTop']) != '0px') {
            $set->setMarginTop($r['marginTop']);
            $return = true;
        }

        if (trim($r['marginRight']) && trim($r['marginRight']) != '0px') {
            $set->setMarginRight($r['marginRight']);
            $return = true;
        }

        if (trim($r['marginBottom']) && trim($r['marginBottom']) != '0px') {
            $set->setMarginBottom($r['marginBottom']);
            $return = true;
        }

        if (trim($r['marginLeft']) && trim($r['marginLeft']) != '0px') {
            $set->setMarginLeft($r['marginLeft']);
            $return = true;
        }

        if (trim($r['paddingTop']) && trim($r['paddingTop']) != '0px') {
            $set->setPaddingTop($r['paddingTop']);
            $return = true;
        }

        if (trim($r['paddingRight']) && trim($r['paddingRight']) != '0px') {
            $set->setPaddingRight($r['paddingRight']);
            $return = true;
        }

        if (trim($r['paddingBottom']) && trim($r['paddingBottom']) != '0px') {
            $set->setPaddingBottom($r['paddingBottom']);
            $return = true;
        }

        if (trim($r['paddingLeft']) && trim($r['paddingLeft']) != '0px') {
            $set->setPaddingLeft($r['paddingLeft']);
            $return = true;
        }

        if (trim($r['borderWidth']) && trim($r['borderWidth']) != '0px') {
            $set->setBorderWidth($r['borderWidth']);
            $set->setBorderStyle($r['borderStyle']);
            $set->setBorderColor($r['borderColor']);
            $set->setBorderRadius($r['borderRadius']);
            $return = true;
        }

        if (trim($r['alignment']) != '') {
            $set->setAlignment($r['alignment']);
            $return = true;
        }

        if ($r['rotate']) {
            $set->setRotate($r['rotate']);
            $return = true;
        }

        if ((trim($r['boxShadowHorizontal']) && trim($r['boxShadowHorizontal']) != '0px')
        || (trim($r['boxShadowVertical']) && trim($r['boxShadowVertical']) != '0px')) {
            $set->setBoxShadowBlur($r['boxShadowBlur']);
            $set->setBoxShadowColor($r['boxShadowColor']);
            $set->setBoxShadowHorizontal($r['boxShadowHorizontal']);
            $set->setBoxShadowVertical($r['boxShadowVertical']);
            $set->setBoxShadowSpread($r['boxShadowSpread']);
            $return = true;
        }

        if ($r['customClass']) {
            $set->setCustomClass($r['customClass']);
            $return = true;
        }

        if ($return) {
            return $set;
        }

        return null;
    }
}
