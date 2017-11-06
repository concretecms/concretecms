<?php
namespace Concrete\Core\Error\ErrorList\Formatter;

class TextFormatter extends AbstractFormatter
{
    /**
     * @return string
     */
    public function getText()
    {
        $lines = [];
        if ($this->error->has()) {
            foreach ($this->error->getList() as $error) {
                $lines[] = (string) $error;
            }
        }

        return implode("\n", $lines);
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Error\ErrorList\Formatter\FormatterInterface::render()
     */
    public function render()
    {
        return $this->getText();
    }
}
