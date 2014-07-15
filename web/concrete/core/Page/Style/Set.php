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
            }
        }
        $css .= '}';
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