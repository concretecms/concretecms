<?php

namespace Concrete\Attribute\Textarea;

use Concrete\Core\Attribute\DefaultController;
use Concrete\Core\Attribute\FontAwesomeIconFormatter;
use Concrete\Core\Editor\LinkAbstractor;
use Concrete\Core\Entity\Attribute\Key\Settings\TextareaSettings;
use Concrete\Core\Entity\Attribute\Value\Value\TextValue;
use Concrete\Core\Utility\Service\Xml;
use SimpleXMLElement;

/**
 * @method \Concrete\Core\Entity\Attribute\Key\Settings\TextareaSettings getAttributeKeySettings()
 */
class Controller extends DefaultController
{
    /**
     * Mode: Plain Text
     *
     * @var string
     */
    public const MODE_TEXT = 'text';

    /**
     * Mode: Rich Text
     *
     * @var string
     */
    public const MODE_RICHTEXT = 'rich_text';

    /**
     * The default mode
     *
     * @var unknown
     */
    public const MODE_DEFAULT = self::MODE_TEXT;

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Controller\AbstractController::$helpers
     */
    public $helpers = ['form'];

    /**
     * @var string|null
     */
    protected $akTextareaDisplayMode;

    protected $akTextareaDisplayModeCustomOptions;

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Attribute\Controller::getIconFormatter()
     */
    public function getIconFormatter()
    {
        return new FontAwesomeIconFormatter('font');
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Attribute\Controller::saveKey()
     */
    public function saveKey($data)
    {
        $type = $this->getAttributeKeySettings();
        $type->setMode($this->getKnownMode($data['akTextareaDisplayMode'] ?? null));

        return $type;
    }

    /**
     * @return string
     */
    public function getValue()
    {
        $this->load();
        $attributeValue = $this->getAttributeValue();
        $value = is_object($attributeValue) ? (string) $attributeValue->getValueObject() : '';
        switch ($this->akTextareaDisplayMode) {
            case static::MODE_RICHTEXT:
                $value = LinkAbstractor::translateFrom($value);
                break;
        }

        return $value;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Attribute\DefaultController::getDisplayValue()
     */
    public function getDisplayValue()
    {
        $value = $this->getValue();
        if ($this->akTextareaDisplayMode === static::MODE_RICHTEXT) {
            return htmLawed($value, [
                'balance' => 0, // off
                'comment' => 3, // allow
                'safe' => 1,
                // default allowed elements for safe option + picture
                'elements' => '* -applet -audio -canvas -embed -iframe -object -script -video +picture'
            ]);
        }

        return nl2br(h($value));
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Attribute\DefaultController::form()
     */
    public function form()
    {
        $this->load();
        $attributeValue = $this->getAttributeValue();
        $value = is_object($attributeValue) ? (string) $attributeValue->getValueObject() : '';
        if ($this->akTextareaDisplayMode === static::MODE_RICHTEXT) {
            $value = LinkAbstractor::translateFromEditMode($value);
        }
        $this->set('value', $value);
    }

    public function composer()
    {
        $this->form();
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Attribute\DefaultController::searchForm()
     */
    public function searchForm($list)
    {
        $list->filterByAttribute($this->attributeKey->getAttributeKeyHandle(), '%' . $this->request('value') . '%', 'like');

        return $list;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Attribute\DefaultController::search()
     */
    public function search()
    {
        $f = $this->app->make('helper/form');
        echo $f->text($this->field('value'), $this->request('value'));
    }

    public function type_form()
    {
        $this->set('akTextareaDisplayModeCustomOptions', []);
        $this->load();
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Attribute\DefaultController::getAttributeValueClass()
     */
    public function getAttributeValueClass()
    {
        return TextValue::class;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Attribute\Controller::exportKey()
     */
    public function exportKey($akey)
    {
        $this->load();
        $akey->addChild('type')->addAttribute('mode', $this->akTextareaDisplayMode);

        return $akey;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Attribute\Controller::importKey()
     */
    public function importKey(SimpleXMLElement $akey)
    {
        $type = $this->getAttributeKeySettings();
        $type->setMode(
            $this->getKnownMode(
                isset($akey->type)
                ? (string) $akey->type['mode']
                : null
            )
        );

        return $type;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Attribute\Controller::exportValue()
     */
    public function exportValue(SimpleXMLElement $akv)
    {
        $this->load();
        $attributeValue = $this->getAttributeValue();
        $value = is_object($attributeValue) ? (string) $attributeValue->getValueObject() : '';
        if ($this->akTextareaDisplayMode === static::MODE_RICHTEXT) {
            $value = LinkAbstractor::export($value);
        }

        return $this->app->make(Xml::class)->createChildElement($akv, 'value', $value);
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Attribute\Controller::importValue()
     */
    public function importValue(SimpleXMLElement $akv)
    {
        $this->load();
        $value = (string) parent::importValue($akv);
        if ($this->akTextareaDisplayMode === static::MODE_RICHTEXT) {
            $value = LinkAbstractor::import($value);
        }

        return $value;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Attribute\DefaultController::createAttributeValue()
     */
    public function createAttributeValue($value)
    {
        $this->load();
        if ($this->akTextareaDisplayMode === static::MODE_RICHTEXT) {
            $value = LinkAbstractor::translateTo($value);
        }

        $av = new TextValue();
        $av->setValue($value);

        return $av;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Attribute\DefaultController::getAttributeKeySettingsClass()
     */
    public function getAttributeKeySettingsClass()
    {
        return TextareaSettings::class;
    }

    /**
     * @return false|null returns false if the attribute key is not set
     */
    protected function load()
    {
        $ak = $this->getAttributeKey();
        if (is_object($ak)) {
            $type = $ak->getAttributeKeySettings();
            $this->akTextareaDisplayMode = $this->getKnownMode($type->getMode());
            $result = null;
        } else {
            $this->akTextareaDisplayMode = static::MODE_DEFAULT;
            $result = false;
        }
        $this->set('akTextareaDisplayMode', $this->akTextareaDisplayMode);

        return $result;
    }

    /**
     * Get all the available modes.
     *
     * @return string[]
     */
    protected function getAllModes(): array
    {
        return [
            static::MODE_TEXT,
            static::MODE_RICHTEXT,
        ];
    }

    /**
     * Check that a wanted mode is valid, otherwise returns the default one.
     *
     * @param string|mixed $wanted
     *
     * @return string
     */
    protected function getKnownMode($wanted): string
    {
        return in_array($wanted, $this->getAllModes(), true) ? $wanted : static::MODE_DEFAULT;
    }
}
