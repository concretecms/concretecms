<?php
namespace Concrete\Controller\SinglePage\Dashboard\Boards;

use Concrete\Core\Board\Permissions\PermissionsManager;
use Concrete\Core\Entity\Board\Board;
use Concrete\Core\Page\Controller\DashboardSitePageController;
use Concrete\Core\Permission\Access\Access;
use Concrete\Core\Permission\Checker;
use Concrete\Core\Permission\Key\Key;

class Permissions extends DashboardSitePageController
{

    public function update_permissions_inheritance()
    {
        $board = $this->getBoard($this->request->request->get('boardID'));
        if (!$this->token->validate('update_permissions_inheritance')) {
            $this->error->add($this->token->getErrorMessage());
        }
        if (!is_object($board)) {
            $this->error->add(t('Invalid board.'));
        }

        $cp = new \Permissions($board);
        if (!$cp->canEditBoardPermissions()) {
            $this->error->add(t('Access Denied.'));
        }

        if (!$this->error->has()) {
            $override = $this->request->request->get('update_inheritance') == 'override' ? true : false;

            $manager = new PermissionsManager($this->entityManager);

            if ($override) {
                $manager->setPermissionsToOverride($board);
            } else {
                $manager->clearCustomPermissions($board);
            }

            $this->flash('success', t('Permissions updated successfully.'));
            $this->redirect('/dashboard/boards/permissions', 'view', $board->getBoardID());
        } else {
            $this->view($this->request->request->get('boardID'));
        }
    }

    public function save_permissions()
    {
        if (!$this->token->validate('save_permissions')) {
            $this->error->add($this->token->getErrorMessage());
        }

        $board = $this->getBoard($this->request->request->get('boardID'));
        if (!is_object($board)) {
            $this->error->add(t('Invalid board.'));
        }

        $cp = new \Permissions($board);
        if (!$cp->canEditBoardPermissions()) {
            $this->error->add(t('Access Denied.'));
        }

        if (!$this->error->has()) {
            $permissions = Key::getList('board');
            foreach ($permissions as $pk) {
                $pk->setPermissionObject($board);
                $paID = $_POST['pkID'][$pk->getPermissionKeyID()];
                $pt = $pk->getPermissionAssignmentObject();
                $pt->clearPermissionAssignment();
                if ($paID > 0) {
                    $pa = Access::getByID($paID, $pk);
                    if (is_object($pa)) {
                        $pt->assignPermissionAccess($pa);
                    }
                }
            }
            $this->flash('success', t('Permissions saved successfully.'));
            $this->redirect('/dashboard/boards/permissions', 'view', $board->getBoardID());
        }

        $this->view($this->request->request->get('caID'));
    }


    /**
     * @param $id
     * @return Board
     */
    protected function getBoard($id)
    {
        $r = $this->entityManager->getRepository(Board::class);
        $board = $r->findOneByBoardID($id);
        if ($board) {
            $checker = new Checker($board);
            if ($checker->canEditBoardPermissions()) {
                return $board;
            }
        }
    }
    
    public function view($id = null)
    {
        $board = $this->getBoard($id);
        if (is_object($board)) {
            $this->set('board', $board);
        } else {
            return $this->redirect('/dashboard/boards/boards');
        }
    }


}
