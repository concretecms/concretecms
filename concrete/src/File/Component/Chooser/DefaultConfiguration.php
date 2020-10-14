<?php
namespace Concrete\Core\File\Component\Chooser;

use Concrete\Core\Entity\Search\SavedFileSearch;
use Concrete\Core\File\Component\Chooser\Option\FileManagerOption;
use Concrete\Core\File\Component\Chooser\Option\FileSetsOption;
use Concrete\Core\File\Component\Chooser\Option\FileUploadOption;
use Concrete\Core\File\Component\Chooser\Option\RecentUploadsOption;
use Concrete\Core\File\Component\Chooser\Option\SavedSearchOption;
use Concrete\Core\File\Component\Chooser\Option\SearchOption;
use Concrete\Core\File\Set\Set;
use Doctrine\ORM\EntityManager;

class DefaultConfiguration implements ChooserConfigurationInterface
{

    public function __construct(EntityManager $entityManager)
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