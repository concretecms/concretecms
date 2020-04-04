<?php
namespace Concrete\Core\Board\Permissions;

use Concrete\Core\Entity\Board\Board;
use Concrete\Core\Entity\Board\BoardPermissionAssignment;
use Concrete\Core\Permission\Key\Key;
use Doctrine\ORM\EntityManagerInterface;

class PermissionsManager
{
    protected $entityManager;

    public function __construct(EntityManagerInterface $entityManagerInterface)
    {
        $this->entityManager = $entityManagerInterface;
    }

    public function clearCustomPermissions(Board $board)
    {
        $em = $this->entityManager;

        $board->setOverridePermissions(false);

        $assignments = $em->getRepository(BoardPermissionAssignment::class)->findByBoard($board);
        foreach($assignments as $assignment) {
            $em->remove($assignment);
        }
        $em->persist($board);
        $em->flush();
    }

    public function setPermissionsToOverride(Board $board)
    {
        if (!$board->arePermissionsSetToOverride()) {
            $this->clearCustomPermissions($board);

            $em = $this->entityManager;

            $permissions = Key::getList('board');
            foreach ($permissions as $pk) {
                $pk->setPermissionObject($board);
                $paID = $pk->getPermissionAccessID();
                if ($paID) {
                    $assignment = new BoardPermissionAssignment();
                    $assignment->setPermissionAccessID($paID);
                    $assignment->setPermissionKeyID($pk->getPermissionKeyID());
                    $assignment->setBoard($board);
                    $em->persist($assignment);
                }
            }

            $board->setOverridePermissions(true);
            $em->persist($board);
            $em->flush();
        }
    }

}
