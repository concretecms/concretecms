<?php

namespace Concrete\Core\Form\Service;

use Concrete\Core\Application\Application;
use Concrete\Core\Application\Service\FileManager;
use Concrete\Core\Error\ErrorList\ErrorList;
use Concrete\Core\File\File;
use Concrete\Core\Form\Service\Widget\PageSelector;
use Concrete\Core\Http\Request;
use Concrete\Core\Page\Page;
use Concrete\Core\Utility\Service\Validation\Numbers;
use Symfony\Component\HttpFoundation\ParameterBag;

/**
 * A class containing methods to pick a destination (that is, a concrete5 page, an external URL, a concrete5 file).
 */
class DestinationPicker
{
    /**
     * Height of the Page Selector.
     *
     * @var int
     */
    const FIELDHEIGHT_PAGESELECTOR = 40;

    /**
     * Height of the External URL field.
     *
     * @var int
     */
    const FIELDHEIGHT_EXTERNALURL = 40;

    /**
     * Height of the File Selector.
     *
     * @var int
     */
    const FIELDHEIGHT_FILESELECTOR = 60;

    /**
     * Destination kind: concrete5 page.
     *
     * @var int
     */
    const DESTINATIONKIND_PAGE = 0b001;

    /**
     * Destination kind: external URL.
     *
     * @var int
     */
    const DESTINATIONKIND_EXTERNALURL = 0b010;

    /**
     * Destination kind: concrete5 file.
     *
     * @var int
     */
    const DESTINATIONKIND_FILE = 0b100;

    /**
     * Destination kind: all.
     *
     * @var int
     */
    const DESTINATIONKIND_ALL = -1;

    /**
     * @var \Concrete\Core\Form\Service\Form
     */
    protected $formService;

    /**
     * @var \Concrete\Core\Application\Application
     */
    protected $app;

    /**
     * @var \Concrete\Core\Form\Service\Widget\PageSelector
     */
    protected $pageSelector;

    /**
     * @var \Concrete\Core\Application\Service\FileManager
     */
    protected $fileManager;

    /**
     * @var \Concrete\Core\Http\Request
     */
    protected $request;

    /**
     * The Numbers validation service instance.
     *
     * @var \Concrete\Core\Utility\Service\Validation\Numbers
     */
    protected $valn;

    public function __construct(Application $app, Form $formService, PageSelector $pageSelector, FileManager $fileManager, Request $request, Numbers $valn)
    {
        $this->app = $app;
        $this->formService = $formService;
        $this->pageSelector = $pageSelector;
        $this->fileManager = $fileManager;
        $this->request = $request;
        $this->valn = $valn;
    }

    /**
     * Renders a field where users can choose an internal page, an external URL, or a file.
     *
     * @param string $key the name/id of the element
     * @param int|\Concrete\Core\Page\Page|null|bool $pageID The pre-selected internal page (if === false users won't be able to enter a page ID)
     * @param string|bool $externalUrl The pre-selected external URL (if === false users won't be able to enter an external URL)
     * @param int|\Concrete\Core\File\File|\Concrete\Core\Entity\File\File|\Concrete\Core\Entity\File\Version|null|bool $fileID The pre-selected file (if === false users won't be able to enter a file ID)
     * @param array $options Valid values are:
     * <ul>
     *     <li><code>required</code> set to a non falsy value if users must choose an internal page/external URL</li>
     *     <li><code>externalUrlMaxLength</code> the maximum length of the external URL</li>
     *     <li><code>fileFilters</code> the filter to pass to the file selector</li>
     * </ul>
     *
     * @see \Concrete\Core\Form\Service\Form::decodeDestination()
     */
    public function destination($key, $pageID, $externalUrl, $fileID, array $options = [])
    {
        if ($pageID !== false) {
            if (is_object($pageID) && !$pageID->isError()) {
                $pageID = (int) $pageID->getCollectionID();
            } else {
                $pageID = is_scalar($pageID) ? (int) $pageID : 0;
            }
            if ($pageID <= 0) {
                $pageID = null;
            }
        }
        if ($externalUrl !== false) {
            $externalUrl = is_scalar($externalUrl) ? (string) $externalUrl : '';
        }
        if ($fileID !== false) {
            if (is_object($fileID)) {
                $fileID = (int) $fileID->getFileID();
            } else {
                $fileID = is_scalar($fileID) ? (int) $fileID : 0;
            }
            if ($fileID <= 0) {
                $fileID = null;
            }
        }

        $optionValue = null;
        $whichHtml = $this->buildWhichSelector($key, $pageID, $externalUrl, $fileID, $options, $optionValue);
        $kindsHtml = '';
        $kindsHeight = 0;
        $kindsHtml .= $this->buildPageSelector($key, $pageID, $optionValue === 'P', $options, $kindsHeight);
        $kindsHtml .= $this->buildExternalUrlSelector($key, $externalUrl, $optionValue === 'X', $options, $kindsHeight);
        $kindsHtml .= $this->buildFileSelector($key, $fileID, $optionValue === 'F', $options, $kindsHeight);

        return $this->buildMergedSelectors($key, $whichHtml, $kindsHtml, $kindsHeight);
    }

