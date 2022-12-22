<?php
namespace Concrete\Core\SiteInformation\Question;

abstract class AbstractQuestion implements QuestionInterface
{

    abstract public function getLabel(): string;


}
