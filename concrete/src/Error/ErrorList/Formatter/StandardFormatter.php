<?php

namespace Concrete\Core\Error\ErrorList\Formatter;

use Concrete\Core\Error\ErrorList\Error\HtmlAwareErrorInterface;

class StandardFormatter extends AbstractFormatter
{
    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Error\ErrorList\Formatter\FormatterInterface::render()
     */
    public function render()
    {
        return $this->getString();
    }

    /**
     * Build an HTML-formatted string describing the errors.
     *
     * @return string
     */
    public function getString()
    {
        $html = '';
        if ($this->error->has()) {
            $html .= '<ul class="ccm-error">';
            foreach ($this->error->getList() as $error) {
                $html .= '<li>';
                if ($error instanceof HtmlAwareErrorInterface && $error->messageContainsHtml()) {
                    $html .= (string) $error;
                } else {
                    $html .= nl2br(h((string) $error));
                }
                $html .= '</li>';
            }
            $html .= '</ul>';
        }

        return $html;
    }
}
