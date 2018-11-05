<?php

namespace Concrete\Attribute\Address;

use Concrete\Core\Attribute\Context\BasicFormContext;
use Concrete\Core\Attribute\Controller as AttributeTypeController;
use Concrete\Core\Attribute\FontAwesomeIconFormatter;
use Concrete\Core\Attribute\Form\Control\View\GroupedView;
use Concrete\Core\Attribute\MulticolumnTextExportableAttributeInterface;
use Concrete\Core\Entity\Attribute\Key\Settings\AddressSettings;
use Concrete\Core\Entity\Attribute\Value\Value\AddressValue;
use Concrete\Core\Error\ErrorList\ErrorList;
use Concrete\Core\Form\Context\ContextInterface;
use Concrete\Core\Geolocator\GeolocationResult;
use Concrete\Core\Http\Response;
use Concrete\Core\Http\ResponseFactoryInterface;
use Concrete\Core\Localization\Service\CountryList;
use Concrete\Core\Localization\Service\StatesProvincesList;
use Concrete\Core\Support\Facade\Application;

class Controller extends AttributeTypeController implements MulticolumnTextExportableAttributeInterface
{
    public $helpers = ['form', 'lists/countries'];

    protected $searchIndexFieldDefinition = [
        'address1' => [
            'type' => 'string',
            'options' => ['length' => '255', 'default' => '', 'notnull' => false],
        ],
        'address2' => [
            'type' => 'string',
            'options' => ['length' => '255', 'default' => '', 'notnull' => false],
        ],
        'city' => ['type' => 'string', 'options' => ['length' => '255', 'default' => '', 'notnull' => false]],
        'state_province' => [
            'type' => 'string',
            'options' => ['length' => '255', 'default' => '', 'notnull' => false],
        ],
        'country' => [
            'type' => 'string',
            'options' => ['length' => '255', 'default' => '', 'notnull' => false],
        ],
        'postal_code' => [
            'type' => 'string',
            'options' => ['length' => '255', 'default' => '', 'notnull' => false],
        ],
    ];

    public function getIconFormatter()
    {
        return new FontAwesomeIconFormatter('map-marker');
    }

    public function searchKeywords($keywords, $queryBuilder)
    {
        $h = $this->attributeKey->getAttributeKeyHandle();

        return $queryBuilder->expr()->orX(
            $queryBuilder->expr()->like("ak_{$h}_address1", ':keywords'),
            $queryBuilder->expr()->like("ak_{$h}_address2", ':keywords'),
            $queryBuilder->expr()->like("ak_{$h}_city", ':keywords'),
            $queryBuilder->expr()->like("ak_{$h}_state_province", ':keywords'),
            $queryBuilder->expr()->like("ak_{$h}_postal_code", ':keywords'),
            $queryBuilder->expr()->like("ak_{$h}_country", ':keywords')
        );
    }

    public function getControlView(ContextInterface $context)
    {
        return new GroupedView($context, $this->getAttributeKey(), $this->getAttributeValue());
    }

    public function getAttributeValueClass()
    {
        return AddressValue::class;
    }

    public function getAttributeValueObject()
    {
        return $this->attributeValue ? $this->entityManager->find(AddressValue::class, $this->attributeValue->getGenericValue()) : null;
    }

    public function searchForm($list)
    {
        $address1 = $this->request('address1');
        $address2 = $this->request('address2');
        $city = $this->request('city');
        $state_province = $this->request('state_province');
        $postal_code = $this->request('postal_code');
        $country = $this->request('country');
        $akHandle = $this->attributeKey->getAttributeKeyHandle();

        if ($address1) {
            $list->filter('ak_' . $akHandle . '_address1', '%' . $address1 . '%', 'like');
        }
        if ($address2) {
            $list->filter('ak_' . $akHandle . '_address2', '%' . $address2 . '%', 'like');
        }
        if ($city) {
            $list->filter('ak_' . $akHandle . '_city', '%' . $city . '%', 'like');
        }
        if ($state_province) {
            $list->filter('ak_' . $akHandle . '_state_province', $state_province);
        }
        if ($postal_code) {
            $list->filter('ak_' . $akHandle . '_postal_code', '%' . $postal_code . '%', 'like');
        }
        if ($country) {
            $list->filter('ak_' . $akHandle . '_country', $country);
        }

        return $list;
    }

