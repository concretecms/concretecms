<?php

namespace Concrete\Core\File\Component\Chooser\Option;

use Concrete\Core\Entity\User\User;
use Concrete\Core\File\Component\Chooser\ChooserOptionInterface;
use Concrete\Core\File\Component\Chooser\OptionSerializableTrait;
use Concrete\Core\File\Filesystem;
use Concrete\Core\Support\Facade\Application;
use Doctrine\ORM\EntityManagerInterface;

class FileManagerOption implements ChooserOptionInterface
{

    use OptionSerializableTrait;

    public function getComponentKey(): string
    {
        return 'file-manager';
    }

    public function getTitle(): string
    {
        return t('File Manager');
    }

    public function getId()
    {
        $user = new \Concrete\Core\User\User();
        $app = Application::getFacadeApplication();
        /** @var EntityManagerInterface $entityManager */
        $entityManager = $app->make(EntityManagerInterface::class);
        $userRepository = $entityManager->getRepository(User::class);
        /** @var User $userEntity */
        $userEntity = $userRepository->findOneBy(["uID" => $user->getUserID()]);

        $session = $app->make('session');
        if ($session->has('concrete.file_manager.chooser.folder_id')) {
            return $session->get('concrete.file_manager.chooser.folder_id');
        }

        if ($userEntity->getHomeFileManagerFolderID() === null) {
            $fileSystem = new Filesystem();
            return (string)$fileSystem->getRootFolder()->getTreeNodeID();
        } else {
            return (string)$userEntity->getHomeFileManagerFolderID();
        }
    }

}