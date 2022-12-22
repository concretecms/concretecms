<?php
namespace Concrete\Core\SiteInformation\Question;

class BuildQuestion extends AbstractSelectQuestion
{

    public function getKey(): string
    {
        return 'build';
    }

    public function getLabel(): string
    {
        return t('What are you trying to build with Concrete?');
    }

    public function getOptions(): array
    {
        return [
            'M' => t('Marketing Site'),
            'H' => t('HR Portal'),
            'I' => t('Internal Communications Intranet'),
            'EC' => t('Ecommerce Site'),
            'P' => t('Personal Site'),
            'O' => t('Something Else'),
        ];
    }

}
