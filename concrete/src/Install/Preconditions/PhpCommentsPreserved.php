<?php

namespace Concrete\Core\Install\Preconditions;

use Concrete\Core\Install\PreconditionInterface;
use Concrete\Core\Install\PreconditionResult;
use ReflectionObject;

class PhpCommentsPreserved implements PreconditionInterface
{
    /**
     * This is to check if comments are being stripped (Doctrine ORM depends on comments not being stripped).
     *
     * @var int
     */
    protected $docCommentCanary = 1;

    /**
     * {@inheritdoc}
     *
     * @see PreconditionInterface::getName()
     */
    public function getName()
    {
        return t('PHP Comments Preserved');
    }

    /**
     * {@inheritdoc}
     *
     * @see PreconditionInterface::getUniqueIdentifier()
     */
    public function getUniqueIdentifier()
    {
        return 'php_comments_preserved';
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
        $reflectionObject = new ReflectionObject($this);
        $reflectionProperty = $reflectionObject->getProperty('docCommentCanary');
        if (!$reflectionProperty->getDocComment()) {
            $result
                ->setState(PreconditionResult::STATE_FAILED)
                ->setMessage(t('concrete5 is not compatible with opcode caches that strip PHP comments. Certain configurations of eAccelerator and Zend opcode caching may use this behavior, and it must be disabled.'))
            ;
        }

        return $result;
    }
}
