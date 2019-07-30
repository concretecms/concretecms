<?php
namespace Concrete\Core\Localization\Service;

use CommerceGuys\Addressing\Address;
use CommerceGuys\Addressing\AddressFormat\AddressField;
use CommerceGuys\Addressing\AddressFormat\AddressFormatRepository;
use CommerceGuys\Addressing\Country\CountryRepository;
use CommerceGuys\Addressing\Subdivision\SubdivisionRepository;
use Concrete\Core\Localization\Address\Formatter;
use Concrete\Core\Localization\Localization;
use Concrete\Core\Utility\Service\Text as TextService;

class AddressFormat
{
    /** @var TextService */
    protected $text;

    /** @var \CommerceGuys\Addressing\Formatter\FormatterInterface */
    protected $formatter;

    /** @var \CommerceGuys\Addressing\AddressFormat\AddressFormatRepositoryInterface */
    protected $addressFormatRepository;

    /**
     * Options that control the address formatting.
     *
     * @var array
     */
    protected $options = [
        // Defines whether the subdivision (state/province) names are displayed
        // instead of their codes.
        // When set to `true`, the state of California (US) will be displayed as
        // "California".
        // When set to `false`, it will be displayed as "CA".
        'subdivision_names' => true,
    ];

    /**
     * The constructor for the service.
     *
     * @param TextService $text the concrete5 text service
     */
    public function __construct(TextService $text)
    {
        $this->text = $text;

        $this->addressFormatRepository = new AddressFormatRepository();
        $countryRepository = new CountryRepository();
        $subdivisionRepository = new SubdivisionRepository(
            $this->addressFormatRepository
        );

        $this->formatter = new Formatter(
            $this->addressFormatRepository,
            $countryRepository,
            $subdivisionRepository
        );
    }

    /**
     * Set the options that control how the address will be rendered. These can
     * be passed to the underlying formatter class.
     *
     * @param array $options the options
     */
    public function setOptions(array $options)
    {
        $this->options = $options;
    }

    /**
     * Formats a local concrete5 address lines array with the underlying address
     * formatting library.
     *
     * The options that can be passed to the formatter in the array of the
     * fourth argument:
     * - subdivision_names - Defines whether the subdivision names are printed
     *   to the output instead of their codes. Default: true.
     * - subdivision_translations - Defines whether the subdivision names are
     *   translated to the given locale if translations are available. Otherwise
     *   the will be printed out in their default locale. Default: true.
     *
     * @param  array       $addressData an array containing the keys and values
     *                                  for all address lines
     * @param  string      $format      the format of the output value, either
     *                                  'html' (default) or 'text'
     * @param  string|null $locale      the locale to be used to translate the
     *                                  country name and possibly the
     *                                  state/province name in case the
     *                                  configuration is set to do so
     *
     * @return string                   the formatted address string
     */
    public function format(
        array $addressData,
        $format = 'html',
        $locale = null
    ) {
        $addressData += [
            'address1' => null,
            'address2' => null,
            'address3' => null,
            'city' => null,
            'state_province' => null,
            'country' => null,
            'postal_code' => null,
        ];

        if (empty($addressData['country'])) {
            // In case the country is not defined, the proper formatting cannot
            // be determined by the underlying formatting library. In this case,
            // return the legacy format that concrete5 used to use prior to the
            // country specific address formatting.
            return $this->formatWithoutCountry($addressData, $format);
        }

        // Addressing does not currently support a third address line.
        // See: https://github.com/commerceguys/addressing/pull/121
        $line2 = trim($addressData['address2']);
        if (!empty($addressData['address3'])) {
            if (!empty($line2)) {
                $line2 .= ', ';
            }
            $line2 .= trim($addressData['address3']);
        }

        $address = new Address();
        if (!empty($addressData['country'])) {
            $address = $address->withCountryCode($addressData['country']);
        }
        if (!empty($addressData['address1'])) {
            $address = $address->withAddressLine1($addressData['address1']);
        }
        if (!empty($line2)) {
            $address = $address->withAddressLine2($line2);
        }
        if (!empty($addressData['state_province'])) {
            $address = $address->withAdministrativeArea(
                $addressData['state_province']
            );
        }
        if (!empty($addressData['city'])) {
            $address = $address->withLocality($addressData['city']);
        }
        if (!empty($addressData['postal_code'])) {
            $address = $address->withPostalCode($addressData['postal_code']);
        }

        if (empty($locale)) {
            $locale = Localization::activeLocale();
        }

        // Pass the options to the formatter
        $options = [];
        if (!empty($locale)) {
            $options['locale'] = $this->convertLocale($locale);
            $address = $address->withLocale($options['locale']);
        }
        if (isset($this->options['subdivision_names'])) {
            $options['subdivision_names'] = $this->options['subdivision_names'];
        }
        if ($format === 'html') {
            $options['html'] = true;
            if (!isset($options['html_tag'])) {
                $options['html_tag'] = 'div';
            }
            if (!isset($options['html_attributes'])) {
                $options['html_attributes'] = [
                    'class' => 'ccm-address-text',
                ];
            }
        } else {
            $options['html'] = false;
        }

        return $this->formatter->format($address, $options);
    }

