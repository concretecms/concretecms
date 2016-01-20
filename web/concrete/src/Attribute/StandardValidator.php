<?php
namespace Concrete\Core\Attribute;

use Concrete\Core\Application\Application;
use Concrete\Core\Attribute\Category\CategoryInterface;
use Concrete\Core\Entity\Attribute\Key\Key;
use Concrete\Core\Error\Error;
use Concrete\Core\Validation\Response;
use Concrete\Core\Entity\Attribute\Type as TypeEntity;
use Symfony\Component\HttpFoundation\Request;

class StandardValidator implements ValidatorInterface
{

    protected $application;

    public function validateAddKeyRequest(CategoryInterface $category, TypeEntity $type, Request $request)
    {
        return $this->validate($category, $type->getController(), $request);
    }

    public function validateUpdateKeyRequest(CategoryInterface $category, Key $key, Request $request)
    {
        return $this->validate($category, $key->getAttributeType()->getController(), $request, $key);
    }

    public function validateSaveValueRequest(Key $key, Request $request, $fieldName = null)
    {
        $fieldName = $fieldName ? $fieldName : $key->getAttributeKeyDisplayName();
        $controller = $key->getAttributeType()->getController();
        $controller->setAttributeKey($key);
        $response = new Response();
        if (method_exists($controller, 'validateForm')) {
            $error = $controller->validateForm($controller->post());
            if ($error instanceof Error || $error == false) {
                $response->setIsValid(false);
                if ($error == false) {
                    $error = $this->application->make('error');
                    $error->add(t('The field "%s" is required', $fieldName));
                }
                $response->setErrorObject($error);
            }
        }
        return $response;
    }

    public function __construct(Application $application)
    {
        $this->application = $application;
    }

    protected function validate(CategoryInterface $category, Controller $controller, Request $request, Key $key = null)
    {
        /** @var \Concrete\Core\Form\Service\Validation $val */
        $val = $this->application->make('helper/validation/form');
        /** @var \Concrete\Core\Validation\CSRF\Token $valt */
        $valt = $this->application->make('helper/validation/token');
        $val->setData($request->request->all());
        $val->addRequired("akHandle", t("Handle required."));
        $val->addRequired("akName", t('Name required.'));
        $val->addRequired("atID", t('Type required.'));
        $val->test();
        $error = $val->getError();

        if (!$valt->validate('add_or_update_attribute')) {
            $error->add($valt->getErrorMessage());
        }

        /** @var \Concrete\Core\Utility\Service\Validation\Strings $stringValidator */
        $stringValidator = $this->application->make('helper/validation/strings');
        if (!$stringValidator->handle($request->request->get('akHandle'))) {
            $error->add(t('Attribute handles may only contain letters, numbers and underscore "_" characters'));
        }

        $existing = $category->getAttributeKeyByHandle($request->request->get('akHandle'));
        if (is_object($existing)) {
            if (is_object($key)) {
                if ($key->getAttributeKeyID() != $existing->getAttributeKeyID()) {
                    $error->add(t("An attribute with the handle %s already exists.", $request->request->get('akHandle')));
                }
            } else {
                $error->add(t("An attribute with the handle %s already exists.", $request->request->get('akHandle')));
            }
        }

        $controllerResponse = $controller->validateKey($request->request->all());
        if ($controllerResponse instanceof Error) {
            $error->add($controllerResponse);
        }

        $response = new Response();
        if ($error->has()) {
            $response->setIsValid(false);
            $response->setErrorObject($error);
        }
        return $response;
    }

}