<?php
namespace Concrete\Tests\Localization\Service;

use Concrete\Core\Localization\Localization;
use Concrete\Core\Localization\Service\AddressFormat;
use Concrete\Core\Localization\Translator\Adapter\Plain\TranslatorAdapterFactory;
use Concrete\Core\Localization\Translator\TranslatorAdapterRepository;
use Concrete\Core\Support\Facade\Facade;
use PHPUnit_Framework_TestCase;

/**
 * Tests for:
 * Concrete\Core\Localization\Service\AddressFormat.
 */
class AddressFormatTest extends PHPUnit_Framework_TestCase
{
    /** @var Localization */
    private $localization;

    /** @var Localization */
    private $localizationOriginal;

    /** @var AddressFormat */
    private $addressFormat;

    /**
     * The locales to test the formatting with. Defines how the country name
     * is printed out.
     *
     * @var array
     */
    private $testLocales = ['en_US', 'fi_FI', 'sv_SE', 'es_ES', 'ru_RU'];

    /**
     * A list of the localal names to be tested with all test locales.
     *
     * Currently the administrative areas (states/provinces) are not translated
     * to the local languages due to the incompleteness of the CLDR dataset
     * regarding these, which is based on the public data available through
     * Wikidata. It is, however, technically possible, which is why these
     * strings are included in this list.
     *
     * @var array
     */
    private $localNames = [
        'en_US' => [
            'country' => [
                'US' => 'United States',
                'FI' => 'Finland',
                'SE' => 'Sweden',
                'ES' => 'Spain',
                'RU' => 'Russia',
            ],
            'administrative' => [
                'US-CA' => 'California',
                'US-NY' => 'New York',
                'US-TX' => 'Texas',
                'ES-Madrid' => 'Madrid',
                'RU-Санкт-Петербург' => 'Санкт-Петербург',
            ],
        ],
        'fi_FI' => [
            'country' => [
                'US' => 'Yhdysvallat',
                'FI' => 'Suomi',
                'SE' => 'Ruotsi',
                'ES' => 'Espanja',
                'RU' => 'Venäjä',
            ],
            'administrative' => [
                'US-CA' => 'California',
                'US-NY' => 'New York',
                'US-TX' => 'Texas',
                'ES-Madrid' => 'Madrid',
                'RU-Санкт-Петербург' => 'Санкт-Петербург',
            ],
        ],
        'sv_SE' => [
            'country' => [
                'US' => 'USA',
                'FI' => 'Finland',
                'SE' => 'Sverige',
                'ES' => 'Spanien',
                'RU' => 'Ryssland',
            ],
            'administrative' => [
                'US-CA' => 'California',
                'US-NY' => 'New York',
                'US-TX' => 'Texas',
                'ES-Madrid' => 'Madrid',
                'RU-Санкт-Петербург' => 'Санкт-Петербург',
            ],
        ],
        'es_ES' => [
            'country' => [
                'US' => 'Estados Unidos',
                'FI' => 'Finlandia',
                'SE' => 'Suecia',
                'ES' => 'España',
                'RU' => 'Rusia',
            ],
            'administrative' => [
                'US-CA' => 'California',
                'US-NY' => 'New York',
                'US-TX' => 'Texas',
                'ES-Madrid' => 'Madrid',
                'RU-Санкт-Петербург' => 'Санкт-Петербург',
            ],
        ],
        'ru_RU' => [
            'country' => [
                'US' => 'Соединенные Штаты',
                'FI' => 'Финляндия',
                'SE' => 'Швеция',
                'ES' => 'Испания',
                'RU' => 'Россия',
            ],
            'administrative' => [
                'US-CA' => 'California',
                'US-NY' => 'New York',
                'US-TX' => 'Texas',
                'ES-Madrid' => 'Madrid',
                'RU-Санкт-Петербург' => 'Санкт-Петербург',
            ],
        ],
    ];

