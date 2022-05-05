<?php

namespace Concrete\Core\Attribute\Key\Component\KeySelector;

use Concrete\Core\Attribute\Category\CategoryInterface;
use Concrete\Core\Attribute\Command\ClearAttributesCommand;
use Concrete\Core\Attribute\Command\SaveAttributesCommand;
use Concrete\Core\Attribute\ObjectInterface;
use Concrete\Core\Validation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Adds required methods to actual REST controllers.
 *
 * @property \Concrete\Core\Application\Application $app
 * @property \Concrete\Core\Http\Request $request
 */
trait ControllerTrait
{
    abstract public function getCategory(): CategoryInterface;

    /**
     * @return ObjectInterface[]
     */
    abstract public function getObjects(): array;

    abstract public function canEditAttributeKey(int $akID): bool;

    public function saveAttributes()
    {
        // Let's retrieve a list of attribute keys that we're trying to set.
        $selectedAttributes = (array) $this->request->request->get('selectedKeys', []);

        // In case of non modified Multiple Valued attribute in bulk edit
        $ignoredAttributes = (array) $this->request->request->get('ignoredKeys', []);

        foreach ($this->getObjects() as $object) {
            // Now, let's divide attributes into piles of those we need to save, and those we need to clear
            $attributesToClear = [];
            $attributesToSave = [];

            $values = $this->category->getAttributeValues($object);
            foreach ($values as $value) {
                $attributeKey = $value->getAttributeKey();
                if ($attributeKey) {
                    if (!in_array($attributeKey->getAttributeKeyID(), $selectedAttributes) &&
                        !in_array($attributeKey->getAttributeKeyID(), $ignoredAttributes) &&
                        $this->canEditAttributeKey($attributeKey->getAttributeKeyID())) {
                        // This is an attribute we have currently set on the object, but it's not
                        // in the request, and it is something we're allowed to edit, so that means it needs
                        // to be cleared
                        $attributesToClear[] = $attributeKey;
                    }
                }
            }

            foreach ($selectedAttributes as $akID) {
                if ($this->canEditAttributeKey($akID)) {
                    $ak = $this->category->getAttributeKeyByID($akID);
                    if ($ak) {
                        $controller = $ak->getController();
                        $validator = $controller->getValidator();
                        /**
                         * @var $response Response
                         */
                        $response = $validator->validateSaveValueRequest(
                            $controller,
                            $this->request
                        );
                        if ($response->isValid()) {
                            $attributesToSave[] = $ak;
                        } else {
                            return $response->getErrorObject();
                        }
                    }
                }
            }

            $this->app->executeCommand(new ClearAttributesCommand($attributesToClear, $object));
            $this->app->executeCommand(new SaveAttributesCommand($attributesToSave, $object));
            return null;
        }
    }

    public function getAttribute()
    {
        $key = $this->category->getByID($this->request->request->get('akID'));
        $keySerializer = new KeySerializer($key);

        return new JsonResponse($keySerializer);
    }
}
