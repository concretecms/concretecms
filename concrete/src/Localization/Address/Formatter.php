<?php

namespace Concrete\Core\Localization\Address;

use CommerceGuys\Addressing\AddressFormat\AddressFormat;
use CommerceGuys\Addressing\AddressInterface;
use CommerceGuys\Addressing\Formatter\DefaultFormatter;
use CommerceGuys\Addressing\Locale;
use CommerceGuys\Addressing\Subdivision\Subdivision;
use Concrete\Core\Localization\Service\StatesProvincesList;
use Concrete\Core\Support\Facade\Application;

class Formatter extends DefaultFormatter
{
    /**
     * The default options.
     *
     * @var array
     */
    protected $defaultOptions = [
        'locale' => 'en',
        'html' => true,
        'html_tag' => 'p',
        'html_attributes' => ['translate' => 'no'],
        'subdivision_names' => true,
    ];

    /**
     * Customized method to get the administrative area's full name for US where
     * they are stored as codes. Both formats work for mail but this is done to
     * preserve backwards compatibility for c5 older versions which print out
     * the state's full name in the address.
     *
     * In addition to the default formatter, this adds the following options
     * with the following possibilities:
     * - subdivision_names - Return the subdivision names in the
     *   return array. By default these are with the default locale as defined
     *   for the country for which the subdivisions belong to.
     *   * Type: boolean
     *   * Default: true
     *
     * Since the subdivisions can be translated for each country, this
     * modification only applies in case the 'locale' option has the English
     * language.
     *
     * {@inheritdoc}
     */
    protected function buildView(
        AddressInterface $address,
        AddressFormat $addressFormat,
        array $options
    ) {
        $view = parent::buildView($address, $addressFormat, $options);

        if ($options['subdivision_names'] === true) {
            // Replace the subdivision values with their names, if a name
            // exists.
            $subValues = $this->getSubdivisionValuesWithNames(
                $address,
                $addressFormat
            );
            foreach ($subValues as $field => $value) {
                $view[$field]['value'] = $value;
            }
        }

        return $view;
    }

    /**
     * Modified version of DefaultFormatter::getValues().
     *
     * This only gets the subdivision values and possibly converts the normally
     * returned codes to subdivision names.
     *
     * @param AddressInterface $address the address for which to fetch
     *                                  the subdivisions
     * @param AddressFormat $addressFormat the address format object
     *                                     defining the used subdivision
     *                                     fields
     *
     * @return array an array containing the keys of
     *               the subdivision fields and their
     *               corresponding values
     */
    protected function getSubdivisionValuesWithNames(
        AddressInterface $address,
        AddressFormat $addressFormat
    ) {
        $values = [];

        $subdivisionFields = $addressFormat->getUsedSubdivisionFields();
        $parents = [$address->getCountryCode()];
        foreach ($subdivisionFields as $index => $field) {
            $getter = 'get' . ucfirst($field);
            $subdivisionValue = $address->{$getter}();
            if (empty($subdivisionValue)) {
                // This level is empty, so there can be no sublevels.
                break;
            }

            $subdivision = $this->subdivisionRepository->get(
                $subdivisionValue,
                $parents
            );

            if (!$subdivision && $field === 'administrativeArea') {
                // The states/provinces are formatted differently in the c5
                // states and provinces list than in commerceguys/addressing.
                // E.g. for Japan, c5 knows Tokyo as '13' where
                // commerceguys/addressing knows it as 'JP-13'. Therefore, try
                // if the alternative ISO code exists.
                $subdivision = $this->subdivisionRepository->get(
                    $address->getCountryCode() . '-' . $subdivisionValue,
                    $parents
                );
                foreach ($this->subdivisionRepository->getAll($parents) as $sd) {
                    if ($sd->getIsoCode() === $subdivisionValue ||
                        $sd->getIsoCode() === $address->getCountryCode() . '-' . $subdivisionValue
                    ) {
                        $subdivision = $sd;
                        break;
                    }
                }

                if (!$subdivision) {
                    // Finally, if the subdivision is unknown to
                    // commerceguys/addressing, fallback to the c5 states and
                    // provinces.
                    $app = Application::getFacadeApplication();
                    $spList = $app->make(StatesProvincesList::class);
                    $subValue = $spList->getStateProvinceName(
                        $subdivisionValue,
                        $address->getCountryCode()
                    );

                    if (!empty($subValue)) {
                        $values[$field] = $subValue;
                    }
                }
            }

            if (!$subdivision) {
                break;
            }

            $parents[] = $subdivisionValue;
            $subValue = $this->getSubdivisionNameValue(
                $address,
                $subdivision
            );

            // Only replace the original value in case a name was found.
            if (!empty($subValue)) {
                $values[$field] = $subValue;
            }

            if (!$subdivision->hasChildren()) {
                // The current subdivision has no children, stop.
                break;
            }
        }

        return $values;
    }

    /**
     * Gets the subdivision's name value.
     *
     * @param AddressInterface $address the address for which to get the
     *                                  name
     * @param Subdivision $subdivision the subdivision's code to get
     *                                 the name for
     *
     * @return string|null a text representation of the
     *                     subdivision's name or null if a
     *                     corresponding text representation
     *                     does not exist
     */
    protected function getSubdivisionNameValue(
        AddressInterface $address,
        Subdivision $subdivision
    ) {
        $useLocalName = Locale::matchCandidates(
            $address->getLocale(),
            $subdivision->getLocale()
        );

        if ($useLocalName) {
            return $subdivision->getLocalName();
        }

        return $subdivision->getName();
    }
}
