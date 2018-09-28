<?php
namespace Concrete\Core\Page\Type\Validator;

use Concrete\Core\Page\Page;
use Concrete\Core\Page\Type\Type;

interface ValidatorInterface
{
    public function getPageTypeObject();
    public function setPageTypeObject(Type $type);
    public function validateCreateDraftRequest($template);
    public function validatePublishDraftRequest(Page $page = null);
    public function validatePublishLocationRequest(Page $target = null, Page $page = null);
}
