<?php

namespace Concrete\Core\Backup\ContentImporter\Importer\Routine;

use Concrete\Core\Package\BrokenPackage;
use Concrete\Core\Package\PackageService;
use Concrete\Core\Utility\Service\Xml;
use Concrete\Core\Validation\CSRF\Token;
use SimpleXMLElement;

/**
 * @example
 * <?xml version="1.0"?>
 * <concrete5-cif version="1.0">
 *     <packages>
 *         <package handle="my_package" />
 *         <package handle="my_package" full-content-swap="true" />
 *         <package handle="my_package" full-content-swap="true" content-swap-file="my-content.xml" />
 *         <package handle="my_package">
 *             <option name="scalar" value="value" />
 *             <option name="array[]" value="value1" />
 *             <option name="array[]" value="value2" />
 *         </package>
 *     </packages>
 * </concrete5-cif>
 */
class ImportPackagesRoutine extends AbstractRoutine
{
    /**
     * @var \Concrete\Core\Package\PackageService
     */
    protected $packageService;

    /**
     * @var \Concrete\Core\Utility\Service\Xml
     */
    protected $xml;

    /**
     * @var \Concrete\Core\Validation\CSRF\Token
     */
    protected $token;

    public function __construct(PackageService $packageService, Xml $xml, Token $token)
    {
        $this->packageService = $packageService;
        $this->xml = $xml;
        $this->token = $token;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Backup\ContentImporter\Importer\Routine\RoutineInterface::getHandle()
     */
    public function getHandle()
    {
        return 'packages';
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Backup\ContentImporter\Importer\Routine\RoutineInterface::import()
     */
    public function import(SimpleXMLElement $sx)
    {
        if (!isset($sx->packages)) {
            return;
        }
        foreach ($sx->packages->package as $xPackage) {
            $pkg = $this->packageService->getByHandle((string) $xPackage['handle']);
            if ($pkg) {
                continue;
            }
            $pkgClass = $this->packageService->getClass((string) $xPackage['handle']);
            if (!$pkgClass || $pkgClass instanceof BrokenPackage) {
                continue;
            }
            $data = [];
            if ($this->xml->getBool($xPackage['full-content-swap'])) {
                // set this token to perform a full content swap when installing starting point packages
                $data['pkgDoFullContentSwap'] = true;
                $data['ccm_token'] = $this->token->generate('install_options_selected');
                if (isset($xPackage['content-swap-file'])) {
                    $data['contentSwapFile'] = (string) $xPackage['content-swap-file'];
                } else {
                    $data['contentSwapFile'] = 'content.xml';
                }
            }
            if (isset($xPackage->option)) {
                foreach ($xPackage->option as $xOption) {
                    $optionName = (string) $xOption['name'];
                    $optionValue = (string) $xOption['value'];
                    if (str_ends_with($optionName, '[]')) {
                        $key = substr($optionName, 0, -2);
                        if (is_array($data[$key] ?? null)) {
                            $data[$key][] = $optionValue;
                        } else {
                            $data[$key] = [$optionValue];
                        }
                    } else {
                        $data[$optionName] = $optionValue;
                    }
                }
            }
            $this->packageService->install($pkgClass, $data);
        }
    }
}
