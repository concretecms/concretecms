<?php

namespace Concrete\Job;

use Concrete\Core\Entity\File\File;
use Concrete\Core\File\FileList;
use Concrete\Core\File\Image\Thumbnail\Path\Resolver as ThumbnailPathResolver;
use Concrete\Core\File\Image\Thumbnail\Type\Type as ThumbnailType;
use Concrete\Core\File\Type\Type as FileType;
use Concrete\Core\Job\QueueableJob;
use Doctrine\ORM\EntityManagerInterface;
use ZendQueue\Message;
use ZendQueue\Queue;

class FillThumbnailsTable extends QueueableJob
{
    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    /**
     * @var \Concrete\Core\File\Image\Thumbnail\Path\Resolver
     */
    protected $thumbnailPathResolver;

    /**
     * @var \Concrete\Core\File\Image\Thumbnail\Type\Version[]|null
     */
    private $thumbnailTypeVersions;

    /**
     * Initialize the instance.
     *
     * @param EntityManagerInterface $entityManager
     * @param ThumbnailPathResolver $thumbnailPathResolver
     */
    public function __construct(EntityManagerInterface $entityManager, ThumbnailPathResolver $thumbnailPathResolver)
    {
        parent::__construct();
        $this->entityManager = $entityManager;
        $this->thumbnailPathResolver = $thumbnailPathResolver;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Job\Job::getJobName()
     */
    public function getJobName()
    {
        return t('Fill thumbnail database table');
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Job\Job::getJobDescription()
     */
    public function getJobDescription()
    {
        return t('Re-populate the thumbnail path database table.');
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Job\QueueableJob::start()
     */
    public function start(Queue $queue)
    {
        $list = new FileList();
        $list->filterByType(FileType::T_IMAGE);
        $q = $list->deliverQueryObject()->execute();
        while (false !== ($row = $q->fetch())) {
            $queue->send((int) $row['fID']);
        }
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Job\QueueableJob::processQueueItem()
     */
    public function processQueueItem(Message $msg)
    {
        $fID = (int) $msg->body;
        if ($fID > 0) {
            $file = $this->entityManager->find(File::class, $fID);
            if ($file !== null) {
                $fileVersion = $file->getApprovedVersion();
                if ($fileVersion !== null) {
                    if ($fileVersion->getTypeObject()->supportsThumbnails()) {
                        $imageWidth = (int) $fileVersion->getAttribute('width');
                        $imageHeight = (int) $fileVersion->getAttribute('height');
                        foreach ($this->getThumbnailTypeVersions() as $thumbnailTypeVersion) {
                            if ($thumbnailTypeVersion->shouldExistFor($imageWidth, $imageHeight, $file)) {
                                $this->thumbnailPathResolver->getPath($fileVersion, $thumbnailTypeVersion);
                            }
                        }
                    }
                }
            }
        }
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Job\QueueableJob::finish()
     */
    public function finish(Queue $q)
    {
        return t('All thumbnail paths have been processed.');
    }

    /**
     * @return \Concrete\Core\File\Image\Thumbnail\Type\Version[]
     */
    protected function getThumbnailTypeVersions()
    {
        if ($this->thumbnailTypeVersions === null) {
            $this->thumbnailTypeVersions = ThumbnailType::getVersionList();
        }

        return $this->thumbnailTypeVersions;
    }
}
