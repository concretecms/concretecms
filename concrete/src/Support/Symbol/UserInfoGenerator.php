<?php

declare(strict_types=1);

namespace Concrete\Core\Support\Symbol;

use Concrete\Core\Support\Symbol\AttributedItemListGenerator\AttributeKeysProviderInterface;
use Concrete\Core\Support\Symbol\CheckerGenerator\Method;
use Concrete\Core\User\UserInfo;

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * Describe the getUserXxx() methods that UserInfo handles with __call(): one for every user attribute key.
 */
final class UserInfoGenerator
{
    /**
     * @var \Concrete\Core\Support\Symbol\AttributedItemListGenerator\AttributeKeysProviderInterface
     */
    private $attributeKeysProvider;

    /**
     * @var \Concrete\Core\Support\Symbol\CheckerGenerator\Method[]|null
     */
    private $methods;

    public function __construct(AttributeKeysProviderInterface $attributeKeysProvider)
    {
        $this->attributeKeysProvider = $attributeKeysProvider;
    }

    public function getNamespace(): string
    {
        return 'Concrete\Core\User';
    }

    /**
     * Get the methods handled by __call() of UserInfo.
     *
     * @return \Concrete\Core\Support\Symbol\CheckerGenerator\Method[]
     */
    public function getMethods(): array
    {
        if ($this->methods === null) {
            $methods = [];
            foreach ($this->attributeKeysProvider->getKeys('user') as $key) {
                $handle = $key->getHandle();
                // UserInfo::__call() converts getUserFooBar() to the 'foo_bar' handle: only lowercase handles with underscores can be mapped back
                if (!preg_match('/^[a-z][a-z0-9]*(?:_[a-z][a-z0-9]*)*$/', $handle)) {
                    continue;
                }
                $name = 'getUser' . camelcase($handle);
                // __call() is not invoked for the methods that actually exist
                if (method_exists(UserInfo::class, $name)) {
                    continue;
                }
                $method = new Method($name);
                $method
                    ->setReturnType('mixed')
                    ->addDescription(sprintf("Get the value of the '%s' user attribute (%s).", $key->getName() === '' ? $handle : $key->getName(), $handle))
                ;
                $methods[] = $method;
            }
            $this->methods = $methods;
        }

        return $this->methods;
    }

    /**
     * Render the class describing the methods of UserInfo that are handled by __call().
     *
     * @return string[]
     */
    public function renderLines(string $padding = '    '): array
    {
        return CheckerGenerator::renderMethodsClassLines('UserInfo', $this->getMethods(), $padding);
    }
}
