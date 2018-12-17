<?php

namespace Concrete\Core\File;

use Concrete\Core\Application\EditResponse as CoreEditResponse;
use Concrete\Core\Entity\File\File as FileEntity;

/**
 * The result of file import.
 */
class EditResponse extends CoreEditResponse
{
    /**
     * The added/modified file versions.
     *
     * @var \Concrete\Core\Entity\File\File[]|\Concrete\Core\Entity\File\Version[]
     */
    protected $files = [];

    /**
     * Add a file to the added/modified file list.
     *
     * @param \Concrete\Core\Entity\File\File $file
     *
     * @return $this
     */
    public function setFile(FileEntity $file)
    {
        $this->files[] = $file;

        return $this;
    }

    /**
     * Set the added/modified file list.
     *
     * @param \Concrete\Core\Entity\File\File[]|\Concrete\Core\Entity\File\Version[] $files
     *
     * @return $this
     */
    public function setFiles($files)
    {
        $this->files = $files;

        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Application\EditResponse::getJSONObject()
     */
    public function getJSONObject()
    {
        $o = parent::getBaseJSONObject();
        foreach ($this->files as $file) {
            $o->files[] = $file->getJSONObject();
        }

        return $o;
    }
}
