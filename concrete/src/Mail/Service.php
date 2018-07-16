<?php

namespace Concrete\Core\Mail;

use Concrete\Core\Application\Application;
use Concrete\Core\Entity\File\File;
use Concrete\Core\Logging\GroupLogger;
use Concrete\Core\Support\Facade\Application as ApplicationFacade;
use Exception;
use Monolog\Logger;
use Throwable;
use Zend\Mail\Header\MessageId as MessageIdHeader;
use Zend\Mail\Message;
use Zend\Mail\Transport\TransportInterface;
use Zend\Mime\Message as MimeMessage;
use Zend\Mime\Mime;
use Zend\Mime\Part as MimePart;

class Service
{
    /**
     * The application instance.
     *
     * @var Application
     */
    protected $app;

    /**
     * The transport to be used to delivery the messages.
     *
     * @var TransportInterface
     */
    protected $transport;

    /**
     * Additional email message headers.
     *
     * @var array
     */
    protected $headers;

    /**
     * List of "To" recipients (every item is an array with at key 0 the email address and at key 1 an optional name).
     *
     * @var array[array]
     */
    protected $to;

    /**
     * List of "Reply-To" recipients (every item is an array with at key 0 the email address and at key 1 an optional name).
     *
     * @var array[array]
     */
    protected $replyto;

    /**
     * List of "CC" recipients (every item is an array with at key 0 the email address and at key 1 an optional name).
     *
     * @var array[array]
     */
    protected $cc;

    /**
     * List of "CC" recipients (every item is an array with at key 0 the email address and at key 1 an optional name).
     *
     * @var array[array]
     */
    protected $bcc;

    /**
     * The sender email address and its name.
     *
     * @var string[]
     */
    protected $from;

    /**
     * A dictionary with the parameters to be sent to the template.
     *
     * @var array
     */
    protected $data;

    /**
     * The message subject.
     *
     * @var string
     */
    protected $subject;

    /**
     * The message attachments.
     *
     * @var MimePart[]
     */
    protected $attachments;

    /**
     * The last leaded message template file.
     *
     * @var string
     */
    protected $template;

    /**
     * The plain text body.
     *
     * @var string|false
     */
    protected $body;

    /**
     * The HTML body.
     *
     * @var string|false
     */
    protected $bodyHTML;

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

    /**
     * Initialize the instance.
     *
     * @param Application $app the application instance
     * @param TransportInterface $transport the transport to be used to delivery the messages
     */
    public function __construct(Application $app, TransportInterface $transport)
    {
        $this->app = $app;
        $this->transport = $transport;
        $this->reset();
    }

