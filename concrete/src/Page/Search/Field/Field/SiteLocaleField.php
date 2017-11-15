<?php
namespace Concrete\Core\Page\Search\Field\Field;

use Concrete\Core\Form\Service\Widget\SiteLocaleSelector;
use Concrete\Core\Search\Field\AbstractField;
use Concrete\Core\Search\ItemList\ItemList;
use Concrete\Core\Support\Facade\Facade;
use Doctrine\ORM\EntityManager;
use Concrete\Core\Entity\Site\SiteTree;
use Concrete\Core\Entity\Site\Locale;

class SiteLocaleField extends AbstractField
{
    /**
     * Initialize the instance.
     */
    public function __construct($localeID = null)
    {
        if ($localeID) {
            $this->data['localeID'] = $localeID;
        }
    }

    /**
     * The list of all the request variables.
     *
     * @var array
     */
    protected $requestVariables = [
        'localeID',
    ];

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Search\Field\FieldInterface::getKey()
     */
    public function getKey()
    {
        return 'site_locale';
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Search\Field\FieldInterface::getDisplayName()
     */
    public function getDisplayName()
    {
        return t('Locale');
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Search\Field\FieldInterface::filterList()
     */
    public function filterList(ItemList $list)
    {
        $localeID = $this->data['localeID'];
        $app = Facade::getFacadeApplication();
        if (isset($this->data['localeID']) && $this->data['localeID'] !== '') {
            $em = $app->make(EntityManager::class);
            $selectedLocale = $em->find(Locale::class, $this->data['localeID']);
            $list->setSiteTreeObject($selectedLocale->getSiteTree());
        }
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Search\Field\AbstractField::renderSearchField()
     */
    public function renderSearchField()
    {
        $app = Facade::getFacadeApplication();
        $selectedLocale = null;
        if (isset($this->data['localeID']) && $this->data['localeID'] !== '') {
            $em = $app->make(EntityManager::class);
            $selectedLocale = $em->find(Locale::class, $this->data['localeID']);
        }
        $site = $app->make('site')->getActiveSiteForEditing();
        $selector = new SiteLocaleSelector();
        print $selector->selectLocale('localeID', $site, $selectedLocale);
    }
}
