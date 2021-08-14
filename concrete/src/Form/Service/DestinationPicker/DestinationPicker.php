<?php

namespace Concrete\Core\Form\Service\DestinationPicker;

use ArrayAccess;
use Concrete\Core\Application\Application;
use Concrete\Core\Form\Service\Form;
use Concrete\Core\Http\Request;
use RuntimeException;

class DestinationPicker
{
    /**
     * @var \Concrete\Core\Application\Application
     */
    protected $app;

    /**
     * @var \Concrete\Core\Form\Service\Form
     */
    protected $formService;

    /**
     * @var \Concrete\Core\Http\Request
     */
    protected $request;

    /**
     * List of registered destination picker.
     *
     * @var \Concrete\Core\Form\Service\DestinationPicker\PickerInterface[]
     */
    protected $registeredPickers = [];

    /**
     * Initialize the instance.
     *
     * @param \Concrete\Core\Application\Application $app
     * @param \Concrete\Core\Form\Service\Form $formService
     * @param \Concrete\Core\Http\Request $request
     */
    public function __construct(Application $app, Form $formService, Request $request)
    {
        $this->app = $app;
        $this->formService = $formService;
        $this->request = $request;
    }

    /**
     * Register a new destination picker.
     *
     * @param string $handle
     * @param \Concrete\Core\Form\Service\DestinationPicker\PickerInterface $picker
     *
     * @return $this
     */
    public function registerPicker($handle, PickerInterface $picker)
    {
        $this->registeredPickers[$handle] = $picker;

        return $this;
    }

    /**
     * Register multiple pickers.
     *
     * @param array $pickers array keys are the picker handles, array values are PickerInterface instances
     *
     * @return $this
     */
    public function registerPickers(array $pickers)
    {
        $this->registeredPickers = $pickers + $this->registeredPickers;

        return $this;
    }

    /**
     * Unregister a registered picker.
     *
     * @param string $handle
     *
     * @return $this
     */
    public function unregisterPicker($handle)
    {
        unset($this->registeredPickers[$handle]);

        return $this;
    }

    /**
     * Get a registered picker given its handle.
     *
     * @param string $handle
     *
     * @return \Concrete\Core\Form\Service\DestinationPicker\PickerInterface|null
     */
    public function getPicker($handle)
    {
        return isset($this->registeredPickers[$handle]) ? $this->registeredPickers[$handle] : null;
    }

    /**
     * Get the list of currently registered pickers.
     *
     * @return \Concrete\Core\Form\Service\DestinationPicker\PickerInterface[]
     */
    public function getRegisteredPickers()
    {
        return $this->registeredPickers;
    }

    /**
     * Generate the HTML that renders the destination picker.
     *
     * @param string $key The field name
     * @param string[]|array[] $pickers The list of picker handles. To pass options to the pickers use the handles as keys, and arrays for values.
     * @param string|null $currentPickerHandle The handle of the pre-selected picker
     * @param mixed|null $currentValue the value of the pre-selected picker
     *
     * @return string
     *
     * @example
     * <code><pre>echo $app->make(DestinationPicker::class)->generate(
     *     'my_field',
     *     [
     *         'none',
     *         'page',
     *         'external_url' => ['maxlength' => 255],
     *         'file' => ['filters' => [['field' => 'type', 'type' => \Concrete\Core\File\Type\Type::T_IMAGE]]],
     *     ]
     * );</pre></code>
     */
    public function generate($key, array $pickers, $currentPickerHandle = null, $currentValue = null)
    {
        $pickerHandlesWithOptions = $this->getHandlesWithOptions($pickers);
        list($selectedPicker, $html) = $this->buildWhichSelector($key, $pickerHandlesWithOptions, $currentPickerHandle);

        $uniqueID = 'ccm-destinationpicker-' . str_replace('.', '-', microtime(true)) . '-' . mt_rand();
        $pickersHtml = '';
        $pickersHeight = 0;
        foreach ($pickerHandlesWithOptions as $handle => $options) {
            $picker = $this->getPicker($handle);
            $pickersHeight = max($pickersHeight, $picker->getHeight());
            $pickerKey = "{$key}_{$handle}";
            $pickerValue = $this->request->isPost() ? $this->request->request->get($pickerKey) : ($handle === $currentPickerHandle ? $currentValue : null);
            $pickerHtml = $picker->generate($pickerKey, $options, $pickerValue);
            if ($pickerHtml !== '') {
                $style = $selectedPicker === $handle ? '' : ' style="display: none"';
                $pickersHtml .= <<<EOT
    <div class="pt-2 {$uniqueID}" id="{$uniqueID}_{$handle}"{$style}>
        {$pickerHtml}
    </div>
EOT
                ;
            }
        }
        if ($pickersHtml !== '') {
            $html .= <<<EOT
<div>
    {$pickersHtml}
</div>
<script>
$(document).ready(function() {
    $('select[name="{$key}__which"]')
        .on('change', function() {
            $('div.{$uniqueID}').hide();
            $('div#{$uniqueID}_' + this.value).show();
        })
        .trigger('change')
    ;
});
</script>
EOT
            ;
        }

        return $html;
    }

