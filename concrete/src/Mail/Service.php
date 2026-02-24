<?php

namespace Concrete\Core\Mail;

use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Entity\File\File;
use Concrete\Core\Logging\Channels;
use Concrete\Core\Logging\GroupLogger;
use Concrete\Core\Logging\LoggerAwareInterface;use Concrete\Core\Logging\LoggerAwareTrait;use Monolog\Logger;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\Header\HeaderInterface;
use Symfony\Component\Mime\Part\DataPart;
use Throwable;

/**
 * @template T of Email
 */
class Service implements LoggerAwareInterface
{
    use LoggerAwareTrait;
    /**
     * The config repository.
     *
     * @var Repository
     */
    protected $config;

    /**
     * The transport to be used to delivery the messages.
     *
     * @var MailerInterface
     */
    protected $mailer;

    /**
     * The email class to be used to create the email messages.
     *
     * @var class-string<Email>
     */
    protected $emailClass;

    /**
     * @var Email
     */
    protected $email;

    /**
     * A dictionary with the parameters to be sent to the template.
     *
     * @var array
     */
    protected $data = [];

    /**
     * The last leaded message template file.
     *
     * @var string
     */
    protected $template;

    /**
     * Are we testing this service?
     *
     * @var bool
     */
    protected $testing;

    /**
     * Should we throw an exception if the delivery fails?
     *
     * @var false
     */
    protected $throwOnFailure;

    const LOG_MAILS_NONE = '';
    const LOG_MAILS_ONLY_METADATA = 'metadata';
    const LOG_MAILS_METADATA_AND_BODY = 'metadata_and_body';

    /**
     * Initialize the instance.
     *
     * @param Repository $config the config repository instance
     * @param MailerInterface $mailer the mailer to be used to delivery the messages
     * @param class-string<T> $emailClass the email class to be used to create the email messages
     */
    public function __construct(Repository $config, MailerInterface $mailer, string $emailClass = Email::class)
    {
        $this->config = $config;
        $this->mailer = $mailer;
        $this->emailClass = $emailClass;
        if ($this->emailClass !== Email::class && !is_subclass_of($this->emailClass, Email::class)) {
            throw new \InvalidArgumentException(t('The email class must be an instance of %s', Email::class));
        }
        $this->reset();
    }

    public function __destruct()
    {
        try {
            $this->mailer = null;
        } catch (Throwable $x) {
            // Ignore error
        }
    }

    /**
     * Clean up the instance of this object (reset the class scope variables).
     */
    public function reset()
    {
        $this->data = [];
        $this->template = '';
        $this->testing = false;
        $this->throwOnFailure = false;
        $this->email = new $this->emailClass();
    }

    /**
     * @return T
     */
    public function getEmail(): Email
    {
        return $this->email;
    }

    /**
     * Adds a parameter for the mail template.
     *
     * @param string $key the name of the parameter
     * @param mixed $val the value of the parameter
     */
    public function addParameter($key, $val)
    {
        $this->data[$key] = $val;
    }

    /**
     * Add a File entity as an attachment of the message.
     *
     * @param File $file The file to attach to the message
     */
    public function addAttachment(File $file)
    {
        $this->addAttachmentWithHeaders($file, []);
    }

    /**
     * Add a File entity as an attachment of the message, specifying the headers of the mail MIME part.
     *
     * @param File $file The file to attach to the message
     * @param array $headers Additional headers fo the MIME part. Valid values are:
     * - filename: The name to give to the attachment (it will be used as the filename part of the Content-Disposition header) [default: the filename of the File instance]
     * - mimetype: the main value of the Content-Type header [default: the content type of the file]
     * - disposition: the main value of the Content-Disposition header attachment, inline, or form-data [default: attachment]
     * - encoding: the value of the Content-Transfer-Encoding header [default: base64]
     * - charset: the charset value of the Content-Type header
     * - description: the value of the Content-Description header
     * - location: the value of the Content-Location header
     * - language: the value of the Content-Language header
     */
    public function addAttachmentWithHeaders(File $file, array $headers)
    {
        $fileVersion = $file->getVersion();
        $resource = $fileVersion->getFileResource();

        if (array_key_exists('filename', $headers)) {
            $filename = $headers['filename'];
            unset($headers['filename']);
        } else {
            $filename = $fileVersion->getFilename();
        }
        if (!array_key_exists('mimetype', $headers)) {
            $headers['mimetype'] = $resource->getMimetype();
        }
        $this->addRawAttachmentWithHeaders(
            $resource->read(),
            $filename,
            $headers,
        );
    }

