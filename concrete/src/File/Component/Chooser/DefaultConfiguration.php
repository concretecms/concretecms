<?php

namespace Concrete\Core\File\Component\Chooser;

use Concrete\Core\Entity\File\Folder\FavoriteFolder;
use Concrete\Core\Entity\Search\SavedFileSearch;
use Concrete\Core\Entity\User\User;
use Concrete\Core\File\Component\Chooser\Option\ExternalFileProviderOption;
use Concrete\Core\File\Component\Chooser\Option\FileManagerOption;
use Concrete\Core\File\Component\Chooser\Option\FileSetsOption;
use Concrete\Core\File\Component\Chooser\Option\FileUploadOption;
use Concrete\Core\File\Component\Chooser\Option\FolderBookmarkOption;
use Concrete\Core\File\Component\Chooser\Option\HomeFolderOption;
use Concrete\Core\File\Component\Chooser\Option\RecentUploadsOption;
use Concrete\Core\File\Component\Chooser\Option\SavedSearchOption;
use Concrete\Core\File\Component\Chooser\Option\SearchOption;
use Concrete\Core\File\ExternalFileProvider\ExternalFileProviderFactory;
use Concrete\Core\File\Set\Set;
use Doctrine\ORM\EntityManager;

class DefaultConfiguration implements ChooserConfigurationInterface
{

    public function __construct(
        EntityManager $entityManager,
        ExternalFileProviderFactory $externalFileProviderFactory
    )
    {
        $sets = Set::getMySets();
        $searches = $entityManager->getRepository(SavedFileSearch::class)->findAll();
        $this->addChooser(new RecentUploadsOption());
        $this->addChooser(new FileManagerOption());
        $this->addChooser(new SearchOption());
        if (count($sets) > 0) {
            $this->addChooser(new FileSetsOption());
        }
        if (count($searches) > 0) {
            $this->addChooser(new SavedSearchOption());
        }
        $this->addUploader(new FileUploadOption());

        // get external file providers
        foreach ($externalFileProviderFactory->fetchList() as $externalFileProvider) {
            $this->addUploader(new ExternalFileProviderOption($externalFileProvider));

        }

        // get all favored folders
        $user = new \Concrete\Core\User\User();
        $favoriteFolderRepository = $entityManager->getRepository(FavoriteFolder::class);
        $userRepository = $entityManager->getRepository(User::class);
        /** @var User $userEntity */
        $userEntity = $userRepository->findOneBy(["uID" => $user->getUserID()]);

        $favoriteFolderEntries = $favoriteFolderRepository->findBy([
            "owner" => $userEntity
        ]);

        foreach ($favoriteFolderEntries as $favoriteFolderEntry) {
            /** @noinspection PhpUnhandledExceptionInspection */
            $this->addChooser(new FolderBookmarkOption($favoriteFolderEntry));
        }

        // add the user's home folder (if available)
        if ($userEntity->getHomeFileManagerFolderID() !== null) {
            $this->addChooser(new HomeFolderOption($userEntity->getHomeFileManagerFolderID()));
        }
    }

    /**
     * @var UploaderOptionInterface[]
     */
    protected $uploaders = [];

    /**
     * @var ChooserOptionInterface[]
     */
    protected $choosers = [];

    public function addChooser(ChooserOptionInterface $chooserOption)
    {
        $this->choosers[] = $chooserOption;
    }

    public function addUploader(UploaderOptionInterface $uploaderOption)
    {
        $this->uploaders[] = $uploaderOption;
    }

    /**
     * @return UploaderOptionInterface[]
     */
    public function getUploaders(): array
    {
        return $this->uploaders;
    }

    /**
     * @return ChooserOptionInterface[]
     */
    public function getChoosers(): array
    {
        return $this->choosers;
    }


}