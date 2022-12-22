<?php
namespace Concrete\Core\SiteInformation\Question;

class RoleQuestion extends AbstractSelectQuestion
{

    public function getKey(): string
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
            'CC' => t('Content Creator'),
            'DE' => t('Designer'),
            'M' => t('Product Owner/Management'),
            'DV' => t('Developer'),
        ];
    }

}
