<?php
namespace Concrete\Core\File\Component\Chooser;

use Concrete\Core\Entity\User\User;
use Concrete\Core\File\Filesystem;
use Concrete\Core\Support\Facade\Application;
use Doctrine\ORM\EntityManagerInterface;

trait ExternalFileProviderOptionTrait
{

    public function jsonSerialize()
    {
        $user = new \Concrete\Core\User\User();
        $app = Application::getFacadeApplication();
        /** @var EntityManagerInterface $entityManager */
        $entityManager = $app->make(EntityManagerInterface::class);
        $userRepository = $entityManager->getRepository(User::class);
        /** @var User $userEntity */
        $userEntity = $userRepository->findOneBy(["uID" => $user->getUserID()]);
        $fileSystem = new Filesystem();
        $uploadDirectoryId = (string)$fileSystem->getRootFolder()->getTreeNodeID();

        if ($userEntity->getHomeFileManagerFolderID() !== null) {
            $uploadDirectoryId = (string)$userEntity->getHomeFileManagerFolderID();
        }

        return [
            'id' => $this->getId(),
            'componentKey' => $this->getComponentKey(),
            'title' => $this->getTitle(),
            'data' => [
                'typeHandle' => $this->externalFileProvider->getTypeObject()->getHandle(),
                'name' => $this->externalFileProvider->getName(),
                'supportFileTypes' => $this->externalFileProvider->getConfigurationObject()->supportFileTypes(),
                'hasCustomImportHandler' => $this->externalFileProvider->getConfigurationObject()->hasCustomImportHandler(),
                'uploadDirectoryId' => $uploadDirectoryId
            ],
        ];
    }

}