    public function search()
    {
        $this->load();
        $this->form();
        $v = $this->getView();
        $this->set('search', true);
        $v->render(new BasicFormContext());
    }

    public function createAttributeValueFromRequest()
    {
        return $this->createAttributeValue($this->post());
    }

    public function validateForm($data)
    {
        return !empty($data['address1'])
        && !empty($data['city'])
        && !empty($data['state_province'])
        && !empty($data['country'])
        && !empty($data['postal_code']);
    }

    public function validateValue()
    {
        $v = $this->getAttributeValue()->getValue();
        if (!is_object($v)) {
            return false;
        }
        if (trim((string) $v) == '') {
            return false;
        }

        return true;
    }

    public function getSearchIndexValue()
    {
        $v = $this->getAttributeValue()->getValue();
        $args = [];
        $args['address1'] = $v->getAddress1();
        $args['address2'] = $v->getAddress2();
        $args['city'] = $v->getCity();
        $args['state_province'] = $v->getStateProvince();
        $args['country'] = $v->getCountry();
        $args['postal_code'] = $v->getPostalCode();

        return $args;
    }

    public function getDisplayValue()
    {
        $value = $this->getAttributeValue()->getValue();
        $v = $this->app->make('helper/text')->entities($value);
        $ret = nl2br($v);

        return $ret;
    }

    /**
     * @deprecated Use $app->make(\Concrete\Core\Form\Service\Form::class')->selectCountry() to link a Country field and a State/Province field
     */
    public function action_load_provinces_js()
    {
        $app = isset($this->app) ? $this->app : Application::getFacadeApplication();
        $h = $app->make('helper/lists/states_provinces');
        $all = $h->getAll();
        $outputList = [];
        foreach ($all as $country => $countries) {
            foreach ($countries as $value => $text) {
                $outputList[] = "$country:$value:$text";
            }
        }
        $rf = $app->make(ResponseFactoryInterface::class);

        return $rf->create(
            'var ccm_attributeTypeAddressStatesTextList = ' . json_encode($outputList, JSON_PRETTY_PRINT) . '.join(\'|\');',
            Response::HTTP_OK,
            [
                'Content-Type' => 'application/javascript; charset=' . APP_CHARSET,
            ]
        );
    }

    public function validateKey($data = false)
    {
        // additional validation for select type
        $akCustomCountries = $data['akCustomCountries'];
        $akHasCustomCountries = $data['akHasCustomCountries'];
        if ($data['akHasCustomCountries'] != 1) {
            $akHasCustomCountries = 0;
        }

        if (!is_array($data['akCustomCountries'])) {
            $akCustomCountries = [];
        }

        $e = $this->app->make('error');

        if ($akHasCustomCountries && (count($akCustomCountries) == 0)) {
            $e->add(t('You must specify at least one country.'));
        } else {
            if (
                $akHasCustomCountries
                && $data['akDefaultCountry'] != ''
                && (!in_array($data['akDefaultCountry'], $akCustomCountries))
            ) {
                $e->add(t('The default country must be in the list of custom countries.'));
            }
        }

        return $e;
    }

    public function exportKey($akey)
    {
        $this->load();
        $type = $akey->addChild('type');
        $type->addAttribute('custom-countries', $this->akHasCustomCountries);
        $type->addAttribute('default-country', $this->akDefaultCountry);
        $type->addAttribute('geolocate-country', $this->akGeolocateCountry ? 1 : 0);
        if ($this->akHasCustomCountries) {
            $countries = $type->addChild('countries');
            foreach ($this->akCustomCountries as $country) {
                $countries->addChild('country', $country);
            }
        }

        return $akey;
    }

