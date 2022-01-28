<?php
namespace Concrete\Attribute\Site;

use Concrete\Core\Attribute\FontAwesomeIconFormatter;
use Concrete\Core\Entity\Attribute\Value\Value\SiteValue;
use Concrete\Core\Attribute\Controller as CoreAttributeController;

class Controller extends CoreAttributeController
{

    protected $searchIndexFieldDefinition = array('type' => 'integer', 'options' => array('default' => 0, 'notnull' => false));

    public function getIconFormatter()
    {
        return new FontAwesomeIconFormatter('globe');
    }

    public function getAttributeValueClass()
    {
        return SiteValue::class;
    }

    public function getValue()
    {
        if ($this->attributeValue) {
            $value = $this->attributeValue->getValueObject();
            if ($value) {
                /**
                 * @var $value SiteValue
                 */
                return $value->getSite();
            }
        }
    }

    public function form()
    {
        $siteID = null;
        if (is_object($this->getValue())) {
            $siteID = $this->getValue()->getSiteID();
        }
        $sites = array('' => t('** Select Site'));
        foreach($this->app->make('site')->getList() as $site) {
            $sites[$site->getSiteID()] = $site->getSiteName();
        }
        $form = $this->app->make('helper/form');
        print $form->select($this->field('siteID'), $sites, $siteID);
    }

    public function getDisplayValue()
    {
        $site = $this->getValue();
        if (is_object($site)) {
            return $site->getSiteName();
        }
    }

	public function createAttributeValue($site)
	{
		$av = new SiteValue();
		$av->setSite($site);
		return $av;
	}

    public function createAttributeValueFromRequest()
    {
        $data = $this->post();
        $site = $this->app->make('site')->getByID($data['siteID']);
        return $this->createAttributeValue($site);
    }

    public function getSearchIndexValue()
    {
        $value = $this->getAttributeValue();
        if (is_object($value)) {
            $value = $value->getValue();
            if (is_object($value)) {
                return $value->getSiteID();
            }
        }
    }


}