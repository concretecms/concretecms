<?php

namespace Concrete\Core\Site\Type\Formatter;

use HtmlObject\Element;

class DefaultFormatter extends AbstractFormatter
{
    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Site\Type\Formatter\FormatterInterface::getSiteTypeDescription()
     */
    public function getSiteTypeDescription(): string
    {
        return '';
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Site\Type\Formatter\FormatterInterface::getSiteTypeIconElement()
     */
    public function getSiteTypeIconElement(): Element
    {
        return (new Element('i'))
            ->addClass('fas fa-file')
        ;
    }
}