    public function exportValue(\SimpleXMLElement $akn)
    {
        $avn = $akn->addChild('value');
        $address = $this->getAttributeValue()->getValue();
        if ($address) {
            $avn->addAttribute('address1', $address->getAddress1());
            $avn->addAttribute('address2', $address->getAddress2());
            $avn->addAttribute('city', $address->getCity());
            $avn->addAttribute('state-province', $address->getStateProvince());
            $avn->addAttribute('country', $address->getCountry());
            $avn->addAttribute('postal-code', $address->getPostalCode());
        }
    }

    public function createAttributeValue($data)
    {
        if ($data instanceof AddressValue) {
            return clone $data;
        }
        extract($data);
        $av = new AddressValue();
        $av->setAddress1($address1);
        $av->setAddress2($address2);
        $av->setCity($city);
        $av->setStateProvince($state_province);
        $av->setCountry($country);
        $av->setPostalCode($postal_code);

        return $av;
    }

    public function importValue(\SimpleXMLElement $akv)
    {
        if (isset($akv->value)) {
            $av = new AddressValue();
            $av->setAddress1((string) $akv->value['address1']);
            $av->setAddress2((string) $akv->value['address2']);
            $av->setCity((string) $akv->value['city']);
            $av->setStateProvince((string) $akv->value['state-province']);
            $av->setCountry((string) $akv->value['country']);
            $av->setPostalCode((string) $akv->value['postal-code']);

            return $av;
        }
    }

    public function importKey(\SimpleXMLElement $akey)
    {
        $type = $this->getAttributeKeySettings();
        if (isset($akey->type)) {
            $type->setHasCustomCountries((bool) $akey->type['custom-countries']);
            $type->setDefaultCountry((string) $akey->type['default-country']);
            if (isset($akey->type->countries)) {
                $countries = [];
                foreach ($akey->type->countries->children() as $country) {
                    $countries[] = (string) $country;
                }
                $type->setCustomCountries($countries);
            }
            $type->setGeolocateCountry(!empty($akey->type['geolocate-country']));
        }

        return $type;
    }

    public function saveKey($data)
    {
        $type = $this->getAttributeKeySettings();

        $akCustomCountries = $data['akCustomCountries'];
        $akHasCustomCountries = $data['akHasCustomCountries'];
        if ($data['akHasCustomCountries'] != 1) {
            $akHasCustomCountries = 0;
        }
        if (!is_array($data['akCustomCountries'])) {
            $akCustomCountries = [];
        }

        $type->setCustomCountries($akCustomCountries);
        $type->setHasCustomCountries($akHasCustomCountries);
        $type->setDefaultCountry($data['akDefaultCountry']);
        $type->setGeolocateCountry(!empty($data['akGeolocateCountry']));

        return $type;
    }

    public function type_form()
    {
        $this->load();
    }

    public function form()
    {
        $this->load();
        $value = null;
        if (is_object($this->attributeValue)) {
            $value = $this->getAttributeValue()->getValue();
            if ($value) {
                $this->set('address1', $value->getAddress1());
                $this->set('address2', $value->getAddress2());
                $this->set('city', $value->getCity());
                $this->set('state_province', $value->getStateProvince());
                $this->set('country', $value->getCountry());
                $this->set('postal_code', $value->getPostalCode());
            }
        }
        if (!$value) {
            $this->set('address1', '');
            $this->set('address2', '');
            $this->set('city', '');
            $this->set('state_province', '');
            $this->set('country', '');
            $this->set('postal_code', '');
            if ($this->akGeolocateCountry) {
                $geolocated = $this->app->make(GeolocationResult::class);
                $this->set('country', $geolocated->getCountryCode());
                $this->set('postal_code', $geolocated->getPostalCode());
                $this->set('state_province', $geolocated->getStateProvinceCode());
                $this->set('city', $geolocated->getCityName());
            }
        }
        $this->set('search', false);
        $this->set('key', $this->attributeKey);
    }

