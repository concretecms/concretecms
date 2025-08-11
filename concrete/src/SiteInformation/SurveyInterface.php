<?php
namespace Concrete\Core\SiteInformation;

interface SurveyInterface
{

    public function render(): string;

    public function getSaver(): SaverInterface;

    public function getResult($question);

}
