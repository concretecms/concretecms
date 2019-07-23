<?php

namespace Concrete\Core\Install\Preconditions;

use Concrete\Core\Foundation\Environment\FunctionInspector;
use Concrete\Core\Install\PreconditionInterface;
use Concrete\Core\Install\PreconditionResult;

class TokenizerExtension implements PreconditionInterface
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
        return t('Tokenizer Extension Enabled');
    }

    /**
     * {@inheritdoc}
     *
     * @see PreconditionInterface::getUniqueIdentifier()
     */
    public function getUniqueIdentifier()
    {
        return 'tokenizer_extension';
    }

    /**
     * {@inheritdoc}
     *
     * @see PreconditionInterface::isOptional()
     */
    public function isOptional()
    {
        return false;
    }

    /**
     * {@inheritdoc}
     *
     * @see PreconditionInterface::performCheck()
     */
    public function performCheck()
    {
        $result = new PreconditionResult();
        if (!$this->functionInspector->functionAvailable('token_get_all')) {
            $result
                ->setState(PreconditionResult::STATE_FAILED)
                ->setMessage(t('The PHP Tokenizer extension has been disabled intentionally on this server and must be enabled.'))
            ;
        }

        return $result;
    }
}