    /**
     * Add a mail attachment by specifying its raw binary data.
     *
     * @param string|resource $content The binary data of or resource pointing to the attachment
     * @param string $filename The name to give to the attachment (it will be used as the filename part of the Content-Disposition header)
     * @param string $mimetype The MIME type of the attachment (it will be the main value of the Content-Type header)
     */
    public function addRawAttachment($content, $filename, $mimetype = 'application/octet-stream')
    {
        $this->addRawAttachmentWithHeaders($content, $filename, ['mimetype' => $mimetype]);
    }

    /**
     * Add a mail attachment by specifying its raw binary data, specifying the headers of the mail MIME part.
     *
     * @param string|resource $content The binary data of or resource pointing to the attachment
     * @param string $filename The name to give to the attachment (it will be used as the filename part of the Content-Disposition header)
     * @param array $headers Additional headers of the MIME part. Valid values are:
     * - mimetype: the main value of the Content-Type header [default: application/octet-stream]
     * - disposition: the main value of the Content-Disposition header attachment, inline, or form-data [default: attachment]
     * - encoding: the value of the Content-Transfer-Encoding header [default: base64]
     * - charset: the charset value of the Content-Type header
     * - description: the value of the Content-Description header
     * - location: the value of the Content-Location header
     * - language: the value of the Content-Language header
     */
    public function addRawAttachmentWithHeaders($content, $filename, array $headers = [])
    {
        if (
            (array_key_exists('boundary', $headers) && $headers['boundary'])
            || (array_key_exists('id', $headers) && $headers['id'])
        ) {
            $message = <<<ERR
                The "boundary" and "id" headers are no longer supported for attachments. Instead custom boundary settings
                can be configured using symfony mime parts directly.
                
                See https://github.com/concretecms/concretecms/issues/12814
                ERR;
            if ($this->logger) {
                $this->logger->critical($message);
            }
            throw new \InvalidArgumentException($message);
        }

        $part = new DataPart(
            $content,
            $filename,
            $headers['mimetype'] ?? 'application/octet-stream',
            $headers['encoding'] ?? 'base64',
        );
        $partHeaders = $part->getHeaders();

        $disposition = $headers['disposition'] ?? 'attachment';
        if ($disposition) {
            $part = $part->setDisposition($disposition);
        }

        $charset = $headers['charset'] ?? null;
        if ($charset) {
            // We have to manually set the content-type header to add the charset because it defaults to null otherwise.
            // This works because the prepareHeaders() method modifies the existing header rather than adding a new one.
            $partHeaders->addParameterizedHeader(
                'Content-Type',
                $part->getMediaType() . '/' . $part->getMediaSubtype(),
                ['charset' => $charset]
            );
        }

        //$boundary = $headers['boundary'] ?? null;
        //if ($boundary) {
            // Setting the boundary isn't supported.
        //}

        //$id = $headers['id'] ?? null;
        //if ($id) {
            /* @see DataPart::getContentID() */
            // Content IDs aren't configurable, they are generated automatically when the part is prepared by the email.
        //}

        $description = $headers['description'] ?? null;
        if ($description) {
            $partHeaders->addTextHeader('Content-Description', $description);
        }

        $location = $headers['location'] ?? null;
        if ($location) {
            $partHeaders->addTextHeader('Content-Location', $location);
        }

        $language = $headers['language'] ?? null;
        if ($language) {
            $partHeaders->addTextHeader('Content-Language', $language);
        }

        $this->email->attachPart($part);
    }

    /**
     * Load an email template from the /mail/ directory.
     *
     * @param string $template The template to load
     * @param string|null $pkgHandle The handle of the package associated to the template
     */
    public function load($template, $pkgHandle = null)
    {
        extract($this->data);

        ob_start();
        if (file_exists(DIR_FILES_EMAIL_TEMPLATES . "/{$template}.php")) {
            include DIR_FILES_EMAIL_TEMPLATES . "/{$template}.php";
        } else {
            if ($pkgHandle != null) {
                if (is_dir(DIR_PACKAGES . '/' . $pkgHandle)) {
                    include DIR_PACKAGES . '/' . $pkgHandle . '/' . DIRNAME_MAIL_TEMPLATES . "/{$template}.php";
                } else {
                    include DIR_PACKAGES_CORE . '/' . $pkgHandle . '/' . DIRNAME_MAIL_TEMPLATES . "/{$template}.php";
                }
            } else {
                include DIR_FILES_EMAIL_TEMPLATES_CORE . "/{$template}.php";
            }
        }
        ob_end_clean();

        $from = $from ?? null;
        $subject = $subject ?? null;

        if (is_array($from) && isset($from[0])) {
            $this->from(...$from);
        }
        $this->template = $template;

        if ($subject) {
            $this->setSubject($subject);
        }

        $this->setBody($body ?? null);
        $this->setBodyHTML($bodyHtml ?? null);
    }

