<?php

namespace Concrete\Core\Page\Type\Composer\Control\CorePageProperty;

use Core;
use Concrete\Core\Page\Page;
use Concrete\Core\Attribute\FontAwesomeIconFormatter;

class DescriptionCorePageProperty extends CorePageProperty
{
    public function __construct()
    {
        $this->setCorePagePropertyHandle('description');
        $this->setPageTypeComposerControlIconFormatter(new FontAwesomeIconFormatter('font'));
    }

    public function getPageTypeComposerControlName()
    {
        return tc('PageTypeComposerControlName', 'Description');
    }

    public function publishToPage(Page $c, $data, $controls)
    {
        if (!is_array($data)) {
            $data = [];
        }
        $data += [
            'description' => null,
        ];
        $this->addPageTypeComposerControlRequestValue('cDescription', $data['description']);
        parent::publishToPage($c, $data, $controls);
    }

    public function validate()
    {
        $e = Core::make('helper/validation/error');
        $val = $this->getRequestValue();
        if ($val['description']) {
            $description = $val['description'];
        } else {
            $description = $this->getPageTypeComposerControlDraftValue();
        }

        /** @var \Concrete\Core\Utility\Service\Validation\Strings $stringValidator */
        $stringValidator = Core::make('helper/validation/strings');
        if (!$stringValidator->notempty($description)) {
            $control = $this->getPageTypeComposerFormLayoutSetControlObject();
            $e->add(t('You haven\'t chosen a valid %s', $control->getPageTypeComposerControlDisplayLabel()));

            return $e;
        }
    }

    public function getRequestValue($args = false)
    {
        $data = parent::getRequestValue($args);
        $data['description'] = Core::make('helper/security')->sanitizeString($data['description']);

        return $data;
    }

    public function getPageTypeComposerControlDraftValue()
    {
        if (is_object($this->page)) {
            $c = $this->page;

            return $c->getCollectionDescription();
        }
    }
}
