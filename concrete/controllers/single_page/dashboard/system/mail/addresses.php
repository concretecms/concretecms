<?php

declare(strict_types=1);

namespace Concrete\Controller\SinglePage\Dashboard\System\Mail;

use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Mail\SenderConfiguration;
use Concrete\Core\Mail\SenderConfiguration\Entry;
use Concrete\Core\Page\Controller\DashboardPageController;
use Concrete\Core\Validator\String\EmailValidator;
use Symfony\Component\HttpFoundation\Response;

class Addresses extends DashboardPageController
{
    public function view(): ?Response
    {
        $this->set('config', $this->app->make(Repository::class));
        $this->set('senderConfiguration', $this->app->make(SenderConfiguration::class));

        return null;
    }

    public function save(): ?Response
    {
        if (!$this->token->validate('addresses')) {
            $this->error->add(t('Invalid CSRF token. Please refresh and try again.'));

            return $this->view();
        }
        $senderConfiguration = $this->app->make(SenderConfiguration::class);
        $emailValidator = $this->app->make(EmailValidator::class, ['strict' => true]);
        $keyValues = [];
        foreach ($senderConfiguration->getEntries() as $entry) {
            $keyValues += $this->processEntry($entry, $emailValidator);
        }
        if ($this->error->has()) {
            return $this->view();
        }
        $config = $this->app->make(Repository::class);
        foreach ($keyValues as $key => $value) {
            $config->save($key, $value);
        }
        $this->flash('message', t('Successfully saved system email addresses.'));

        return $this->buildRedirect('/dashboard/system/mail/addresses');
    }

    protected function processEntry(Entry $entry, EmailValidator $emailValidator): array
    {
        $post = $this->request->request;
        $result = [];
        $keyPrefix = $entry->getPackageHandle();
        if ($keyPrefix !== '') {
            $keyPrefix = "{$keyPrefix}::";
        }
        $key = $entry->getNameKey();
        if ($key !== '') {
            $value = trim((string) $post->get(str_replace('.', '__', 'name@' . $keyPrefix . $key)));
            if ($value === '') {
                if (($entry->getRequired() & $entry::REQUIRED_EMAIL_AND_NAME) === $entry::REQUIRED_EMAIL_AND_NAME) {
                    $this->error->add(t('Please specify the sender name in the "%s" section.', $entry->getName()));
                }
            }
            $result[$keyPrefix . $key] = $value;
        }
        $key = $entry->getEmailKey();
        $value = trim((string) $post->get(str_replace('.', '__', 'address@' . $keyPrefix . $key)));
        if ($value === '') {
            if (($entry->getRequired() & $entry::REQUIRED_EMAIL) === $entry::REQUIRED_EMAIL) {
                $this->error->add(t('Please specify the email address in the "%s" section.', $entry->getName()));
            }
        } elseif (!$emailValidator->isValid($value)) {
            $this->error->add(t('The email address specified in the "%s" section is invalid.', $entry->getName()));
        }
        $result[$keyPrefix . $key] = $value;

        return $result;
    }
}
