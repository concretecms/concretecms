<?php
namespace Concrete\Core\Page\Type\Validator;

use Concrete\Core\Page\Page;
use Concrete\Core\Page\Type\Type;

/**
 * @since 5.7.4
 */
interface ValidatorInterface
{
    /**
     * @since 5.7.5.2
     */
    public function getPageTypeObject();
    public function setPageTypeObject(Type $type);
    public function validateCreateDraftRequest($template);
    public function validatePublishDraftRequest(Page $page = null);
    /**
     * @since 5.7.5.2
     */
    public function validatePublishLocationRequest(Page $target = null, Page $page = null);
}
