<?php

namespace Concrete\Core\Entity\File\Image\Thumbnail\Type;

use Concrete\Core\File\Set\Set as FileSet;
use Doctrine\ORM\Mapping as ORM;

/**
 * Entity representing the association between a thumbnail type and file sets.
 *
 * @ORM\Entity
 * @ORM\Table(name="FileImageThumbnailTypeFileSets")
 */
class TypeFileSet
{
    /**
     * The associated thumbnail type.
     *
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="Type", inversedBy="ftAssociatedFileSets")
     * @ORM\JoinColumn(name="ftfsThumbnailType", referencedColumnName="ftTypeID", nullable=false, onDelete="CASCADE")
     *
     * @var Type
     */
    protected $ftfsThumbnailType;

    /**
     * The ID of the associated file set.
     *
     * @ORM\Id
     * @ORM\Column(type="integer", nullable=false, options={"unsigned":true})
     *
     * @var int
     */
    protected $ftfsFileSetID;

    /**
     * Initialize the instance.
     *
     * @param Type $thumbnailType
     * @param FileSet $fileSet
     */
    public function __construct(Type $thumbnailType, FileSet $fileSet)
    {
        $this->ftfsThumbnailType = $thumbnailType;
        $this->ftfsFileSetID = (int) $fileSet->getFileSetID();
    }

    /**
     * Get the associated thumbnail type.
     *
     * @return Type
     */
    public function getThumbnailType()
    {
        return $this->ftfsThumbnailType;
    }

    /**
     * Get the ID of the associated file set.
     *
     * @return int
     */
    public function getFileSetID()
    {
        return $this->ftfsFileSetID;
    }
}