    /**
     * The test addresses to pass to the address formatter and their expected
     * output with the text and html formatting.
     *
     * @var array
     */
    private $testAddresses;

    public function setUp()
    {
        $this->testAddresses = $this->getTestAddresses();

        $localization = new Localization();

        $translatorAdapterFactory = new TranslatorAdapterFactory();
        $repository = new TranslatorAdapterRepository($translatorAdapterFactory);
        $localization->setTranslatorAdapterRepository($repository);
        $localization->setActiveContext(Localization::CONTEXT_SITE);
        $this->localization = $localization;

        $app = Facade::getFacadeApplication();
        $this->addressFormat = $app->make(AddressFormat::class);

        // Temporarily bind the localization class to the local instance during
        // these tests to avoid any database interaction from loading the
        // package translations. This instance is returned by
        // Localization::getInstance() which is called by the AddressFormat
        // service in case the locale is not specifically defined.
        $this->localizationOriginal = $app->make(Localization::class);
        $app->bind(
            Localization::class,
            function () use ($localization) {
                return $localization;
            }
        );
    }

    public function tearDown()
    {
        $this->localization->setActiveContext(Localization::CONTEXT_SYSTEM);

        // Forget the local localization binding
        $app = Facade::getFacadeApplication();
        $localization = $this->localizationOriginal;
        $app->bind(
            Localization::class,
            function () use ($localization) {
                return $localization;
            }
        );
    }

    /**
     * Tests the text formatting of addresses.
     */
    public function testFormatText()
    {
        foreach ($this->testLocales as $locale) {
            foreach ($this->testAddresses as $data) {
                $expected = $this->getExpectedFormatWithLocale(
                    $data,
                    'text',
                    $locale
                );

                $formatted = $this->addressFormat->format(
                    $data['source'],
                    'text',
                    $locale
                );

                $this->assertEquals($expected, $formatted);
            }
        }
    }

    /**
     * Tests the HTML formatting of addresses.
     */
    public function testFormatHtml()
    {
        foreach ($this->testLocales as $locale) {
            foreach ($this->testAddresses as $data) {
                $expected = '<div class="ccm-address-text">' . "\n";
                $expected .= $this->getExpectedFormatWithLocale(
                    $data,
                    'html',
                    $locale
                );
                $expected .= "\n" . '</div>';

                $formatted = $this->addressFormat->format(
                    $data['source'],
                    'html',
                    $locale
                );

                $this->assertEquals($expected, $formatted);
            }
        }
    }

    /**
     * Tests Japanese addresses as they are formatted differently.
     */
    public function testJapaneseFormatting()
    {
        $formatted = $this->addressFormat->format(
            [
                'address1' => '１丁目１３番地',
                'city' => '千代田区',
                'state_province' => '東京都',
                'country' => 'JP',
                'postal_code' => '101-0054',
            ],
            'text',
            'ja_JP'
        );
        $expected = '日本' . "\n" .
            '〒101-0054' . "\n" .
            '東京都千代田区' . "\n" .
            '１丁目１３番地'
        ;

        $this->assertEquals($expected, $formatted);
    }

    /**
     * Tests the text formatting of addresses in case the locale is not
     * specifically defined for the `AddressFormat::format()` method.
     */
    public function testSystemLocalization()
    {
        foreach ($this->testLocales as $locale) {
            Localization::changeLocale($locale);

            foreach ($this->testAddresses as $data) {
                $country = $this->getCountryNameWithLocale(
                    $data['source']['country'],
                    $locale
                );
                $expected = $this->getExpectedFormatWithLocale(
                    $data,
                    'text',
                    $locale
                );

                $formatted = $this->addressFormat->format(
                    $data['source'],
                    'text'
                );

                $this->assertEquals($expected, $formatted);
            }
        }

        Localization::changeLocale(Localization::BASE_LOCALE);
    }

