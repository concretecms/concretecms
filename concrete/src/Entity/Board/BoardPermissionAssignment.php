<?php
namespace Concrete\Core\Entity\Board;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="BoardPermissionAssignments", indexes={
 * @ORM\Index(name="paID", columns={"paID"}),
 * @ORM\Index(name="pkID", columns={"pkID"})
 * })
 */
class BoardPermissionAssignment
{

    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="Board", inversedBy="permission_assignments")
     * @ORM\JoinColumn(name="boardID", referencedColumnName="boardID")
     */
    protected $board;

    /**
     * @ORM\Id @ORM\Column(type="integer", nullable=false, options={"unsigned": true})
     */
    protected $pkID;


    /**
     * @ORM\Id @ORM\Column(type="integer", nullable=false, options={"unsigned": true})
     */
    protected $paID;

    /**
     * @return mixed
     */
    public function getBoard()
    {
        return $this->board;
    }

    /**
     * @param mixed $board
     */
    public function setBoard($board): void
    {
        $this->board = $board;
    }

    /**
     * @return mixed
     */
    public function getPermissionKeyID()
    {
        return $this->pkID;
    }

    /**
     * @param mixed $pkID
     */
    public function setPermissionKeyID($pkID)
    {
        $this->pkID = $pkID;
    }

    /**
     * @return mixed
     */
    public function getPermissionAccessID()
    {
        return $this->paID;
    }

    /**
     * @param mixed $paID
     */
    public function setPermissionAccessID($paID)
    {
        $this->paID = $paID;
    }






}