    /**
     * Decode received destination the value.
     *
     * @param string $key The key of the destination field
     * @param int $allowedKind The allowed destination kinds (sum of one or more values of DestinationPicker::DESTINATIONKIND_... constants)
     * @param \Concrete\Core\Error\ErrorList\ErrorList $errors Errors will be added to this ErrorList instance
     * @param array $options Allowed values:
     * <ul>
     *     <li><code>required</code> set to a non falsy value if users must choose an internal page/external URL</li>
     *     <li><code>data</code> set to 'get' to parse fields received in GET, set to an array to fetch data from it. Otherwise we'll use POST.</li>
     *     <li><code>keepOthers</code> set to a non falsy value to get the unselected values (for example: if users specify a page, but previusly specified an external URL, with this flag you'll get the external URL).</li>
     *     <li><code>externalUrlMaxLength</code> the maximum length of the external URL</li>
     *     <li><code>externalTooLong</code> the error message to be shown when the user specified a too long external url</li>
     *     <li><code>noDestinationSpecified</code> the error message to be shown when the user did not specify the destination value</li>
     * </ul>
     *
     * @return array Will return an array with these index-based values:
     * <ul>
     *     <li><code>0</code>: the selected kind of destination (one of the DestinationPicker::DESTINATIONKIND_... constants, or NULL if no item was selected)</li>
     *     <li><code>1</code>: the selected internal page ID (or NULL if no page was selected).</li>
     *     <li><code>2</code>: the selected external URL (or an empty string if no external URL was selected).</li>
     *     <li><code>3</code>: the selected file ID (or NULL if no file was selected).</li>
     *
     * @see \Concrete\Core\Form\Service\Form::destination()
     */
    public function decodeDestination($key, $allowedKind, ErrorList &$errors = null, array $options = [])
    {
        $allowedKind = (int) $allowedKind;
        if ($errors === null) {
            $errors = $this->app->make(ErrorList::class);
        }
        $bag = null;
        if (isset($options['data'])) {
            if (is_array($options['data'])) {
                $bag = new ParameterBag($options['data']);
            } elseif (is_string($options['data']) && strcasecmp($options['data'], 'get') === 0) {
                $bag = $this->request->query;
            }
        }
        if ($bag === null) {
            $bag = $this->request->request;
        }
        $which = $bag->get("{$key}__which");
        $keepOthers = !empty($options['keepOthers']);
        $receivedKind = null;

        $receivedPageID = null;
        if ($allowedKind & static::DESTINATIONKIND_PAGE && ($which === 'P' || $keepOthers)) {
            $receivedPageID = $this->getReceivedPageID($bag, $key, $which === 'P', $options, $errors);
            if ($receivedPageID !== null && $which === 'P') {
                $receivedKind = static::DESTINATIONKIND_PAGE;
            }
        }

        $receivedExternalURL = '';
        if ($allowedKind & static::DESTINATIONKIND_EXTERNALURL && ($which === 'X' || $keepOthers)) {
            $receivedExternalURL = $this->getReceivedExternalUrl($bag, $key, $which === 'X', $options, $errors);
            if ($receivedExternalURL !== '' && $which === 'X') {
                $receivedKind = static::DESTINATIONKIND_EXTERNALURL;
            }
        }

        $receivedFileID = null;
        if ($allowedKind & static::DESTINATIONKIND_FILE && ($which === 'F' || $keepOthers)) {
            $receivedFileID = $this->getReceivedFileID($bag, $key, $which === 'F', $options, $errors);
            if ($receivedFileID !== null && $which === 'F') {
                $receivedKind = static::DESTINATIONKIND_FILE;
            }
        }

        if ($receivedKind === null && !empty($options['required'])) {
            if (empty($options['noDestinationSpecified'])) {
                $errors->add(t('Please specify a value for the destination.'));
            } else {
                $errors->add($options['noDestinationSpecified']);
            }
        }

        return [
            $receivedKind,
            $receivedPageID,
            $receivedExternalURL,
            $receivedFileID,
        ];
    }

