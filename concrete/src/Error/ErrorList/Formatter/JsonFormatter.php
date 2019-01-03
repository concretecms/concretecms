<?php

namespace Concrete\Core\Error\ErrorList\Formatter;

use Concrete\Core\Error\ErrorList\Error\HtmlAwareErrorInterface;

class JsonFormatter extends AbstractFormatter
{
    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Error\ErrorList\Formatter\FormatterInterface::render()
     */
    public function render()
    {
        return json_encode($this->asArray());
    }

    /**
     * Build an array describing the errors.
     *
     * @return array|null return NULL if the error list is empty, an array otherwise
     */
    public function asArray()
    {
        $error = $this->error;
        if ($error->has()) {
            $o = [
                'error' => true,
                'errors' => [],
                'htmlErrorIndexes' => [],
            ];
            $index = 0;
            foreach ($error->getList() as $error) {
                $o['errors'][] = (string) $error;
                if ($error instanceof HtmlAwareErrorInterface && $error->messageContainsHtml()) {
                    $o['htmlErrorIndexes'][] = $index;
                }
                ++$index;
            }
            if (empty($o['htmlErrorIndexes'])) {
                unset($o['htmlErrorIndexes']);
            }

            return $o;
        }
    }
}
