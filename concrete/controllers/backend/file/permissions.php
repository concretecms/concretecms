<?php

namespace Concrete\Controller\Backend\File;

use Concrete\Core\Application\Service\UserInterface;
use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Controller\Controller;
use Concrete\Core\Entity\File\File;
use Concrete\Core\Entity\File\StorageLocation\StorageLocation;
use Concrete\Core\Error\UserMessageException;
use Concrete\Core\File\EditResponse;
use Concrete\Core\File\StorageLocation\StorageLocationFactory;
use Concrete\Core\Form\Service\Form;
use Concrete\Core\Http\ResponseFactoryInterface;
use Concrete\Core\Permission\Checker;
use Concrete\Core\Url\Resolver\Manager\ResolverManagerInterface;
use Concrete\Core\Utility\Service\Validation\Numbers;
use Concrete\Core\Validation\CSRF\Token;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;

defined('C5_EXECUTE') or die('Access Denied.');

class Permissions extends Controller
{
    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Controller\Controller::$viewPath
     */
    protected $viewPath = '/backend/file/permissions';

    public function view(): ?Response
    {
        $this->set('file', $this->getFile());
        $this->set('permissionsModel', $this->getPermissionsModel());
        $this->set('resolverManager', $this->app->make(ResolverManagerInterface::class));
        $this->set('storageLocations', $this->app->make(StorageLocationFactory::class)->fetchList());
        $this->set('token', $this->app->make(Token::class));
        $this->set('form', $this->app->make(Form::class));
        $this->set('ui', $this->app->make(UserInterface::class));

        return null;
    }

    public function setPassword(): Response
    {
        $file = $this->getFile();
        $this->checkCSRF("set_password_{$file->getFileID()}");
        $password = $this->request->request->get('fPassword');
        $file->setPassword(is_string($password) ? $password : '');
        $response = new EditResponse();
        $response->setFile($file);
        $response->setMessage(t('File password saved successfully.'));

        return $this->app->make(ResponseFactoryInterface::class)->json($response);
    }

    public function setLocation(): Response
    {
        $file = $this->getFile();
        $this->checkCSRF("set_location_{$file->getFileID()}");
        $fsl = $this->getFileStorageLocation();
        $file->setFileStorageLocation($fsl);
        $response = new EditResponse();
        $response->setFile($file);
        $response->setMessage(t('File storage location saved successfully.'));

        return $this->app->make(ResponseFactoryInterface::class)->json($response);
    }

    /**
     * @throws \Concrete\Core\Error\UserMessageException
     */
    protected function checkCSRF(string $action): void
    {
        $valt = $this->app->make(Token::class);
        if (!$valt->validate($action)) {
            throw new UserMessageException($valt->getErrorMessage());
        }
    }

    protected function getFileID(): ?int
    {
        $fID = $this->request->request->get('fID', $this->request->query->get('fID'));

        return $this->app->make(Numbers::class)->integer($fID, 1) ? (int) $fID : null;
    }

    /**
     * @throws \Concrete\Core\Error\UserMessageException
     */
    protected function getFile(): File
    {
        $fID = $this->getFileID();
        if ($fID === null) {
            throw new UserMessageException(t('Invalid parameters received.'));
        }
        $file = $this->app->make(EntityManagerInterface::class)->find(File::class, $fID);
        if ($file === null) {
            throw new UserMessageException(t('File Not Found.'));
        }
        $this->checkFileAccess($file);

        return $file;
    }

    /**
     * @throws \Concrete\Core\Error\UserMessageException
     */
    protected function checkFileAccess(File $file): void
    {
        $checker = new Checker($file);
        if (!$checker->canAdmin()) {
            throw new UserMessageException(t('Access Denied.'));
        }
    }

    protected function getFileStorageLocationID(): ?int
    {
        $fslID = $this->request->request->get('fslID');

        return $this->app->make(Numbers::class)->integer($fslID, 1) ? (int) $fslID : null;
    }

    protected function getFileStorageLocation(): StorageLocation
    {
        $fslID = $this->getFileStorageLocationID();

        $fsl = $fslID === null ? null : $this->app->make(EntityManagerInterface::class)->find(StorageLocation::class, $fslID);
        if ($fsl === null) {
            throw new UserMessageException(t('Invalid storage location.'));
        }

        return $fsl;
    }

    protected function getPermissionsModel(): string
    {
        return (string) $this->app->make(Repository::class)->get('concrete.permissions.model');
    }
}
