<?php

namespace Concrete\Tests\Localization\Address;

use CommerceGuys\Addressing\Address;
use CommerceGuys\Addressing\AddressFormat\AddressFormatRepository;
use CommerceGuys\Addressing\Country\CountryRepository;
use CommerceGuys\Addressing\Subdivision\SubdivisionRepository;
use Concrete\Core\Localization\Address\Formatter;
use Concrete\Tests\TestCase;

class FormatterTest extends TestCase
{
    /**
     * @var \CommerceGuys\Addressing\Formatter\FormatterInterface
     */
    protected $formatter;

    public function setUp():void
    {
        $addressFormatRepository = new AddressFormatRepository();
        $countryRepository = new CountryRepository();
        $subdivisionRepository = new SubdivisionRepository(
            $addressFormatRepository
        );

        $this->formatter = new Formatter(
            $addressFormatRepository,
            $countryRepository,
            $subdivisionRepository
        );
    }

    /**
     * The CMS knows the Japanese subdivisions as plain numbers, e.g. '13', but
     * commerceguys/addressing knows them as e.g. 'JP-13'. This causes the
     * subdivision (state/province) values for Japan to be stored as the numbers
     * known to the CMS. This tests that these are properly mapped to the
     * commerceguys/addressing values when this kind of addresses are formatted
     * to the user.
     */
    public function testFormatUnknownIsoCompatibleAdministrativeArea()
    {
        $address = new Address();
        // The address locale sets the order of the address fields in the print
        // out and defines in which language the administrative area is printed
        $address = $address->withLocale('ja');
        $address = $address->withCountryCode('JP');
        $address = $address->withAddressLine1('１丁目１３番地');
        $address = $address->withAdministrativeArea('13');
        $address = $address->withLocality('千代田区');
        $address = $address->withPostalCode('101-0054');

        $this->assertEquals(
            '日本' . "\n" .
            '〒101-0054' . "\n" .
            '東京都千代田区' . "\n" .
            '１丁目１３番地',
            $this->formatTextAddressFor($address, 'ja')
        );
    }

    /**
     * Same as above but address is written in English and printed out in
     * English.
     */
    public function testFormatUnknownIsoCompatibleAdministrativeAreaInEnglish()
    {
        $address = new Address();
        // The address locale sets the order of the address fields in the print
        // out and defines in which language the administrative area is printed
        $address = $address->withLocale('en');
        $address = $address->withCountryCode('JP');
        $address = $address->withAddressLine1('1 Chome - 13');
        $address = $address->withAdministrativeArea('13');
        $address = $address->withLocality('Chiyoda');
        $address = $address->withPostalCode('101-0054');

        $this->assertEquals(
            '1 Chome - 13' . "\n" .
            'Chiyoda, Tokyo' . "\n" .
            '101-0054' . "\n" .
            'Japan',
            $this->formatTextAddressFor($address)
        );
    }

    /**
     * The CMS has subdivisions defined for some countries that do not have any
     * known subdivisions in commerceguys/addressing. This tests that for this
     * kind of country subdivision that cannot be found from
     * commerceguys/addressing is correctly fetched through the CMS's own
     * country/province list.
     */
    public function testFormatUnknownAdministrativeAreaKnownToCms()
    {
        $address = new Address();
        // The address locale sets the order of the address fields in the print
        // out and defines in which language the administrative area is printed
        $address = $address->withLocale('en');
        $address = $address->withCountryCode('IE');
        $address = $address->withAddressLine1('Baldoyle Ind Est, 13');
        $address = $address->withAdministrativeArea('CO DUBLIN');
        $address = $address->withLocality('Dublin');
        $address = $address->withPostalCode('WN7 4TN');

        $this->assertEquals(
            'Baldoyle Ind Est, 13' . "\n" .
            'Dublin' . "\n" .
            'County Dublin'. "\n" .
            'WN7 4TN' . "\n" .
            'Ireland',
            $this->formatTextAddressFor($address)
        );
    }

    /**
     * Formats the address for the given address object.
     *
     * @param Address $address the address object to be formatted
     * @param string $locale the locale code in which to format the address,
     *                       affects the country name
     *
     * @return string the formatted address text
     */
    private function formatTextAddressFor(
        Address $address,
        $locale = 'en'
    ) {
        $options = [
            'locale' => $locale, // the locale in which the country is printed
            'html' => false,
        ];

        return $this->formatter->format($address, $options);
    }
}
