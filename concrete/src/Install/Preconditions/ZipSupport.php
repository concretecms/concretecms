<?php

namespace Concrete\Core\Install\Preconditions;

use Concrete\Core\Install\PreconditionInterface;
use Concrete\Core\Install\PreconditionResult;

class ZipSupport implements PreconditionInterface
{
    /**
     * {@inheritdoc}
     *
     * @see PreconditionInterface::getName()
     */
    public function getName()
    {
        return t('Zip Support');
    }

    /**
     * {@inheritdoc}
     *
     * @see PreconditionInterface::getUniqueIdentifier()
     */
    public function getUniqueIdentifier()
    {
        return 'zip_support';
    }

    /**
     * {@inheritdoc}
     *
     * @see PreconditionInterface::isOptional()
     */
    public function isOptional()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     *
     * @see PreconditionInterface::performCheck()
     */
    public function performCheck()
    {
        $result = new PreconditionResult();
        if (!class_exists('ZipArchive')) {
            $result
                ->setState(PreconditionResult::STATE_FAILED)
                ->setMessage(t('Downloading zipped files from the file manager, remote updating and marketplace integration requires the Zip extension.'))
            ;
        }

        return $result;
    }
}
