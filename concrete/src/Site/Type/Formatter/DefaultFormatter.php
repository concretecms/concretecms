<?php
namespace Concrete\Core\Site\Type\Formatter;

use HtmlObject\Element;

class DefaultFormatter extends AbstractFormatter
{

    public function getSiteTypeDescription()
    {
        return '';
    }

    public function getSiteTypeIconElement()
    {
        return (new Element('i'))
            ->addClass('fa fa-file');
    }


}