    /**
     * Build the HTML to select the destination kind.
     *
     * @param string $key
     * @param int|null|false $pageID
     * @param string|false $externalUrl
     * @param int|null|false $fileID
     * @param array $options
     * @param string $optionValue [output]
     *
     * @return string
     */
    protected function buildWhichSelector($key, $pageID, $externalUrl, $fileID, array $options, &$optionValue)
    {
        $required = !empty($options['required']);
        $optionValues = [];
        if (!$required) {
            $optionValues['N'] = tc('Destination', 'Not specified');
        }
        if ($pageID !== false) {
            $optionValues['P'] = t('Page');
        }
        if ($externalUrl !== false) {
            $optionValues['X'] = t('External URL');
        }
        if ($fileID !== false) {
            $optionValues['F'] = t('File');
        }
        $optionValue = $this->request->request->get("{$key}__which");
        if ($optionValue === null || !is_string($optionValue) || !isset($optionValues[$optionValue])) {
            if ($pageID !== false && $pageID !== null) {
                $optionValue = 'P';
            } elseif ($externalUrl !== false && $externalUrl !== '') {
                $optionValue = 'X';
            } elseif ($fileID !== false && $fileID !== null) {
                $optionValue = 'F';
            } else {
                if ($required) {
                    $optionValues = ['' => t('Please select')] + $optionValues;
                    $optionValue = '';
                } else {
                    $optionValue = 'N';
                }
            }
        }

        $miscFields = [];
        if ($required) {
            $miscFields['required'] = 'required';
        }

        return $this->formService->select("{$key}__which", $optionValues, $optionValue, $miscFields);
    }

    /**
     * @param string $key
     * @param int|null|false $pageID
     * @param bool $current
     * @param array $options
     * @param int $kindsHeight
     *
     * @return string
     */
    protected function buildPageSelector($key, $pageID, $current, array $options, &$kindsHeight)
    {
        if ($pageID === false) {
            $result = '';
        } else {
            $result = "<div id=\"{$key}__which_P\"" . ($current ? '' : ' style="display:none"') . '>' . $this->pageSelector->selectPage("{$key}__cid", $pageID) . '</div>';
            $kindsHeight = max($kindsHeight, static::FIELDHEIGHT_PAGESELECTOR);
        }

        return $result;
    }

    /**
     * @param string $key
     * @param string|false $externalUrl
     * @param bool $current
     * @param array $options
     * @param int $kindsHeight
     *
     * @return string
     */
    protected function buildExternalUrlSelector($key, $externalUrl, $current, array $options, &$kindsHeight)
    {
        if ($externalUrl === false) {
            $result = '';
        } else {
            $attrs = [
                'placeholder' => t('URL'),
            ];
            if (!empty($options['externalUrlMaxLength'])) {
                $attrs['maxlength'] = (int) $options['externalUrlMaxLength'];
            }
            $result = "<div id=\"{$key}__which_X\"" . ($current ? '' : ' style="display:none"') . '>' . $this->formService->text("{$key}__externalurl", $externalUrl, $attrs) . '</div>';
            $kindsHeight = max($kindsHeight, static::FIELDHEIGHT_EXTERNALURL);
        }

        return $result;
    }

