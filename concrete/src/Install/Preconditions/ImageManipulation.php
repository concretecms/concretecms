<?php

namespace Concrete\Core\Install\Preconditions;

use Concrete\Core\Foundation\Environment\FunctionInspector;
use Concrete\Core\Install\PreconditionInterface;
use Concrete\Core\Install\PreconditionResult;

class ImageManipulation implements PreconditionInterface
{
    const MINIMUM_PHP_VERSION = '5.5.9';

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
        return t('Image Manipulation Available');
    }

    /**
     * {@inheritdoc}
     *
     * @see PreconditionInterface::getUniqueIdentifier()
     */
    public function getUniqueIdentifier()
    {
        return 'image_manipulation';
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
        if (!(
            $this->functionInspector->functionAvailable('imagecreatetruecolor')
            && $this->functionInspector->functionAvailable('imagepng')
            && $this->functionInspector->functionAvailable('imagegif')
            && $this->functionInspector->functionAvailable('imagejpeg')
        )) {
            $result
                ->setState(PreconditionResult::STATE_FAILED)
                ->setMessage(t('concrete5 requires GD library 2.0.1 with JPEG, PNG and GIF support. Doublecheck that your installation has support for all these image types.'))
            ;
        }

        return $result;
    }
}
