<?php

namespace Concrete\Core\File\Component\Chooser;

use Concrete\Core\Entity\File\Folder\FavoriteFolder;
use Concrete\Core\Entity\Search\SavedFileSearch;
use Concrete\Core\File\Component\Chooser\Option\ExternalFileProviderOption;
use Concrete\Core\File\Component\Chooser\Option\FileManagerOption;
use Concrete\Core\File\Component\Chooser\Option\FileSetsOption;
use Concrete\Core\File\Component\Chooser\Option\FileUploadOption;
use Concrete\Core\File\Component\Chooser\Option\FolderBookmarkOption;
use Concrete\Core\File\Component\Chooser\Option\RecentUploadsOption;
use Concrete\Core\File\Component\Chooser\Option\SavedSearchOption;
use Concrete\Core\File\Component\Chooser\Option\SearchOption;
use Concrete\Core\File\ExternalFileProvider\ExternalFileProviderFactory;
use Concrete\Core\File\Set\Set;
use Concrete\Core\User\User;
use Doctrine\ORM\EntityManager;
use Concrete\Core\Entity\User\User as UserEntity;

class DefaultConfigurationFactory
{

    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @var ExternalFileProviderFactory
     */
    protected $externalFileProviderFactory;

    public function __construct(
        EntityManager $entityManager,
        ExternalFileProviderFactory $externalFileProviderFactory
    ) {
        $this->entityManager = $entityManager;
        $this->externalFileProviderFactory = $externalFileProviderFactory;
    }

    public function createConfiguration(): ChooserConfigurationInterface
    {
        $configuration = new ChooserConfiguration();

        $sets = Set::getMySets();
        $searches = $this->entityManager->getRepository(SavedFileSearch::class)->findAll();
        $configuration->addChooser(new FileManagerOption());
        $configuration->addChooser(new RecentUploadsOption());
        $configuration->addChooser(new SearchOption());
        if (count($sets) > 0) {
            $configuration->addChooser(new FileSetsOption());
        }
        if (count($searches) > 0) {
            $configuration->addChooser(new SavedSearchOption());
        }
        $configuration->addUploader(new FileUploadOption());

        // get external file providers
        foreach ($this->externalFileProviderFactory->fetchList() as $externalFileProvider) {
            $configuration->addUploader(new ExternalFileProviderOption($externalFileProvider));
        }

        // get all favored folders
        $user = new User();
        $favoriteFolderRepository = $this->entityManager->getRepository(FavoriteFolder::class);
        $userRepository = $this->entityManager->getRepository(UserEntity::class);
        /** @var User $userEntity */
        $userEntity = $userRepository->findOneBy(["uID" => $user->getUserID()]);

        $favoriteFolderEntries = $favoriteFolderRepository->findBy(
            [
                "owner" => $userEntity
            ]
        );

        foreach ($favoriteFolderEntries as $favoriteFolderEntry) {
            /** @noinspection PhpUnhandledExceptionInspection */
            $configuration->addChooser(new FolderBookmarkOption($favoriteFolderEntry));
        }

        return $configuration;
    }

}