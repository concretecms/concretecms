<?php

namespace Concrete\Core\Page\Type\Composer\Control\CorePageProperty;

use Concrete\Core\Attribute\FontAwesomeIconFormatter;
use Concrete\Core\Page\Page;
use Concrete\Core\Support\Facade\Application;

class VersionCommentCorePageProperty extends CorePageProperty
{
    public function __construct()
    {
        $this->setCorePagePropertyHandle('version_comment');
        $this->setPageTypeComposerControlIconFormatter(new FontAwesomeIconFormatter('comment-alt'));
    }

    public function getPageTypeComposerControlName()
    {
        return tc('PageTypeComposerControlName', 'Version Comment');
    }

    public function publishToPage(Page $c, $data, $controls)
    {
        parent::publishToPage($c, $data, $controls);
        if (isset($data['version_comment'])) {
            $c->getVersionObject()->setComment($data['version_comment']);
        }
    }

    public function validate()
    {
        $app = Application::getFacadeApplication();
        $e = $app->make('helper/validation/error');
        $val = $this->getRequestValue();
        $version_comment = $val['version_comment'] ?? '';

        /** @var \Concrete\Core\Utility\Service\Validation\Strings $stringValidator */
        $stringValidator = $app->make('helper/validation/strings');
        if (!$stringValidator->notempty($version_comment)) {
            $control = $this->getPageTypeComposerFormLayoutSetControlObject();
            $e->add(t('Please specify the version comments'));

            return $e;
        }
    }

    public function getRequestValue($args = false)
    {
        $app = Application::getFacadeApplication();
        $data = parent::getRequestValue($args);
        $data['version_comment'] = $app->make('helper/security')->sanitizeString($data['version_comment']);

        return $data;
    }

    public function getPageTypeComposerControlDraftValue()
    {
        if (is_object($this->page)) {
            $cv = $this->page->getVersionObject();
            if (!$cv->isApproved()) {
                return $cv->getVersionComments();
            }
        }
    }
}
