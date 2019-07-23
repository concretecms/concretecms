<?php

namespace Concrete\Core\Install\Preconditions;

use Concrete\Core\Foundation\Environment\FunctionInspector;
use Concrete\Core\Install\PreconditionInterface;
use Concrete\Core\Install\PreconditionResult;

class RemoteFileImporting implements PreconditionInterface
{
    /**
     * The FunctionInspector instance.
     *
     * @var FunctionInspector
     */
    protected $functionInspector;

    /**
     * Initialize the instance.
     *
     * @param FunctionInspector $functionInspector the FunctionInspector instance
     */
    public function __construct(FunctionInspector $functionInspector)
    {
        $this->functionInspector = $functionInspector;
    }

    /**
     * {@inheritdoc}
     *
     * @see PreconditionInterface::getName()
     */
    public function getName()
    {
        return t('Remote File Importing');
    }

    /**
     * {@inheritdoc}
     *
     * @see PreconditionInterface::getUniqueIdentifier()
     */
    public function getUniqueIdentifier()
    {
        return 'remote_file_importing';
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
        if (!$this->functionInspector->functionAvailable('iconv')) {
            $result
                ->setState(PreconditionResult::STATE_FAILED)
                ->setMessage(t('Remote file importing through the file manager requires the iconv PHP extension.'))
            ;
        }

        return $result;
    }
}
