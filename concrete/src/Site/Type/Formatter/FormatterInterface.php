<?php

namespace Concrete\Core\Site\Type\Formatter;

use HtmlObject\Element;

interface FormatterInterface
{
    public function getSiteTypeDescription(): string;

    public function getSiteTypeIconElement(): Element;
}
