<?php

namespace Concrete\Controller;

use Concrete\Controller\Backend\UserInterface as BackendUserInterfaceController;
use Concrete\Core\Cache\Cache;
use Concrete\Core\Marketplace\PackageRepositoryInterface;
use Concrete\Core\Permission\Checker;
use Concrete\Core\System\Mutex\MutexInterface;
use Concrete\Core\Updater\Update;
use Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use View;

final class Marketplace extends BackendUserInterfaceController
{

    protected $validationToken = 'connect';

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Controller\Backend\UserInterface::canAccess()
     */
    public function canAccess()
    {
        $p = new Checker();
        return $p->canInstallPackages();
    }

    public function connect()
    {
        if ($this->validateAction()) {
            $repository = $this->app->make(PackageRepositoryInterface::class);
            $current = $repository->getConnection();
            if (!$current) {
                $connection = $repository->connect();
                return new JsonResponse($connection);
            }
        }
        throw new \Exception(t('Access Denied.'));
    }

}