    /**
     * Tests the address formatting when not all required fields are provided
     * for the country.
     */
    public function testFormatWithoutRequiredFields()
    {
        $address = [
            'address1' => '877 S Figueroa St',
            'country' => 'US',
            'postal_code' => '90017',
        ];

        $expected = (
            '877 S Figueroa St' . "\n" .
            '90017' . "\n" .
            'United States'
        );
        $formatted = $this->addressFormat->format($address, 'text');

        $this->assertEquals($expected, $formatted);
    }

    /**
     * Tests the address formatting when a country is not provided.
     */
    public function testFormatWithoutCountry()
    {
        $address = [
            'address1' => '877 S Figueroa St',
            'city' => 'Los Angeles',
            'state_province' => 'CA',
            'postal_code' => '90017',
        ];

        $expected = (
            '877 S Figueroa St' . "\n" .
            'Los Angeles, CA 90017'
        );
        $formatted = $this->addressFormat->format($address, 'text');

        $this->assertEquals($expected, $formatted);
    }

    public function testFormatWithSubdivisionCodes()
    {
        $this->addressFormat->setOptions(['subdivision_names' => false]);

        $address = [
            'address1' => '877 S Figueroa St',
            'city' => 'Los Angeles',
            'country' => 'US',
            'state_province' => 'CA',
            'postal_code' => '90017',
        ];

        $expected = (
            '877 S Figueroa St' . "\n" .
            'Los Angeles, CA 90017' . "\n" .
            'United States'
        );
        $formatted = $this->addressFormat->format($address, 'text');

        $this->assertEquals($expected, $formatted);

        $this->addressFormat->setOptions(['subdivision_names' => true]);
    }

    public function testCountryUsedFields()
    {
        $fields = $this->addressFormat->getCountryAddressUsedFields('US');
        sort($fields);
        $this->assertEquals([
            'address1',
            'address2',
            'city',
            'postal_code',
            'state_province',
        ], $fields);

        $fields = $this->addressFormat->getCountryAddressUsedFields('FI');
        sort($fields);
        $this->assertEquals([
            'address1',
            'address2',
            'city',
            'postal_code',
        ], $fields);

        $fields = $this->addressFormat->getCountryAddressUsedFields('SE');
        sort($fields);
        $this->assertEquals([
            'address1',
            'address2',
            'city',
            'postal_code',
        ], $fields);

        $fields = $this->addressFormat->getCountryAddressUsedFields('ES');
        sort($fields);
        $this->assertEquals([
            'address1',
            'address2',
            'city',
            'postal_code',
            'state_province',
        ], $fields);

        $fields = $this->addressFormat->getCountryAddressUsedFields('RU');
        sort($fields);
        $this->assertEquals([
            'address1',
            'address2',
            'city',
            'postal_code',
            'state_province',
        ], $fields);

        $fields = $this->addressFormat->getCountryAddressUsedFields('UNDEF');
        sort($fields);
        $this->assertEquals([
            'address1',
            'address2',
            'city',
        ], $fields);
    }

    public function testCountryRequiredFields()
    {
        $fields = $this->addressFormat->getCountryAddressRequiredFields('US');
        sort($fields);
        $this->assertEquals([
            'address1',
            'city',
            'postal_code',
            'state_province',
        ], $fields);

        $fields = $this->addressFormat->getCountryAddressRequiredFields('FI');
        sort($fields);
        $this->assertEquals([
            'address1',
            'city',
            'postal_code',
        ], $fields);

        $fields = $this->addressFormat->getCountryAddressRequiredFields('SE');
        sort($fields);
        $this->assertEquals([
            'address1',
            'city',
            'postal_code',
        ], $fields);

        $fields = $this->addressFormat->getCountryAddressRequiredFields('ES');
        sort($fields);
        $this->assertEquals([
            'address1',
            'city',
            'postal_code',
            'state_province',
        ], $fields);

        $fields = $this->addressFormat->getCountryAddressRequiredFields('RU');
        sort($fields);
        $this->assertEquals([
            'address1',
            'city',
            'postal_code',
            'state_province',
        ], $fields);

        $fields = $this->addressFormat->getCountryAddressRequiredFields('UNDEF');
        sort($fields);
        $this->assertEquals([
            'address1',
            'city',
        ], $fields);
    }

