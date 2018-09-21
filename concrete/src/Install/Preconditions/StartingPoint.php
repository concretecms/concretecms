<?php

namespace Concrete\Core\Install\Preconditions;

use Concrete\Core\Install\InstallerOptions;
use Concrete\Core\Install\OptionsPreconditionInterface;
use Concrete\Core\Install\PreconditionResult;
use Concrete\Core\Package\StartingPointPackage;

class StartingPoint implements OptionsPreconditionInterface
{
    /**
     * @var \Concrete\Core\Application\Application
     */
    protected $application;

    /**
     * @var \Concrete\Core\Install\InstallerOptions|null
     */
    protected $installerOptions;

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Install\PreconditionInterface::getName()
     */
    public function getName()
    {
        return t('Starting Point');
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Install\PreconditionInterface::getUniqueIdentifier()
     */
    public function getUniqueIdentifier()
    {
        return 'starting_point';
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Install\PreconditionInterface::isOptional()
     */
    public function isOptional()
    {
        return false;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Install\OptionsPreconditionInterface::setInstallerOptions()
     */
    public function setInstallerOptions(InstallerOptions $installerOptions)
    {
        $this->installerOptions = $installerOptions;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Install\PreconditionInterface::performCheck()
     */
    public function performCheck()
    {
        $handle = $this->installerOptions->getStartingPointHandle();
        if ($handle === '') {
            $result = new PreconditionResult(PreconditionResult::STATE_WARNING, t('The starting point has not been defined: if you proceed the default one will be used.'));
        } else {
            $sp = StartingPointPackage::getClass($handle);
            if ($sp === null) {
                $result = new PreconditionResult(PreconditionResult::STATE_FAILED, t('Invalid starting point: %s', $handle));
            } else {
                $result = new PreconditionResult(PreconditionResult::STATE_PASSED);
            }
        }

        return $result;
    }
}
