<?php
namespace Concrete\Core\Permission\Assignment;

use Concrete\Core\Entity\Board\Board;
use Concrete\Core\Permission\Access\Access;
use Concrete\Core\Permission\Key\Key;

class BoardAssignment extends Assignment
{
    protected $board;
    /**
     * @var Board
     */
    protected $permissionObject;

    protected $permissionObjectToCheck;
    protected $inheritedPermissions = array(
        'view_board' => 'view_boards',
        'edit_board_settings' => 'edit_boards_settings',
        'edit_board_permissions' => 'edit_boards_permissions',
        'edit_board_contents' => 'edit_boards_contents',
        'edit_board_locked_rules' => 'edit_boards_locked_rules',
        'delete_board' => 'delete_boards',
    );

    /**
     * @param $board Board
     */
    public function setPermissionObject($board)
    {
        $this->permissionObject = $board;

        // if the area overrides the collection permissions explicitly (with a one on the override column) we check
        if ($board->arePermissionsSetToOverride()) {
            $this->permissionObjectToCheck = $board;
        }
    }

    public function getPermissionAccessObject()
    {
        $db = \Database::connection();
        if ($this->permissionObjectToCheck instanceof Board) {
            $r = $db->GetOne('select paID from BoardPermissionAssignments where boardID = ? and pkID = ?', array(
                $this->permissionObject->getBoardID(), $this->pk->getPermissionKeyID(),
            ));
            if ($r) {
                return Access::getByID($r, $this->pk, false);
            }
        } elseif (isset($this->inheritedPermissions[$this->pk->getPermissionKeyHandle()])) {
            $pk = Key::getByHandle($this->inheritedPermissions[$this->pk->getPermissionKeyHandle()]);
            $pae = $pk->getPermissionAccessObject();

            return $pae;
        }

        return false;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Permission\Assignment\Assignment::getPermissionKeyTaskURL()
     */
    public function getPermissionKeyTaskURL(string $task = '', array $options = []): string
    {
        $board = $this->getPermissionObject();
        
        return parent::getPermissionKeyTaskURL($task, $options + ['boardID' => $board->getBoardID()]);
    }

    public function clearPermissionAssignment()
    {
        $db = \Database::connection();
        $board = $this->getPermissionObject();
        $db->Execute('update BoardPermissionAssignments set paID = 0 where pkID = ? and boardID = ?', array($this->pk->getPermissionKeyID(), $board->getBoardID()));
    }

    public function assignPermissionAccess(Access $pa)
    {
        $db = \Database::connection();
        $db->Replace(
            'BoardPermissionAssignments',
            array(
                'boardID' => $this->getPermissionObject()->getBoardID(),
                'paID' => $pa->getPermissionAccessID(),
                'pkID' => $this->pk->getPermissionKeyID(),
            ),
            array('boardID', 'pkID'),
            true
        );
        $pa->markAsInUse();
    }
}
