<?
namespace Concrete\Core\Page\Style;
use Database;
use Core;
/**
 * @Entity
 * @Table(name="PageStyleSets")
 */
class Set
{

    protected $containerClass;

    /**
     * @Id @Column(type="integer")
     * @GeneratedValue
     */
    protected $pssID;

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
    protected $spacingTop;

    /**
     * @Column(type="string")
     */
    protected $spacingBottom;

    /**
     * @Column(type="string")
     */
    protected $spacingLeft;

    /**
     * @Column(type="string")
     */
    protected $spacingRight;

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
     * @param mixed $spacingBottom
     */
    public function setSpacingBottom($spacingBottom)
    {
        $this->spacingBottom = $spacingBottom;
    }

    /**
     * @return mixed
     */
    public function getSpacingBottom()
    {
        return $this->spacingBottom;
    }

    /**
     * @param mixed $spacingLeft
     */
    public function setSpacingLeft($spacingLeft)
    {
        $this->spacingLeft = $spacingLeft;
    }

    /**
     * @return mixed
     */
    public function getSpacingLeft()
    {
        return $this->spacingLeft;
    }

    /**
     * @param mixed $spacingRight
     */
    public function setSpacingRight($spacingRight)
    {
        $this->spacingRight = $spacingRight;
    }

    /**
     * @return mixed
     */
    public function getSpacingRight()
    {
        return $this->spacingRight;
    }

    /**
     * @param mixed $spacingTop
     */
    public function setSpacingTop($spacingTop)
    {
        $this->spacingTop = $spacingTop;
    }

    /**
     * @return mixed
     */
    public function getSpacingTop()
    {
        return $this->spacingTop;
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
        return $this->pssID;
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
     * @param $pssID
     * @return \Concrete\Core\Page\Style\Set
     */
    public function getByID($pssID)
    {
        $db = Database::get();
        $em = $db->getEntityManager();
        return $em->find('\Concrete\Core\Page\Style\Set', $pssID);
    }

    public function save()
    {
        $db = Database::get();
        $em = $db->getEntityManager();
        $em->persist($this);
        $em->flush();
    }

    public function getContainerClass()
    {
        if (isset($this->containerClass)) {
            return $this->containerClass;
        } else {
            return 'ccm-custom-style-style-set-' . $this->getID();
        }
    }

    public function setContainerClass($class)
    {
        $this->containerClass = $class;
    }

    public function getCSS()
    {
        $css = '.' . $this->getContainerClass() . '{';
        if ($this->backgroundColor) {
            $css .= 'background-color:' . $this->backgroundColor . ';';
        }
        if ($this->backgroundImageFileID) {
            $f = $this->getBackgroundImageFileObject();
            if (is_object($f)) {
                $css .= 'background-image: url(' . $f->getRelativePath() . ');';
                $css .= 'background-repeat: ' . $this->backgroundRepeat . ';';
            }
        }
        if ($this->baseFontSize) {
            $css .= 'font-size:' . $this->baseFontSize . ';';
        }
        if ($this->textColor) {
            $css .= 'color:' . $this->textColor . ';';
        }
        if ($this->baseFontSize) {
            $css .= 'font-size:' . $this->baseFontSize . ';';
        }
        $css .= '}';

        if ($this->linkColor) {
            $css .= '.' . $this->getContainerClass() . ' a {';
                $css .= 'color:' . $this->linkColor . ' !important;';
            $css .= '}';
        }
        return $css;
    }

    /**
     * Utility method for generating these classes
     */
    public static function generateCustomStyleContainerClass()
    {
        $args = func_get_args();
        $class = 'ccm-custom-style-';
        $txt = Core::make('helper/text');
        foreach($args as $ag) {
            $class .= strtolower($txt->filterNonAlphaNum($ag)) . '-';
        }
        return trim($class, '-');
    }

}