<?php

namespace Concrete\Core\Backup\ContentImporter\Exception;

use Concrete\Core\Error\UserMessageException;

/**
 * Exception thrown when importing the page structure if the parent page does not exist (yet).
 */
class MissingPageAtPathException extends UserMessageException
{
    /**
     * @var string[]
     */
    private $missingPagePaths = [];

    /**
     * @param string|string[] $missingPagePaths
     * @param string $message
     */
    public function __construct($missingPagePaths, $message = '')
    {
        $this->missingPagePaths = is_array($missingPagePaths) ? $missingPagePaths : [$missingPagePaths];
        if (!$message) {
            if (count($this->missingPagePaths) === 1) {
                $message = t('Missing the page with path %s', $this->missingPagePaths[0]);
            } else {
                $message = t('Missing the pages with the following paths:') . "\n- " . implode("\n- ", $this->missingPagePaths);
            }
        }
        parent::__construct($message);
    }

    /**
     * @return string[]
     */
    public function getMissingPagePaths()
    {
        return $this->missingPagePaths;
    }

    /**
     * @return static
     */
    public function merge(MissingPageAtPathException $other)
    {
        return new static(array_merge($this->getMissingPagePaths(), $other->getMissingPagePaths()));
    }
}
