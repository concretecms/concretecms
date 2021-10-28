<?php
namespace Concrete\Core\Search\Field;

use Concrete\Core\Form\Service\Widget\SiteSelector;

abstract class AbstractSiteField extends AbstractField
{

    protected $requestVariables = [
        'siteID'
    ];

    public function getKey()
    {
        return 'site_field';
    }

    public function getDisplayName()
    {
        return t('Site');
    }

    /**
     * @return bool
     */
    public function isSetToCurrent(): bool
    {
        return $this->data['siteID'] === 'current';
    }

    /**
     * @return bool
     */
    public function isSetToAll(): bool
    {
        return $this->data['siteID'] === 'all';
    }

    public function renderSearchField()
    {
        $selector = new SiteSelector();
        return $selector->selectSite('siteID', $this->data['siteID'], true, true);
    }


}
