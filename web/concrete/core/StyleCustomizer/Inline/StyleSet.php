<?
namespace Concrete\Core\StyleCustomizer\Inline;
use Database;
use Core;
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

    public function getBackgroundImageFileObject()
    {
        $f = \File::getByID($this->backgroundImageFileID);
        return $f;
    }

    /**
     * @param $issID
     * @return \Concrete\Core\Page\Style\Set
     */
    public function getByID($issID)
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
}