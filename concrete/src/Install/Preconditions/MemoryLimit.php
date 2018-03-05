<?php

namespace Concrete\Core\Install\Preconditions;

use Concrete\Core\Install\PreconditionInterface;
use Concrete\Core\Install\PreconditionResult;
use Concrete\Core\Utility\Service\Number;

class MemoryLimit implements PreconditionInterface
{
    /**
     * The minimum memory limit (in bytes).
     *
     * @var string
     */
    const MINIMUM_MEMORY = '24m';

    /**
     * The minimum memory limit (in bytes).
     *
     * @var string
     */
    const MINIMUM_RECOMMENDED_MEMORY = '64m';

    /**
     * The number helper.
     *
     * @var Number
     */
    protected $numberHelper;

    /**
     * Initialize the instance.
     *
     * @param Number $numberHelper the number helper
     */
    public function __construct(Number $numberHelper)
    {
        $this->numberHelper = $numberHelper;
    }

    /**
     * {@inheritdoc}
     *
     * @see PreconditionInterface::getName()
     */
    public function getName()
    {
        $bytes = $this->numberHelper->getBytes(static::MINIMUM_RECOMMENDED_MEMORY);

        return t('Memory limit %s.', $this->numberHelper->formatSize($bytes));
    }

    /**
     * {@inheritdoc}
     *
     * @see PreconditionInterface::getUniqueIdentifier()
     */
    public function getUniqueIdentifier()
    {
        return 'memory_limit';
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

        $memoryLimit = ini_get('memory_limit');
        if (empty($memoryLimit) || $memoryLimit == -1) {
            $memoryLimit = null;
        } else {
            $memoryLimit = $this->numberHelper->getBytes($memoryLimit);
        }
        $recommended = $this->numberHelper->getBytes(static::MINIMUM_RECOMMENDED_MEMORY);
        $minumum = $this->numberHelper->getBytes(static::MINIMUM_MEMORY);
        if ($memoryLimit !== null && $memoryLimit < $minumum) {
            $result
                ->setState(PreconditionResult::STATE_FAILED)
                ->setMessage(
                    t('concrete5 will not install with less than %1$s of RAM. Your memory limit is currently %2$s. Please increase your memory_limit using ini_set.',
                        $this->numberHelper->formatSize($minumum),
                        $this->numberHelper->formatSize($memoryLimit)
                    )
                )
            ;
        } elseif ($memoryLimit !== null && $memoryLimit < $recommended) {
            $result
                ->setState(PreconditionResult::STATE_WARNING)
                ->setMessage(
                    t('concrete5 runs best with at least %1$s of RAM. Your memory limit is currently %2$s. You may experience problems uploading and resizing large images, and may have to install concrete5 without sample content.',
                        $this->numberHelper->formatSize($recommended),
                        $this->numberHelper->formatSize($memoryLimit)
                    )
                )
            ;
        } elseif ($memoryLimit !== null) {
            $result->setMessage(t('current memory limit: %1$s', $this->numberHelper->formatSize($memoryLimit)));
        }

        return $result;
    }
}
