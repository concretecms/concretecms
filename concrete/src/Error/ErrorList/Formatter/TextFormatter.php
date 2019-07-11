<?php

namespace Concrete\Core\Error\ErrorList\Formatter;

use Concrete\Core\Error\ErrorList\Error\HtmlAwareErrorInterface;
use Concrete\Core\Error\UserMessageException;

class TextFormatter extends AbstractFormatter
{
    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Error\ErrorList\Formatter\FormatterInterface::render()
     */
    public function render()
    {
        return $this->getText();
    }

    /**
     * Build an plain text string describing the errors.
     *
     * @return string
     */
    public function getText()
    {
        $lines = [];
        if ($this->error->has()) {
            foreach ($this->error->getList() as $error) {
                if ($error instanceof UserMessageException) {
                    $errorMessage = $error->getMessage();
                } else {
                    $errorMessage = (string) $error;
                }
                if ($error instanceof HtmlAwareErrorInterface && $error->messageContainsHtml()) {
                    $lines[] = strip_tags($errorMessage);
                } else {
                    $lines[] = $errorMessage;
                }
            }
        }

        return implode("\n", $lines);
    }
}
