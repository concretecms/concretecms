<?php

declare(strict_types=1);

namespace Concrete\Core\Support\CodingStyle\PhpFixer;

use PhpCsFixer\Error\ErrorsManager;

/**
 * Result of for PhpFixer::fix().
 */
class Result
{
    /**
     * @var array
     */
    private $counters;

    /**
     * @var array
     */
    private $changes;

    /**
     * @var \PhpCsFixer\Error\ErrorsManager
     */
    private $errors;

    public function __construct(array $counters, array $changes, ErrorsManager $errors)
    {
        $this->counters = $counters;
        $this->changes = $changes;
        $this->errors = $errors;
    }

    public function getCounters(): array
    {
        return $this->counters;
    }

    public function getChanges(): array
    {
        return $this->changes;
    }

    public function getErrors(): ErrorsManager
    {
        return $this->errors;
    }
}