    /**
     * PHP 5.5 trick because we cannot concatenate in the class variables.
     *
     * @return array the test address definitions
     */
    private function getTestAddresses()
    {
        return [
            [
                'source' => [
                    'address1' => '877 S Figueroa St',
                    'city' => 'Los Angeles',
                    'country' => 'US',
                    'state_province' => 'CA',
                    'postal_code' => '90017',
                ],
                'expected' => [
                    'text' => (
                        '877 S Figueroa St' . "\n" .
                        'Los Angeles, %administrative% 90017' . "\n" .
                        '%country%'
                    ),
                    'html' => (
                        '<span class="address-line1">877 S Figueroa St</span>' .
                        '<br>' . "\n" .
                        '<span class="locality">Los Angeles</span>, ' .
                        '<span class="administrative-area">%administrative%</span> ' .
                        '<span class="postal-code">90017</span>' .
                        '<br>' . "\n" .
                        '<span class="country">%country%</span>'
                    ),
                ],
            ],
            [
                'source' => [
                    'address1' => '30 Rockefeller Plaza',
                    'address2' => 'NBC Studio 6A',
                    'city' => 'New York',
                    'country' => 'US',
                    'state_province' => 'NY',
                    'postal_code' => '10111',
                ],
                'expected' => [
                    'text' => (
                        '30 Rockefeller Plaza' . "\n" .
                        'NBC Studio 6A' . "\n" .
                        'New York, %administrative% 10111' . "\n" .
                        '%country%'
                    ),
                    'html' => (
                        '<span class="address-line1">30 Rockefeller Plaza</span>' .
                        '<br>' . "\n" .
                        '<span class="address-line2">NBC Studio 6A</span>' .
                        '<br>' . "\n" .
                        '<span class="locality">New York</span>, ' .
                        '<span class="administrative-area">%administrative%</span> ' .
                        '<span class="postal-code">10111</span>' .
                        '<br>' . "\n" .
                        '<span class="country">%country%</span>'
                    ),
                ],
            ],
            [
                'source' => [
                    'address1' => '1321 Commerce St',
                    'address2' => '#APT 123',
                    'address3' => 'Mr. Smith',
                    'city' => 'Dallas',
                    'country' => 'US',
                    'state_province' => 'TX',
                    'postal_code' => '75202',
                ],
                'expected' => [
                    'text' => (
                        '1321 Commerce St' . "\n" .
                        '#APT 123, Mr. Smith' . "\n" .
                        'Dallas, %administrative% 75202' . "\n" .
                        '%country%'
                    ),
                    'html' => (
                        '<span class="address-line1">1321 Commerce St</span>' .
                        '<br>' . "\n" .
                        '<span class="address-line2">#APT 123, Mr. Smith</span>' .
                        '<br>' . "\n" .
                        '<span class="locality">Dallas</span>, ' .
                        '<span class="administrative-area">%administrative%</span> ' .
                        '<span class="postal-code">75202</span>' .
                        '<br>' . "\n" .
                        '<span class="country">%country%</span>'
                    ),
                ],
            ],
            [
                'source' => [
                    'address1' => 'Veneentekijäntie 4 A',
                    'city' => 'Helsinki',
                    'country' => 'FI',
                    'postal_code' => '00210',
                ],
                'expected' => [
                    'text' => (
                        'Veneentekijäntie 4 A' . "\n" .
                        '00210 Helsinki' . "\n" .
                        '%country%'
                    ),
                    'html' => (
                        '<span class="address-line1">Veneentekijäntie 4 A</span>' .
                        '<br>' . "\n" .
                        '<span class="postal-code">00210</span> ' .
                        '<span class="locality">Helsinki</span>' .
                        '<br>' . "\n" .
                        '<span class="country">%country%</span>'
                    ),
                ],
            ],
            [
                'source' => [
                    'address1' => 'Kungsgatan 44',
                    'city' => 'Stockholm',
                    'country' => 'SE',
                    'postal_code' => '111 35',
                ],
                'expected' => [
                    'text' => (
                        'Kungsgatan 44' . "\n" .
                        '111 35 Stockholm' . "\n" .
                        '%country%'
                    ),
                    'html' => (
                        '<span class="address-line1">Kungsgatan 44</span>' .
                        '<br>' . "\n" .
                        '<span class="postal-code">111 35</span> ' .
                        '<span class="locality">Stockholm</span>' .
                        '<br>' . "\n" .
                        '<span class="country">%country%</span>'
                    ),
                ],
            ],
            [
                'source' => [
                    'address1' => 'Plaza de España, 1',
                    'city' => 'Mostoles',
                    'country' => 'ES',
                    'state_province' => 'Madrid',
                    'postal_code' => '28934',
                ],
                'expected' => [
                    'text' => (
                        'Plaza de España, 1' . "\n" .
                        '28934 Mostoles %administrative%' . "\n" .
                        '%country%'
                    ),
                    'html' => (
                        '<span class="address-line1">Plaza de España, 1</span>' .
                        '<br>' . "\n" .
                        '<span class="postal-code">28934</span> ' .
                        '<span class="locality">Mostoles</span> ' .
                        '<span class="administrative-area">%administrative%</span>' .
                        '<br>' . "\n" .
                        '<span class="country">%country%</span>'
                    ),
                ],
            ],
            [
                'source' => [
                    'address1' => 'Леонтьевская ул., 28',
                    'city' => 'Пушкин',
                    'country' => 'RU',
                    'state_province' => 'Санкт-Петербург',
                    'postal_code' => '196601',
                ],
                'expected' => [
                    'text' => (
                        'Леонтьевская ул., 28' . "\n" .
                        'Пушкин' . "\n" .
                        '%administrative%' . "\n" .
                        '196601' . "\n" .
                        '%country%'
                    ),
                    'html' => (
                        '<span class="address-line1">Леонтьевская ул., 28</span>' .
                        '<br>' . "\n" .
                        '<span class="locality">Пушкин</span>' .
                        '<br>' . "\n" .
                        '<span class="administrative-area">%administrative%</span>' .
                        '<br>' . "\n" .
                        '<span class="postal-code">196601</span>' .
                        '<br>' . "\n" .
                        '<span class="country">%country%</span>'
                    ),
                ],
            ],
        ];
    }

    private function getExpectedFormatWithLocale(array $data, $format, $locale)
    {
        $src = $data['source'];
        $expected = $data['expected'][$format];
        if (isset($src['state_province'])) {
            $administrative = $this->getAdministrativeNameWithLocale(
                $src['country'],
                $src['state_province'],
                $locale
            );
            if ($format === 'html') {
                $administrative = htmlspecialchars(
                    $administrative,
                    ENT_QUOTES,
                    'UTF-8'
                );
            }
            $expected = str_replace(
                '%administrative%',
                $administrative,
                $expected
            );
        }
        $country = $this->getCountryNameWithLocale(
            $src['country'],
            $locale
        );
        if ($format === 'html') {
            $country = htmlspecialchars($country, ENT_QUOTES, 'UTF-8');
        }
        $expected = str_replace(
            '%country%',
            $country,
            $expected
        );

        return $expected;
    }

    private function getAdministrativeNameWithLocale(
        $countryCode,
        $areaCode,
        $locale
    ) {
        $fullCode = $countryCode . '-' . $areaCode;

        return $this->localNames[$locale]['administrative'][$fullCode];
    }

    private function getCountryNameWithLocale($countryCode, $locale)
    {
        return $this->localNames[$locale]['country'][$countryCode];
    }
}
