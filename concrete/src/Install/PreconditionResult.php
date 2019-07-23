<?php

namespace Concrete\Core\Install;

/**
 * The result of a precondition check.
 */
class PreconditionResult
{
    /**
     * Precondition result: passed.
     *
     * @var int
     */
    const STATE_PASSED = 1;

    /**
     * Precondition result: passed with warnings.
     *
     * @var int
     */
    const STATE_WARNING = 2;

    /**
     * Precondition result: skipped.
     *
     * @var int
     */
    const STATE_SKIPPED = 3;

    /**
     * Precondition result: failed.
     *
     * @var int
     */
    const STATE_FAILED = 4;

    /**
     * The precondition result state.
     *
     * @var int One of the PreconditionResult::STATE_... constants.
     */
    protected $state;

    /**
     * The warning/error message.
     *
     * @var string
     */
    protected $message;

    /**
     * Initialize the class instance.
     *
     * @param int $state One of the PreconditionResult::STATE_... constants
     * @param string $message warning/error message
     */
    public function __construct($state = self::STATE_PASSED, $message = '')
    {
        $this->setState($state);
        $this->setMessage($message);
    }

    /**
     * Get the precondition result state.
     *
     * @return int One of the PreconditionResult::STATE_... constants.
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * Set the precondition result state.
     *
     * @param int $value One of the PreconditionResult::STATE_... constants.
     *
     * @return $this
     */
    public function setState($value)
    {
        $this->state = (int) $value;

        return $this;
    }

    /**
     * Get the warning/error message.
     *
     * @return string
     */
    public function getMessage()
    {
        $result = $this->message;
        if ($result === '') {
            switch ($this->state) {
                case self::STATE_PASSED:
                case self::STATE_SKIPPED:
                    break;
                case self::STATE_WARNING:
                    $result = t('The installation precondition passed with warnings.');
                    break;
                case self::STATE_FAILED:
                default:
                    $result = t('The installation precondition failed.');
                    break;
            }
        }

        return $result;
    }

    /**
     * Set the warning/error message.
     *
     * @param string $value
     *
     * @return $this
     */
    public function setMessage($value)
    {
        $this->message = trim((string) $value);

        return $this;
    }
}