    /**
     * Gets an array of the used local address format keys, i.e. which address
     * lines are used for the country.
     *
     * @param  string $code a country code
     *
     * @return array        an array containing the address line keys
     */
    public function getCountryAddressUsedFields($code)
    {
        $format = $this->getCountryAddressFormat($code);
        if (!is_object($format)) {
            return ['address1', 'city'];
        }

        return $this->convertFormat($format->getUsedFields());
    }

    /**
     * Gets an array of the required local address format keys, i.e. which
     * address lines are required for the country.
     *
     * @param  string $code a country code
     *
     * @return array        an array containing the address line keys
     */
    public function getCountryAddressRequiredFields($code)
    {
        $format = $this->getCountryAddressFormat($code);
        if (!is_object($format)) {
            return ['address1', 'city'];
        }

        return $this->convertFormat($format->getRequiredFields());
    }

    /**
     * Converts the passed locale to the format expected by the underlying
     * address formatting library.
     *
     * @param  string $locale the locale in the concrete5 format
     *
     * @return string         the locale in the address formatting library
     *                        format
     */
    protected function convertLocale($locale)
    {
        $parts = explode('_', $locale);
        if (count($parts) > 1) {
            return $parts[0] . '-' . $parts[1];
        }

        return $locale;
    }

    /**
     * Converts the underlying address format field array to the "local" format
     * better understood within the concrete5 context.
     *
     * @param  array  $sourceFormat an array of the field definitions of the
     *                              underyling address format repository
     *
     * @return array                an array of the local address format keys
     */
    protected function convertFormat(array $sourceFormat)
    {
        $local = [];
        foreach ($sourceFormat as $field) {
            if ($field === AddressField::ADDRESS_LINE1) {
                $local[] = 'address1';
            } elseif ($field === AddressField::ADDRESS_LINE2) {
                $local[] = 'address2';
            } elseif ($field === AddressField::LOCALITY) {
                $local[] = 'city';
            } elseif ($field === AddressField::ADMINISTRATIVE_AREA) {
                $local[] = 'state_province';
            } elseif ($field === AddressField::POSTAL_CODE) {
                $local[] = 'postal_code';
            }
        }

        return $local;
    }

    /**
     * Fetches the country's address format from the address format repository.
     *
     * @param  string $code the country code
     *
     * @return \CommerceGuys\Addressing\AddressFormat\AddressFormat the address
     *         format for the country
     */
    protected function getCountryAddressFormat($code)
    {
        if (!is_string($code)) {
            return null;
        }

        return $this->addressFormatRepository->get($code);
    }

    /**
     * This is used only used when no country value is provided. Otherwise
     * formatting is handled by the underlying address formatting library.
     * Therefore, this does not have to care about the country specific
     * formatting as there is no country information available.
     *
     * @param  array  $addressData the address data to print out
     * @param  string $format      the display format, 'html' (default) or
     *                             'text'
     *
     * @return string              a text representation of the address
     */
    protected function formatWithoutCountry(
        array $addressData,
        $format = 'html'
    ) {
        $lines = [];
        if (isset($addressData['address1'])) {
            $lines[] = $addressData['address1'];
        }
        if (isset($addressData['address2'])) {
            $lines[] = $addressData['address2'];
        }
        $postalAreaLine = '';
        if (isset($addressData['city'])) {
            $postalAreaLine = $addressData['city'];
            if ($addressData['state_province']) {
                // The country is unknown, so the text representation of the
                // state or the provice cannot be determined. Fall back to the
                // code.
                $postalAreaLine .= ', ' . $addressData['state_province'];
            }
        }
        if (isset($addressData['postal_code'])) {
            $postalAreaLine .= ' ' . $addressData['postal_code'];
        }
        if (!empty($postalAreaLine)) {
            $lines[] = trim($postalAreaLine);
        }

        if ($format === 'html') {
            return implode('<br>', $lines);
        }

        return implode("\n", $lines);
    }
}
