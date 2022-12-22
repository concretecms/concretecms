<?php
namespace Concrete\Core\SiteInformation\Question;

use HtmlObject\Element;

interface QuestionInterface
{

    public function getKey(): string;

    public function getTag(array $results): Element;

}
