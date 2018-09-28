<?php

namespace Concrete\Core\Express\Formatter;

use Psr\Log\LoggerInterface;

class LabelFormatter implements FormatterInterface
{

    /** @var \Psr\Log\LoggerInterface */
    protected $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * Format a mask using the standard format given a callable
     *
     * Ex: if you have attributes with handles `student_first_name` and `student_last_name`
     * `%student_last_name%, %student_first_name%`
     *
     * @param $mask
     * @param callable $matchHandler
     * @return mixed
     */
    public function format($mask, callable $matchHandler)
    {
        try {
            // Run a regular expression to match the mask
            return preg_replace_callback('/%(.*?)%/i', function ($matches) use ($matchHandler) {
                // Return the result returned from the matchHandler
                return $this->getResult($matches[1], $matchHandler) ?: '';
            }, $mask);
        } catch (\Exception $e) {
            // Log any failures
            $this->logger->debug(
                'Failed to format express mask "{mask}": {message}',
                ['mask' => $mask, 'message' => $e->getMessage(), 'exception' => $e]
            );
        }
    }

    /**
     * Get a result given a key
     * @param $key
     * @param callable $matchHandler
     * @return string
     */
    private function getResult($key, callable $matchHandler)
    {
        if ($key = trim($key)) {
            return $matchHandler($key) ?: '';
        }

        return '';
    }

}