    /**
     * Manually set the plain text body of a mail message (typically the body is set in the template + load method).
     *
     * @param resource|string|null|false $body Set the text body (false to not use a plain text body)
     */
    public function setBody($body)
    {
        $this->email = $this->email->text($body === false ? null : $body);
    }

    /**
     * Manually set the message's subject (typically the body is set in the template + load method).
     *
     * @param string $subject
     */
    public function setSubject($subject)
    {
        $this->email = $this->email->subject($subject);
    }

    /**
     * Get the message subject.
     *
     * @return string
     */
    public function getSubject()
    {
        return $this->email->getSubject() ?? '';
    }

    /**
     * Get the plain text body.
     *
     * @return resource|string|false
     */
    public function getBody()
    {
        return $this->email->getTextBody() ?? false;
    }

    /**
     * Get the html body.
     *
     * @return resource|string|false
     */
    public function getBodyHTML()
    {
        return $this->email->getHtmlBody() ?? false;
    }

    /**
     * Manually set the HTML body of a mail message (typically the body is set in the template + load method).
     *
     * @param string|resource|null|false $html Set the html body (false to not use an HTML body)
     */
    public function setBodyHTML($html)
    {
        $this->email->html($html, APP_CHARSET);
    }

    /**
     * @param Importer\MailImporter $importer
     * @param array $data
     */
    public function enableMailResponseProcessing($importer, $data)
    {
        /**
         * @TODO make sure this works
         */
        foreach ($this->email->getTo() as $address) {
            $importer->setupValidation($address->getAddress(), $data);
        }
        $this->from($importer->getMailImporterEmail());
        $this->body = $importer->setupBody(($this->getBody() === false) ? '' : $this->getBody());
    }

    /**
     * Set the from address on the message.
     *
     * @param string $email
     * @param string|null $name
     */
    public function from($email, $name = null)
    {
        $this->email = $this->email->from(new Address($email, $name ?? ''));
    }

    protected function parseEmailList(string $emailList, ?string $name = null): array
    {
        $emails = [];
        $emailArray = explode(',', $emailList);
        foreach ($emailArray as $email) {
            $emails[] = new Address($email, $name ?? '');
        }
        return $emails;
    }

    /**
     * Add one or more "To" recipients to the message.
     *
     * @param string $email (separate multiple email addresses with commas)
     * @param string|null $name The name to associate to the email address
     */
    public function to($email, $name = null)
    {
        $this->email = $this->email->addTo(...$this->parseEmailList($email, $name));
    }

    public function resetTo(): void
    {
        $this->email = $this->email->to();
    }

    /**
     * Add one or more "CC" recipients to the message.
     *
     * @param string $email (separate multiple email addresses with commas)
     * @param string|null $name The name to associate to the email address
     */
    public function cc($email, $name = null)
    {
        $this->email = $this->email->addCc(...$this->parseEmailList($email, $name));
    }

    public function resetCc(): void
    {
        $this->email = $this->email->cc();
    }

    /**
     * Add one or more "BCC" recipients to the message.
     *
     * @param string $email (separate multiple email addresses with commas)
     * @param string|null $name The name to associate to the email address
     */
    public function bcc($email, $name = null)
    {
        $this->email = $this->email->addBcc(...$this->parseEmailList($email, $name));
    }

    public function resetBcc(): void
    {
        $this->email = $this->email->bcc();
    }

    /**
     * Sets the Reply-To addresses of the message.
     *
     * @param string $email (separate multiple email addresses with commas)
     * @param string|null $name The name to associate to the email address
     */
    public function replyto($email, $name = null)
    {
        $this->email = $this->email->addReplyTo(...$this->parseEmailList($email, $name));
    }

    public function resetReplyto(): void
    {
        $this->email = $this->email->replyTo();
    }

    /**
     * Set the testing state (if true the email logging never occurs and sending errors will throw an exception).
     *
     * @param bool $testing
     */
    public function setTesting($testing)
    {
        $this->testing = (bool) $testing;
    }

    /**
     * Retrieve the testing state.
     *
     * @return bool
     */
    public function getTesting()
    {
        return $this->testing;
    }

    /**
     * Should an exception be thrown if the delivery fails (if false, the sendMail() method will simply return false on failure).
     *
     * @param bool $throwOnFailure
     *
     * @return $this
     */
    public function setIsThrowOnFailure($throwOnFailure)
    {
        $this->throwOnFailure = (bool) $throwOnFailure;

        return $this;
    }

    /**
     * Should an exception be thrown if the delivery fails (if false, the sendMail() method will simply return false on failure).
     *
     * @return bool
     */
    public function isThrowOnFailure()
    {
        return $this->throwOnFailure;
    }

