<?
namespace Concrete\Core\Page\Style;
use Database;
/**
 * @Entity
 * @Table(name="PageStyleSets")
 */
class Set
{

    /**
     * @Id @Column(type="integer")
     * @GeneratedValue
     */
    protected $pssID;

    /**
     * @Column(type="string")
     */
    protected $backgroundColor;

    public function getID()
    {
        return $this->pssID;
    }

    public function setBackgroundColor($backgroundColor)
    {
        $this->backgroundColor = $backgroundColor;
    }

    public function getBackgroundColor()
    {
        return $this->backgroundColor;
    }

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


}