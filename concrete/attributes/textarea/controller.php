<?php

namespace Concrete\Attribute\Textarea;

use Concrete\Core\Attribute\DefaultController;
use Concrete\Core\Attribute\FontAwesomeIconFormatter;
use Concrete\Core\Editor\LinkAbstractor;
use Concrete\Core\Entity\Attribute\Key\Settings\TextareaSettings;
use Concrete\Core\Entity\Attribute\Value\Value\TextValue;
use Core;

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
        $data += [
            'akTextareaDisplayMode' => null,
        ];
        $akTextareaDisplayMode = $data['akTextareaDisplayMode'];
        if (!$akTextareaDisplayMode) {
            $akTextareaDisplayMode = static::MODE_DEFAULT;
        }

        $type->setMode($akTextareaDisplayMode);

        return $type;
    }

    /**
     * @return string|null
     */
    public function getValue()
    {
        $this->load();
        if ($this->akTextareaDisplayMode == static::MODE_TEXT) {
            $value = $this->getAttributeValue()->getValueObject();

            return (string) $value;
        }

        $value = null;
        if (is_object($this->attributeValue)) {
            $value = $this->getAttributeValue()->getValueObject();

            if ($value) {
                $this->load();
                $value = (string) $value;
                if ($this->akTextareaDisplayMode == static::MODE_RICHTEXT) {
                    $value = LinkAbstractor::translateFrom($value);
                }
            }
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
        if ($this->akTextareaDisplayMode == static::MODE_RICHTEXT) {
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
        $value = null;
        if (is_object($this->attributeValue)) {
            $value = $this->getAttributeValue()->getValueObject();

            if ($value) {
                if ($this->akTextareaDisplayMode == static::MODE_RICHTEXT) {
                    $value = LinkAbstractor::translateFromEditMode($value);
                }
            }
        }
        $this->set('akTextareaDisplayMode', $this->akTextareaDisplayMode);
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
        $f = Core::make('helper/form');
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
     * @see \Concrete\Core\Attribute\DefaultController::createAttributeValue()
     */
    public function createAttributeValue($value)
    {
        $this->load();
        if ($this->akTextareaDisplayMode == static::MODE_RICHTEXT) {
            $value = LinkAbstractor::translateTo($value);
        }

        $av = new TextValue();
        $av->setValue($value);

        return $av;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Attribute\Controller::importKey()
     */
    public function importKey(\SimpleXMLElement $akey)
    {
        $type = $this->getAttributeKeySettings();
        if (isset($akey->type)) {
            $data['akTextareaDisplayMode'] = $akey->type['mode'];
            $type->setMode((string) $akey->type['mode']);
        }

        return $type;
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
        if (!is_object($ak)) {
            return false;
        }

        $type = $ak->getAttributeKeySettings();
        $this->akTextareaDisplayMode = $type->getMode();
        $this->set('akTextareaDisplayMode', $type->getMode());
    }
}
