<?php
namespace Concrete\Core\SiteInformation\Question;

class BuildQuestion extends AbstractSelectQuestion
{

    const MARKETING = 'M';
    const HR_PORTAL = 'H';
    const INTRANET = 'I';
    const ECOMMERCE = 'EC';
    const PERSONAL_SITE = 'P';
    const OTHER = 'O';

    public static function getKey(): string
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
            self::MARKETING => t('Marketing Site'),
            self::HR_PORTAL => t('HR Portal'),
            self::INTRANET => t('Internal Communications Intranet'),
            self::ECOMMERCE => t('Ecommerce Site'),
            self::PERSONAL_SITE => t('Personal Site'),
            self::OTHER => t('Something Else'),
        ];
    }

}
