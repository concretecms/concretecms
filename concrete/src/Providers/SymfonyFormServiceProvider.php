<?php

namespace Concrete\Core\Providers;

use Concrete\Core\Foundation\Service\Provider;
use Symfony\Component\Form\Extension\HttpFoundation\HttpFoundationExtension;
use Symfony\Component\Form\Extension\Validator\ValidatorExtension;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\Forms;
use Symfony\Component\Security\Csrf\CsrfTokenManager;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class SymfonyFormServiceProvider extends Provider
{
    public function register(): void
    {
        $validatorBuilder = Validation::createValidatorBuilder()
            ->enableAnnotationMapping()
        ;

        $this->app->singleton(ValidatorInterface::class, function () use ($validatorBuilder) {
            return $validatorBuilder->getValidator();
        });
        $this->app->bind(FormFactoryInterface::class, FormFactory::class);
        $this->app->singleton(FormFactory::class, function () {
            /**
             * @var ValidatorInterface $validatorInterface
             */
            $validatorInterface = $this->app->make(ValidatorInterface::class);

            return Forms::createFormFactoryBuilder()
                ->addExtension(new HttpFoundationExtension())
                ->addExtension(new ValidatorExtension(
                    $validatorInterface
                ))
                ->getFormFactory()
            ;
        });

        $this->app->singleton(CsrfTokenManager::class, function () {
            return new CsrfTokenManager();
        });
    }
}
