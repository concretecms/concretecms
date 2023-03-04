<?php
namespace Concrete\Core\SiteInformation\Question;

class OrganizationQuestion extends AbstractSelectQuestion
{

    public static function getKey(): string
    {
        return 'organization';
    }

    public function getLabel(): string
    {
        return t('What type of organization is this for?');
    }

    public function getOptions(): array
    {
        return [
            'AG' => t('Agriculture, forestry and fishing'),
            'MI' => t('Mining and quarrying'),
            'MA' => t('Manufacturing'),
            'EL' => t('Electricity, gas, steam and air conditioning supply'),
            'WA' => t('Water supply; sewage, waste management and remediation activities'),
            'CO' => t('Construction'),
            'WH' => t('Wholesale and retail trade; repair of motor vehicles and motorcycles'),
            'TR' => t('Transportation and storage'),
            'FS' => t('Accommodation and food service activities'),
            'IC' => t('Information and communication'),
            'FI' => t('Financial and insurance activities'),
            'RE' => t('Real estate activities'),
            'SC' => t('Professional, scientific and technical activities'),
            'AD' => t('Administrative and support service activities'),
            'PA' => t('Public administration and defense; compulsory social security'),
            'ED' => t('Education'),
            'HE' => t('Human health and social work activities'),
            'AR' => t('Arts, entertainment and recreation'),
            'O' => t('Other'),
        ];
    }

}
