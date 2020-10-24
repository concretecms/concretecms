<?php

namespace Concrete\Controller\Permissions\Set;

use Concrete\Core\Controller\AbstractController;
use Concrete\Core\Error\UserMessageException;
use Concrete\Core\Http\ResponseFactoryInterface;
use Concrete\Core\Permission\Access\Access;
use Concrete\Core\Permission\Key\Key;
use Concrete\Core\Permission\Set;
use Concrete\Core\Utility\Service\Validation\Numbers;
use Concrete\Core\Validation\CSRF\Token;
use Concrete\Core\View\View;
use Symfony\Component\HttpFoundation\Response;

defined('C5_EXECUTE') or die('Access Denied.');

class Clipboard extends AbstractController
{
    public function copy(): Response
    {
        $this->checkCSRF('copy_permission_set');
        $set = new Set();
        $set->setPermissionKeyCategory($this->getCategoryHandle());
        $pkIDs = $this->getPermissionKeysAndEntityIDs();
        foreach ($pkIDs as $pkID => $paID) {
            $set->addPermissionAssignment($pkID, $paID);
        }
        $set->saveToSession();

        return $this->app->make(ResponseFactoryInterface::class)->json(['success' => true]);
    }

    public function paste(): Response
    {
        $this->checkCSRF('paste_permission_set');
        $set = Set::getSavedPermissionSetFromSession() ?: null;
        $r = [];
        if ($set instanceof Set) {
            if ($set->getPermissionKeyCategory() === $this->getCategoryHandle()) {
                foreach ($set->getPermissionAssignments() as $pkID => $paID) {
                    $pk = Key::getByID($pkID);
                    if ($pk !== null) {
                        $r[] = [
                            'pkID' => $pkID,
                            'paID' => $paID,
                            'html' => $this->render($pk, $paID),
                        ];
                    }
                }
            }
        }

        return $this->app->make(ResponseFactoryInterface::class)->json($r);
    }

    protected function checkCSRF(string $action): void
    {
        $valt = $this->app->make(Token::class);
        if (!$valt->validate($action)) {
            throw new UserMessageException($valt->getErrorMessage());
        }
    }

    protected function getCategoryHandle(): string
    {
        $pkCategoryHandle = $this->request->request->get('pkCategoryHandle');
        if (!is_string($pkCategoryHandle) || $pkCategoryHandle === '') {
            throw new UserMessageException(t('Invalid parameters received.'));
        }

        return $pkCategoryHandle;
    }

    protected function getPermissionKeysAndEntityIDs(): array
    {
        $pkIDs = $this->request->request->get('pkID');
        if (!is_array($pkIDs) || $pkIDs === []) {
            return [];
        }
        $valn = $this->app->make(Numbers::class);

        $pkIDs = array_filter(
            $pkIDs,
            static function ($paID, $pkID) use ($valn) {
                return $valn->integer($pkID, 1) && $valn->integer($paID, 1);
            },
            ARRAY_FILTER_USE_BOTH
        );

        return array_map('intval', $pkIDs);
    }

    protected function render(Key $pk, int $paID): string
    {
        $pa = Access::getByID($paID, $pk);
        ob_start();
        try {
            View::element('permission/labels', ['pk' => $pk, 'pa' => $pa]);

            return ob_get_contents();
        } finally {
            ob_end_clean();
        }
    }
}
