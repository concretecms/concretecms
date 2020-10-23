<?php

namespace Concrete\Controller\Permissions\Access;

use Concrete\Core\Controller\Controller;
use Concrete\Core\Error\UserMessageException;
use Concrete\Core\Form\Service\Form;
use Concrete\Core\Http\ResponseFactoryInterface;
use Concrete\Core\Permission\Access\Entity\Entity as PermissionAccessEntity;
use Concrete\Core\Permission\Category;
use Concrete\Core\Permission\Checker;
use Concrete\Core\Permission\Duration;
use Concrete\Core\Url\Resolver\Manager\ResolverManagerInterface;
use Concrete\Core\Utility\Service\Validation\Numbers;
use Symfony\Component\HttpFoundation\Response;

defined('C5_EXECUTE') or die('Access Denied.');

class Entity extends Controller
{
    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Controller\Controller::$viewPath
     */
    protected $viewPath = '/backend/permissions/access/entity';

    public function view(): ?Response
    {
        $this->checkAccess();
        $disableDuration = $this->isDisableDuration();
        $this->set('pkCategory', $this->getPermissionKeyCategory());
        $this->set('accessType', $this->getAccessType());
        $this->set('pae', $this->getPermissionAccessEntity());
        $this->set('disableDuration', $disableDuration);
        $this->set('pd', $disableDuration ? null : $this->getPermissionDuration());
        $this->set('resolverManager', $this->app->make(ResolverManagerInterface::class));
        $this->set('form', $this->app->make(Form::class));

        return null;
    }

    public function save(): Response
    {
        $this->checkAccess();
        $pae = $this->getPermissionAccessEntity();
        if ($pae === null) {
            throw new UserMessageException(t('You must choose who this permission is for.'));
        }
        $pd = Duration::createFromRequest();

        return $this->app->make(ResponseFactoryInterface::class)->json([
            'peID' => $pae->getAccessEntityID(),
            'pdID' => $pd === null ? 0 : $pd->getPermissionDurationID(),
        ]);
    }

    /**
     * @throws \Concrete\Core\Error\UserMessageException
     */
    protected function checkAccess(): void
    {
        $permissionChecker = new Checker();
        if (!$permissionChecker->canAccessUserSearch() && !$permissionChecker->canAccessGroupSearch()) {
            throw new UserMessageException(t('You do not have user search or group search permissions.'));
        }
    }

    protected function getPermissionAccessEntityID(): ?int
    {
        $paeID = $this->request->get('peID');

        return $this->app->make(Numbers::class)->integer($paeID, 1) ? (int) $paeID : null;
    }

    /**
     * @throws \Concrete\Core\Error\UserMessageException
     */
    protected function getPermissionAccessEntity(): ?PermissionAccessEntity
    {
        $paeID = $this->getPermissionAccessEntityID();
        if ($paeID === null) {
            return null;
        }
        $pae = PermissionAccessEntity::getByID($paeID);
        if (!$pae) {
            throw new UserMessageException(t('Invalid permission access entity.'));
        }

        return $pae;
    }

    protected function getPermissionDurationID(): ?int
    {
        $pdID = $this->request->get('pdID');

        return $this->app->make(Numbers::class)->integer($pdID, 1) ? (int) $pdID : null;
    }

    /**
     * @throws \Concrete\Core\Error\UserMessageException
     */
    protected function getPermissionDuration(): ?Duration
    {
        $pdID = $this->getPermissionDurationID();
        if ($pdID === null) {
            return null;
        }
        $pd = Duration::getByID($pdID);
        if (!$pd) {
            throw new UserMessageException(t('Invalid permission duration.'));
        }

        return $pd;
    }

    protected function getAccessType(): string
    {
        $result = $this->request->get('accessType');

        return is_string($result) ? $result : '';
    }

    protected function getPermissionKeyCategoryHandle(): string
    {
        $result = $this->request->get('pkCategoryHandle');

        return is_string($result) ? $result : '';
    }

    protected function getPermissionKeyCategory(): ?Category
    {
        $handle = $this->getPermissionKeyCategoryHandle();
        if ($handle === '') {
            return null;
        }
        $category = Category::getByHandle($handle);
        if ($category === null) {
            throw new UserMessageException(t('Invalid permission category.'));
        }

        return $category;
    }

    protected function isDisableDuration(): bool
    {
        return !empty($this->request->request->get('disableDuration')) || !empty($this->request->query->get('disableDuration'));
    }
}