    /**
     * Set additional message headers.
     *
     * @param array<string|HeaderInterface> $headers
     */
    public function setAdditionalHeaders($headers)
    {
        $emailHeaders = $this->email->getHeaders();
        foreach ($headers as $header) {
            if (is_string($header)) {
                $split = array_map('trim', explode(':', $header, 2));
                $emailHeaders->addTextHeader($split[0] ?? '', $split[1] ?? '');
            } else {
                $emailHeaders->add($header);
            }
        }
    }

    private function isMetaDataLoggingEnabled()
    {
        $logging = $this->config->get('concrete.log.emails');

        if ((is_numeric($logging) && $logging == 1) || $logging === true) {
            // legacy support
            return true;
        } else {
            return $logging === self::LOG_MAILS_ONLY_METADATA || $logging === self::LOG_MAILS_METADATA_AND_BODY;
        }
    }

    private function isBodyLoggingEnabled()
    {
        $logging = $this->config->get('concrete.log.emails');

        if ((is_numeric($logging) && $logging == 1) || $logging === true) {
            // legacy support
            return true;
        } else {
            return $logging === self::LOG_MAILS_METADATA_AND_BODY;
        }
    }

    /**
     * Sends the email.
     *
     * @param bool $resetData Whether or not to reset the service to its default when this method is done
     *
     * @return bool Returns true upon success, or false if the delivery fails and if the service is not in "testing" state and throwOnFailure is false
     * @throws Throwable Throws an exception if the delivery fails and if the service is in "testing" state or throwOnFailure is true
     *
     */
    public function sendMail($resetData = true)
    {
        $config = $this->config;

        if (count($this->email->getFrom()) === 0) {
            $this->email = $this->email->from(
                new Address(
                    $config->get('concrete.email.default.address'),
                    $config->get('concrete.email.default.name'),
                ),
            );
        }

        $headers = $this->email->getHeaders();
        if (!$headers->has('Message-ID')) {
            $headers->addIdHeader('Message-ID', $this->email->generateMessageId());
        }

        $sendError = null;
        if ($config->get('concrete.email.enabled')) {
            try {
                $this->mailer->send($this->email);
            } catch (Throwable $x) {
                $sendError = $x;
            }
        }
        if ($sendError !== null) {
            if ($this->getTesting()) {
                throw $sendError;
            }
            $l = new GroupLogger(Channels::CHANNEL_EXCEPTIONS, Logger::CRITICAL);
            $l->write(t('Mail Exception Occurred. Unable to send mail: ') . $sendError->getMessage());
            $l->write($sendError->getTraceAsString());
            $l->close();
        }

        if ($this->isMetaDataLoggingEnabled() && !$this->getTesting()) {
            $l = new GroupLogger(Channels::CHANNEL_EMAIL, Logger::NOTICE);

            if ($config->get('concrete.email.enabled')) {
                if ($sendError === null) {
                    $l->write('**' . t('EMAILS ARE ENABLED. THIS EMAIL HAS BEEN SENT') . '**');
                } else {
                    $l->write('**' . t('EMAILS ARE ENABLED. THIS EMAIL HAS NOT BEEN SENT') . '**');
                }
            } else {
                $l->write('**' . t('EMAILS ARE DISABLED. THIS EMAIL WAS LOGGED BUT NOT SENT') . '**');
            }

            $l->write(t('Template Used') . ': ' . $this->template);
            if ($this->isBodyLoggingEnabled()) {
                /** @var T $email */
                $email = new $this->emailClass();
                $email->setHeaders(clone $this->email->getHeaders());

                if ($this->email->getTextBody()) {
                    $email->text($this->email->getTextBody(), $this->email->getTextCharset());
                }
                if ($this->email->getHtmlBody()) {
                    $email->html($this->email->getHtmlBody(), $this->email->getHtmlCharset());
                }

                // append the attached file names to the mail log
                $mailDetails = $email->toString();
                foreach ($this->email->getAttachments() as $attachedFile) {
                    $mailDetails .= "\n" . t("[Attached File: %s]", $attachedFile->asDebugString());
                }

                $l->write(t('Mail Details: %s', htmlspecialchars("\n{$mailDetails}")));
            }

            $l->close();
        }

        if ($sendError !== null && $this->isThrowOnFailure()) {
            if ($resetData) {
                $this->reset();
            }
            throw $sendError;
        }

        // clear data if applicable
        if ($resetData) {
            $this->reset();
        }

        return $sendError === null;
    }

    public function getLoggerChannel(): string
    {
        return Channels::CHANNEL_EMAIL;
    }
}