    /**
     * Clean up the instance of this object (reset the class scope variables).
     */
    public function reset()
    {
        $this->headers = [];
        $this->to = [];
        $this->replyto = [];
        $this->cc = [];
        $this->bcc = [];
        $this->from = [];
        $this->data = [];
        $this->subject = '';
        $this->attachments = [];
        $this->template = '';
        $this->body = false;
        $this->bodyHTML = false;
        $this->testing = false;
        $this->throwOnFailure = false;
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
     * - disposition: the main value of the Content-Disposition header [default: attachment]
     * - encoding: the value of the Content-Transfer-Encoding header [default: base64]
     * - charset: the charset value of the Content-Type header
     * - boundary: the boundary value of the Content-Type header
     * - id: the value of the Content-ID header (without angular brackets)
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
            $headers
        );
    }

    /**
     * Add a mail attachment by specifying its raw binary data.
     *
     * @param string $content The binary data of the attachemt
     * @param string $filename The name to give to the attachment (it will be used as the filename part of the Content-Disposition header)
     * @param string $mimetype The MIME type of the attachment (it will be the main value of the Content-Type header)
     */
    public function addRawAttachment($content, $filename, $mimetype = 'application/octet-stream')
    {
        $this->addRawAttachmentWithHeaders(
            $content,
            $filename,
            [
                'mimetype' => $mimetype,
            ]
        );
    }

    /**
     * Add a mail attachment by specifying its raw binary data, specifying the headers of the mail MIME part.
     *
     * @param string $content The binary data of the attachemt
     * @param string $filename The name to give to the attachment (it will be used as the filename part of the Content-Disposition header)
     * @param array $headers Additional headers fo the MIME part. Valid values are:
     * - mimetype: the main value of the Content-Type header [default: application/octet-stream]
     * - disposition: the main value of the Content-Disposition header [default: attachment]
     * - encoding: the value of the Content-Transfer-Encoding header [default: base64]
     * - charset: the charset value of the Content-Type header
     * - boundary: the boundary value of the Content-Type header
     * - id: the value of the Content-ID header (without angular brackets)
     * - description: the value of the Content-Description header
     * - location: the value of the Content-Location header
     * - language: the value of the Content-Language header
     */
    public function addRawAttachmentWithHeaders($content, $filename, array $headers = [])
    {
        $headers += [
            'mimetype' => 'application/octet-stream',
            'disposition' => Mime::DISPOSITION_ATTACHMENT,
            'encoding' => Mime::ENCODING_BASE64,
            'charset' => '',
            'boundary' => '',
            'id' => '',
            'description' => '',
            'location' => '',
            'language' => '',
        ];
        $mp = new MimePart($content);
        $mp
            ->setFileName($filename)
            ->setType($headers['mimetype'])
            ->setDisposition($headers['disposition'])
            ->setEncoding($headers['encoding'])
            ->setCharset($headers['charset'])
            ->setBoundary($headers['boundary'])
            ->setId($headers['id'])
            ->setDescription($headers['description'])
            ->setLocation($headers['location'])
            ->setLanguage($headers['language'])
        ;
        $this->attachments[] = $mp;
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

        if (isset($from) && is_array($from) && isset($from[0])) {
            $this->from($from[0], isset($from[1]) ? $from[1] : null);
        }
        $this->template = $template;
        $this->subject = $subject;
        $this->body = (isset($body) && is_string($body)) ? $body : false;
        $this->bodyHTML = (isset($bodyHTML) && is_string($bodyHTML)) ? $bodyHTML : false;
    }

    /**
     * Manually set the plain text body of a mail message (typically the body is set in the template + load method).
     *
     * @param string|false $body Set the text body (false to not use a plain text body)
     */
    public function setBody($body)
    {
        $this->body = $body;
    }

    /**
     * Manually set the message's subject (typically the body is set in the template + load method).
     *
     * @param string $subject
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;
    }

    /**
     * Get the message subject.
     *
     * @return string
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * Get the plain text body.
     *
     * @return string|false
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * Get the html body.
     *
     * @return string|false
     */
    public function getBodyHTML()
    {
        return $this->bodyHTML;
    }

    /**
     * Manually set the HTML body of a mail message (typically the body is set in the template + load method).
     *
     * @param string|false $html Set the html body (false to not use an HTML body)
     */
    public function setBodyHTML($html)
    {
        $this->bodyHTML = $html;
    }

    /**
     * @param \Concrete\Core\Mail\Importer\MailImporter $importer
     * @param array $data
     */
    public function enableMailResponseProcessing($importer, $data)
    {
        foreach ($this->to as $em) {
            $importer->setupValidation($em[0], $data);
        }
        $this->from($importer->getMailImporterEmail());
        $this->body = $importer->setupBody(($this->body === false) ? '' : $this->body);
    }

    /**
     * Set the from address on the message.
     *
     * @param string $email
     * @param string|null $name
     */
    public function from($email, $name = null)
    {
        $this->from = [$email, $name];
    }

    /**
     * Add one or more "To" recipients to the message.
     *
     * @param string $email (separate multiple email addresses with commas)
     * @param string|null $name The name to associate to the email address
     */
    public function to($email, $name = null)
    {
        if (strpos($email, ',') > 0) {
            $email = explode(',', $email);
            foreach ($email as $em) {
                $this->to[] = [$em, $name];
            }
        } else {
            $this->to[] = [$email, $name];
        }
    }

    /**
     * Add one or more "CC" recipients to the message.
     *
     * @param string $email (separate multiple email addresses with commas)
     * @param string|null $name The name to associate to the email address
     */
    public function cc($email, $name = null)
    {
        if (strpos($email, ',') > 0) {
            $email = explode(',', $email);
            foreach ($email as $em) {
                $this->cc[] = [$em, $name];
            }
        } else {
            $this->cc[] = [$email, $name];
        }
    }

    /**
     * Add one or more "BCC" recipients to the message.
     *
     * @param string $email (separate multiple email addresses with commas)
     * @param string|null $name The name to associate to the email address
     */
    public function bcc($email, $name = null)
    {
        if (strpos($email, ',') > 0) {
            $email = explode(',', $email);
            foreach ($email as $em) {
                $this->bcc[] = [$em, $name];
            }
        } else {
            $this->bcc[] = [$email, $name];
        }
    }

    /**
     * Sets the Reply-To addresses of the message.
     *
     * @param string $email (separate multiple email addresses with commas)
     * @param string|null $name The name to associate to the email address
     */
    public function replyto($email, $name = null)
    {
        if (strpos($email, ',') > 0) {
            $email = explode(',', $email);
            foreach ($email as $em) {
                $this->replyto[] = [$em, $name];
            }
        } else {
            $this->replyto[] = [$email, $name];
        }
    }

    /**
     * Set the testing state (if true the email logging never occurs and sending errors will throw an exception).
     *
     * @param bool $testing
     */
    public function setTesting($testing)
    {
        $this->testing = $testing ? true : false;
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
     * @param bool $testing
     * @param mixed $throwOnFailure
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
     * @param array $headers
     */
    public function setAdditionalHeaders($headers)
    {
        $this->headers = $headers;
    }

    /**
     * Sends the email.
     *
     * @param bool $resetData Whether or not to reset the service to its default when this method is done
     *
     * @throws Exception Throws an exception if the delivery fails and if the service is in "testing" state or throwOnFailure is true
     *
     * @return bool Returns true upon success, or false if the delivery fails and if the service is not in "testing" state and throwOnFailure is false
     */
    public function sendMail($resetData = true)
    {
        $config = $this->app->make('config');
        $fromStr = $this->generateEmailStrings([$this->from]);
        $toStr = $this->generateEmailStrings($this->to);
        $replyStr = $this->generateEmailStrings($this->replyto);

        $mail = (new Message())->setEncoding(APP_CHARSET);

        if (is_array($this->from) && count($this->from)) {
            if ($this->from[0] != '') {
                $from = $this->from;
            }
        }
        if (!isset($from)) {
            $from = [$config->get('concrete.email.default.address'), $config->get('concrete.email.default.name')];
            $fromStr = $config->get('concrete.email.default.address');
        }

        // The currently included Zend library has a bug in setReplyTo that
        // adds the Reply-To address as a recipient of the email. We must
        // set the Reply-To before any header with addresses and then clear
        // all recipients so that a copy is not sent to the Reply-To address.
        if (is_array($this->replyto)) {
            foreach ($this->replyto as $reply) {
                $mail->setReplyTo($reply[0], $reply[1]);
            }
        }

        $mail->setFrom($from[0], $from[1]);
        $mail->setSubject($this->subject);
        foreach ($this->to as $to) {
            $mail->addTo($to[0], $to[1]);
        }

        if (is_array($this->cc) && count($this->cc)) {
            foreach ($this->cc as $cc) {
                $mail->addCc($cc[0], $cc[1]);
            }
        }

        if (is_array($this->bcc) && count($this->bcc)) {
            foreach ($this->bcc as $bcc) {
                $mail->addBcc($bcc[0], $bcc[1]);
            }
        }
        $headers = $mail->getHeaders();
        if ($headers->has('messageid')) {
            $messageIdHeader = $headers->get('messageid');
        } else {
            $messageIdHeader = new MessageIdHeader();
            $headers->addHeader($messageIdHeader);
        }

        $headers->addHeaders($this->headers);

        $messageIdHeader->setId();

        $body = new MimeMessage();
        $textPart = $this->buildTextPart();
        $htmlPart = $this->buildHtmlPart();
        if ($textPart === null && $htmlPart === null) {
            $emptyPart = new MimePart('');
            $emptyPart->setType(Mime::TYPE_TEXT);
            $emptyPart->setCharset(APP_CHARSET);
            $body->addPart($emptyPart);
        } elseif ($textPart !== null && $htmlPart !== null) {
            $alternatives = new MimeMessage();
            $alternatives->addPart($textPart);
            $alternatives->addPart($htmlPart);
            $alternativesPart = new MimePart($alternatives->generateMessage());
            $alternativesPart->setType(Mime::MULTIPART_ALTERNATIVE);
            $alternativesPart->setBoundary($alternatives->getMime()->boundary());
            $body->addPart($alternativesPart);
        } else {
            if ($textPart !== null) {
                $body->addPart($textPart);
            }
            if ($htmlPart !== null) {
                $body->addPart($htmlPart);
            }
        }
        foreach ($this->attachments as $attachment) {
            if (!$this->isInlineAttachment($attachment)) {
                $body->addPart($attachment);
            }
        }

        $mail->setBody($body);

        $sendError = null;
        if ($config->get('concrete.email.enabled')) {
            try {
                $this->transport->send($mail);
            } catch (Exception $x) {
                $sendError = $x;
            } catch (Throwable $x) {
                $sendError = $x;
            }
        }
        if ($sendError !== null) {
            if ($this->getTesting()) {
                throw $sendError;
            }
            $l = new GroupLogger(LOG_TYPE_EXCEPTIONS, Logger::CRITICAL);
            $l->write(t('Mail Exception Occurred. Unable to send mail: ') . $sendError->getMessage());
            $l->write($sendError->getTraceAsString());
            if ($config->get('concrete.log.emails')) {
                $l->write(t('Template Used') . ': ' . $this->template);
                $l->write(t('To') . ': ' . $toStr);
                $l->write(t('From') . ': ' . $fromStr);
                if (isset($this->replyto)) {
                    $l->write(t('Reply-To') . ': ' . $replyStr);
                }
                $l->write(t('Subject') . ': ' . $this->subject);
                $l->write(t('Body') . ': ' . $this->body);
            }
            $l->close();
        }

        // add email to log
        if ($config->get('concrete.log.emails') && !$this->getTesting()) {
            $l = new GroupLogger(LOG_TYPE_EMAILS, Logger::INFO);
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
            $l->write(t('Mail Details: %s', $mail->toString()));
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

    /**
     * @deprecated To get the mail transport, call \Core::make(\Zend\Mail\Transport\TransportInterface::class)
     */
    public static function getMailerObject()
    {
        $app = ApplicationFacade::getFacadeApplication();

        return [
            'mail' => (new Message())->setEncoding(APP_CHARSET),
            'transport' => $app->make(TransportInterface::class),
        ];
    }

    /**
     * Convert a list of email addresses to a string.
     *
     * @param array $arr
     *
     * @return string
     */
    protected function generateEmailStrings($arr)
    {
        $str = '';
        for ($i = 0; $i < count($arr); ++$i) {
            $v = $arr[$i];
            if (isset($v[1])) {
                $str .= '"' . $v[1] . '" <' . $v[0] . '>';
            } elseif (isset($v[0])) {
                $str .= $v[0];
            }
            if (($i + 1) < count($arr)) {
                $str .= ', ';
            }
        }

        return $str;
    }

    /**
     * Get the MIME part for the plain text body (if available).
     *
     * @return MimePart|null
     */
    protected function buildTextPart()
    {
        if ($this->body === false) {
            $result = null;
        } else {
            $result = new MimePart($this->body);
            $result->setType(Mime::TYPE_TEXT);
            $result->setCharset(APP_CHARSET);
        }

        return $result;
    }

    /**
     * Determine if an attachment should be used as an inline attachment associated to the HTML body.
     *
     * @param MimePart $attachment
     *
     * @return bool
     */
    protected function isInlineAttachment(MimePart $attachment)
    {
        return $this->bodyHTML !== false
            && $attachment->getId()
            && in_array((string) $attachment->getDisposition(), ['', Mime::DISPOSITION_INLINE], true)
        ;
    }

    /**
     * Get the MIME part for the plain text body (if available).
     *
     * @return MimePart|null
     */
    protected function buildHtmlPart()
    {
        if ($this->bodyHTML === false) {
            $result = null;
        } else {
            $html = new MimePart($this->bodyHTML);
            $html->setType(Mime::TYPE_HTML);
            $html->setCharset(APP_CHARSET);
            $inlineAttachments = [];
            foreach ($this->attachments as $attachment) {
                if ($this->isInlineAttachment($attachment)) {
                    $inlineAttachments[] = $attachment;
                }
            }
            if (empty($inlineAttachments)) {
                $result = $html;
            } else {
                $related = new MimeMessage();
                $related->addPart($html);
                foreach ($inlineAttachments as $inlineAttachment) {
                    $related->addPart($inlineAttachment);
                }
                $result = new MimePart($related->generateMessage());
                $result->setType(Mime::MULTIPART_RELATED);
                $result->setBoundary($related->getMime()->boundary());
            }
        }

        return $result;
    }
}
