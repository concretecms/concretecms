<?php
namespace Concrete\Core\File\Component\Chooser;

use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Entity\User\User;
use Concrete\Core\File\Filesystem;
use Concrete\Core\Support\Facade\Application;
use Doctrine\ORM\EntityManagerInterface;

trait DefaultUploadDirectoryIdTrait
{

    protected function getDefaultUploadDirectoryId(): string
    {
        $user = new \Concrete\Core\User\User();
        $app = Application::getFacadeApplication();
        /** @var EntityManagerInterface $entityManager */
        $entityManager = $app->make(EntityManagerInterface::class);
        /** @var Repository $config */
        $config = $app->make(Repository::class);
        $userRepository = $entityManager->getRepository(User::class);
        /** @var User|null $userEntity */
        $userEntity = $userRepository->findOneBy(['uID' => $user->getUserID()]);
        $fileSystem = new Filesystem();
        $uploadDirectoryId = (string) $fileSystem->getRootFolder()->getTreeNodeID();

        if ($config->has('concrete.external_file_providers.preferred_upload_directory_id')) {
            $uploadDirectoryId = $config->get('concrete.external_file_providers.preferred_upload_directory_id');
        }

        if ($userEntity && $userEntity->getHomeFileManagerFolderID() !== null) {
            $uploadDirectoryId = (string) $userEntity->getHomeFileManagerFolderID();
        }

        return $uploadDirectoryId;
    }

}