    public function getAttributeKeySettingsClass()
    {
        return AddressSettings::class;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Attribute\MulticolumnTextExportableAttributeInterface::getAttributeTextRepresentationHeaders()
     */
    public function getAttributeTextRepresentationHeaders()
    {
        return [
            'street1',
            'street2',
            'street3',
            'city',
            'state_province',
            'country',
            'postal_code',
        ];
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Attribute\MulticolumnTextExportableAttributeInterface::getAttributeValueTextRepresentation()
     */
    public function getAttributeValueTextRepresentation()
    {
        $value = $this->getAttributeValueObject();

        return [
            $value ? (string) $value->getAddress1() : '',
            $value ? (string) $value->getAddress2() : '',
            $value ? (string) $value->getAddress3() : '',
            $value ? (string) $value->getCity() : '',
            $value ? (string) $value->getStateProvince() : '',
            $value ? (string) $value->getCountry() : '',
            $value ? (string) $value->getPostalCode() : '',
        ];
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Attribute\MulticolumnTextExportableAttributeInterface::updateAttributeValueFromTextRepresentation()
     */
    public function updateAttributeValueFromTextRepresentation(array $textRepresentation, ErrorList $warnings)
    {
        $textRepresentation = array_map('trim', $textRepresentation);
        $value = $this->getAttributeValueObject();
        if ($value === null) {
            if (implode('', $textRepresentation) !== '') {
                $value = new AddressValue();
            }
        }
        if ($value !== null) {
            /* @var AddressValue $value */
            $value->setAddress1(trim(array_shift($textRepresentation)));
            $value->setAddress2(trim(array_shift($textRepresentation)));
            $value->setAddress3(trim(array_shift($textRepresentation)));
            $value->setCity(trim(array_shift($textRepresentation)));
            $value->setStateProvince(trim(array_shift($textRepresentation)));
            $value->setCountry(trim(array_shift($textRepresentation)));
            $value->setPostalCode(trim(array_shift($textRepresentation)));

            $countryCode = (string) $value->getCountry();
            if ($countryCode !== '') {
                $cl = $this->app->make(CountryList::class);
                /* @var CountryList $cl */
                $countries = $cl->getCountries();
                if (!isset($countries[$countryCode])) {
                    $warnings->add(t('"%1$s" is not a valid Country code for the attribute with handle %2$s', $countryCode, $this->attributeKey->getAttributeKeyHandle()));
                } else {
                    $countryName = $countries[$countryCode];
                    $stateProvinceCode = $value->getStateProvince();
                    if ($stateProvinceCode !== '') {
                        $spl = $this->app->make(StatesProvincesList::class);
                        /* @var StatesProvincesList $spl */
                        $statesProvinces = $spl->getStateProvinceArray($countryCode);
                        if ($statesProvinces !== null && !isset($statesProvinces[$stateProvinceCode])) {
                            $warnings->add(t('"%1$s" is not a valid State/Province code for the Country %2$s (attribute with handle %3$s)', $stateProvinceCode, $countryName, $this->attributeKey->getAttributeKeyHandle()));
                        }
                    }
                }
            }
        }

        return $value;
    }

    protected function load()
    {
        $ak = $this->getAttributeKey();
        if (!is_object($ak)) {
            return false;
        }

        $type = $ak->getAttributeKeySettings();
        /*
         * @var $type AddressType
         */
        $this->akHasCustomCountries = $type->hasCustomCountries();
        $this->akDefaultCountry = $type->getDefaultCountry();
        $this->akCustomCountries = $type->getCustomCountries();
        $this->akGeolocateCountry = $type->geolocateCountry();
        $this->set('akDefaultCountry', $this->akDefaultCountry);
        $this->set('akHasCustomCountries', $this->akHasCustomCountries);
        $this->set('akCustomCountries', $this->akCustomCountries);
        $this->set('akGeolocateCountry', $this->akGeolocateCountry);
    }
}
