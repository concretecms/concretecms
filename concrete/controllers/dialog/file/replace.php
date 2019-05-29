<?php

namespace Concrete\Controller\Dialog\File;

use Concrete\Core\Entity\File\File;
use Concrete\Core\Permission\Checker;
use Doctrine\ORM\EntityManagerInterface;

class Replace extends Import
{
    /**
     * The file being replaced (false: not yet initialized; null: not found; File otherwise).
     *
     * @var \Concrete\Core\Entity\File\File|null|false
     */
    private $replacingFile = false;

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Controller\Dialog\File\Import::setViewSets()
     */
    protected function setViewSets()
    {
        parent::setViewSets();
        $this->set('replacingFile', $this->getReplacingFile());
    }

    /**
     * Get the file being replaced.
     *
     * @return \Concrete\Core\Entity\File\File|null null if not found, a File instance otherwise
     */
    protected function getReplacingFile()
    {
        if ($this->replacingFile === false) {
            $replacingFile = null;
            $fID = $this->request->request->get('fID', $this->request->query->get('fID'));
            if ($fID && is_scalar($fID)) {
                $fID = (int) $fID;
                if ($fID !== 0) {
                    $replacingFile = $this->app->make(EntityManagerInterface::class)->find(File::class, $fID);
                }
            }
            $this->setReplacingFile($replacingFile);
        }

        return $this->replacingFile;
    }

    /**
     * Set the file being replaced.
     *
     * @param \Concrete\Core\Entity\File\File|null $value null if not found, a File instance otherwise
     *
     * @return $this
     */
    protected function setReplacingFile(File $value = null)
    {
        if ($value !== $this->replacingFile) {
            $this->replacingFile = $value;
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Controller\Dialog\File\Import::getCurrentFolder()
     */
    protected function getCurrentFolder()
    {
        return null;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Controller\Dialog\File\Import::getOriginalPage()
     */
    protected function getOriginalPage()
    {
        return null;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Controller\Dialog\File\Import::canAccess()
     */
    protected function canAccess()
    {
        $replacingFile = $this->getReplacingFile();
        if ($replacingFile === null) {
            return false;
        }
        $replacingFilePermissions = new Checker($replacingFile);

        return $replacingFilePermissions->canEditFileContents();
    }
}
