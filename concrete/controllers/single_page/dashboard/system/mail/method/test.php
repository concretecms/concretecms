<?php

declare(strict_types=1);

namespace Concrete\Controller\SinglePage\Dashboard\System\Mail\Method;

use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Error\UserMessageException;
use Concrete\Core\Http\ResponseFactoryInterface;
use Concrete\Core\Mail\Service as MailService;
use Concrete\Core\Page\Controller\DashboardPageController;
use Concrete\Core\Page\Page;
use Concrete\Core\Permission\Checker;
use Concrete\Core\Site\Service as SiteService;
use Concrete\Core\Url\Resolver\Manager\ResolverManagerInterface;
use Concrete\Core\User\User;
use Concrete\Core\User\UserInfoRepository;
use Concrete\Core\Validator\String\EmailValidator;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

defined('C5_EXECUTE') or die('Access Denied.');

class Test extends DashboardPageController
{
    public function view(): ?Response
    {
        $config = $this->app->make(Repository::class);
        $this->set('emailEnabled', (bool) $config->get('concrete.email.enabled'));
        $me = $this->app->make(User::class);
        $myInfo = $me->isRegistered() ? $this->app->make(UserInfoRepository::class)->getByID($me->getUserID()) : null;
        $this->set('myEmailAddress', $myInfo ? $myInfo->getUserEmail() : '');
        $this->set('settingsPageUrl', $this->getPageInfo('/dashboard/system/mail/method')['url'] ?? '');
        $this->set('senderEmailAddress', (string) $config->get('concrete.email.default.address'));
        $this->set('senderEmailName', (string) $config->get('concrete.email.default.name'));
        $this->set('systemEmailAddressesPage', $this->getPageInfo('/dashboard/system/mail/addresses'));

        return null;
    }

    public function send_test_email(): JsonResponse
    {
        if (!$this->token->validate('send_test_email')) {
            throw new UserMessageException($this->token->getErrorMessage());
        }
        $config = $this->app->make(Repository::class);
        if (!$config->get('concrete.email.enabled')) {
            throw new UserMessageException(t('The mail system is disabled.'));
        }
        $emailRecipient = $this->request->request->get('emailRecipient');
        if (!is_string($emailRecipient) || $emailRecipient === '') {
            throw new UserMessageException(t('The recipient address of the test email has not been specified.'));
        }
        if (!$this->app->make(EmailValidator::class)->isValid($emailRecipient)) {
            throw new UserMessageException(t('The recipient address is not valid.'));
        }
        $numEmails = $this->request->request->getInt('numEmails');
        if ($numEmails < 1) {
            throw new UserMessageException(t('Please specify an integer greater than zero for the number of the emails to be sent'));
        }
        $baseSubject = t(/* i18n: %s is the site name */'Test message from %s', $this->app->make(SiteService::class)->getSite()->getSiteName());
        $mail = $this->app->make(MailService::class);
        for ($cycle = 1; $cycle <= $numEmails; $cycle++) {
            $mail->setTesting(true);
            if ($numEmails > 1) {
                $mail->setSubject("{$baseSubject} [{$cycle}/{$numEmails}]");
            } else {
                $mail->setSubject($baseSubject);
            }
            $mail->to($emailRecipient);
            $mail->setBody($this->buildMessageBody());
            try {
                $mail->sendMail();
            } catch (\Throwable $x) {
                throw new UserMessageException(t('The following error was found while trying to send the test email:') . "\n" . $x->getMessage());
            }
        }

        return $this->app->make(ResponseFactoryInterface::class)->json(true);
    }

    private function buildMessageBody(): string
    {
        $config = $this->app->make(Repository::class);
        $lines = [
            t('This is a test message.'),
            '',
            t('Configuration:'),
            '- ' . t('Send mail method: %s', $config->get('concrete.mail.method')),
        ];
        switch ($config->get('concrete.mail.method')) {
            case 'smtp':
                $lines[] = '- ' . t('SMTP Server: %s', $config->get('concrete.mail.methods.smtp.server'));
                $lines[] = '- ' . t('SMTP Port: %s', $config->get('concrete.mail.methods.smtp.port', tc('SMTP Port', 'default')));
                $lines[] = '- ' . t('SMTP Encryption: %s', $this->describeSmtpEncryption((string) $config->get('concrete.mail.methods.smtp.encryption')));
                $lines[] = '- ' . t(/* i18n: %1%s is HELO, %2$s is the domain */'SMTP %1$s Domain: %2$s', 'HELO', $config->get('concrete.mail.methods.smtp.helo_domain'));
                if (!$config->get('concrete.mail.methods.smtp.username')) {
                    $lines[] = '- ' . t('SMTP Authentication: none');
                } else {
                    $lines[] = '- ' . t('SMTP Username: %s', $config->get('concrete.mail.methods.smtp.username'));
                    $lines[] = '- ' . t('SMTP Password: %s', tc('Password', '<hidden>'));
                }
                break;
        }

        return implode("\n", $lines);
    }

    private function describeSmtpEncryption(string $value): string
    {
        $value = strtoupper(trim($value));
        switch ($value) {
            case 'STARTTLS':
                return t('STARTTLS');
            case 'TLS':
            case 'SSL':
                return t('TLS/SSL (implicit)');
            default:
                return t('None / automatic');
        }
    }

    /**
     * @return array{name:string,url:string}|null
     */
    private function getPageInfo(string $path): ?array
    {
        $page = Page::getByPath($path);
        if (!$page || $page->isError()) {
            return null;
        }

        return [
            'name' => t($page->getCollectionName()),
            'url' => (new Checker($page))->canViewPage() ? (string) $this->app->make(ResolverManagerInterface::class)->resolve([$page]) : '',
        ];
    }
}