    /**
     * @param string $key
     * @param int|null|false $fileID
     * @param bool $current
     * @param array $options
     * @param int $kindsHeight
     *
     * @return string
     */
    protected function buildFileSelector($key, $fileID, $current, array $options, &$kindsHeight)
    {
        if ($fileID === false) {
            $result = '';
        } else {
            $args = [];
            if (!empty($options['fileFilters']) && is_array($options['fileFilters'])) {
                $args['filters'] = $options['fileFilters'];
            }
            $result = "<div id=\"{$key}__which_F\"" . ($current ? '' : ' style="display:none"') . '>' . $this->fileManager->file("{$key}__fid", "{$key}__fid", t('Choose File'), $fileID, $args) . '</div>';
            $kindsHeight = max($kindsHeight, static::FIELDHEIGHT_FILESELECTOR);
        }

        return $result;
    }

    /**
     * @param string $key
     * @param string $whichHtml
     * @param string $kindsHtml
     * @param int $kindsHeight
     *
     * @return string
     */
    protected function buildMergedSelectors($key, $whichHtml, $kindsHtml, $kindsHeight)
    {
        return <<<EOT
{$whichHtml}
<div id="{$key}__which_container" style="height: {$kindsHeight}px">
    {$kindsHtml}
</div>
<script>
$(document).ready(function() {
    var which = $('#{$key}__which'),
        dict = {
            P: $('#{$key}__which_P'),
            X: $('#{$key}__which_X'),
            F: $('#{$key}__which_F'),
        };
    which
        .on('change', function() {
            var v = which.val() || '';
            for (var k in dict) {
                if (dict[k].length > 0) {
                    if (k === v) {
                        dict[k].show();
                    } else {
                        dict[k].hide();
                    }
                }
            }
        })
        .trigger('change')
    ;
});
</script>
EOT
    ;
    }

    /**
     * Get the received page ID.
     *
     * @param ParameterBag $bag
     * @param string $key
     * @param bool $current
     * @param array $options
     * @param ErrorList $errors
     *
     * @return int|null
     */
    protected function getReceivedPageID(ParameterBag $bag, $key, $current, array $options, ErrorList $errors)
    {
        $result = null;
        $cid = $bag->get("{$key}__cid");
        if ($this->valn->integer($cid, 1)) {
            $page = Page::getByID($cid);
            if ($page && !$page->isError()) {
                $result = (int) $cid;
            }
        }

        return $result;
    }

    /**
     * Get the received external URL.
     *
     * @param ParameterBag $bag
     * @param string $key
     * @param bool $current
     * @param array $options
     * @param ErrorList $errors
     *
     * @return string
     */
    protected function getReceivedExternalUrl(ParameterBag $bag, $key, $current, array $options, ErrorList $errors)
    {
        $result = $bag->get("{$key}__externalurl");
        $result = is_string($result) ? trim($result) : '';
        if ($result !== '') {
            if (!empty($options['externalUrlMaxLength']) && $this->valn->integer($options['externalUrlMaxLength'], 1)) {
                $maxLength = (int) $options['externalUrlMaxLength'];
                $length = function_exists('mb_strlen') ? mb_strlen($result) : strlen($result);
                if ($length > $maxLength) {
                    $result = '';
                    if ($current) {
                        if (empty($options['externalTooLong'])) {
                            $errors->add(t('Please specify a shorter external URL.'));
                        } else {
                            $errors->add($options['externalTooLong']);
                        }
                    }
                }
            }
        }

        return $result;
    }

    /**
     * Get the received file ID.
     *
     * @param ParameterBag $bag
     * @param string $key
     * @param bool $current
     * @param array $options
     * @param ErrorList $errors
     *
     * @return int|null
     */
    protected function getReceivedFileID(ParameterBag $bag, $key, $current, array $options, ErrorList $errors)
    {
        $result = null;
        $fid = $bag->get("{$key}__fid");
        if ($this->valn->integer($fid, 1)) {
            $file = File::getByID($fid);
            if (!$file !== null) {
                $result = (int) $fid;
            }
        }

        return $result;
    }
}