    /**
     * Parse and validate the data received in POST.
     *
     * @param string $key The field name
     * @param string[]|array[] $pickers The list of picker handles. To pass options to the pickers use the handles as keys, and arrays for values.
     * @param \ArrayAccess|null $errors A list to add errors to
     * @param string|null $fieldDisplayName The name of the field (used to describe errors)
     * @param array|null $data An array containing the data to be decoded (if null, we'll use the POST data from the current request)
     *
     * @return string[]|mixed[]|null[] Returns two NULLs in case of errors, or the selected picker handle and its value otherwise
     *
     * @example
     * <code><pre>
     * $errors = $app->make('errors');
     * $dp = $app->make(DestinationPicker::class);
     * list($handle, $value) = $dp->decode(
     *     'my_field',
     *     [
     *         'none',
     *         'page',
     *         'external_url' => ['maxlength' => 255],
     *         'file',
     *     ],
     *     $errors,
     *     t('Destination')
     * );
     * </pre></code>
     * $handle and $value will be NULL if (and only if) errors occurred (added to the $errors parameter).
     */
    public function decode($key, array $pickers, ArrayAccess $errors = null, $fieldDisplayName = null, array $data = null)
    {
        $handle = null;
        $value = null;
        if ($data === null) {
            $data = $this->request->request->all();
        }
        $which = array_get($data, "{$key}__which");
        if (is_string($which)) {
            $pickerHandlesWithOptions = $this->getHandlesWithOptions($pickers);
            if (isset($pickerHandlesWithOptions[$which])) {
                $picker = $this->getPicker($which);
                $pickerKey = "{$key}_{$which}";
                $value = $picker->decode($data, $pickerKey, $pickerHandlesWithOptions[$which], $errors, $fieldDisplayName);
                if ($value !== null) {
                    $handle = $which;
                }
            }
            if ($handle === null && count($pickerHandlesWithOptions) === 1) {
                $handle = key($pickerHandlesWithOptions);
            }
        }
        if ($handle === null) {
            if ($errors !== null) {
                $errors[] = t('Please select a value for %s', (string) $fieldDisplayName === '' ? $key : $fieldDisplayName);
            }
        }

        return [$handle, $value];
    }

    /**
     * @param array $pickers
     *
     * @throws \RuntimeException
     *
     * @return array[]
     */
    protected function getHandlesWithOptions(array $pickers)
    {
        $result = [];
        foreach ($pickers as $key => $value) {
            if (is_array($value)) {
                $handle = $key;
                $options = $value;
            } else {
                $handle = $value;
                $options = [];
            }
            if ($this->getPicker($handle) === null) {
                throw new RuntimeException(t('The destination picker with handle "%s" is not registered.', $handle));
            }
            $result[$handle] = $options;
        }

        return $result;
    }

    /**
     * @param string $key
     * @param array $pickerHandlesWithOptions
     * @param string|null $currentHandler
     *
     * @return string[]
     */
    protected function buildWhichSelector($key, array $pickerHandlesWithOptions, $currentHandler)
    {
        $selectOptions = [];
        foreach ($pickerHandlesWithOptions as $handle => $options) {
            $selectOptions[$handle] = $this->getPicker($handle)->getDisplayName($options);
        }
        $selectedOption = $this->request->isPost() ? $this->request->request->get("{$key}__which") : null;
        if (!is_string($selectOptions) || !isset($selectOptions[$selectedOption])) {
            if ((string) $currentHandler !== '' && isset($pickerHandlesWithOptions[$currentHandler])) {
                $selectedOption = $currentHandler;
            } else {
                $selectedOption = null;
                foreach (array_keys($pickerHandlesWithOptions) as $handle) {
                    if ($this->getPicker($handle) instanceof NoDestinationPicker) {
                        $selectedOption = $handle;
                        break;
                    }
                }
                if ($selectedOption === null) {
                    $selectOptions = array_merge(['' => t('** Please Select')], $selectOptions);
                    $selectedOption = '';
                }
            }
        }

        return [$selectedOption, $this->formService->select("{$key}__which", $selectOptions, $selectedOption, ['required' => 'required'])];
    }
}
