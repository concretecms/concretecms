<?php

namespace Concrete\Core\Install\Command;

use Concrete\Core\Application\ApplicationAwareInterface;
use Concrete\Core\Application\ApplicationAwareTrait;
use Concrete\Core\Error\ErrorList\ErrorList;
use Concrete\Core\Error\UserMessageException;
use Concrete\Core\Install\ConnectionOptionsPreconditionInterface;
use Concrete\Core\Install\ExecutedPrecondition;
use Concrete\Core\Install\Installer;
use Concrete\Core\Install\InstallerOptionsFactory;
use Concrete\Core\Install\PreconditionService;
use Concrete\Core\Validator\String\EmailValidator;

class ValidateEnvironmentCommandHandler implements ApplicationAwareInterface
{

    use ApplicationAwareTrait;

    /**
     * @var InstallerOptionsFactory
     */
    protected $installerOptionsFactory;

    public function __construct(InstallerOptionsFactory $installerOptionsFactory)
    {
        $this->installerOptionsFactory = $installerOptionsFactory;
    }

    /**
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function __invoke(ValidateEnvironmentCommand $command): ValidateEnvironmentCommandResponse
    {
        $environment = $command->getEnvironment();

        // First, let's validate those things that, if they are not right, we cannot proceed, period.
        $error = new ErrorList();
        if (!$environment->getSiteName()) {
            $error->add(t("Please specify your site's name"));
        }
        $validator = new EmailValidator();
        if (!$validator->isValid($environment->getEmail())) {
            $error->add(t("Please specify a valid email address"));
        }
        if (!$environment->getDbDatabase()) {
            $error->add(t("You must specify a valid database name"));
        }
        if (!$environment->getDbServer()) {
            $error->add(t("You must specify a valid database server"));
        }
        if (!$environment->getTimezone()) {
            $error->add(t("You must specify the system time zone"));
        }
        if (!$environment->isAcceptPrivacyPolicy()) {
            $error->add(t("You must agree to the privacy policy"));
        }

        $password = $environment->getPassword();
        $passwordConfirm = $environment->getConfirmPassword();
        $this->app->make('validator/password')->isValid($password, $error);
        if ($password) {
            if ($password != $passwordConfirm) {
                $error->add(t('The two passwords provided do not match.'));
            }
        }

        $options = $this->installerOptionsFactory->createFromEnvironment($environment);
        $installer = $this->app->make(Installer::class);
        $installer->setOptions($options);

        try {
            $connection = $installer->createConnection();
        } catch (UserMessageException $x) {
            $error->add($x->getMessage());
            $connection = null;
        }

        $executedPreconditions = [];
        $preconditions = $this->app->make(PreconditionService::class)->getOptionsPreconditions();
        foreach ($preconditions as $precondition) {
            if ($precondition instanceof ConnectionOptionsPreconditionInterface) {
                if ($connection === null) {
                    continue;
                }
                $precondition->setConnection($connection);
            }
            $precondition->setInstallerOptions($options);
            $check = $precondition->performCheck();
            $executedPreconditions[] = new ExecutedPrecondition($check, $precondition);
        }

        $response = new ValidateEnvironmentCommandResponse();
        $response->setError($error);
        $response->setPreconditions($executedPreconditions);
        return $response;
    }
}
