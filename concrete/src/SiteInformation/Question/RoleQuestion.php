<?php
namespace Concrete\Core\SiteInformation\Question;

class RoleQuestion extends AbstractSelectQuestion
{

    const CONTENT_CREATOR = 'CC';
    const DESIGNER = 'DE';
    const PRODUCT_OWNER = 'M';
    const DEVELOPER = 'DV';

    public static function getKey(): string
    {
        return 'role';
    }

    public function getLabel(): string
    {
        return t('What role do you identify with most?');
    }

    public function getOptions(): array
    {
        return [
            self::CONTENT_CREATOR => t('Content Creator'),
            self::DESIGNER => t('Designer'),
            self::PRODUCT_OWNER => t('Product Owner/Management'),
            self::DEVELOPER => t('Developer'),
        ];
    }

}
